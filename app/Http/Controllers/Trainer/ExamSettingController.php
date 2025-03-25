<?php

namespace App\Http\Controllers\Trainer;

use Log;
use Auth;
use DataTables;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseMain;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;
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
use Assessments\Student\Models\AssessmentSubmissionAttachment;

class ExamSettingController extends Controller
{
    //
    public function __construct(CourseService $courseService)
    {
        $this->middleware('auth');
        $this->courseService = $courseService;
    }


    //Temp code for soft delete need to change in future (13-10-2023)
    public function index()
    {
        return view('trainer.examsettings.courserun-student-assign');
        // return view('trainer.examsettings.index');
    }

    // new function start
    public function examCourseRunsListDatatable(Request $request)
    {
        $records = $this->courseService->getAllExams($request);
        return Datatables::of($records)
            ->addIndexColumn()
            ->addColumn('course_name', function ($row) {
                return $row->courseMain->name;
            })
            ->addColumn('pax', function($row){
                return $row->registeredusercount.'/'.$row->intakesize;
            })
            ->filterColumn('course_name', function($query, $keyword) {
                $query->whereHas('courseMain', function($query) use ($keyword){
                    $query->where('course_mains.name', 'LIKE', '%'. strtolower($keyword) .'%');
               });   
            })
            ->addColumn('action', function ($row) {
                    $btn = '';
                        $btn .= '<div class="dropdown dot-list">
                                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                            <ul  class="dropdown-menu">';
                                                if($row->is_assigned != 1) {
                                                    $btn .= '<li>
                                                        <a href="" id="generate_student_assessment" data-courserunId ='. $row->id .'>
                                                            <i class="fas fa-check-circle font-16"></i>Generate Assessment
                                                         </a>
                                                            </li>';
                                                } else {
                                                    $btn .= '<li><span class="badge badge-success text-white p-2">Assessment Assigned</span></li>';
                                                }
                                                
                                                $btn .= '<li><a href="'.route('trainer.exam-settings.view_trainees',['courserunid' => $row->id]).'">
                                                <i class="fas fa-eye font-16"></i>View Trainees
                                            </a></li>';
                                            '</ul>
                                    </div>';
                        return $btn;
            })
            ->rawColumns(['course_name', 'action', 'pax'])
            ->make(true);
    }
    // new function end

    public function listDatatable(Request $request)
    {
        $records = $this->courseService->getAllExams();
        // dd($records);
        return Datatables::of($records)
            ->editColumn('course_id', function ($row) {
                return $row->name;
            })
            ->editColumn('exam_time', function ($row) {
                return Carbon::parse($row->exam_time)->format('g:i a');
            })
            ->addColumn('record_id', function($row){
                return $row->exam_id;
            })
            
            ->addColumn('action', function ($row) {
                    $btn = '';
                        $btn .= '
                                <div class="dropdown dot-list">
                                <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                    <ul  class="dropdown-menu">';
                        $btn .= '<li><a href="' . route('trainer.dashboard.edit', ['id' => $row->exam_id]) . '"><i class="fas fa-eye font-16"></i>Edit</a></li>';
                        $btn .= '<li><a href="' . route('trainer.dashboard.add-questions', $row->exam_id) . '" ><i class="fa-solid fa-messages-question font-16"></i></i>Add Questions</a></li>';
                        // $btn .= '<li><a href="' . route('trainer.exam-settings.course_runlist', $row->course_id) . '" ><i class="fas fa-eye font-16"></i>Course Run</a></li>';
                        // $btn .= '<li><a href="' . route('trainer.exam-settings.delete', $row->id) . '" ><i class="far fa-trash-alt font-16"></i></i>Cancel Exam</a></li>';
                        '</ul>
                            </div>';
                        return $btn;
            })
            ->rawColumns(['action', 'record_id'])
            ->make(true);
    }

