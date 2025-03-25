<?php

namespace Assessments\Student\Http\Controllers;

use DB;
use DataTables;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseMain;
use Illuminate\Http\Request;
use App\Models\CourseResource;
use Illuminate\Support\Carbon;
use App\Models\StudentEnrolment;
use App\Models\Settings;
use App\Services\StudentService;
use App\Http\Controllers\Controller;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseResourceCourseMain;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Assessments\Student\Models\AssessmentSubmission;
use Assessments\Student\Models\AssessmentStudentExam;
use Assessments\Student\Services\AssessmentExamService;
use Assessments\Student\Models\AssessmentSubmissionAttachment;
use App\Services\Google;
use Illuminate\Support\Facades\Config;
use App\Models\Refreshers;

class AssessmentController extends Controller
{
    protected $assessmentExamService;
    protected $studentService;
    private $client;
    private $drive;

    public function __construct(AssessmentExamService $assessmentExamService, StudentService $studentService, Google $google) {
        $this->assessmentExamService = $assessmentExamService;
        $this->studentService = $studentService;
        $this->middleware('auth');

        $this->client = $google->client();
        $this->client->setAuthConfig(config('google.service.file'));
        // $this->client->setAccessToken(session('user.token'));
        $this->drive = $google->drive($this->client);
    }
    
    public function index($usrData){
        if ($usrData) {
            $encryptData = Crypt::decryptString($usrData);
            $userData = explode('-', $encryptData);
            if ($studentData) {
                $checkPaymentStatus = '';
                return view('assessments::student.auth.login');
            } else {
                return view('assessments::student.errors.403');
            }
        } else {
            return view('assessments::student.errors.403');
        }
    }

    /*public function OLD_assessmentDashboard(){

        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        $studentEnrollments = StudentEnrolment::where('student_id', $studentId)
                                                ->get();
        
        $studentEnrollmentIds = $studentEnrollments->pluck('id')->toArray();
        $assinedExams = AssessmentStudentExam::whereIn('student_enrol_id', $studentEnrollmentIds)
                                                ->get();
        $assessmentEnrolments = $assinedExams->pluck('student_enrol_id')->toArray();
        
        $getMaincourseIds = StudentEnrolment::whereIn('id', $assessmentEnrolments)->with('courseRun')->get();
        $checkIds = [];
        foreach($getMaincourseIds as $courseRuns){
            $checkIds[] = $courseRuns->courseRun->courseMain->id;
        }

        $examtData = AssessmentMainExam::join('tms_exam_course_mains', function($join){
            $join->on('tms_exams.id', '=', 'tms_exam_course_mains.exam_id');
        })
        ->join('course_mains', function($join){
            $join->on('tms_exam_course_mains.course_main_id', '=', 'course_mains.id');
            })
        ->join('tms_exam_assessments', function($join){
            $join->on('tms_exams.id', '=', 'tms_exam_assessments.exam_id'); 
        })
        ->join('tms_exam_assement_course_runs', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id', '=', 'tms_exam_assessments.id')
                    ->where('tms_exam_assement_course_runs.is_assigned', 1);
        })
        ->join('courses', function($join){
            $join->on('tms_exam_assement_course_runs.course_run_id', '=', 'courses.id');
        })
        ->join('student_enrolments', function($join) use ($studentId){
            $join->on('tms_exam_assement_course_runs.course_run_id', '=', 'student_enrolments.course_id')
                ->where('student_enrolments.student_id', $studentId);
        })
        ->join('tms_student_assessment', function($join){
            $join->on('tms_exam_assement_course_runs.id', '=', 'tms_student_assessment.assessment_run_id')
                ->on('tms_student_assessment.student_enrol_id', '=', 'student_enrolments.id');
        })
        ->get([
                'course_mains.id',
                'course_mains.name',
                'tms_exam_course_mains.course_main_id',
                'tms_exams.exam_duration',
                'courses.course_end_date',
                'courses.id as course_id',
                'tms_exams.exam_time',
                'tms_exam_assessments.type as assessment_type',
                'tms_exam_assessments.title as assessment_name',
                'tms_exams.id AS exam_id',
                // 'tms_student_assessment.is_finished',
                'tms_exam_assement_course_runs.is_assigned',
                'student_enrolments.id AS student_id',
                'student_enrolments.payment_status',
        ]);
        
        return view('assessments::student.dashboard.index', compact('examtData'));
    }*/

