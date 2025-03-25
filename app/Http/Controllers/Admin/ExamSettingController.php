<?php

namespace App\Http\Controllers\admin;

use Log;
use Auth;
use DataTables;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseMain;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Services\CommonAssessmentService;
use App\Models\StudentEnrolment;
use App\Mail\EmailStudentExamLink;
use App\Http\Controllers\Controller;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ExamSettingStoreRequest;
use App\Http\Requests\QuestionAnsStoreRequest;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Assessments\Student\Models\AssessmentMainCourse;
use Assessments\Student\Models\AssessmentStudentExam;
use Assessments\Student\Models\AssessmentQuestionImage;
use Assessments\Student\Models\AssessmentStudentExamsUrls;
use Assessments\Student\Models\AssessmentSubmissionResult;
use Assessments\Student\Models\AssessmentSubmissionAttachment;
use PDF;
use Illuminate\Support\Facades\File;

class ExamSettingController extends Controller
{
    //
    public function __construct(CourseService $courseService, CommonService $commonService)
    {
        $this->middleware('auth');
        $this->courseService = $courseService;
        $this->commonService = $commonService;
    }


    //Temp code for soft delete need to change in future (13-10-2023)
    public function index()
    {
        return view('admin.examsettings.index');
    }

    public function listDatatable(Request $request)
    {
        $records = $this->commonService->getListDatatableOfAdmin();
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('course_main_name', function($row) {
                    $courseNames = '';
                    if($row->courseMain){
                        $data = $row->courseMain->pluck('name')->toArray();
                        $courseNames = implode('<br/>', $data);
                    }
                    return $courseNames;
                })
                ->filterColumn('course_main_name', function($query, $keyword) {
                    $query->whereHas('courseMain', function($query) use ($keyword){
                        $query->where('course_mains.name', 'LIKE', '%'. strtolower($keyword) .'%');
                   });   
                })
                ->addColumn('assessment_count', function($row){
                    return count($row->assessment);
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.assessments.exam-settings.edit', $row->id).'" ><i class="fas fa-eye font-16"></i>Edit Exam</a></li>';
                            $btn .= '<li><a href="'.route('admin.assessments.exam-settings.get-assessments', $row->id).'" ><i class="fas fa-eye font-16"></i>Main Assessments</a></li>
                        </ul>
                    </div>';
                return $btn;
                })
                ->rawColumns(['course_main_name', 'assessment_count', 'action'])
                ->make(true);
                // $btn .= '<li><a href="'.route('admin.assessments.examdashboard.all_assessments', $row->id).'" ><i class="fas fa-eye font-16"></i>Total Assessments</a></li>
    }

    public function create(ExamSettingStoreRequest $request)
    {
        $allCourseRuns = CourseMain::with(['courseRun'])->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS)->get();

