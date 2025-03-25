<?php

namespace App\Console\Commands;

use Log;
use App\Models\Course;
use App\Models\Student;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\StudentEnrolment;
use App\Mail\EmailStudentExamLink;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\Mail;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentMaincourse;
use Assessments\Student\Models\AssessmentStudentExam;

class GenerateAssessmentForCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:generate-assessments-for-courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Generate Assessments for Courses.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Console Command Assign Assessment Call");
        $today = Carbon::now()->format('Y-m-d');
        $curruntTime = Carbon::now();
        // $courses = Course::where('course_end_date', $today)->where('assessment_status',0)->get();
        $courses = Course::where('course_start_date', $today)->where('assessment_status', 0)->get();
        $commonService = new \App\Services\CommonService;
        foreach($courses as $courseRun){
                     
            $getExamTime = $commonService->getExamIdByRunId($courseRun->course_main_id);
            if($getExamTime){
                $examTime = Carbon::parse($getExamTime);
                $cT = Carbon::parse($curruntTime);
                $diffDuration = $examTime->diffInMinutes($cT);
                // dd($diffDuration);
                
                if($diffDuration <= 60){
                    Log::info("========= Start Cron Job with Time Diff ========");
                    $assignStudent =   StudentEnrolment::select('students.name as student_name', 'students.id as student_id', 'students.nric', 'students.email', 'courses.course_main_id', 'student_enrolments.id as studentenr_id')
                        ->join('courses', 'student_enrolments.course_id', '=', 'courses.id')
                        ->join('students', 'student_enrolments.student_id', '=', 'students.id')
                        ->where('student_enrolments.status', '=', StudentEnrolment::STATUS_ENROLLED)
                        ->where('courses.id', $courseRun->id)->get();

                
                    $courseMainId = Course::find($courseRun->id);

                    $startDate = $courseMainId->course_start_date;
                    $endDate = $courseMainId->course_end_date;

                    Log::info("========= Course run Id " . $courseRun->id . " ========");
                
                    if(count($assignStudent) > 0){
                        $assessments = ExamAssessment::select('tms_exam_assessments.id')
                                            ->join('tms_exam_course_mains', 'tms_exam_course_mains.exam_id', '=', 'tms_exam_assessments.exam_id')
                                            ->where('tms_exam_course_mains.course_main_id', $courseMainId->course_main_id)
                                            ->get()->pluck('id')->toArray();
                        
                        Log::info('==== Exam Assessment ID =====');
                        Log::info(print_r($assessments,true));
                        
                        if(count($assessments) > 0){

                            $assignedCourseRuns = AssessmentExamCourse::whereIn('assessment_id', $assessments)
                                                        ->where('course_run_id', $courseRun->id)->get();
                            
                            $assessmentWithTime = ExamAssessment::whereIn('id', $assessments)->get();
                            
                            Log::info('==== Exam Assessment With Time =====');
                            Log::info(print_r($assessmentWithTime,true));

                            if($assignedCourseRuns->count() == 0){
                                foreach($assessmentWithTime as $assessment){
                                    $examCourseRuns = new AssessmentExamCourse;
                                    $examCourseRuns->assessment_id = $assessment->id;
                                    $examCourseRuns->course_run_id = $courseRun->id;
                                    if($assessment->date_option == 1){
                                        $examCourseRuns->started_at = $startDate . " " . $assessment->assessment_time;
                                        $examCourseRuns->ended_at   = $endDate . " 23:59:59";
                                    } else {
                                        $examCourseRuns->started_at = $endDate . " " . $assessment->assessment_time;
                                        $examCourseRuns->ended_at   = $endDate . " 23:59:59";
                                    }
                                    $examCourseRuns->save();
                                }
                            }                    
                        }

                        $flag = false;

                        foreach ($assignStudent as $student) {
                            $assementIds = [];
                            $assignedAssessment = AssessmentExamCourse::where('course_run_id', $courseRun->id)->get();

                            if($assignedAssessment->count() > 0){
                                foreach($assignedAssessment as $assignAssessment){                            

                                    $assessment = AssessmentExamCourse::where('id', $assignAssessment->id)->first();
                                    $assessment->is_assigned = 1;
                                    $assessment->update();
                                    if($assessment){
                                        $flag = true;
                                    }
                                
                                }
                                $assementIds = $assignedAssessment->pluck('id')->toArray();
                            }


                            Log::info("========= Student Enr Id " . $student->studentenr_id . " ========");

                            $assignedAssessmentToStudent = AssessmentStudentExam::whereIn('assessment_run_id', $assementIds)
                                                        ->where('student_enrol_id', $student->studentenr_id)->get();
                            
                            if($assignedAssessmentToStudent->count() == 0){
                                foreach($assignedAssessment as $assignedStudent){
                                    $getAssessmentTimeDuration = ExamAssessment::find($assignedStudent->assessment_id);

                                    $studentExam = new AssessmentStudentExam;
                                    $studentExam->assessment_run_id = $assignedStudent->id;
                                    $studentExam->student_enrol_id = $student->studentenr_id;
                                    $studentExam->exam_duration = $getAssessmentTimeDuration->assessment_duration;
                                    $studentExam->assessment_duration = $getAssessmentTimeDuration->assessment_duration;
                                    $studentExam->save();
                                }
                            }

                            if($flag){
                                $studentNric = Student::where('id', $student->student_id)->first();
                                // Log::info(print_r($studentNric, true));
                            /* if (isset($studentNric->nric)) {
                                    $emailTemplate = EmailTemplate::where('slug', 'assessment-exam')->first();

                                    if(!empty($emailTemplate)) {
                                        $shortStudentUrl = env('APP_URL')."login";
                                        $content = $emailTemplate['template_text'];
                                        $content = str_ireplace("{studentname}", $student->student_name, $content);
                                        $content = str_ireplace("{assessmentexamurl}", $shortStudentUrl, $content);
                                        $content = str_ireplace("{user_id}", $student->nric, $content);
                                        $content = str_ireplace("{password}", $student->nric, $content);
                        
                                        if (Mail::to($student->email)->send(new EmailStudentExamLink($content))) {
                                            Log::info("Mail send Successfully.");
                                        } 
                                    }
                                }*/
                            }
                        }

                        if($assignStudent){
                            Log::info("========= Start Cron Job to assign assessment ========");
                            Log::info($assignStudent);
                            Log::info("========= End Cron Job to assessment ========");
                        }
                    }
                    else {
                        Log::info("========= Start assign assessment and create ========");
                        Log::info("In this course havan't any student");
                        Log::info("========= End assign assessment and create ========");
                    }

                    $courseRun->assessment_status = 1;
                    $courseRun->update();

                    Log::info("========= End Cron Job with Time Diff ========");
                }
            }
        }

        Log::info("Console Command Assign Assessment End.");
    }
}
