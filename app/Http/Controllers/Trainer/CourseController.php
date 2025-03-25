<?php

namespace App\Http\Controllers\Trainer;

use PDF;
use Excel;
use DataTables;
use App\Models\CourseMain;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\CourseResource;
use App\Services\VenueService;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Services\StudentService;
use App\Services\TPGatewayService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Exports\CourseRunTraineeExport;
use App\Models\CourseResourceCourseMain;
use App\Exports\CourseRunRefresherExport;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseResourceStoreRequest;


class CourseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CourseService $courseService)
    {
        $this->middleware('auth');
        $this->courseService = $courseService;
    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('trainer.course.list');
    }


    public function listDatatable(Request $request)
    {
        $records = $this->courseService->getTrainerCourseRuns();
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('is_published', function($row) {
                    if( $row->is_published == 1 ) { return '<span class="badge badge-soft-success">Published</span>'; }
                    elseif( $row->is_published == 2 ) { return '<span class="badge badge-soft-danger">Cancelled</span>'; }
                    else { return '<span class="badge badge-soft-danger">Un Published</span>'; }
                })
                ->editColumn('registeredusercount', function($row) {
                    if( $row->registeredusercount ) {
                        return '<span class="badge badge-soft-success">
                                    <a href="'.route('admin.studentenrolment.list',$row->id).'">'.$row->registeredusercount.'</a>
                                </span>';
                    } else { return $row->registeredusercount; }
                })
                ->editColumn('modeoftraining', function($row) {
                    return '<span class="badge badge-soft-primary">'.getModeOfTraining($row->modeoftraining).'</span>';
                })
                ->editColumn('slot', function($row) {
                    return $row->registeredusercount."/".$row->intakesize;
                })
                ->filterColumn('modeoftraining', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('classroom', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 1);
                    }
                    if( (substr('asynchronous elearning', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 2);
                    }
                    if( (substr('in-house', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 3);
                    }
                    if( (substr('on-the-job', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 4);
                    }
                    if( (substr('practical / practicum', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 5);
                    }
                    if( (substr('supervised field', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 6);
                    }
                    if( (substr('traineeship', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 7);
                    }
                    if( (substr('assessment', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 8);
                    }
                    if( (substr('synchronous elearning', 0, $len) === strtolower($keyword)) ) {
                        $query->where('modeoftraining', 9);
                    }
                })
                ->filterColumn('is_published', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('published', 0, $len) === strtolower($keyword)) ) {
                        $query->where('is_published', 1);
                    }
                    if( (substr('un published', 0, $len) === strtolower($keyword)) ) {
                        $query->where('is_published', 0);
                    }
                    if( (substr('cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('is_published', 2);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('trainer.course.courserunview',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                            $btn .= '<li><a href="'.route('trainer.course.get-attendance-assessment',$row->id).'" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action','modeoftraining', 'registeredusercount', 'coursevacancy_code', 'course_type', 'is_published'])
                ->make(true);

    }

    public function courseRunView($id, Request $request)
    {
        $data = $this->courseService->getCourseRunFullDetailsById($id);
        // check the trainer has access to this course or not
        if( $data->maintrainer != \Auth::id() ) {
            setflashmsg("You don't have access to this course", 0);
            return redirect()->route('trainer.course.list');
        }
        $studentService = new \App\Services\StudentService;
        $isGrantError = $studentService->hasGrantError($id);
        $isEnrollmentError = $studentService->hasEnrollmentError($id);
        // get payment list for all enrollment for this course
        $studentEnrollmentIds = $studentService->getAllStudentIdsForCourseRun($id);
        $paymentService = new \App\Services\PaymentService;
        $payments = $paymentService->getPaymentByStudentEnrolmentIds($studentEnrollmentIds);

        // $currentCourse = \App\Models\Course::find($id);
        // $revisions = $currentCourse->revisionHistory;

        return view('trainer.course.courserunview', compact('data', 'payments', 'isGrantError', 'isEnrollmentError'));
    }

    public function studentList($id)
    {
        $studentService = new \App\Services\StudentService;
        $students = $studentService->getAllStudentsForCourseRun($id);
        $course = $this->courseService->getCourseById($id);
        return view('trainer.course.studentlist',compact('students', 'course'));
    }

    /*
    *  $id -> course run id
    */
    public function generateAttendance($id)
    {
        // dd($id);
        $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        $selectedTrainers = $result->trainers->pluck('id')->all();
        $common = new CommonService;
        $courseRunName = $common->makeAssessmentSessionString($result->course_start_date, $result->course_end_date);
        $totalHours = $common->getAttendanceSessionHours($result->session);
        $pdfdata = [
            'result'  => $result,
            'courseRunName' => $courseRunName,
            'totalHours' => $totalHours
        ];
        $pdf = PDF::loadView('admin.pdf.generate-attendance', $pdfdata, [], [
            'format' => 'A4-L',
            // 'orientation' => 'L'
        ]);
        $filename = $result->courseMain->name." - ".$courseRunName.".pdf";
        // download($filename)
        return $pdf->stream($filename);
    }

    public function generateAssessment($id)
    {
        // dd($id);
        $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        $selectedTrainers = $result->trainers->pluck('id')->all();
        $common = new CommonService;
        $courseRunName = $common->makeAssessmentSessionString($result->course_start_date, $result->course_end_date);
        $pdfdata = [
            'result'  => $result,
            'courseRunName' => $courseRunName,
        ];
        $pdf = PDF::loadView('admin.pdf.generate-assessment', $pdfdata, [], [
            'format' => 'A4-L',
            // 'orientation' => 'L'
        ]);
        $filename = $result->courseMain->name." - ".$courseRunName.".pdf";
        // download($filename)
        return $pdf->stream($filename);
    }

    public function attendanceAssessment($id)
    {
        $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        // $selectedTrainers = $result->trainers->pluck('id')->all();
        $common = new CommonService;
        $courseRunName = $common->makeSessionString($result->session);
        return view('trainer.course.attendance-assessment', compact('result', 'courseRunName'));
    }

    public function saveAttendanceAssessment($id, Request $request)
    {
        // dd($id);
        $record = $this->courseService->saveCourseRunAttendanceAssessment($id, $request);
        if( empty($record['status']) ) {
            setflashmsg(trans('msg.attendanceSuccess'), 1);
        } else {
            setflashmsg(trans('msg.attendanceFail'), 0);
            // setflashmsg(trans('msg.attendanceFail'), 0);
        }
        return redirect()->route('trainer.course.get-attendance-assessment', $id);
    }

    public function submitAttendanceTpGateway($id)
    {
        $result = $this->courseService->getCourseByIdAndStudentEnrolment($id);
        $isError = false;
        // add to TP Gateway
        $req_data = [];
        $req_data = [
            "uen" => config('settings.tpgateway_uenno'),
            "course" => [
                "referenceNumber" => $result->courseMain->reference_number
            ],
        ];
        $studentService = new \App\Services\StudentService;
        // check all student attendance has been filled
        foreach ($result->courseActiveEnrolments as $s => $record) {
            if( is_null($record->attendance) ) {
                setflashmsg(trans('msg.fillattendance'), 0);
                return redirect()->back();
            }
            // pending this
            $req_data['course']['attendance'] = [
                "status" => [ "code" => 1 ]
            ];
            $req_data['course']['trainee'] = [
                "id" => $record->student->nric,
                "name" => $record->student->name,
                "email" => $record->email,
                "idType" => [ "code" => $record->nationality == "Non-Singapore Citizen/PR" ? "OT" : "NRIC" ],
                "contactNumber" => [
                    "mobile" => $record->mobile_no,
                    "areaCode" => "",
                    "countryCode" => "+65"
                ],
                "surveyLanguage" => [ "code" => "EL" ]
            ];
            // submit to tpgateway
            $tpgatewayReq = new TPGatewayService;
            $stuEnrol = $studentService->getStudentEnrolmentById($record->id);
            $attendRes = $tpgatewayReq->courseAttendance($result->tpgateway_id, $req_data);
            if( isset($attendRes->status) && $attendRes->status == 200 ) {
                $stuEnrol->isAttendanceError = 0;
            } else {
                $isError = true;
                $stuEnrol->isAttendanceError = 1;
            }
            $stuEnrol->attendanceResponse = json_encode($attendRes);
            // update the student enrolment data
            $stuEnrol->save();
        }
        if( $isError ) {
            setflashmsg(trans('msg.attendancetpgFailed'), 0);
        } else {
            setflashmsg(trans('msg.attendancetpgSuccess'), 1);
        }
        $courseRun = $this->courseService->getCourseById($id);
        $courseRun->isAttendanceSubmitedTPG = 1;
        $courseRun->save();
        return redirect()->back();
    }

    public function submitAssessmentTpGateway($id)
    {
        $result = $this->courseService->getCourseByIdAndStudentEnrolment($id);
        $isError = false;
        // add to TP Gateway
        $req_data = [];
        $req_data['assessment'] = [
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $result->courseMain->reference_number,
                "run" => [ "id" => $result->tpgateway_id ]
            ],
        ];
        $studentService = new \App\Services\StudentService;
        // check all student Assessment has been filled
        foreach ($result->courseActiveEnrolments as $s => $record) {
            if( is_null($record->assessment) ) {
                setflashmsg(trans('msg.fillassessment'), 0);
                return redirect()->back();
            }
            $req_data['assessment']['trainee'] = [
                "id" => $record->student->nric,
                "idType" => [ "type" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC" ],
                "fullName" => $record->student->name,
            ];
            $req_data['assessment']['result'] = $record->assessment == 'c' ? 'Pass' : 'Fail';
            $req_data['assessment']['assessmentDate'] = $record->assessment_date;
            // submit to tpgateway
            $tpgatewayReq = new TPGatewayService;
            $stuEnrol = $studentService->getStudentEnrolmentById($record->id);
            $assessRes = $tpgatewayReq->courseAssessments($req_data);
            if( isset($assessRes->status) && $assessRes->status == 200 ) {
                $stuEnrol->isAssessmentError = 0;
            } else {
                $isError = true;
                $stuEnrol->isAssessmentError = 1;
            }
            $stuEnrol->assessmentResponse = json_encode($assessRes);
            // update the student enrolment data
            $stuEnrol->save();
        }
        if( $isError ) {
            setflashmsg(trans('msg.assessmenttpgFailed'), 0);
        } else {
            setflashmsg(trans('msg.assessmenttpgSuccess'), 1);
        }
        $courseRun = $this->courseService->getCourseById($id);
        $courseRun->isAssessmentSubmitedTPG = 1;
        $courseRun->save();
        return redirect()->back();
    }

    public function studentEnrolmentView($id)
    {
        $studentService = new StudentService;
        $data = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        $singleCourse = true;
        if($data->courseRun->courseMain->course_type_id != 1) {
            $singleCourse = false;
        }
        return view('trainer.course.studentenrolmentview', compact('data', 'singleCourse'));
    }

    public function courseRunTraineeExportExcel($id, Request $request)
    {
        // get details of course run and students for course runs
        $courseRun = $this->courseService->getCourseById($id);
        // get list of enrollment with student data
        $studentService = new StudentService;
        $students = $studentService->getStudentEnrollmentsByCourseRunId($id);
        // get list of refreshers
        return Excel::download(new CourseRunTraineeExport($courseRun, $students), 'courseRunTrainee-list.xlsx');
    }

    public function courseRunRefreshersExportExcel($id, Request $request)
    {
        // get details of course run and students for course runs
        $courseRun = $this->courseService->getCourseById($id);
        // get list of refreshers
        $courseRefreshers = $courseRun->courseRefreshers;
        return Excel::download(new CourseRunRefresherExport($courseRun, $courseRefreshers), 'courseRunTrainee-list.xlsx');
    }

    // Course Refreshers
    public function refreshersAdd(Request $request, $id)
    {
        if( $request->method() == 'POST') {
            // check if this student has already been added to this course
            if( $this->courseService->checkRefreshersAdded($request) ) {
                setflashmsg('Student already added', 0);
                return redirect()->route('trainer.course.courserunview', $id);
            }
            $record = $this->courseService->registerRefreshersCourse($request);
            if( $record ) {
                setflashmsg(trans('msg.courseRefresherCreated'), 1);
                return redirect()->route('trainer.course.courserunview', $record->course_id);
            }
        }
        // Get the Course Run details First
        $data = $this->courseService->getCourseById($id);
        // check if this course start date is not gone
        if( \Carbon\Carbon::parse($data->course_start_date)->isPast() ) {
            setflashmsg("Course already started", 0);
            return redirect()->back();
        }
        /**
         * Check if this user has attended this Course previously within 1 year
         * get all course run Ids which happend for this main course within 1 year
        **/
        $courseRunIds = $this->courseService->getCourseRunIdsForMainCourse($data->course_main_id);
        // redirect back if not with error message
        if( empty($courseRunIds) ) {
            setflashmsg("No course run found within 1 year", 0);
            return redirect()->back();
        }
        $studentIds = \App\Models\StudentEnrolment::whereIn('course_id', $courseRunIds)
                            ->where('status', '!=', 1)->pluck('student_id');
        if( empty($studentIds) ) {
            setflashmsg("No student enrolled to this course within 1 year", 0);
            return redirect()->back();
        }
        $students = \App\Models\Student::whereIn('id', $studentIds)->get();
        /**
         * allow this user to be added in course refresher. But don't add them in TPGateway
         * neither increase the count of registration. But they can get the certification
         * and assessment can be done as well
        **/
        return view('trainer.course.refresher-add', compact('students', 'data'));
    }

    public function refreshersEdit(Request $request, $id)
    {
        if( $request->method() == 'POST') {
            $record = $this->courseService->updateRefreshersCourse($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.courseRefresherUpdated'), 1);
                return redirect()->route('trainer.course.courserunview', $record->course_id);
            }
            setflashmsg('Details not updated', 0);
            return redirect()->back();
            // return redirect()->route('admin.course.courserunview', $id);
        }
        $data = $this->courseService->getRefreshersById($id);
        return view('trainer.course.refresher-edit', compact('data'));
    }

    public function uploadCourseRunDocuments(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'courserun_id' => 'required',
            'category_id' => 'required',
            'file_name' => 'required',
        ],[
            'courserun_id.required'   => 'No Course Selected',
            'category_id.required'   => 'Please select category',
            'file_name.required' => 'Please select Document'
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $response = [
                'success'   => false,
                'message'   => $error,
            ];
            return response()->json($response, 200);
        }
        $data = $this->courseService->storeUploadCourseRunDocuments($request);
        $success = false;
        $msg = "Document Not Uploaded";
        if( $data ) {
            $success = true;
            $msg = "Document Uploaded Successfully";
        }
        $response = [
            'success'   => $success,
            'message'   => $msg,
        ];
        return response()->json($response, 200);
    }

    public function uploadCourseRunDocumentsEdit(Request $request)
    {
        $docs_id = $request->get('id');
        $record = $this->courseService->getCourserRunDocument($docs_id);
        $view = view('trainer.partial.courserun-upload-document-edit', compact('record'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function uploadCourseRunDocumentsUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'courserun_id' => 'required',
            'category_id' => 'required',
        ],[
            'courserun_id.required'   => 'No Course Selected',
            'category_id.required'   => 'Please select category',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $response = [
                'success'   => false,
                'message'   => $error,
            ];
            return response()->json($response, 200);
        }
        $data = $this->courseService->updateUploadCourseRunDocuments($request);
        $success = false;
        $msg = "Document Not Updated";
        if( $data ) {
            $success = true;
            $msg = "Document Updated Successfully";
        }
        $response = [
            'success'   => $success,
            'message'   => $msg,
        ];
        return response()->json($response, 200);
    }

    public function refresherNotesView(Request $request)
    {
        $main_id = $request->get('id');
        $data = $this->courseService->getRefreshersById($main_id);
        $view = view('admin.partial.view-notes', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function searchCourseRuns(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->courseService->searchCourseRunsAjax($query);
        return json_encode($ret);
    }

    public function searchTrainerCourseRuns(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->courseService->searchTrainerCourseRunAjax($query);
        return json_encode($ret);
    }

    public function courseResourceIndex(){
        return view('trainer.courseresource.list');
    }

    public function resourceListDatatable(Request $request)
    {
        $commonService = new CommonService;
        $userId = \Auth::user()->id;
        $records = $commonService->getAllCourseResources($userId);
        return Datatables::of($records)
                    ->addIndexColumn()
                    ->addColumn('course_main_name', function($row) {
                        $name = $row->name;
                        return $name; 
                    })
                    ->addColumn('resource_count', function($row) {
                        $count = CourseResourceCourseMain::where('course_main_id', $row->course_main_id)
                                                        ->join('course_resources', 'course_resources_coursemains.course_resource_id', '=', 'course_resources.id')
                                                        ->whereNull('course_resources.deleted_at')->count();
                        return $count;
                    })
                    ->addColumn('action', function($row) {
                        $btn = '';      
                        $btn .= '
                            <div class="d-flex">';
                            $btn .= '<a class="btn btn-success"  href="'.route('trainer.course-resources.get-resources',['id' => $row->course_main_id,'resourceId' => $row->id]).'">View Resources</a>';
                            $btn .= "</div>";
                        return $btn;
                        })
                ->rawColumns(['course_main_name', 'resource_count', 'action'])
                ->make(true);

    }

    public function courseResourceAdd(CourseResourceStoreRequest $request){
        if( $request->method() == 'POST') {
            $commonService = new CommonService;
            $resourcesData = $commonService->storeCourseResource($request);
            if($resourcesData){
                setflashmsg(trans('msg.CourseResourceAdd'), 1);
                return redirect()->route('trainer.course-resources.index');
            } else {
                setflashmsg(trans('msg.CourseResourceError'), 0);
                return redirect()->route('trainer.course-resources.index');
            }
        }
        $user_id = \Auth::user()->id;
        $allCources = CourseMain::with(['courseRun'])
                                    ->whereHas('courseRun', function($query) use ($user_id) { 
                                        $query->where('courses.maintrainer', $user_id);
                                    })->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS )->get();
        return view('trainer.courseresource.add', compact('allCources'));
    }

    public function courseResourceEdit(Request $request, $id){
        $commonService = new CommonService;
        if( $request->method() == 'POST') {
            $editResource = $commonService->updateCourseResource($request, $id);
            if( $editResource ) {
                setflashmsg(trans('Resource updated successfully'), 1);
                return redirect()->route('trainer.course-resources.index');
            }
        }
        $resource = $commonService->getResourceById($id);
        $user_id = \Auth::user()->id;
        $allCources = CourseMain::with(['courseRun'])
                                    ->whereHas('courseRun', function($query) use ($user_id) { 
                                        $query->where('courses.maintrainer', $user_id);
                                    })->whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS )->get();
        $getCourseMainResource = CourseResourceCourseMain::where('course_resource_id', $id)->pluck('course_main_id')->toArray();
        return view('trainer.courseresource.edit', compact('resource', 'allCources', 'getCourseMainResource'));
    }

    public function getResourceById(Request $request, $id, $resourceId){
        $commonService = new CommonService;
        $resourcesData = $commonService->getCourseResource($id);
        $mainCourse = CourseResource::select('course_mains.name')
                                            ->join('course_resources_coursemains', function($join){
                                                $join->on('course_resources.id', '=', 'course_resources_coursemains.course_resource_id');
                                            })
                                            ->join('course_mains', function($join){
                                                $join->on('course_resources_coursemains.course_main_id', '=', 'course_mains.id');
                                            })
                                            ->where('course_resources.id', $resourceId)
                                            ->get();
        // dd($mainCourse);
        return view('trainer.courseresource.preview', compact('resourcesData', 'mainCourse'));
    }

    public function removeResourceById($id){
        $commonService = new CommonService;
        $removeResource = $commonService->removeCourseResource($id);
        if($removeResource) {
            setflashmsg("Resource deleted successfully", 1);
            return redirect()->back();
        } else {
            setflashmsg("Resource not deleted something went wrong", 0);
            return redirect()->back();
        }
    }

}