    //new function start
    public function assessmentDashboard(){
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;

        $examtData = StudentEnrolment::join('courses', function($join){
            $join->on('student_enrolments.course_id', '=', 'courses.id');
        })
        ->join('tms_student_assessment', function($join){
            $join->on('student_enrolments.id', '=', 'tms_student_assessment.student_enrol_id');
        })
        ->join('tms_exam_assement_course_runs', function($join) use ($today){
            $join->on('tms_student_assessment.assessment_run_id', '=', 'tms_exam_assement_course_runs.id')
                ->where('tms_exam_assement_course_runs.is_assigned', 1)
                ->where('tms_student_assessment.deleted_at', "=", NULL);
            })
        ->join('tms_exam_assessments', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id', '=', 'tms_exam_assessments.id');
        })
        ->join('tms_exam_course_mains', function($join){
            $join->on('tms_exam_assessments.exam_id', '=', 'tms_exam_course_mains.exam_id');
        })
        ->join('course_mains', function($join){
            $join->on('tms_exam_course_mains.course_main_id', '=', 'course_mains.id')
                ->on('courses.course_main_id', '=', 'course_mains.id');
        })
        ->join('tms_exams', function($join){
            $join->on('tms_exam_course_mains.exam_id', '=', 'tms_exams.id');
        })
        ->leftjoin('tms_student_results', function($join){
            $join->on('student_enrolments.id', '=', 'tms_student_results.student_enr_id')
                ->on('tms_exam_assessments.id', '=', 'tms_student_results.assessment_id');
        })
        ->where('student_enrolments.student_id', $studentId)
        ->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED)
        ->where(function ($query) use ($today) {
            $query->where(function ($innerQuery) use ($today) {
                $innerQuery->where('tms_student_assessment.is_reschedule', 0)
                    ->where(function ($viewQuery) use ($today) {
                        $viewQuery->where('tms_exam_assement_course_runs.started_at', '<=', $today)
                            ->where('tms_exam_assement_course_runs.ended_at', '>=', $today);
                    });
                    // ->orWhere(function ($viewQuery) use ($today) {
                    //     $viewQuery->where('tms_exam_assessments.trainee_view_access', 1)
                    //     ->where('tms_exam_assement_course_runs.ended_at', '>=', $today);
                    // });
            })
            ->orWhere(function ($rescheduleQuery) use ($today) {
                $rescheduleQuery->where('tms_student_assessment.is_reschedule', 1)
                    ->where('tms_exam_assement_course_runs.ended_at', '>=', $today);
            });
        })
        ->get([
            'course_mains.id',
            'course_mains.name',
            'courses.id as course_id',
            'courses.course_end_date',
            'courses.course_start_date',
            'student_enrolments.id AS student_id',
            'student_enrolments.payment_status',
            'student_enrolments.status',
            'tms_exam_assessments.id as assessment_id',
            'tms_exam_course_mains.course_main_id',
            'tms_exam_assessments.assessment_duration',
            'tms_exam_assessments.assessment_time',
            'tms_exam_assessments.date_option',
            'tms_exam_assessments.type as assessment_type',
            'tms_exam_assessments.title as assessment_name',
            'tms_exam_assessments.trainee_view_access',
            'tms_exams.id AS exam_id',
            'tms_student_assessment.is_finished',
            'tms_exam_assement_course_runs.is_assigned',
            'tms_student_results.is_passed',
        ]);

        $studentEnrolment = StudentEnrolment::join('courses', function($join){
            $join->on('student_enrolments.course_id', '=', 'courses.id');
        })
        ->where('student_enrolments.student_id', $studentId)
        ->where('student_enrolments.is_feedback_submitted', 0)
        ->get([
            'student_enrolments.id AS student_id',
            'courses.id as course_id',
        ])
        ->pluck('student_id', 'course_id');
        
        $studentEnrolment = json_encode($studentEnrolment);
        
        $traineeFeedback  =  $this->feedbackOn($studentId);
        return view('assessments::student.dashboard.index', compact('examtData', 'traineeFeedback', 'studentEnrolment'));
    }
    //new function end

    //feedback function
    public function feedbackOn($studentId){
        $count = [];
        $traineeFeedback = [];
        $totalCourse = StudentEnrolment::select('student_enrolments.course_id', 'student_enrolments.id AS studentEnrolId')
                        ->join('tms_student_assessment', function($join){
                            $join->on('student_enrolments.id', '=', 'tms_student_assessment.student_enrol_id');
                        })
                        ->where([
                            'student_enrolments.student_id' => $studentId,
                        ])
                        ->groupBy('student_enrolments.course_id')
                        ->get();
                        
        
        foreach($totalCourse as $course){
            $traineeFeedback = StudentEnrolment::select(
                                                    'student_enrolments.is_feedback_submitted', 
                                                    'student_enrolments.course_id', 
                                                    'tms_student_assessment.is_finished',
                                                    'student_enrolments.id AS studentEnrolId',
                                                    'tms_exam_assement_course_runs.started_at'
                                                )
                                                ->join('courses', function($join) use ($course){
                                                    $join->on('student_enrolments.course_id', '=', 'courses.id')
                                                        ->where('courses.id', $course->course_id);
                                                })
                                                ->join('tms_exam_assement_course_runs', function($join) use ($course){
                                                    $join->on('student_enrolments.course_id', '=', 'tms_exam_assement_course_runs.course_run_id')
                                                        ->where('tms_exam_assement_course_runs.course_run_id', $course->course_id);
                                                })
                                                ->join('tms_student_assessment', function($join){
                                                    $join->on('student_enrolments.id', '=', 'tms_student_assessment.student_enrol_id');
                                                })
                                                ->where([
                                                    'student_enrolments.student_id' => $studentId, 
                                                    'is_finished' => 1, 
                                                    'is_feedback_submitted' => 0
                                                ])
                                                ->orderBy('tms_exam_assement_course_runs.started_at', 'DESC')
                                                ->pluck('tms_exam_assement_course_runs.started_at')
                                                ->toArray();
            // ->get();
            // ->pluck('student_enrolments.course_id', 'tms_exam_assement_course_runs.started_at');
            // $count[$course->course_id ."-" . $course->studentEnrolId] = $traineeFeedback;
            // return $count;
        }      
        return $traineeFeedback;
    }

    public function examinationSystem($courseId, $student_id, $examId ,$assessmentId){
        $allQuestion = ExamAssessment::with(['questions.questionStudentAttachments'])->find($assessmentId);
        $examDetails = AssessmentMainExam::find($examId);

        $getAssessmentCourseRun = AssessmentExamCourse::where(['assessment_id' => $assessmentId, 'course_run_id' =>$courseId])->first();
        $assessmentExam = AssessmentStudentExam::where(['assessment_run_id' => $getAssessmentCourseRun->id , 'student_enrol_id' => $student_id])->first();

        $submitedQA = $this->getSubmitedQuestions($allQuestion, $assessmentId, $student_id);

        return view('assessments::student.dashboard.exam', compact('allQuestion' , 'examDetails', 'student_id', 'examId','courseId', 'assessmentId', 'assessmentExam', 'submitedQA'));
    }

    public function getSubmitedQuestions($allQuestion, $assessmentId, $student_id) {
        $submittedQuestions = $allQuestion->questions->pluck('id');
        $submittedAnswer = AssessmentSubmission::with(['examQuestion.questionStudentAttachments', 'examQuestion.questionImages', 'examQuestion.examQuestions'])->whereIn('question_id', $submittedQuestions)
                                ->where('assessment_id', $assessmentId)
                                ->where('student_enr_id', $student_id)
                                ->get();
        return $submittedAnswer;
    }


    public function examSubmission(Request $request){
        $data = $this->assessmentExamService->storeAnswer($request);
        return response()->json($data);
    }

    public function examUpdateFinish(Request $request){
        $assessmentId = $request->get('assessment_id');
        $studentEnrolId = $request->get('student_enrol_id');
        $courseId = $request->get('courseId');
        $data = $this->assessmentExamService->updateExamFlag($assessmentId, $studentEnrolId, $courseId);
        // $this->assessmentExamService->storeAllAnswer(json_decode($request->get('all_question_answer')));
        $this->assessmentExamService->storeAllAnswer($request);
        return response()->json($data);
    }

    public function examStarted(Request $request){
        $assessmentId = $request->get('assessment_id');
        $studentEnrolId = $request->get('student_enr_id');
        $course_run_id = $request->get('course_run_id');
        $data = $this->assessmentExamService->examStarted($assessmentId, $studentEnrolId, $course_run_id);  
        return response()->json($data);
    }

    public function assessmentRules($course_id, $student_id, $exam_id){
        $allQuestion = AssessmentMainExam::with(['questions'])->find($exam_id);
        $exam = AssessmentMainExam::find($exam_id);
        return view('assessments::student.dashboard.exam-rules', compact('exam', 'student_id', 'course_id'));
    }

    public function assessmentRulesModal(Request $request)
    {
        $course_id = $request->get('courseId');
        $student_id = $request->get('studentId');
        $exam_id = $request->get('examID');
        $assessment_id = $request->get('assessmentId');
        $view = view('assessments::student.dashboard.exam-rules', compact('course_id', 'student_id', 'exam_id', 'assessment_id'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }
    
    public function examPreview($assessmentId, $studentId){
        $viewAssessment = $this->assessmentExamService->getStudentQuestionAnswer($assessmentId, $studentId);
        $previewExam =  $viewAssessment['previewExam'];
        $getAssessmentName = $viewAssessment['getAssessmentName'];
        $getAttachment = $viewAssessment['getAttachment'];
        return view('assessments::student.dashboard.view-assessment', compact('previewExam', 'getAssessmentName', 'getAttachment'));
    }

    public function storeAssessment(Request $request){
        $storeData = $this->assessmentExamService->storeAssessmentService($request);
        return $storeData;
    }

    public function storeCkImage(Request $request){
        $uploadedFile = $request->file('upload');
        $filename = "student_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

        // Use Storage to store the file in the desired location
        Storage::putFileAs('public/images/students', $uploadedFile, $filename);

        // You can also use the following code to generate a URL for the stored file
        $url = asset('storage/images/students/' . $filename);
        return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]);
    }

    public function CourseResourcesList()
    {
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        $today = Carbon::now()->format('Y-m-d');
        // DB::raw('MAX(DATE_ADD(courses.course_start_date, INTERVAL 3 YEAR)) AS course_start_date')
        $getCoursesStudentEnroll =  DB::table('student_enrolments')->select(
                                                'student_enrolments.id', 
                                                'courses.id AS courseId',
                                                'courses.course_main_id', 
                                                'course_mains.name', 
                                                'student_enrolments.status', 
                                                'student_enrolments.tpgateway_refno')
                                        ->where('student_enrolments.student_id', $studentId)
                                        ->join('courses', function($join) use($today) {
                                            $join->on('student_enrolments.course_id', '=', 'courses.id')
                                                ->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED)
                                                ->where('courses.course_start_date', '<=', $today);
                                        })
                                        ->join('course_mains', function($join) {
                                            $join->on('courses.course_main_id', '=', 'course_mains.id')
                                            ->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS);        
                                        })
                                        ->groupBy('courses.course_main_id');

        $getCoursesRefresher =  DB::table('refreshers')->select( 
                                        'refreshers.id',
                                        'courses.id AS courseId',
                                        'courses.course_main_id', 
                                        'course_mains.name', 
                                        'refreshers.status',
                                        'refreshers.student_id')
                                ->where('refreshers.student_id', $studentId)
                                ->join('courses', function($join) use($today) {
                                    $join->on('refreshers.course_id', '=', 'courses.id')
                                        ->where('refreshers.status', Refreshers::STATUS_ACCEPTED)
                                        ->where('courses.course_start_date', '<=', $today);
                                })
                                ->join('course_mains', function($join) {
                                    $join->on('courses.course_main_id', '=', 'course_mains.id')
                                    ->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS);        
                                })
                                ->groupBy('courses.course_main_id');

        $combinedQuery = $getCoursesStudentEnroll->union($getCoursesRefresher);
        $subQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
                    ->mergeBindings($getCoursesStudentEnroll)
                    ->select('id','courseId', 'course_main_id', 'name', 'status')
                    ->groupBy('course_main_id');

        $coureResource = DB::table(DB::raw("({$subQuery->toSql()}) as final"))
                                ->mergeBindings($subQuery) 
                                ->simplePaginate(8);

        return view('assessments::student.courseresource.resource-list', compact('coureResource'));
    }

    public function getCourseResourceById($id){
        $resourcesData = CourseResourceCourseMain::where('course_main_id', $id)
                                                  ->join('course_resources', function($join){
                                                        $join->on('course_resources_coursemains.course_resource_id', '=', 'course_resources.id');
                                                  })
                                                  ->simplePaginate(8);
        $courseMainName = CourseMain::find($id);
        if($resourcesData){
            return view('assessments::student.courseresource.preview', compact(['resourcesData', 'courseMainName', 'id']));
        } else {
            return view('assessments::student.courseresource.preview', compact(['resourcesData', 'courseMainName', 'id']));
        }
    }

    public function submitAttachment(Request $request){
        $enrolmentId = $request->student_enr_id;
        $assessmentId = $request->assessment_id;
        $questionId = $request->question_id;

        $documentFolder = Storage::path('public')."/assesment-submission/answer-documents/" . $enrolmentId . "/" . $assessmentId . "/" . $questionId;
        
        if(!File::exists($documentFolder)) {
            $filesaved = File::makeDirectory($documentFolder, 0755, true, true);
        }

        if($request->hasFile('submission_attchment')){
            $attachmentName = $request->file('submission_attchment')->GetClientOriginalName();
            $getAttachmentSize = $request->file('submission_attchment')->getSize();
            $path = "public/assesment-submission/answer-documents/" . $enrolmentId . "/" . $assessmentId . "/" . $questionId . "/";
            Storage::putFileAs($path, $request->file('submission_attchment'), $attachmentName);
            $storeAnsAttachment = AssessmentSubmissionAttachment::create([
                'assessment_id' => $request->assessment_id,
                'question_id'   => $request->question_id,
                'student_enrol_id' => $request->student_enr_id,
                'submission_attchment' => $attachmentName,
                'attachment_size' => $getAttachmentSize,
            ]);
        }
        return ['status' => TRUE, 'msg' => 'Attachment submitted.', 'filename' => $attachmentName];
    }

    Public function removeAttachment(Request $request){

        $attachmentName = $request->get('filename');
        $curruntStudentId = $request->get('curruntStudentId');
        $examId = $request->get('assessmentID');
        $questionId = $request->get('questionId');
        if(!empty($attachmentName)){
            $deteleAttachment = AssessmentSubmissionAttachment::where(['question_id'=> $questionId, 'student_enrol_id'=> $curruntStudentId])->where('submission_attchment', $attachmentName)->first();
            $name = $deteleAttachment->submission_attchment;
            $deteleAttachment->delete();
            $attachmentPath = Storage::path('public/assesment-submission/answer-documents/'.$curruntStudentId."/".$examId."/".$questionId."/".$name);
            unlink($attachmentPath);
        }       
        return ['status' => TRUE, 'msg' => 'Attachment deleted.'];
    }

    public function getAttachment(Request $request){
        $getSubmissionAttachment = AssessmentSubmissionAttachment::where('student_enrol_id', $request->studentenrol)->get();
        return ['status' => TRUE, 'data' => $getSubmissionAttachment];
    }

    //comment code trainee feedback
    /*public function traineeFeedback(Request $request){

        $studentEnrol = $request->get('studentEnrolId');
        $studentFeedback = StudentEnrolment::find($studentEnrol);
        $studentFeedback->is_feedback_submitted = 1;
        $studentFeedback->update();

        return ['status' => TRUE, 'msg' => 'Feedback submitted successfully.'];
    }*/

    public function traineeFeedback(){
        $getSettings = Settings::where('group', 'feedback')->get();
        $feedbackSettingData = [];
        foreach($getSettings as $value){
            $feedbackSettingData[$value->name] = $value->val;
        }
        return view('assessments::student.feedback.index', compact('feedbackSettingData'));
    }

    public function examLastTime(Request $request) {
        $getAssessmentCourseRun = AssessmentExamCourse::where(['assessment_id' => $request->assessment_id, 'course_run_id' =>$request->course_run_id])->first();
        $assessmentExam = AssessmentStudentExam::where(['assessment_run_id' => $getAssessmentCourseRun->id , 'student_enrol_id' => $request->student_enrol_id])->first();
        if($request->last_time){
            $assessmentExam->assessment_duration = $request->last_time;
            $assessmentExam->save();
        }
    }

    public function autoSaveAnswer(Request $request) {

        $submittedData = AssessmentSubmission::where(['question_id' => $request->question_id, 'assessment_id' => $request->assessment_id, 'student_enr_id' => $request->student_enrol_id])->first();

        $submittedData->submitted_answer =  $request->submitted_student_answer;
        $submittedData->save();
    }

    public function uploadScreenshotToDrive(Request $request){
        \Log::info("uploadScreenshotToDrive start===>>" . $request->enrolment_id);
        // $file = $request->file('file');
        // $mime_type = $file->getMimeType();
        // $extension = $file->getClientOriginalExtension();
        // $title = $file->getClientOriginalName();
        // $description = $file->hashName();
        
        // $student = StudentEnrolment::with('student')->find($enrolmentId);
        // $fileName = $student->course_id . '-' . $student->student->nric . '-' .$student->id .'.'.$extension;
        
        // // $teamDriveId = '0AAypVDhiKcXJUk9PVA'; // Replace with your actual folder ID
        // $teamDriveId = '1GMFQzhC311-MNz2ZMA6T2rkY7-z0XNUb'; // Replace with your actual folder ID
        // Config::set('filesystems.disks.google.teamDriveId', $teamDriveId);
        // // $folderName = 'Feedback Screenshots';
        // $folderName = $teamDriveId;
        // $link = Storage::disk('google')->putFileAs($folderName, $file, $fileName);
        
        // \Log::info("uploadScreenshotToDrive end ===>>" . $request->enrolment_id);
        

        $enrolmentId = $request->enrolment_id;
        $feedbackSubmitted = $request->get('is_feedback_submitted');
        
        if(!empty($feedbackSubmitted)) {
            $student = StudentEnrolment::find($enrolmentId);
            $student->is_feedback_submitted = 1;
            $student->save();
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function getFeedbackData(Request $request){
        \Log::info("call get data start");
        $allData = $request->all();
        
        $assesmentStartTime = null;
        
        if(isset($allData['courseid'])){
            $courseId = $allData['courseid'];
            $studentEnrolmentId = $allData['student_enr_id'];
            $wsqCourses = Course::whereHas('courseMain', function($query){
                $query->where('course_type', CourseMain::COURSE_TYPE_WSQ);
            })
            ->where('id', $courseId)
            ->pluck('id')
            ->toArray();
            
            if(!empty($wsqCourses)){
                $assesmentStartTime = AssessmentExamCourse::where('course_run_id', $wsqCourses)
                                    ->select(DB::raw('max(started_at) AS course_started_at'), 'course_run_id')
                                    ->groupBy('course_run_id')
                                    ->pluck('course_started_at', 'course_run_id')
                                    ->toArray();

                $courseId = array_keys($assesmentStartTime);
                $startTime = array_values($assesmentStartTime);


                $assesmentStartTime = AssessmentExamCourse::select(
                                            'tms_exam_assement_course_runs.id', 
                                            'tms_exam_assement_course_runs.assessment_id', 
                                            'tms_exam_assement_course_runs.course_run_id', 
                                            'tms_exam_assement_course_runs.started_at',
                                            'tms_student_assessment.student_enrol_id'
                                        )
                                        ->leftJoin('tms_student_assessment', function($join){
                                            $join->on('tms_exam_assement_course_runs.id', '=' ,'tms_student_assessment.assessment_run_id');
                                        })
                                        ->where([
                                            'tms_exam_assement_course_runs.course_run_id' => $courseId[0], 
                                            'tms_exam_assement_course_runs.started_at' => $startTime[0],
                                            'tms_student_assessment.student_enrol_id' => $studentEnrolmentId,
                                            'tms_student_assessment.is_reschedule' => 0
                                        ])
                                        ->first();
                $assesmentStartTime = $assesmentStartTime ?? null;
            }
        } else {
            $courseId = array_keys($allData);
            $studentEnrollId = array_values($allData);
            $today = Carbon::today();
    
            $wsqCourses = Course::whereHas('courseMain', function($query){
                $query->where('course_type', CourseMain::COURSE_TYPE_WSQ);
            })
            ->whereIn('id', $courseId)
            ->pluck('id')
            ->toArray();
    
            $assesmentStartTime = AssessmentExamCourse::whereIn('course_run_id', $wsqCourses)
                                    ->select(DB::raw('max(started_at) AS course_started_at'), 'course_run_id')
                                    ->groupBy('course_run_id')
                                    ->pluck('course_started_at', 'course_run_id')
                                    ->toArray();
            asort($assesmentStartTime);
            
            foreach($assesmentStartTime as $id => $date){
                $startDate = Carbon::parse($date)->startOfDay();
                if(!$startDate->eq($today)){
                    unset($assesmentStartTime[$id]);
                }
            }
        }

        
        \Log::info("call get data end");
        if($assesmentStartTime) {
            return response()->json(['status' => 'success', 'data' => $assesmentStartTime]);
        } else {
            return response()->json(['status' => 'fail', 'data' => []]);
        }
    }

    public function downloadAllResourceZip(Request $request)
    {
        $id = $request->id;
        $resourcesData = CourseResourceCourseMain::where('course_main_id', $id)
            ->join('course_resources', function($join) {
                $join->on('course_resources_coursemains.course_resource_id', '=', 'course_resources.id');
            })->get();
        $zipFileName = $id . "_All_Course_Resource.zip";
        $zipFilePath = storage_path("app/public/" . $zipFileName);

        $zip = new \ZipArchive();

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($resourcesData as $resource) {
                $resourcePath = storage_path('app/public/course-resources/' . $resource->resource_file);
                if (file_exists($resourcePath)) {
                    $zip->addFile($resourcePath, $resource->resource_file);
                }
            }
            $zip->close();
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
        return response()->json(['error' => 'Failed to create the zip file.'], 500);
    }   
}