<?php

namespace Assessments\Student\Services;

use App\Models\Course;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;
use App\Models\StudentEnrolment;
use App\Services\StudentService;
use Illuminate\Support\Facades\Log;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Assessments\Student\Models\AssessmentSubmission;
use Assessments\Student\Models\AssessmentStudentExam;
use Assessments\Student\Models\AssessmentSubmissionResult;
use Assessments\Student\Models\AssessmentSubmissionAttachment;

class AssessmentExamService {
    protected $studentService;
    protected $encryptId;

    public function __construct(StudentService $studentService){
        $this->studentService = $studentService;
    } 

    public function storeAnswer($request){
        if( $request->method() == 'POST') {
            $questions = json_decode($request->get('all_question_answer'));
            $assessmentId = $request->get('assessment_id');
            $studentEnrId = $request->get('student_enrol_id');
            foreach($questions as $key => $value){
                $existingSubmission = AssessmentSubmission::where([
                    'question_id' => $value->question_id,
                    'assessment_id' => $assessmentId,
                    'student_enr_id' => $studentEnrId,
                ])->first();
                
                if (!empty($existingSubmission)) {
                    $existingSubmission->increment('edited_count');
                    $existingSubmission->update([
                        "submitted_answer" => $value->submitted_answer,
                    ]);
                }
            }           
            return ['status' => TRUE, 'msg' => 'Your answers have been successfully saved.'];    
        }
        else{
            return ['status' => FALSE, 'msg' => 'Invalid Request'];
        }   
        
    }

    public function storeAllAnswer($data){
        $questions = json_decode($data->get('all_question_answer'));
        $assessmentId = $data->get('assessment_id');
        $studentEnrId = $data->get('student_enr_id');
        foreach($questions as $key => $value){
            $existingSubmission = AssessmentSubmission::where([
                'question_id' => $value->question_id,
                'assessment_id' => $assessmentId,
                'student_enr_id' => $studentEnrId,
            ])->first();
            
            if (!empty($existingSubmission)) {
                $existingSubmission->increment('edited_count');
                $existingSubmission->update([
                    "submitted_answer" => $value->submitted_answer,
                ]);
            }
        }
    }

    public function updateExamFlag($assessmentId, $studentEnrolId, $courseId){

        \Log::info("assessment " .$assessmentId);
        \Log::info("course " .$courseId);
        $getAssessmentCourseRun = AssessmentExamCourse::where(['assessment_id' => $assessmentId, 'course_run_id' =>$courseId])->first();
        $assessmentExam = AssessmentStudentExam::where(['assessment_run_id' => $getAssessmentCourseRun->id , 'student_enrol_id' => $studentEnrolId])->first();
        $submittedTime = Carbon::now()->format('Y-m-d H:i:s');
        if($assessmentExam){
            $assessmentExam->is_finished = 1;
            $assessmentExam->is_started = 2;
            $assessmentExam->finished_time = $submittedTime;
            $assessmentExam->save();
            return ['status' => TRUE, 'msg' => 'Your answer has been submitted.'];
        }
        else{
            return ['status' => FALSE, 'msg' => 'Invalid Request.'];
        }
        
    }

    public function examStarted($assessmentId, $studentEnrolId, $course_run_id){

        $getQuestions = AssessmentQuestions::where('assessment_id', $assessmentId)->get();

        foreach($getQuestions as $getQuestion){
            $storeSubmitAssessment = AssessmentSubmission::updateOrCreate([
                'question_id'   => $getQuestion->id,
                'assessment_id' => $getQuestion->assessment_id,
                'student_enr_id' => $studentEnrolId,
            ],[
                'question_id'   => $getQuestion->id,
                'assessment_id' => $getQuestion->assessment_id,
                'student_enr_id' => $studentEnrolId,
            ]);

            // save answer format if it's null
            \Log::info('check if submitted answer is null or not ==>> '. ($storeSubmitAssessment->submitted_answer == null));
            if($storeSubmitAssessment->submitted_answer == null){
                $storeSubmitAssessment->submitted_answer = $getQuestion->answer_format;
                $storeSubmitAssessment->update();
            }

        }

        $getAssessmentCourseRun = AssessmentExamCourse::where(['assessment_id' => $assessmentId, 'course_run_id' =>$course_run_id])->first();
        $assessmentExam = AssessmentStudentExam::where(['assessment_run_id' => $getAssessmentCourseRun->id , 'student_enrol_id' => $studentEnrolId])->first();
        
        /*$getExamId = ExamAssessment::find($assessmentId);
        $getExamTime = AssessmentMainExam::find($getExamId->exam_id);
        $compareExamTime = Carbon::createFromTimestampUTC($getExamId->assessment_duration)->diffInSeconds();
        $compareAssessmentTime = Carbon::createFromTimestampUTC($assessmentExam->assessment_duration)->diffInSeconds();*/
    
       /* $examTime = $assessmentExam->assessment_duration;*/
        $actualRemaingTime = Carbon::parse($assessmentExam->assessment_duration)->format('H:i:s');
        $remainingSeconds = Carbon::parse($assessmentExam->assessment_duration)->format('H:i:s');

        /*$assessmentTime = Carbon::parse($assessmentExam->assessment_duration);
        $cT = Carbon::parse($actualRemaingTime);
        $diffDurationSeconds = $assessmentTime->diffInSeconds($cT);*/

        $d = explode(':', $remainingSeconds);
        $examDurationSeconds = ($d[0] * 3600) + ($d[1] * 60) + $d[2];

        /*$ed = Carbon::parse($examDurationSeconds);
        $dd = Carbon::parse($diffDurationSeconds);

        $remainingSeconds = $ed->diffInSeconds($dd);*/

        if($assessmentExam->is_started != 1){
            $stratedTime = Carbon::now()->format('Y-m-d H:i:s');
            $assessmentExam->is_started = 1;
            $assessmentExam->started_time = $stratedTime;
            $assessmentExam->save();
        }

        return ['status' => TRUE, 'msg' => 'Your exam has been started.', 'time_remaining' => $actualRemaingTime, 'inSecond' => $examDurationSeconds];
        
    }

