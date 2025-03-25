<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Carbon;
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
use Assessments\Student\Models\AssessmentMainCourse;
use Assessments\Student\Models\AssessmentSubmission;
use Assessments\Student\Models\AssessmentStudentExam;
use Assessments\Student\Models\AssessmentSubmissionResult;
use Assessments\Student\Models\AssessmentSubmissionAttachment;

class CommonAssessmentService {
    // protected $studentService;
    // protected $encryptId;

    public function __construct(){
        // $this->studentService = $studentService;
    } 

    public function storeAssessmentService($requestData)
    {

        $commonService = new \App\Services\CommonService;
        $assessmentIds  = $requestData->get('assessment_ids');
        $submittedAssessmentId         = $requestData->get('submitted_assessment_id');
        $studentEnrId   = $requestData->get('student_enr_id');
        $isPassed       = $requestData->get('pass_marking');
        $courseRunId    = StudentEnrolment::find($studentEnrId)->course_id;
        $assementPassed = $requestData->get('is_passed');
        $currentStudentEnrol = StudentEnrolment::find($studentEnrId);
        $reviewedTime = Carbon::now()->format('Y-m-d H:i:s');
        $reScheduleTime = Carbon::now()->format('Y-m-d H:i:s');

        if(!empty($assessmentIds) && !empty($isPassed)){
            $assessmentGet = AssessmentSubmission::where('student_enr_id', $studentEnrId)
                                ->where('assessment_id', $submittedAssessmentId)
                                ->update(['is_pass' => null]);
            foreach($isPassed as $value){
                if(isset($value['value'])){
                    $assessOutcomes = $value['value'] == 1 ? "c" : "nyc";
                } else {
                    $assessOutcomes = $requestData->is_passed;
                }
                $markings = AssessmentSubmission::find($value['id']);
                $markings->is_pass = $assessOutcomes;
                $markings->is_reviewed = 1;
                $markings->save();
            }
        } else {
            AssessmentSubmission::where('student_enr_id', $studentEnrId)
                                ->where('assessment_id', $submittedAssessmentId)
                                ->update(['is_pass' => null]);
        }

        $assessmentCount = AssessmentSubmission::where('student_enr_id', $studentEnrId)
                                                ->where('assessment_id', $submittedAssessmentId)
                                                ->count();

        $passingCount   = AssessmentSubmission::where('student_enr_id', $studentEnrId)
                                                ->where('assessment_id', $submittedAssessmentId)
                                                ->where('is_pass', 'c')
                                                ->count();

        if($passingCount == $assessmentCount){
            $assessmentResult = AssessmentSubmissionResult::updateOrCreate([
                'student_enr_id' => $studentEnrId,
                'assessment_id'  => $submittedAssessmentId,
            ],[
                'is_passed'         =>  $assementPassed == 'reschedule'? $assementPassed : 'c',
                'assessment_recovery'  => $requestData->get('assessment_recovery'),
                'assessment_reschedule_note'  => $requestData->get('assessment_reschedule_note'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);
                        
        } elseif($passingCount == 0 && $assessmentCount == 1) {

            $assessmentResult = AssessmentSubmissionResult::updateOrCreate([
                'student_enr_id' => $studentEnrId,
                'assessment_id'  => $submittedAssessmentId,
            ],[
                'is_passed' =>  $assementPassed == 'reschedule'? $assementPassed : 'nyc',
                'assessment_recovery'  => $requestData->get('assessment_recovery'),
                'assessment_reschedule_note'  => $requestData->get('assessment_reschedule_note'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);

        } elseif ($passingCount == 0) {
            $assessmentResult = AssessmentSubmissionResult::updateOrCreate([
                'student_enr_id' => $studentEnrId,
                'assessment_id'  => $submittedAssessmentId,
            ],[
                'is_passed' =>  $assementPassed == 'reschedule'? $assementPassed : 'nyc',
                'assessment_recovery'  => $requestData->get('assessment_recovery'),
                'assessment_reschedule_note'  => $requestData->get('assessment_reschedule_note'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);

        } elseif($passingCount < $assessmentCount) {
            $assessmentResult = AssessmentSubmissionResult::updateOrCreate([
                'student_enr_id' => $studentEnrId,
                'assessment_id'  => $submittedAssessmentId,
            ],[
                'is_passed' =>  $assementPassed == 'reschedule'? $assementPassed : 'nyc',
                'assessment_recovery'  => $requestData->get('assessment_recovery'),
                'assessment_reschedule_note'  => $requestData->get('assessment_reschedule_note'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);
            
        } else {
            $assessmentResult = AssessmentSubmissionResult::updateOrCreate([
                'student_enr_id' => $studentEnrId,
                'assessment_id'  => $submittedAssessmentId,
            ],[
                'is_passed' =>  $assementPassed == 'reschedule'? $assementPassed : 'nyc',
                'assessment_recovery'  => $requestData->get('assessment_recovery'),
                'assessment_reschedule_note'  => $requestData->get('assessment_reschedule_note'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);
        }

        $assessmentId = AssessmentExamCourse::where('assessment_id', $submittedAssessmentId)->where('course_run_id', $courseRunId)->first();
        if($assementPassed == 'reschedule'){
            $studentExam = AssessmentStudentExam::where('assessment_run_id', $assessmentId->id)
                                                ->where('student_enrol_id', $studentEnrId)
                                                ->update([
                                                    'is_reviewed' => 1,
                                                    'is_started' => 0,
                                                    'is_finished' => 0,
                                                    'reviewed_time' => $reviewedTime,
                                                    'is_reschedule' => 1,
                                                    'is_reschedule_time' => $reScheduleTime
                                                    ]);

        } else {

            $studentExam = AssessmentStudentExam::where('assessment_run_id', $assessmentId->id)
                                                ->where('student_enrol_id', $studentEnrId)
                                                ->update(['is_reviewed' => 1, 'reviewed_time' => $reviewedTime]);
        }

        $courseMain = Course::find($courseRunId);
        $examCourseMain = AssessmentMainCourse::where('course_main_id', $courseMain->course_main_id)->first();

        if(!empty($examCourseMain)){
            $examId = AssessmentMainExam::find($examCourseMain->exam_id);
        }

        $totalAssessment = ExamAssessment::where('exam_id' , $examId->id)->get()->count();
        $getStudentResult = AssessmentSubmissionResult::where('student_enr_id', $studentEnrId)->pluck('is_passed')->toArray();

        $ccArrayResult = array_count_values($getStudentResult);

        if($totalAssessment < count($getStudentResult)){
            $finalResult = 'incomplete';
            
        } else {

            if(!empty($ccArrayResult['c']) && $ccArrayResult['c'] == count($getStudentResult)){
                $finalResult = 'c';
    
            } elseif(!empty($ccArrayResult['reschedule']) && $ccArrayResult['reschedule'] <= count($getStudentResult)){
                $finalResult = 'reschedule';
            }
            else {
                $finalResult = 'nyc';
            } 
        } 

        $currentStudentEnrol->assessment =  $finalResult;
        $currentStudentEnrol->update();

        if($assessmentResult){
            return ['success' => true, 'message' => 'success', 'course_run_main' => $courseRunId];
        }

    }

    public function getAssessmentPdf($assessmentId, $studentEnrId){

        /* AssessmentSubmission::select('tms_student_submitted_assessments.submitted_answer', 'tms_questions.question')
                    ->leftjoin('tms_questions',function($join) {
                        $join->on('tms_student_submitted_assessments.question_id','=', 'tms_questions.id');
                    })
                    ->where([
                        'tms_student_submitted_assessments.assessment_id' => $assessmentId, 
                        'tms_student_submitted_assessments.student_enr_id' => $studentEnrId
                    ])
                    ->get(); */
        $getData =  AssessmentQuestions::with(['questionStudentAttachments', 'questionImages'])
                                        ->join('tms_student_submitted_assessments', function($join) use($assessmentId, $studentEnrId){
                                                $join->on('tms_questions.id' ,'=', 'tms_student_submitted_assessments.question_id')
                                                ->where('tms_student_submitted_assessments.student_enr_id' ,'=', $studentEnrId)
                                                ->where('tms_student_submitted_assessments.assessment_id' ,'=', $assessmentId);
                                        })
                                        ->orderBy('tms_questions.id', 'ASC')
                                        ->get([
                                            'tms_questions.question',
                                            'tms_questions.question_weightage',
                                            'tms_questions.id',
                                            'tms_student_submitted_assessments.question_id AS answer_que_id',
                                            'tms_student_submitted_assessments.submitted_answer AS submitted_answer',
                                            'tms_student_submitted_assessments.answer_image AS submitted_image',
                                        ]);


        $getAttachment = AssessmentSubmissionAttachment::where(['assessment_id' => $assessmentId, 'student_enrol_id' => $studentEnrId])->get();
        $getAssessmentName = ExamAssessment::find($assessmentId);
        $getCourseName = AssessmentExamCourse::select('course_mains.name as courseName')
                                                ->join('courses', 'tms_exam_assement_course_runs.course_run_id', '=', 'courses.id')
                                                ->join('course_mains', 'courses.course_main_id', '=', 'course_mains.id')
                                                ->where('assessment_id', $assessmentId)->first();
        $getStudentName = StudentEnrolment::with('student')->find($studentEnrId);

        $pdfData['assessments'] = $getData;
        $pdfData['getStudentAttachment'] = $getAttachment;
        $pdfData['getAssessmentName'] = $getAssessmentName;
        $pdfData['getCourseName'] = $getCourseName;
        $pdfData['getStudentName'] = $getStudentName;

        return $pdfData;
    }

}