    public function create(ExamSettingStoreRequest $request)
    {
        $user_id = \Auth::user()->id;
        $allCourseRuns = CourseMain::with(['courseRun'])
                                    ->whereHas('courseRun', function($query) use ($user_id) { 
                                        $query->where('courses.maintrainer', $user_id);
                                    })
                                    ->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS)
                                    ->get();

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
                        'type' => 'assessment'.$key,
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
                return redirect()->route('trainer.dashboard');
            }
        }
        
        return view('trainer.examsettings.create', compact('allCourseRuns'));
    }

    public function update(ExamSettingStoreRequest $request, $id)
    {
        
        $user = Auth::user();
        $examMainData = AssessmentMainExam::find($id);
        $getCourseMain = AssessmentMainCourse::where('exam_id',$id)->pluck('course_main_id')->toArray();
        $getAssessmentData = ExamAssessment::where('exam_id',$id)->get();
        $allTrainerCourseRuns = Course::with(['courseMain', 'trainers'])
                                        ->where('maintrainer', \Auth::id())
                                        ->whereHas('courseMain', function($query){
                                            $query->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS);
                                        })
                                        ->groupBy('course_main_id')
                                        ->get();

        if ($request->method() == "POST") {
            // $examMainData->exam_time = $request->get('exam_time');
            // $examMainData->exam_duration = $request->get('exam_duration');
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
                                                        "trainee_view_access" => $assess['trainee_view_access'],
                                                    ]);
                }
                $examMainCourse =  $examMainData->courseMain()->sync($courseMainIds);
            }

            if ($examMainData) {
                setflashmsg(trans('Exam updated successfully'), 1);
                // return redirect()->route('trainer.exam-settings.list');
                return redirect()->route('trainer.dashboard');
            }
        }
        return view('trainer.examsettings.edit', compact('examMainData', 'allTrainerCourseRuns', 'getCourseMain','getAssessmentData'));
    }

    public function delete($id)
    {
        $assessmentMainExam = AssessmentMainExam::findOrFail($id);
        $assessmentMainExam->delete();
        return redirect()->route('trainer.exam-settings.list');
    }

    public function restore($id)
    {
        AssessmentMainExam::withTrashed()->find($id)->restore();
        return redirect()->route('trainer.exam-settings.list');
    }

    public function assignedExamStudent($courseId, $courseRunId, $exam_id)
    {
        $allQuestions = AssessmentQuestions::where('exam_id', $exam_id)->first();
        if($allQuestions){
            $allStudentEnroll = StudentEnrolment::Where('student_enrolments.course_id', $courseRunId)->get();
            if (count($allStudentEnroll) > 0) {
                foreach ($allStudentEnroll as $studEnroll) {
                    $studentExam = new AssessmentStudentExam;
                    $studentExam['exam_id'] = $exam_id;
                    $studentExam['student_enrol_id'] = $studEnroll->id;
                    $studentExam->save();
                    $mainExam = AssessmentExamCourse::where('id', '=', $courseId)->first();
                    $mainExam->is_assigned = 1;
                    $mainExam->save();
                }
                setflashmsg(trans('msg.courseMainExamAssigned'), 1);
                return redirect()->route('trainer.exam-settings.list');
            } else {
                setflashmsg(trans('msg.courseMainExamNotAssigned'), 0);
                return redirect()->route('trainer.exam-settings.list');
            }
        } else {
            setflashmsg(trans('msg.questionEmpty'), 0);
            return redirect()->back();
        }
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
        return view('trainer.examsettings.mark-assessment', compact('courseId'));
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
        // ->join('assessment_student_exams', function($join){
        //     $join->on('assessment_student_exams.exam_id', '=', 'assessment_exam_courses.exam_id')
        //         ->on('assessment_student_exams.student_enrol_id', '=', 'student_enrolments.student_id');
        // })
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
            // 'assessment_student_exams.is_started',
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
                                        $btn .= '<li><a href="'.route('trainer.exam-settings.review_stud_exam', ['examid' => $row->exam_id , 'assessmenttype' => $row->assessment_type, 'studentenr' => $row->studentenr]).'"><i class="fas fa-eye font-16"></i></i>Assess Trainee</a></li>';
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

    public function  old_generateAssessmentTrainer($courseId, $courseMainId){

        $assignStudent =   StudentEnrolment::select('students.name as student_name', 'students.id as student_id', 'students.nric', 'students.email', 'courses.course_main_id', 'student_enrolments.id as studentenr_id')
        ->join('courses', 'student_enrolments.course_id', '=', 'courses.id')
        ->join('students', 'student_enrolments.student_id', '=', 'students.id')
        ->where('courses.id', $courseId)->get();
        // dd($assignStudent);
        if(count($assignStudent) > 0){
            foreach ($assignStudent as $student) {

                $studentNric = Student::where('id', $student->student_id)->first();
                    if (isset($studentNric->nric)) {
                        $emailTemplate = EmailTemplate::where('slug', 'assessment-exam')->first();

                        if(!empty($emailTemplate)) {
                            $shortStudentUrl = env('APP_URL')."login";
                            $content = $emailTemplate['template_text'];
                            $content = str_ireplace("{studentname}", $student->student_name, $content);
                            $content = str_ireplace("{assessmentexamurl}", $shortStudentUrl, $content);
                            $content = str_ireplace("{user_id}", $student->nric, $content);
                            $content = str_ireplace("{password}", $student->nric, $content);
            
                            // if (Mail::to($student->email)->send(new EmailStudentExamLink($content))) {
                            //     Log::info("Mail send Successfully.");
                            // } 
                        }  
                    }

                $assignedAssess = AssessmentExamCourse::where('course_id', $courseId)->get();
                foreach($assignedAssess as $assignAssessment){
                    $mainExam = AssessmentExamCourse::where('id', '=', $assignAssessment->id)->first();
                    $mainExam->is_assigned = 1;
                    $mainExam->save();
                }

                $assignedAssessToStudent = AssessmentMainCourse::where('coursemain_id', $courseMainId)->get();
                // dd($assignedAssessToStudent);
                foreach($assignedAssessToStudent as $assessStudent){
                    $studentExam = new AssessmentStudentExam;
                    $studentExam['exam_id'] = $assessStudent->mainexam_id;
                    $studentExam['student_enrol_id'] = $student->studentenr_id;
                    $studentExam->save();
                }
            }
            
            if($assignStudent){
                Log::info($assignStudent);
                setflashmsg(trans('msg.studentExamLink'), 1);
                return redirect()->back();
               
            }
        } else {
            setflashmsg(trans('msg.courseMainExamNotAssigned'), 0);
            return redirect()->back(); 
        }
    }

    public function allAssessments($examId){
        $mainName = AssessmentMainExam::find($examId)->courseMain[0]->name;
        return view('trainer.examsettings.view-assessment', compact('examId', 'mainName'));
    }

    public function getAssessmentByID(Request $request){
        $examId = $request->get('exam_id');
        $records = $this->courseService->assessmentById($examId);
        // dd($records->get());
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('assessment_id', function($row){
                    return $row->id;
                })
                ->addColumn('test', function($row){
                    return $row->id ."/". $row->course_run_id;
                })
                ->addColumn('exam_start_date', function($row){
                    return $row->courseRuns[0]->course_start_date;
                })
                ->addColumn('exam_end_date', function($row){
                    return $row->courseRuns[0]->course_end_date;
                })
                ->addColumn('pax', function($row){
                    return $row->courseRuns[0]->registeredusercount.'/'.$row->courseRuns[0]->intakesize;
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.$row->id.'" ><i class="fas fa-eye font-16"></i>View trainee</a></li>
                        </ul>
                    </div>';
                return $btn;
                })
                ->rawColumns(['action', 'assessment_id', 'exam_start_date', 'test'])
                ->make(true);

    }

    public function getMainAssessments($examId){
        $mainName = AssessmentMainExam::find($examId)->courseMain[0]->name;
        return view('trainer.examsettings.main-assessment', compact('examId', 'mainName'));
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
                            $btn .= '<li><a href="' . route('trainer.dashboard.add-questions', $row->id) . '" ><i class="add-new font-16"></i>Add Questions</a></li>
                        </ul>
                    </div>';
                return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function createQuestions(Request $request, $id)
    {
        $allCourseRunQuestion = ExamAssessment::with(['questions'])->find($id);
        $getCourseMain = AssessmentMainExam::find($allCourseRunQuestion->exam_id);
        $allTrainerCourseRuns = "";
        // $allCourseRunQuestion   = AssessmentMainExam::with(['questions', 'courses'])->find($id);
        // $getCourseMain = AssessmentMainCourse::where('mainexam_id',$id)->get();
        // $allTrainerCourseRuns = Course::with(['courseMain', 'trainers'])->where('maintrainer', \Auth::id())->groupBy('course_main_id')->get();
        
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

                // AssessmentQuestionImage::where('question_id', $assessmentQuestion->id)->withTrashed()->forceDelete();
                if(!empty($value['upload_file'])) {
                    // $uploadedFile = $value['upload_file'];
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
        return view('trainer.examsettings.assessment-question', compact('allCourseRunQuestion', 'latestQuesId', 'allTrainerCourseRuns', 'getCourseMain'));
    }


    public function  generateAssessmentTrainer(Request $request){

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
        }else {
            // setflashmsg(trans('msg.courseMainExamNotAssigned'), 0);
            // return redirect()->back(); 
            return ['status' => false, 'msg' => 'There are no student avalible in this course.'];
        }
    }

    public function viewTrainees($courseRunId){

        return view('trainer.examsettings.view-trainees', compact('courseRunId'));
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
                            $btn .= '<li><a href="'.route('trainer.exam-settings.review_stud_exam', ['assessmentId' => $row->assessment_id ,'studentenr' => $row->studentenr]).'"><i class="fas fa-eye font-16"></i></i>Review Assessment</a></li>';
                        '</div>';
                return $btn;
            })
            ->rawColumns(['action','is_started', 'is_passed'])
            ->make(true);
    }

    public function reviewAssessment($assessmentId, $studentEnr){
        $studentData = StudentEnrolment::with(['courseRun', 'student'])->find($studentEnr);
        $allStudentQuestionAns = $this->courseService->getStudentQuestionAnswer($assessmentId, $studentEnr);
        $getStudentAttachment = AssessmentSubmissionAttachment::where(['student_enrol_id' => $studentEnr, 'assessment_id' => $assessmentId])->get();
        return view('trainer.examsettings.student-assess-review', compact('allStudentQuestionAns', 'studentData', 'getStudentAttachment', 'assessmentId'));
    }

    public function storeAssessment(Request $request){
        $assessmentExamService = new CommonAssessmentService;
        $storeData = $assessmentExamService->storeAssessmentService($request);
        return $storeData;
    }
}