        if ($request->method() == "POST") {     
            $courseMainIds = $request->get('course_main_id');            
            $assessmentDetails = $request->get('assessments');

            $studentMainExam = AssessmentMainExam::create([
                'main_exam' => 'tms_exam_data'                     
            ]);

            if($assessmentDetails){
                foreach($assessmentDetails as $key => $assess){

                    if(isset($assess['trainee_view_access'])){
                        $assess['trainee_view_access'] = 1;
                    } else {
                        $assess['trainee_view_access'] = 0;
                    }
                    $examAssessment = ExamAssessment::create([
                        'exam_id' => $studentMainExam->id,
                        'title' => $assess['title'],
                        'type' => 'assessment'.($key + 1),
                        'assessment_duration' => $assess['assessment_duration'],
                        'assessment_time' => $assess['assessment_time'],
                        "date_option" => $assess['date_option'],
                        'trainee_view_access' => $assess['trainee_view_access']
                    ]);
                }
                $examMainCourse =  $studentMainExam->courseMain()->attach($courseMainIds);
            } else {
                setflashmsg(trans('Please add alteast one assessment'), 0);
                return redirect()->back();
            }
            if ($studentMainExam) {
                setflashmsg(trans('Exam created successfully'), 1);
                return redirect()->route('admin.assessments.exam-settings.list');
            }
        }
        return view('admin.examsettings.create', compact('allCourseRuns'));
    }

    public function update(ExamSettingStoreRequest $request, $id)
    {
        $user = Auth::user();
        $examMainData = AssessmentMainExam::find($id);
        $getCourseMain = AssessmentMainCourse::where('exam_id',$id)->pluck('course_main_id')->toArray();
        $getAssessmentData = ExamAssessment::where('exam_id',$id)->get();
        $allTrainerCourseRuns = Course::with(['courseMain', 'trainers'])
                                        ->whereHas('courseMain', function($query){
                                            $query->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS);
                                        })
                                        ->groupBy('course_main_id')->get();

        if ($request->method() == "POST") {
            // $examMainData->assessment_time = $request->get('assessment_time');
            // $examMainData->assessment_duration = $request->get('assessment_duration');
            // $examMainData->save();

            $assessmentDetails = $request->get('assessments');
            $courseMainIds = $request->get('course_main_id');

            
            $examAssmentIds = ExamAssessment::where('exam_id', $id)->pluck('id')->toArray();
            $assessmentOfCourse = AssessmentExamCourse::whereIn('assessment_id', $examAssmentIds)->get();
            $getOldMainIds =  AssessmentMainExam::find($id)->courseMain->pluck('id')->toArray();
        

            if($assessmentDetails){
                foreach($assessmentDetails as $key => $assess){

                    if(isset($assess['trainee_view_access'])){
                        $assess['trainee_view_access'] = 1;
                    } else {
                        $assess['trainee_view_access'] = 0;
                    }
                    $examAssessment = ExamAssessment::where("id", $assess['id'])
                                                    ->update([
                                                        "title" => $assess['title'],
                                                        "assessment_time" => $assess['assessment_time'],
                                                        "assessment_duration" => $assess['assessment_duration'],
                                                        "date_option" => $assess['date_option'],
                                                        'trainee_view_access' => $assess['trainee_view_access']
                                                    ]);
                }
                $examMainCourse =  $examMainData->courseMain()->sync($courseMainIds);
            }

            if ($examMainData) {
                setflashmsg(trans('Exam updated successfully'), 1);
                return redirect()->route('admin.assessments.exam-settings.list');
            }
        }
        return view('admin.examsettings.edit', compact('examMainData', 'allTrainerCourseRuns', 'getCourseMain','getAssessmentData'));
    }

    public function delete($id)
    {
        $assessmentMainExam = AssessmentMainExam::findOrFail($id);
        $assessmentMainExam->delete();

        // AssessmentMainExam::find($id)->softDeletes();
        return redirect()->route('admin.assessments.exam-settings.list');
    }

    public function restore($id)
    {
        AssessmentMainExam::withTrashed()->find($id)->restore();

        return redirect()->route('admin.assessments.exam-settings.list');
    }

    public function createQuestions(Request $request, $id)
    {
        $allCourseRunQuestion = ExamAssessment::with(['questions'])->find($id);
        $getCourseMain = AssessmentMainExam::find($allCourseRunQuestion->exam_id);
        $allTrainerCourseRuns = "";
        
        if(count($allCourseRunQuestion->questions) > 0){
            $latestQuesId = $allCourseRunQuestion->questions->last()->id;
        }else {
            $latestQuesId = 0;
        }
        $assessmentQuestion = "";
        if ($request->method() == "POST") {
            $assessmentData = $request->assessment;
            $currentIds = [];
            $notDeletedIds = [];
            $notDeletedIds = AssessmentQuestions::where('assessment_id', $id)->whereNull('deleted_at')->pluck('id')->toArray();
            foreach ($assessmentData as $value) {

                $currentIds[] = isset($value['question_id']) ? $value['question_id'] : "";
                $assessmentQuestion = AssessmentQuestions::updateOrCreate([
                    'id' => isset($value['question_id']) ? $value['question_id'] : null,
                ], [
                    "assessment_id"      => $id,
                    "question"           => $value['question'],
                    "answer_format"      => $value['template_text'],
                    "created_by"         => Auth::user()->id,
                    "updated_by"         => Auth::user()->id,
                ]);

                if(!empty($value['upload_file'])) {
                    foreach($value['upload_file'] as $key => $image){
                        $filename = "images-" . $assessmentQuestion->id. '-' . $key . "-" . rand(1000,9999) . "-" . time() . "." . $image->getClientOriginalExtension();
                        Storage::putFileAs('public/images/question-images/', $image, $filename);
                        $assessmentQuestionImage = AssessmentQuestionImage::create([
                            'question_id'       =>  $assessmentQuestion->id,
                            'question_image'    =>  $filename,
                        ]);
                    }
                }

            }
            $deleteIDs = array_diff($notDeletedIds, $currentIds);
            if (!empty($deleteIDs)) {
                AssessmentQuestions::whereIn('id', $deleteIDs)->delete();
            }
        }
        if ($assessmentQuestion) {
            setflashmsg(trans('msg.questionAdded'), 1);
            return redirect()->back();
        }
        return view('admin.examsettings.assessment-question', compact('allCourseRunQuestion', 'latestQuesId', 'allTrainerCourseRuns', 'getCourseMain'));
    }

    public function deleteImageById(Request $request){
        $image = AssessmentQuestionImage::find($request->id);
        if(Storage::exists('public/images/question-images/' . $image->question_image)){
            Storage::delete('public/images/question-images/' . $image->question_image);
        }
        if($image->delete()){
            return response()->json(['msg' => 'true']);
        } else {
            return response()->json(['msg' => 'false']);
        }
    }

    public function deleteQuestions(Request $request, $id)
    {
        $assessmentQuestion = AssessmentQuestions::findOrFail($id);
        $assessmentQuestion->delete();
        setflashmsg(trans('msg.questionDeleted'), 1);
        return redirect()->back();
    }

    public function storeCkImage(Request $request){
        if ($request->hasFile('upload')) {
            $uploadedFile = $request->file('upload');
            $filename = "student_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();
            
            $temp = Storage::path('public/images/students/');           
            $max_width = 1000;
            $max_height = 1000;
            

            $file_dimensions = getimagesize($uploadedFile);
            $file_type = strtolower($file_dimensions['mime']);
            
            if($file_type == "image/png"){
                $img = imagecreatefrompng($uploadedFile);
                $thumb = resizeImage($uploadedFile, $max_width, $max_height);
                imagepng($thumb, $temp . $filename);
                $img = imagecreatefrompng($temp . $filename);
                $url = asset('storage/images/students/' . $filename);
                return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]);

            }else if ($file_type=='image/jpeg'||$file_type=='image/pjpeg') {
                $img = imagecreatefromjpeg($uploadedFile);
                $thumb = resizeImage($uploadedFile, $max_width, $max_height);
                imagejpeg($thumb, $temp . $filename);
                $img = imagecreatefromjpeg($temp . $filename);
                $url = asset('storage/images/students/' . $filename);
                return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]); 
            }

            // $temp = Storage::path('public/images/students/');           
            // $newwidth = 700;
            // $newheight = 500;
            // if($uploadedFile->getClientOriginalExtension() == "png"){
            //     $img = imagecreatefrompng($uploadedFile);
            //     list($width, $height) = getimagesize($uploadedFile);
            //     $thumb = imagecreatetruecolor($newwidth, $newheight);
            //     $source = imagecreatefrompng($uploadedFile);
            //     imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            //     imagepng($thumb, $temp . $filename);
            //     $img = imagecreatefrompng($temp . $filename);
            //     $url = asset('storage/images/students/' . $filename);
            //     return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]);
            // }else{
            //     $img = imagecreatefromjpeg($uploadedFile);
            //     list($width, $height) = getimagesize($uploadedFile);
            //     $thumb = imagecreatetruecolor($newwidth, $newheight);
            //     $source = imagecreatefromjpeg($uploadedFile);
            //     imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            //     imagejpeg($thumb, $temp . $filename);
            //     $img = imagecreatefromjpeg($temp . $filename);
            //     $url = asset('storage/images/students/' . $filename);
            //     return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]); 
            // }
            // Use Storage to store the file in the desired location
            // Storage::putFileAs('public/images/students', $uploadedFile, $filename);

            // You can also use the following code to generate a URL for the stored file
            // $url = Storage::url('public/images/students/' . $filename);
            // $url = storage_path('public/images/students/' . $filename);
            // $url = asset('storage/images/students/' . $filename);
            // return response()->json(['fileName' => $filename, 'uploaded' => 1, 'url' => $url]);
        }
    }


    public function markAssessment($courseId){

        return view('admin.examsettings.mark-assessment', compact('courseId'));
    }

    public function listDatatableMark($courseId){
        
        $records = AssessmentMainExam::join('assessment_main_course_exams', function($join){
            $join->on('assessment_main_exams.id', '=', 'assessment_main_course_exams.mainexam_id');
        })
        ->join('course_mains', function($join){
            $join->on('assessment_main_course_exams.coursemain_id', '=', 'course_mains.id');
        })
        ->join('assessment_exam_courses', function($join){
            $join->on('assessment_exam_courses.exam_id', '=', 'assessment_main_exams.id');
        })
        ->join('student_enrolments', function($join) use($courseId){
            $join->on('assessment_exam_courses.course_id', '=', 'student_enrolments.course_id')
                ->where('assessment_exam_courses.course_id', $courseId);
        })
        ->join('students', function($join){
            $join->on('student_enrolments.student_id', '=', 'students.id');
        })
        ->leftjoin('assessment_submissions_results', function($join){
            $join->on('assessment_main_exams.id','=', 'assessment_submissions_results.exam_id')
                ->on('student_enrolments.id','=', 'assessment_submissions_results.student_enr_id');
        })
        ->get([
            'assessment_main_exams.id as exam_id',
            'assessment_main_exams.assessment_type as assessment_type',
            'assessment_main_exams.assessment_name as assessment_name',
            'students.nric',
            'students.name',
            'assessment_submissions_results.is_passed',
            'student_enrolments.id as studentenr',
        ]);

        return Datatables::of($records)
                        ->editcolumn('is_passed', function($row){
                            return ($row->is_passed) ? $row->is_passed : '--';
                        })
                        ->editcolumn('is_started',function($row){
                            if($row->is_started == AssessmentStudentExam::NOT_STARTED){
                                return '<span class="badge badge-soft-danger">Not Started</span>';
                            } elseif($row->is_started == AssessmentStudentExam::STARTED) {
                                return '<span class="badge badge-soft-primary">Started</span>';
                            } elseif($row->is_started == AssessmentStudentExam::COMPLETED) {
                                return '<span class="badge badge-soft-success">Completed</span>';
                            } elseif($row->is_started == AssessmentStudentExam::MARKED) {
                                return '<span class="badge badge-soft-success">Marked</span>';
                            } 
                        })
                        ->addColumn('action', function($row){
                            // dd($row->id);
                            $btn = '';
                            $btn .= '
                                    <div class="dropdown dot-list">
                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                        <ul  class="dropdown-menu">';
                                        $btn .= '<li><a href="'.route('admin.assessments.examdashboard.review_stud_exam', ['examid' => $row->exam_id , 'assessmenttype' => $row->assessment_type, 'studentenr' => $row->studentenr]).'"><i class="fas fa-eye font-16"></i></i>Assess Trainee</a></li>';
                                    '</div>';
                            return $btn;
                        })
                        ->rawColumns(['action','is_started'])
                        ->make(true);

    }

    public function assessName(Request $request){

        $id = $request->get('record_id');

        $assessmentNameUpdate = AssessmentMainExam::find($id);
        $assessmentNameUpdate->assessment_name = $request->get('value');
        $assessmentNameUpdate->save();

        if($assessmentNameUpdate){
            return $assessmentNameUpdate;
        }

        return false;
    }


    // Start New Structure Admin changes

    public function  generateAssessmentadmin(Request $request){

        $courseRunId = $request->courseRunId;
        $assignStudent =   StudentEnrolment::select('students.name as student_name', 'students.id as student_id', 'students.nric', 'students.email', 'courses.course_main_id', 'student_enrolments.id as studentenr_id')
        ->join('courses', 'student_enrolments.course_id', '=', 'courses.id')
        ->join('students', 'student_enrolments.student_id', '=', 'students.id')
        ->where('student_enrolments.status', '=', StudentEnrolment::STATUS_ENROLLED)
        ->where('courses.id', $courseRunId)->get();

        $courseMainId = Course::find($courseRunId);
        $startDate = $courseMainId->course_start_date;
        $endDate = $courseMainId->course_end_date;

        Log::info("========= Course run Id " . $courseRunId . " ========");
    
        if(count($assignStudent) > 0){
            $assessments = ExamAssessment::select('tms_exam_assessments.id')
                                ->join('tms_exam_course_mains', 'tms_exam_course_mains.exam_id', '=', 'tms_exam_assessments.exam_id')
                                ->where('tms_exam_course_mains.course_main_id', $courseMainId->course_main_id)
                                ->get()->pluck('id')->toArray();
            
            if(count($assessments) > 0){

                $assignedCourseRuns = AssessmentExamCourse::whereIn('assessment_id', $assessments)
                                            ->where('course_run_id', $courseRunId)->get();
                
                $assessmentWithTime = ExamAssessment::whereIn('id', $assessments)->get();
                
                if($assignedCourseRuns->count() == 0){
                    foreach($assessmentWithTime as $assessment){
                        $examCourseRuns = new AssessmentExamCourse;
                        $examCourseRuns->assessment_id = $assessment->id;
                        $examCourseRuns->course_run_id = $courseRunId;
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
                $assignedAssessment = AssessmentExamCourse::where('course_run_id', $courseRunId)->get();

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
                return ['status' => true, 'msg' => 'Assessment Generated Successfully.'];
            }
        }
         else {
            // setflashmsg(trans('msg.courseMainExamNotAssigned'), 0);
            // return redirect()->back(); 
            return ['status' => false, 'msg' => 'There are no student avalible in this course.'];
        }
}

    public function getMainAssessments($examId){
        $mainName = AssessmentMainExam::find($examId)->courseMain[0]->name;
        return view('admin.examsettings.main-assessment', compact('examId', 'mainName'));
    }

    public function getMainAssessmentByID(Request $request){
        $examId = $request->get('exam_id');
        $records = $this->courseService->mainAssessmentById($examId);
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="' . route('admin.assessments.exam-settings.add-questions', $row->id) . '" ><i class="add-new font-16"></i>Add Questions</a></li>
                        </ul>
                    </div>';
                return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function viewTrainees($courseRunId){

        return view('admin.examsettings.view-trainees', compact('courseRunId'));
    }

    public function viewAllTraineesDataTable($courseRunId){
        $records = Course::join('tms_exam_assement_course_runs', function($join){
            $join->on('courses.id', '=', 'tms_exam_assement_course_runs.course_run_id');
        })

        ->join('tms_exam_assessments', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id', '=', 'tms_exam_assessments.id');
        })
       
        ->join('tms_student_assessment', function($join){
            $join->on('tms_student_assessment.assessment_run_id', '=', 'tms_exam_assement_course_runs.id');
        })
       
        ->join('student_enrolments', function($join) use($courseRunId){
            $join->on('tms_student_assessment.student_enrol_id', '=', 'student_enrolments.id')
                ->where('student_enrolments.course_id', $courseRunId)
                ->where('tms_exam_assement_course_runs.course_run_id', $courseRunId)
                ->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED);
        })

        ->join('students', function($join){
            $join->on('student_enrolments.student_id', '=', 'students.id');
        })

        ->leftjoin('tms_student_results', function($join){
            $join->on('tms_exam_assement_course_runs.assessment_id','=', 'tms_student_results.assessment_id')
                ->on('student_enrolments.id','=', 'tms_student_results.student_enr_id');
        })
        
        ->get([
            'tms_exam_assement_course_runs.assessment_id as assessment_id',
            'tms_exam_assessments.title as assessment_name',
            'students.nric',
            'students.name',
            'tms_student_assessment.is_started',
            'tms_student_assessment.finished_time',
            'student_enrolments.id as studentenr',
            'tms_student_results.is_passed',
        ]);
    

        return Datatables::of($records)
            ->editcolumn('is_passed', function($row){
                // return ($row->is_passed) ? $row->is_passed : '--';
                if(!is_null($row->is_passed)){
                    if($row->is_passed == 'c'){
                        return '<span class="badge badge-soft-success">'. syncAssessmentWithTrainer($row->is_passed). '</span>';
                    } else {
                        return '<span class="badge badge-soft-danger">'. syncAssessmentWithTrainer($row->is_passed). '</span>';
                    }
                } else {
                    return "--";
                }
            })
            ->editcolumn('is_started',function($row){
                if($row->is_started == AssessmentStudentExam::NOT_STARTED){
                    return '<span class="badge badge-soft-danger">Not Started</span>';
                } elseif($row->is_started == AssessmentStudentExam::STARTED) {
                    return '<span class="badge badge-soft-primary">Started</span>';
                } elseif($row->is_started == AssessmentStudentExam::COMPLETED) {
                    return '<span class="badge badge-soft-success">Completed</span>';
                } elseif($row->is_started == AssessmentStudentExam::MARKED) {
                    return '<span class="badge badge-soft-success">Marked</span>';
                } 
            })
            ->editColumn('finished_time', function($row){
                if(!empty($row->finished_time)){
                    return Carbon::parse($row->finished_time)->format('g:i a');
                } else {
                    return "--";
                }
            })
            ->addColumn('action', function($row){
                
                $btn = '';
                $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.assessments.examdashboard.review_stud_exam', ['assessmentId' => $row->assessment_id ,'studentenr' => $row->studentenr]).'"><i class="fas fa-eye font-16"></i></i>Review Assessment</a></li>';
                        '</div>';
                return $btn;
            })
            ->rawColumns(['action','is_started', 'is_passed'])
            ->make(true);
    }

    public function reviewAssessment($assessmentId, $studentEnr){
        $studentData = StudentEnrolment::with('courseRun')->find($studentEnr);
        $allStudentQuestionAns = $this->courseService->getStudentQuestionAnswer($assessmentId, $studentEnr);
        $getStudentAttachment = AssessmentSubmissionAttachment::where(['student_enrol_id' => $studentEnr, 'assessment_id' => $assessmentId])->get();
        // $getStudentAttachment = [];
        // dd($getStudentAttachment);
        return view('admin.examsettings.student-assess-review', compact('allStudentQuestionAns', 'studentData', 'getStudentAttachment', 'assessmentId', 'studentEnr'));
    }

    public function storeAssessment(Request $request){
        $assessmentExamService = new CommonAssessmentService;
        $storeData = $assessmentExamService->storeAssessmentService($request);
        return $storeData;
    }

    public function getDataAssessmentPdf($assessmentId, $studentEnrId){
        $assessmentExamService = new CommonAssessmentService;
        $getData = $assessmentExamService->getAssessmentPdf($assessmentId, $studentEnrId);

        $pdfData = $getData;
        $studentName = str_replace(' ', '_', $pdfData['getStudentName']['student']['name']);
        $pdf = PDF::loadView('admin.examsettings.assessment-pdf', $pdfData, [], [
            'format' => 'A4',
        ]);
        $pdf->shrink_tables_to_fit = 1;
        // $assessmentFolder = Storage::path('public/trainee-assessment/');
        // if( !File::exists($assessmentFolder) ) {
        //     File::makeDirectory($assessmentFolder, 0755, true, true);
        // }
        // $fileName = 22396 . "_" . time() . '.pdf';
        // $fullFilePath = $assessmentFolder . $fileName;
        // $pdf->save($fullFilePath);
        // $pdf->stream($fullFilePath);

        $Filename = $studentName ."_". $studentEnrId . '.pdf';
        $pdf->download($Filename);
    }
}