    public function getStudentQuestionAnswer($assessmentId, $studentId){

        $previewExam = AssessmentQuestions::with(['questionStudentAttachments', 'questionImages'])
        ->join('tms_student_submitted_assessments', function($join){
                $join->on('tms_questions.id' ,'=', 'tms_student_submitted_assessments.question_id');
        })
        ->where('tms_student_submitted_assessments.student_enr_id' ,'=', $studentId)
        ->where('tms_student_submitted_assessments.assessment_id' ,'=', $assessmentId)
        ->orderBy('tms_questions.id', 'ASC')
        ->get([
            'tms_questions.question',
            'tms_questions.question_weightage',
            'tms_questions.id',
            'tms_student_submitted_assessments.question_id AS answer_que_id',
            'tms_student_submitted_assessments.submitted_answer AS submitted_answer',
            'tms_student_submitted_assessments.answer_image AS submitted_image',
        ]);

        // ->whereHas('questionStudentAttachments', function($query) use($studentId) {
        //     $query->where('assessment_submission_attachments.student_enrol_id', $studentId);
        // })

        $getAttachment = AssessmentSubmissionAttachment::where(['assessment_id' => $assessmentId, 'student_enrol_id' => $studentId])->get();
        $getAssessmentName = ExamAssessment::find($assessmentId);

        return ['previewExam' => $previewExam, 'getAssessmentName' => $getAssessmentName, 'getAttachment' => $getAttachment];
    }

    public function  assessmentReport(){

        $assessmentReport = Course::join('tms_exam_assement_course_runs', function($join){
            $join->on('courses.id', '=', 'tms_exam_assement_course_runs.course_run_id');
        })

        ->join('tms_exam_assessments', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id', '=', 'tms_exam_assessments.id');
        })
        ->join('tms_exams', function($join){
            $join->on('tms_exam_assessments.exam_id', '=', 'tms_exams.id');
        })
        ->join('tms_student_assessment', function($join){
            $join->on('tms_student_assessment.assessment_run_id', '=', 'tms_exam_assement_course_runs.id');
        })
       
        ->join('student_enrolments', function($join) {
            $join->on('tms_student_assessment.student_enrol_id', '=', 'student_enrolments.id');
        })

        ->join('students', function($join){
            $join->on('student_enrolments.student_id', '=', 'students.id');
        })

        ->leftjoin('tms_student_results', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id','=', 'tms_student_results.assessment_id')
                ->on('student_enrolments.id','=', 'tms_student_results.student_enr_id');
        })
        
        ->get([
            'tms_exams.id AS exam_id',
            'courses.course_end_date as exam_date',
            'courses.id As courserun_id',
            'courses.tpgateway_id As courserun_tpg_id',
            'tms_exams.exam_time',
            'tms_exam_assessments.type as assessment_type',
            'tms_exam_assessments.title as assessment_name',
            'courses.id as course_id As courserun_id',
            'students.nric',
            'students.name',
            'students.email',
            'students.mobile_no',
            'tms_student_assessment.is_started',
            'tms_student_results.is_passed',
            'tms_student_results.updated_at',
            'student_enrolments.id AS studentenr',
        ]);

        return $assessmentReport;
    }
}