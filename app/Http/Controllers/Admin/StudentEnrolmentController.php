<?php

namespace App\Http\Controllers\Admin;

use Log;
use DataTables;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Models\StudentEnrolment;
use App\Services\PaymentService;
use App\Services\StudentService;
use App\Services\CourseMainService;
use App\Http\Controllers\Controller;
use App\Services\SoftBookingService;
use App\Services\WaitingListService;
use Illuminate\Support\Facades\Gate;
use Webfox\Xero\OauthCredentialManager;
use App\Http\Requests\StudentEnrolmentStoreRequest;
use App\Http\Requests\StudentEnrolmentUpdateRequest;

class StudentEnrolmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StudentService $studentService)
    {
        $this->middleware('auth');
        $this->studentService = $studentService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('studentenrolment-list')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.studentenrolment.list', compact('courseMainList'));
    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('studentenrolment-list')) { return abort(403); }
        $paymentReport = false;
        $records = $this->studentService->getAllStudentEnrolmentWithFilter($request, $paymentReport);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->editColumn('student_name', function($row){
                    return '<a href="'.route('admin.studentenrolment.view',$row->id).'" target="_blank">'. $row->student->name .'</a>';
                })
                ->editColumn('courseName', function($row){
                    return '<a href="'.route('admin.course.courserunview',$row->course_id).'" target="_blank">'. $row->tpgateway_id . ' ('. $row->course_start_date .') - ' . $row->coursemainname .'</a>';
                })
                ->addColumn('status', function($row) {
                    return enrollStatusBadge($row->status);
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                    if( (substr('not enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 3);
                    }
                    if( (substr('enrolment cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('holding list', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 2);
                    }
                })
                ->addColumn('payment_status', function($row) {
                    return paymentStatusBadge($row->payment_status);
                })
                ->filterColumn('payment_status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 1);
                    }
                    if( (substr('partial', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 2);
                    }
                    if( (substr('full', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 3);
                    }
                    if( (substr('refunded', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 4);
                    }
                })
                ->addColumn('courseName', function($row) {
                    return $row->courseNameWithTPG;
                })
                ->filterColumn('students.name', function($query, $keyword) {
                    $query->where('students.name', 'LIKE', '%'. strtolower($keyword) .'%');
                })
                ->filterColumn('nric', function($query, $keyword) {
                    $query->where('nric', 'LIKE', '%'. strtolower($keyword) .'%');
                })
                /*->filterColumn('tpgateway_refno', function($query, $keyword) {
                    $query->where('tpgateway_refno', 'LIKE', '%'. strtolower($keyword) .'%');
                })*/
                /*->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->orderColumn('nric', function ($query, $order) {
                    $query->orderBy('nric', $order);
                })*/
                ->addColumn('cancelcheckbox' , function($row){
                if( $row->course_type_id != 2 && $row->status != StudentEnrolment::STATUS_CANCELLED ) {
                    $btn = '';
                    $btn .= $row->id;
                    return $btn;
                }
                })
                ->addColumn('action', function($row) {

                    $start_date = date("Y-m-d");
                    $end_date = $row->courseRun['course_start_date'];

                    $getDateDiff  = getDateDifference($start_date,$end_date);
                    $btn = '';
                    $btn .= '<div class="dropdown dot-list">
                            <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                <ul  class="dropdown-menu">';
                    if( Gate::allows('studentenrolment-view') ) {
                    $btn .= '<li><a href="'.route('admin.studentenrolment.view',$row->id).'"><i class="fas fa-eye font-16"></i> View</a></li>';
                    }
                    $btn .= '<li><a href="'.route('admin.studentenrolment.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>';

                    $btn .= '<li><a class="viewpayment" href="javascript:void(0)" enrolement_id="'.$row->id.'"><i class="fas fa-dollar-sign font-16"></i>View Payments</a></li>';
                    $btn .= '<li><a href="'.route('admin.payment.add').'?studentenrollment='.$row->id.'"><i class="fas fa-dollar-sign font-16"></i>Add Payment</a></li>';

                    if($row->course_full_fees != 0){
                        $btn .= '<li><a href="'.route('admin.course.preview-invoice', $row->id).'"><i class="fa fa-sharp fa-solid fa-file"></i>Preview Invoices</a></li>';
                    }

                    if( $row->course_type_id == 1 && $row->status != StudentEnrolment::STATUS_CANCELLED ) {
                        $btn .= '<li><a class="viewenrolmentresponse" href="javascript:void(0)" type="enrolment" enrolement_id="'.$row->id.'"><i class="fas fa-eye font-16"></i>Enrollment Res</a></li>';
                    }
                    if( $row->course_type_id != 2 && $row->status != StudentEnrolment::STATUS_CANCELLED ) {
                        $btn .= '<li><a class="cancelenrolement" href="javascript:void(0)" enrolement_id="'.$row->id.'" ><i class="far fa-trash-alt font-16"></i>Cancel Enrolement</a></li>';
                    }
                    if( $row->course_type_id != 2 && $row->status == StudentEnrolment::STATUS_ENROLLED ) {
                        $btn .= '<li><a class="holdenrolement" href="javascript:void(0)" enrolement_id="'.$row->id.'" ><i class="far fa-stop-circle font-16"></i>Move to Hold List</a></li>';
                    }
                    $btn .= '</ul>
                            </div>';
                    /*$btn .= '<a href="'.route('admin.payment.view', $row->id).'" data-toggle="tooltip" data-placement="bottom" title="Payment View" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>';*/
                    return $btn;
                })

                ->addColumn('attendedSessions', function($row) {
                    $totalSession = count($row->attendances);
                    $presentSessionCount = $row->attendances->where('is_present',1)->where('attendance_sync',1)->count();
                    $percentProgress = 0;
                    if($presentSessionCount && $totalSession != 0){
                        $percentProgress = round($presentSessionCount / $totalSession * 100);
                    }
                    return $percentProgress .'%';
                })
                
                /*->filterColumn('course_id', function($query) use ($request) {
                    $thiscourseRunId = $request->get('courserun');
                    if( $thiscourseRunId > 0 ) {
                        $query->where('course_id', $thiscourseRunId);
                    }
                })*/
                ->rawColumns(['action', 'status', 'payment_status','attendedSessions','student_name', 'courseName', 'cancelcheckbox'])
                ->make(true);
    }

    public function studentEnrolmentAdd(StudentEnrolmentStoreRequest $request, OauthCredentialManager $xeroCredentials)
    {
        // dd($request->method());
        if (! Gate::allows('studentenrolment-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            // check if student has enroled or not
            if( !$this->studentService->checkstudentalreadyenrolled($request) ) {
                setflashmsg(trans('msg.alreadyEnrolled'), 0);
                return redirect()->back()->withInput($request->input());
            }
            // $courseId = $request->get('course_id');
            // $courseData = ['f2f_course_id' => $courseId, 'online_course_id' => $courseId];
            // $request->request->add($courseData); //add request
            $trainer = $this->studentService->addStudentEnrolment($request, $xeroCredentials);
            if( $trainer ) {
                setflashmsg(trans('msg.studentEnrolmentCreated'), 1);
                return redirect()->route('admin.studentenrolment.list');
            }else{
                setflashmsg(trans('msg.studentEnrolmentCreatedNotSync'), 0);
                return redirect()->route('admin.studentenrolment.list');
            }
        }

        // if id exist get data from soft booking
        $studentData = NULL;
        if( $request->has('softbooking') && $request->get('softbooking') ) {
            $softBookingService = new SoftBookingService;
            $id = $request->get('softbooking');
            $studentData = $softBookingService->getSoftBookingById($id);
        }
        if( $request->has('waitinglist') && $request->get('waitinglist') ) {
            $waitingListService = new WaitingListService;
            $id = $request->get('waitinglist');
            $studentData = $waitingListService->getWaitingListById($id);
        }
        $singleCourse = true;
        if( !is_null($studentData) ) {
            if($studentData->course->courseMain->course_type_id != 1) {
                $singleCourse = false;
            }
        }
        $courseRunId = $request->get('courseRunId');
        $getCourseRun = Course::where('id',$courseRunId)->with('courseMain')->first();
        if(!is_null($courseRunId)){
            $studentEnrollAdd = view('admin.studentenrolment.add', compact('studentData', 'singleCourse', 'courseRunId', 'getCourseRun'))->render();
            return response()->json(['view' => $studentEnrollAdd]);  
        }
        return view('admin.studentenrolment.add', compact('studentData', 'singleCourse', 'courseRunId', 'getCourseRun'));
    }

    public function studentEnrolmentAddViaCourseRun(StudentEnrolmentStoreRequest $request, OauthCredentialManager $xeroCredentials)
    {
        if (! Gate::allows('studentenrolment-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            // check if student has enroled or not
            if( !$this->studentService->checkstudentalreadyenrolled($request) ) {
                setflashmsg(trans('msg.alreadyEnrolled'), 0);
                return redirect()->back()->withInput($request->input());
            }
            // $courseId = $request->get('course_id');
            // $courseData = ['f2f_course_id' => $courseId, 'online_course_id' => $courseId];
            // $request->request->add($courseData); //add request
            $trainer = $this->studentService->addStudentEnrolment($request, $xeroCredentials);
            if( $trainer ) {
                setflashmsg(trans('msg.studentEnrolmentCreated'), 1);
                return redirect()->route('admin.studentenrolment.list');
            }else{
                setflashmsg(trans('msg.studentEnrolmentCreatedNotSync'), 0);
                return redirect()->route('admin.studentenrolment.list');
            }
        }

        // if id exist get data from soft booking
        $studentData = NULL;
        if( $request->has('softbooking') && $request->get('softbooking') ) {
            $softBookingService = new SoftBookingService;
            $id = $request->get('softbooking');
            $studentData = $softBookingService->getSoftBookingById($id);
        }
        if( $request->has('waitinglist') && $request->get('waitinglist') ) {
            $waitingListService = new WaitingListService;
            $id = $request->get('waitinglist');
            $studentData = $waitingListService->getWaitingListById($id);
        }
        $singleCourse = true;
        if( !is_null($studentData) ) {
            if($studentData->course->courseMain->course_type_id != 1) {
                $singleCourse = false;
            }
        }
        $courseRunId = $request->courserun;
        $getCourseRun = Course::where('id',$courseRunId)->with('courseMain')->first();

        return view('admin.studentenrolment.add',compact('studentData', 'singleCourse', 'courseRunId', 'getCourseRun'));
    }



    public function studentEnrolmentEdit($id, StudentEnrolmentUpdateRequest $request, OauthCredentialManager $xeroCerd)
    {
        if (! Gate::allows('studentenrolment-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $allCourses = $this->studentService->updateStudentEnrolment($id, $request, $xeroCerd);
            if( $allCourses ) {
                setflashmsg(trans('msg.studentEnrolmentUpdated'), 1);
                return redirect()->route('admin.studentenrolment.list');
            }
        }

        $data = $this->studentService->getStudentEnrolmentById($id);
        $singleCourse = true;
        if($data->courseRun->courseMain->course_type_id != 1) {
            $singleCourse = false;
        }
        // get upcoming course run list
        $courseService = new CourseService;
        $courseRunListService = $courseService->getCourseWithSessionForEnrollmentEdit($data->courseRun);
        return view('admin.studentenrolment.edit', compact('data', 'singleCourse', 'courseRunListService'));
    }

    public function studentEnrolmentView($id)
    {
        if (! Gate::allows('studentenrolment-view')) { return abort(403); }
        $data = $this->studentService->getStudentEnrolmentByIdWithRealtionData($id);
        $totalSession = count($data->attendances);
        $presentSessionCount = $data->attendances->where('is_present',1)->where('attendance_sync',1)->count();
        $percentProgress = 0;
        if($presentSessionCount && $totalSession != 0){
            $percentProgress = round($presentSessionCount / $totalSession * 100);
        }
        $singleCourse = true;
        if($data->courseRun->courseMain->course_type_id != 1) {
            $singleCourse = false;
        }
        return view('admin.studentenrolment.view', compact('data', 'singleCourse', 'percentProgress'));
    }

    public function getPaymentList(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->getStudentEnrolmentByIdWithRealtionData($enrolement_id);
        $view = view('admin.partial.student-enrolement-payment-list', compact('records'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function searchStudentEnrolment(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->studentService->searchStudentEnrolmentAjax($query);
        return json_encode($ret);
    }

    public function searchStudent(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->studentService->searchStudentAjax($query);
        return json_encode($ret);
    }

    /**
     * Show the list of students.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentsIndex(Request $request)
    {
        if (! Gate::allows('students-list')) { return abort(403); }
        return view('admin.students.list');
    }

    public function studentListDatatable(Request $request)
    {
        if (! Gate::allows('students-list')) { return abort(403); }
        $records = $this->studentService->getAllStudents();
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="javascript:void(0)" student_id="'.$row->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="editstudent mr-2 edit-back"><i class="fas fa-pencil-alt text-info font-16"></i></a>';
                    $btn .= '<a class="viewcourserun mr-2 eye-back" href="javascript:void(0)" student_id="'.$row->id.'" data-toggle="tooltip" data-placement="bottom" title="View Course Runs"><i class="fas fa-eye text-info font-16"></i></a>';
                    // $btn .= '<a class="viewactivity mr-2 eye-back" href="javascript:void(0)" student_id="'.$row->id.'" data-toggle="tooltip" data-placement="bottom" title="View Activity"><i class="fas fa-lightbulb text-info font-16"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function getStudentsCourseRunsList(Request $request)
    {
        $student_id = $request->get('id');
        $records = $this->studentService->getStudentEnrolmentByStudentIdWithRealtionData($student_id);
        $courseService = new CourseService;
        // $courseRunListService = $courseService->getCourseWithSessionForEnrollment();
        $refreshers = $courseService->getStudentRefreshersByStudentIdWithRealtionData($student_id);
        $student = $this->studentService->getStudentById($student_id);
        $view = view('admin.partial.student-courserun-list', compact('records', 'refreshers', 'student'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function getStudentsEdit(Request $request)
    {
        $student_id = $request->get('id');
        $student = $this->studentService->getStudentById($student_id);
        $view = view('admin.partial.student-edit', compact('student'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function getStudentsEditSubmit(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'student_id' => 'required',
            'name' => 'required',
            'nric' => 'required',
        ],[
            'student_id.required'   => 'No Student Selected',
            'name.required'   => 'Name is required',
            'nric.required' => 'Nric is required'
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $response = [
                'success'   => false,
                'message'   => $error,
            ];
            return response()->json($response, 200);
        }
        $response = $this->studentService->updateStudentByDetails($request);
        return response()->json($response, 200);
    }

    public function studentEnrolmentCancel(Request $request)
    {
        $enrolement_id = $request->get('id');
        $enrolement_ids = $request->get('ids');
        
        $data = $this->studentService->cancelStudentEnrolmentbyID($enrolement_id, $enrolement_ids );
        //$view = view('ajax.studentEnrolment.modal.cancel', compact('enrolement_id'))->render();

        return response()->json($data);
    }

    public function studentEnrolmentHold(Request $request)
    {
        $enrolement_id = $request->get('id');
        $data = $this->studentService->holdStudentEnrolmentbyID($enrolement_id);

        return response()->json($data);
    }

    public function getEnrolmentResponseView(Request $request)
    {
        $enrolement_id = $request->get('id');
        $type = $request->get('type');
        $records = $this->studentService->getStudentEnrolmentById($enrolement_id);
        $view = view('admin.partial.student-enrolement-response-view', compact('records', 'type'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function getRefresherResponseView(Request $request)
    {
        $enrolement_id = $request->get('id');
        $type = $request->get('type');
        $courseService = new CourseService;
        $record = $courseService->getRefreshersById($enrolement_id);
        $view = view('admin.partial.student-refresher-response-view', compact('record', 'type'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function doEnrolmentAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $enrolement_ids = $request->get('ids');
        $records = $this->studentService->doStudentEnrolmentAgainbyID($enrolement_id,$enrolement_ids);
        if($records['status']) {
            $data = [ 'status' => true, 'msg' => 'Enrolment successfully sync with TPG' ];
        } else {
            $data = [ 'status' => false, 'msg' => 'TPG sync failed' ];
        }

        return response()->json($data);
    }

    public function doEnrolmentGrantSearchAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->doStudentEnrolmentGrantCheckAgainbyID($enrolement_id);

        return response()->json($records);
    }

    public function doEnrolmentAttendanceAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->doStudentEnrolmentAttendanceAgainbyID($enrolement_id);
        if($records) {
            $data = [ 'status' => true, 'msg' => 'Attendance done' ];
        } else {
            $data = [ 'status' => false, 'msg' => 'Attendance error' ];
        }

        return response()->json($data);
    }

    public function doEnrolmentAssessmentAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->doStudentEnrolmentAssessmentAgainbyID($enrolement_id);
        if($records) {
            $data = [ 'status' => true, 'msg' => 'Assessment done' ];
        } else {
            $data = [ 'status' => false, 'msg' => 'Assessment error' ];
        }

        return response()->json($data);
    }

    public function doRefresherAttendanceAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->doStudentRefresherAttendanceAgainbyID($enrolement_id);
        if($records) {
            $data = [ 'status' => true, 'msg' => 'Attendance done' ];
        } else {
            $data = [ 'status' => false, 'msg' => 'Attendance error' ];
        }

        return response()->json($data);
    }

    public function doRefresherAssessmentAgain(Request $request)
    {
        $enrolement_id = $request->get('id');
        $records = $this->studentService->doStudentRefresherAssessmentAgainbyID($enrolement_id);
        if($records) {
            $data = [ 'status' => true, 'msg' => 'Assessment done' ];
        } else {
            $data = [ 'status' => false, 'msg' => 'Assessment error' ];
        }

        return response()->json($data);
    }

    /*public function getStudentsActivityList(Request $request)
    {
        //$revisions = [];
        $student_id = $request->get('id');
        $currentStudent = Student::find($student_id);
       
        $revisions = $currentStudent->revisionHistory;
        
        $studEnrolls = Student::with('enrolments')->find($student_id);

        foreach($studEnrolls->enrolments as $enroll)
        {
            $currentEnroll = StudentEnrolment::find($enroll->id);
            $revisions[]['enrollments'] = $currentEnroll->revisionHistory;
        }
               
        $student = $this->studentService->getStudentById($student_id);
        $view = view('admin.partial.student-activity-list', compact('revisions', 'student'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }*/

    public function getPaymentResponseView(Request $request)
    {
        $enrolement_id = $request->get('id');
        $type = $request->get('type');
        $records = $this->studentService->getStudentEnrolmentById($enrolement_id);
        $view = view('admin.partial.student-enrolement-response-view', compact('records', 'type'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

}
