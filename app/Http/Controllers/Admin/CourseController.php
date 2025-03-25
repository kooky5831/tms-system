<?php

namespace App\Http\Controllers\Admin;

use PDF;
use DataTables;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\VenueService;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Models\StudentEnrolment;
use App\Models\StudentCourseAttendance;
use App\Jobs\MakeTrainerFolderJob;
use App\Services\TPGatewayService;
use App\Services\CourseMainService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CourseStoreRequest;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
    public function index($id, Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        $courseMainService = new \App\Services\CourseMainService;
        $courseMain = $courseMainService->getCourseMainById($id);
        return view('admin.course.list', compact('courseMain'));
    }


    public function listDatatable($id, Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        $records = $this->courseService->getAllCourseRunByCourseMainId($id);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('is_published', function($row) {
                    if( $row->is_published == 1 ) { return '<span class="badge badge-soft-success">Published</span>'; }
                    elseif( $row->is_published == 2 ) { return '<span class="badge badge-soft-danger">Cancelled</span>'; }
                    else { return '<span class="badge badge-soft-danger">Un Published</span>'; }
                })
                ->editColumn('addtpgateway', function($row) {
                    if( !empty($row->tpgateway_id) ) { return '<span class="badge badge-soft-success">Added to TPG</span>'; }
                    else { return '<span class="badge badge-soft-danger">Not Added to TPG</span>'; }
                })
                ->filterColumn('addtpgateway', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('added to tpg', 0, $len) === strtolower($keyword)) ) {
                        $query->whereRaw('tpgateway_id != "" OR tpgateway_id IS NOT NULL');
                    }
                    if( (substr('not added to tpg', 0, $len) === strtolower($keyword)) ) {
                        $query->whereRaw('tpgateway_id = "" OR tpgateway_id IS NULL');
                    }
                })
                ->editColumn('registeredusercount', function($row) {
                    if( $row->registeredusercount ) {
                        return '<span class="badge badge-soft-success">
                                    <a href="'.route('admin.studentenrolment.list',$row->id).'">'.$row->registeredusercount.'</a>
                                </span>';
                    } else { return $row->registeredusercount; }
                })
                ->editColumn('slot', function($row) {
                    return $row->registeredusercount."/".$row->intakesize;
                })
                ->editColumn('modeoftraining', function($row) {
                    return '<span class="badge badge-soft-primary">'.getModeOfTraining($row->modeoftraining).'</span>';
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
                ->editColumn('coursevacancy_code', function($row) {
                    switch ($row->coursevacancy_code) {
                        case "A": $thisclass = "success"; break;
                        case "L": $thisclass = "warning"; break;
                        default: $thisclass = "danger"; break;
                    }
                    return '<span class="badge badge-soft-'.$thisclass.'">'.getCourseVacancy($row->coursevacancy_code).'</span>';
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
                            $btn .= '<li><a href="'.route('admin.course.courserunview',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.student',$row->id).'"><i class="fas fa-eye font-16"></i>Students List</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.get-attendance-assessment',$row->id).'" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.edit',$row->id).'" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>';
                            if($row->is_published != 2){ 
                                $btn .= '<li><a class="cancelcourserun" courserun_id="'.$row->id.'" 
                                 href="javascript:void(0)" ><i class="far fa-trash-alt font-16"></i>Cancel Course Run</a></li>';
                                }
                        $btn .= '</ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action','modeoftraining','addtpgateway', 'registeredusercount', 'coursevacancy_code', 'is_published'])
                ->make(true);

    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function listAllIndex(Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        $courseMainService = new CourseMainService;
        $courseList = $courseMainService->getAllCourseMainListForRuns();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();
        return view('admin.course.listall', compact('courseList','trainers'));
    }


    public function listAllDatatable(Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        $records = $this->courseService->getSignupsForCoursesWithFilter($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return '<a href="'.route('admin.course.courserunview',$row->id).'">' . $row->name . '</a>';
                })
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
                ->addColumn('cancelcheckbox' , function($row){
                    if( $row->course_type_id != 2 && $row->status != StudentEnrolment::STATUS_CANCELLED ) {
                        $btn = '';
                        $btn .= $row->id;
                        return $btn;
                    }
                    })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.course.courserunview',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.student',$row->id).'"><i class="fas fa-eye font-16"></i>Students List</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.get-attendance-assessment',$row->id).'" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.edit',$row->id).'?editpage=all" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>';
                            if($row->is_published != 2){ 
                            $btn .= '<li><a class="cancelcourserun" courserun_id="'.$row->id.'" 
                             href="javascript:void(0)" ><i class="far fa-trash-alt font-16"></i>Cancel Course Run</a></li>';
                            }
                            $btn .= '</ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action','modeoftraining', 'registeredusercount', 'coursevacancy_code', 'course_type', 'is_published', 'name'])
                ->make(true);

    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function listAllCompletedIndex(Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        return view('admin.course.listallcompleted');
    }


    public function listAllCompletedDatatable(Request $request)
    {
        if (! Gate::allows('course-list')) { return abort(403); }
        $records = $this->courseService->getAllCompletedCourse();
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
                ->editColumn('slot', function($row) {
                    return $row->registeredusercount."/".$row->intakesize;
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
                            $btn .= '<li><a href="'.route('admin.course.courserunview',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.student',$row->id).'"><i class="fas fa-eye font-16"></i>Students List</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.get-attendance-assessment',$row->id).'" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.edit',$row->id).'?editpage=completed" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action','modeoftraining','is_published'])
                ->make(true);

    }

    public function courseRunView($id, Request $request)
    {
        $data = $this->courseService->getCourseRunFullDetailsById($id);
        $studentService = new \App\Services\StudentService;
        $isGrantError = $studentService->hasGrantError($id);
        $isEnrollmentError = $studentService->hasEnrollmentError($id);
        // get payment list for all enrollment for this course
        $studentEnrollmentIds = $studentService->getAllStudentIdsForCourseRun($id);
        $paymentService = new \App\Services\PaymentService;
        $payments = $paymentService->getPaymentByStudentEnrolmentIds($studentEnrollmentIds);

        // $currentCourse = Course::find($id);
        // $revisions = $currentCourse->revisionHistory;
    
        return view('admin.course.courserunview', compact('data', 'payments', 'isGrantError', 'isEnrollmentError'));
    }

    public function studentList($id)
    {
        $studentService = new \App\Services\StudentService;
        $students = $studentService->getAllStudentsForCourseRun($id);
        $course = $this->courseService->getCourseById($id);
        return view('admin.course.studentlist',compact('students', 'course'));
    }

    public function courseAdd($id, CourseStoreRequest $request)
    {
        if (! Gate::allows('course-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $trainer = $this->courseService->registerCourse($request);
            if( $trainer ) {
                setflashmsg(trans('msg.courseCreated'), 1);
                return redirect()->route('admin.coursemain.list');
            }
        }
        // get course main data
        $venueService = new VenueService;
        $venueslist = $venueService->getAllVenuesList();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();
        $courseMainService = new \App\Services\CourseMainService;
        $courseMain = $courseMainService->getCourseMainById($id);
        return view('admin.course.add',compact('venueslist', 'trainers', 'courseMain'));
    }

    public function courseEdit($id, CourseStoreRequest $request)
    {
        if (! Gate::allows('course-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $courseRun = $this->courseService->updateCourse($id, $request);
            if( $courseRun ) {
                setflashmsg(trans('msg.courseUpdated'), 1);
                if( $request->has('editpage') && $request->get('editpage') == "completed" ) {
                    return redirect()->route('admin.course.listallcompleted');
                }
                return redirect()->route('admin.course.listall');
                // return redirect()->route('admin.course.list', $courseRun->course_main_id);
            }
        }

        $data = $this->courseService->getCourseByIdWithSession($id);
        $selectedTrainers = $data->trainers->pluck('id')->all();
        $venueService = new VenueService;
        $venueslist = $venueService->getAllVenuesList();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();
        $courseMainService = new \App\Services\CourseMainService;
        $courseMain = $courseMainService->getCourseMainById($data->course_main_id);
        return view('admin.course.edit', compact('data', 'venueslist', 'trainers', 'selectedTrainers', 'courseMain'));
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

    public function generateCertificateForAll($id)
    {
        $commonService = new \App\Services\CommonService;
        $genrateCertificate = $commonService->generateCommonCertificate($id, $request=null, __FUNCTION__);
        if($genrateCertificate == false){
            return redirect()->back();
        }
        return response()->download(Storage::path($genrateCertificate))->deleteFileAfterSend(true);
    }

    public function attendanceAssessment($id)
    {
        $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        $tpgatewayReq = new TPGatewayService;
        $sessionRes = $tpgatewayReq->getCourseSessionsFromTpGateway($result->tpgateway_id,$result->courseMain->reference_number);
        // dd($sessionRes);
        if( isset($sessionRes->status) && $sessionRes->status == 200 ) {
            // update the tpgateway session id
            foreach( $sessionRes->data->sessions as $tpgsession ) {
                $sessionId = 0;
                foreach( $result->session as $sess ) {
                    // dd($sess);
                    if( convertToTPDate($sess->start_date) == $tpgsession->startDate &&
                    convertToTPTime($sess->start_time) == $tpgsession->startTime ) {
                        if( $sess->id != 0 ) {
                            $this->courseService->updateSessionForCourseRun($sess->id, $tpgsession->id);
                        }
                        break;
                    }
                }
            }
            $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        }
        // $selectedTrainers = $result->trainers->pluck('id')->all();
        $common = new CommonService;
        $courseRunName = $common->makeSessionString($result->session);
        return view('admin.course.attendance-assessment', compact('result', 'courseRunName'));
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
        return redirect()->route('admin.course.get-attendance-assessment', $id);
    }

    public function submitAttendanceTpGateway($id, Request $request)
    {
        $enrollIds = $request->hiddenStudentsId;
        $refresherIds = $request->hiddenStudentsRefresherId;
        
        if( empty($enrollIds) && empty($refresherIds) ) {
            setflashmsg(trans('msg.selectStudent'), 0);
            return redirect()->back();
        }
        // add to TP Gateway
        $studentService = new \App\Services\StudentService;
        $tpgatewayReq = new TPGatewayService;
        $isError = false;
        if( !empty($enrollIds) ) {
            $result = $this->courseService->getCourseByIdAndStudentEnrolmentByIds($id,$enrollIds);
            foreach ($result as $record)
            {
                /* Code for Payment Start */
                ///tpg/enrolments/feeCollections/{referenceNumber}
                /* Code for Payment End */

                if( is_null($record->attendance) ) {
                    setflashmsg(trans('msg.fillattendance'), 0);
                    return redirect()->back();
                }

                if( !empty($record->isAttendanceError) && $record->isAttendanceError == 0 ) {
                    continue;
                }
                $req_data = [];
                $req_data = [
                    "uen" => config('settings.tpgateway_uenno'),
                    "corppassId" => config('settings.tpgateway_corppassId'),
                ];

                $req_data['course']['referenceNumber'] = $record->courseRun->courseMain->reference_number;

                try
                {
                    $stuEnrol = $studentService->getStudentEnrolmentById($record->id);
                    $isAttendanceError = false;
                    $attendanceData = json_decode($record->attendance);
                    $attendResData = json_decode($record->attendanceResponse, true);
                    foreach( $attendanceData as $key => $att )
                    {
                        if(!empty($att->att_sync) && $att->att_sync == 1)
                        {
                            continue;
                        }

                        $req_data['course']['sessionID'] = $att->tpgId;
                        $req_data['course']['attendance'] = [
                            "status" => [ "code" => $att->ispresent ? 1 : 2 ]
                        ];
                        $idType = "OT";
                        switch ($record->nationality) {
                            case "Singapore Permanent Resident":
                                $idType = "SB";
                            break;
                            case "Singapore Citizen":
                                $idType = "SP";
                            break;
                            case "Non-Singapore Citizen/PR":
                                $idType = "SO";
                            break;
                        }

                        $req_data['course']['attendance']['trainee'] = [
                            "id" => $record->student->nric,
                            "name" => $record->student->name,
                            "email" => $record->email,
                            "idType" => [ "code" => $idType ],
                            "contactNumber" => [
                                "mobile" => $record->mobile_no,
                                "areaCode" => "",
                                "countryCode" => 65
                            ],
                            "surveyLanguage" => [ "code" => "EL" ],
                            "numberOfHours" => 4
                        ];

                        // submit to tpgateway
                        if($att->ispresent){
                            
                            $attendRes = $tpgatewayReq->courseAttendance($record->courseRun->tpgateway_id, $req_data);
                            if( isset($attendRes->status) && $attendRes->status == 200 ) {
                                $attendanceData[$key]->att_sync = 1;
                                $attendance = StudentCourseAttendance::where("session_id", $attendanceData[$key]->session_id)
                                ->update(["attendance_sync" => 1]);
                            } else {
                                $isError = true;
                                $isAttendanceError = true;
                                $attendanceData[$key]->att_sync = 2;
                                $attendance = StudentCourseAttendance::where("session_id", $attendanceData[$key]->session_id)
                                ->update(["attendance_sync" => 0]);
                            }

                            $attendResData[$att->tpgId] = $attendRes;
                        }
                        
                    }

                    if($isAttendanceError) {
                        $stuEnrol->isAttendanceError = 1;
                    } else {
                        $stuEnrol->isAttendanceError = 0;
                    }

                    $stuEnrol->attendanceResponse = json_encode($attendResData);
                    $stuEnrol->attendance = json_encode($attendanceData);
                    // update the student enrolment data
                    $stuEnrol->save();
                } catch (\Exception $e) {
                    \Log::info($e->getMessage());
                }
            }
        }


        if( !empty($refresherIds) ) {
            $refresherResult = $this->courseService->getCourseByIdAndStudentrefResherByIds($id,$refresherIds);
            // For Refreshers
            foreach ($refresherResult as $refresh)
            {
                /* Code for Payment Start */
                ///tpg/enrolments/feeCollections/{referenceNumber}
                /* Code for Payment End */

                if( is_null($refresh->attendance) ) {
                    setflashmsg(trans('msg.fillattendance'), 0);
                    return redirect()->back();
                }

                if( !empty($refresh->isAttendanceError) && $refresh->isAttendanceError == 0 ) {
                    continue;
                }
                $req_data = [];
                $req_data = [
                    "uen" => config('settings.tpgateway_uenno'),
                    "corppassId" => config('settings.tpgateway_corppassId'),
                ];

                $req_data['course']['referenceNumber'] = $refresh->course->courseMain->reference_number;

                try
                {
                    $stuEnrol = $this->courseService->getRefreshersById($refresh->id);
                    $isAttendanceError = false;
                    $attendanceData = json_decode($refresh->attendance);
                    $attendResData = json_decode($refresh->attendanceResponse, true);
                    foreach( $attendanceData as $key => $att )
                    {
                        if(!empty($att->att_sync) && $att->att_sync == 1)
                        {
                            continue;
                        }

                        $req_data['course']['sessionID'] = $att->tpgId;
                        $req_data['course']['attendance'] = [
                            "status" => [ "code" => $att->ispresent ? 1 : 2 ]
                        ];
                        $idType = "OT";
                        switch ($refresh->student->nationality) {
                            case "Singapore Permanent Resident":
                                $idType = "SB";
                            break;
                            case "Singapore Citizen":
                                $idType = "SP";
                            break;
                            case "Non-Singapore Citizen/PR":
                                $idType = "SO";
                            break;
                        }

                        $req_data['course']['attendance']['trainee'] = [
                            "id" => $refresh->student->nric,
                            "name" => $refresh->student->name,
                            "email" => $refresh->student->email,
                            "idType" => [ "code" => $idType ],
                            "contactNumber" => [
                                "mobile" => $refresh->student->mobile_no,
                                "areaCode" => "",
                                "countryCode" => 65
                            ],
                            "surveyLanguage" => [ "code" => "EL" ],
                            "numberOfHours" => 4
                        ];

                        // submit to tpgateway
                        if($att->ispresent){
                            
                            $attendRes = $tpgatewayReq->courseAttendance($refresh->course->tpgateway_id, $req_data);
                            if( isset($attendRes->status) && $attendRes->status == 200 ) {
                                $attendanceData[$key]->att_sync = 1;
                            } else {
                                $isError = true;
                                $isAttendanceError = true;
                                $attendanceData[$key]->att_sync = 2;
                            }

                            $attendResData[$att->tpgId] = $attendRes;
                        }

                        
                    }

                    if($isAttendanceError) {
                        $stuEnrol->isAttendanceError = 1;
                    } else {
                        $stuEnrol->isAttendanceError = 0;
                    }

                    $stuEnrol->attendanceResponse = json_encode($attendResData);
                    $stuEnrol->attendance = json_encode($attendanceData);
                    // update the student enrolment data
                    $stuEnrol->save();
                } catch (\Exception $e) {
                    \Log::info($e->getMessage());
                }
            }
        }

        if( $isError ) {
            setflashmsg(trans('msg.attendancetpgFailed'), 0);
        } else {
            setflashmsg(trans('msg.attendancetpgSuccess'), 1);
            /*$courseRun = $this->courseService->getCourseById($id);
            $courseRun->isAttendanceSubmitedTPG = 1;
            $courseRun->save();*/
        }
        return redirect()->back();
    }

    public function submitPaymentTpGateway($id, Request $request)
    {
        $enrollIds = $request->hiddenStudentsId;
        if( empty($enrollIds) ) {
            setflashmsg(trans('msg.selectStudent'), 0);
            return redirect()->back();
        }
        $result = $this->courseService->getCourseByIdAndStudentEnrolmentByIds($id, $enrollIds);
        $isError = false;
        $studentService = new \App\Services\StudentService;
        $tpgatewayReq = new TPGatewayService;

        foreach ($result as $key => $record) {
            if( !empty($record->isPaymentError) && $record->isPaymentError == 0 ) {
               continue;
            }
            $stuEnrol = $studentService->getStudentEnrolmentById($record->id);
            $payment_status = getPaymentStatusForTPG($record->payment_tpg_status);
            $req_data = [];
            $req_data['enrolment'] = [
                'fees' => [
                    "collectionStatus" => $payment_status
                ],
            ];

            $referenceNumber = $record->tpgateway_refno;
            $paymentRes = $tpgatewayReq->coursePayments($referenceNumber, $req_data);
            if( isset($paymentRes->status) && $paymentRes->status == 200 ) {
                $stuEnrol->tpg_payment_sync = 1;
                $stuEnrol->isPaymentError = 0;
            } else {
                $stuEnrol->tpg_payment_sync = 2;
                $isError = true;
                $stuEnrol->isPaymentError = 1;
            }
            
            $stuEnrol->tgp_payment_response = json_encode($paymentRes);
            // update the student enrolment data
            $stuEnrol->save();
        }
        if( $isError ) {
            setflashmsg(trans('msg.paymenttpgFailed'), 0);
        } else {
            setflashmsg(trans('msg.paymenttpgSuccess'), 1);
        }
        return redirect()->back();
    }

    public function submitAssessmentTpGateway($id, Request $request)
    {
        $enrollIds = $request->hiddenStudentsId;
        
        if( empty($enrollIds) ) {
            setflashmsg(trans('msg.selectStudent'), 0);
            return redirect()->back();
        }

        $result = $this->courseService->getCourseByIdAndStudentEnrolmentByIds($id, $enrollIds);
        $isError = false;
        $studentService = new \App\Services\StudentService;
        $tpgatewayReq = new TPGatewayService;

        foreach ($result as $key => $record) 
        {
            if( is_null($record->assessment) ) {
                setflashmsg(trans('msg.fillassessment'), 0);
                return redirect()->back();
            }

            if( !empty($record->isAssessmentError) && $record->isAssessmentError == 0 ) {
                continue;
            }

            $req_data = [];
            $req_data['assessment'] = [
                'trainingPartner' => [
                    "code" => config('settings.tpgateway_code'),
                    "uen" => config('settings.tpgateway_uenno')
                ],
                "course" => [
                    "referenceNumber" => $record->courseRun->courseMain->reference_number,
                    "run" => [ "id" => $record->courseRun->tpgateway_id ]
                ],
            ];

            try 
            {
                $stuEnrol = $studentService->getStudentEnrolmentById($record->id);

                if($record->assessment == 'c'){
                    $assResult = 'Pass';
                }else if($record->assessment == 'nyc'){
                    $assResult = 'Fail';
                }
                else{
                    $assResult = 'Exempt';
                }
               
               
                $req_data['assessment']['trainee'] = [
                    "id" => $record->student->nric,
                    "idType" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC",
                    "fullName" => $record->student->name,
                ];
                $req_data['assessment']['result'] = $assResult;
                $req_data['assessment']['assessmentDate'] = $record->assessment_date;
                $req_data['assessment']['skillCode'] = $record->courseRun->courseMain->skill_code;
                $req_data['assessment']['conferringInstitute'] = [ "code" => config('settings.tpgateway_code')];

                if(!empty($stuEnrol->assessment_ref_no)){
                    $req_data['assessment']['action'] = "update";
                    $assessRes = $tpgatewayReq->updateVoidCourseAssessment($stuEnrol->assessment_ref_no, $req_data);
                }else{
                    $assessRes = $tpgatewayReq->courseAssessments($req_data);
                }
                
                $stuEnrol->assessmentResponse = json_encode($assessRes);
                if( isset($assessRes->status) && $assessRes->status == 200 ) {
                    $stuEnrol->assessment_sync = 1;
                    $stuEnrol->isAssessmentError = 0;
                    $stuEnrol->assessment_ref_no = $assessRes->data->assessment->referenceNumber;
                } else {
                    $stuEnrol->assessment_sync = 2;
                    $isError = true;
                    $stuEnrol->isAssessmentError = 1;
                }
                //$assessResData[$key] = $assessRes;
                
                //$stuEnrol->assessmentResponse = json_encode($assessResData);
                // update the student enrolment data
                $stuEnrol->save();

            }
            catch (\Exception $e) 
            {
                \Log::info($e->getMessage());
            }
        }

        if( $isError ) {
            setflashmsg(trans('msg.assessmenttpgFailed'), 0);
        } else {
            setflashmsg(trans('msg.assessmenttpgSuccess'), 1);
            /*$courseRun = $this->courseService->getCourseById($id);
            $courseRun->isAssessmentSubmitedTPG = 1;
            $courseRun->save();*/
        }
        return redirect()->back();
    }

    public function addCourseRunToTpGateway($id)
    {
        $result = $this->courseService->getCourseById($id);
        if( empty($result->id) ) {
            return redirect()->back();
        }
        if( !empty($result->tpgateway_id) ) {
            setflashmsg("Already added to tp gateway", 0);
            return redirect()->back();
        }
        $courserun = $this->courseService->addCourseRunToTPGateway($id);
        if( $courserun['status'] ) {
            setflashmsg("Course Run added successfully", 1);
        } else {
            setflashmsg("Course Run adding Error. Please check response", 0);
        }
        return redirect()->back();
    }

    public function trainerView($id)
    {
        if (! Gate::allows('trainer-view')) { return abort(403); }
        dd('pending');
        $doctor = $this->doctorService->getDoctorById($id);
        return view('backend.doctors.view', compact('doctor'));
    }

    public function grantCalculator()
    {
        $courseService = new CourseService;
        $courseMain = $courseService->showGrant();
    }

    public function getSessions($id)
    {
        return $data = $this->courseService->getCourseByIdWithSession($id);
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
        $view = view('admin.partial.courserun-upload-document-edit', compact('record'))->render();
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

    public function searchCourseRuns(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->courseService->searchCourseRunsAjax($query);
        return json_encode($ret);
    }

    // Course Refreshers
    public function refreshersAdd(Request $request, $id)
    {
        if( $request->method() == 'POST') {
            // check if this student has already been added to this course
            if( $this->courseService->checkRefreshersAdded($request) ) {
                setflashmsg('Student already added', 0);
                return redirect()->route('admin.course.courserunview', $id);
            }
            $record = $this->courseService->registerRefreshersCourse($request);
            if( $record ) {
                setflashmsg(trans('msg.courseRefresherCreated'), 1);
                return redirect()->route('admin.course.courserunview', $record->course_id);
            }
        }
        // Get the Course Run details First
        $data = $this->courseService->getCourseById($id);
        // check if this course start date is not gone
        if( \Carbon\Carbon::parse($data->course_end_date)->addWeeks(1)->isPast() ) {
            setflashmsg("Course already started", 0);
            return redirect()->back();
        }
        /**
         * Check if this user has attended this Course previously within 1 year
         * get all course run Ids which happend for this main course within 1 year
        **/
        /*$courseRunIds = $this->courseService->getCourseRunIdsForMainCourse($data->course_main_id);
        // redirect back if not with error message
        if( empty($courseRunIds) ) {
            setflashmsg("No course run found within 1 year", 0);
            return redirect()->back();
        }*/

        /*$studentIds = \App\Models\StudentEnrolment::whereIn('course_id', $courseRunIds)
                            ->where('status', '!=', 1)->pluck('student_id');
        if( empty($studentIds) ) {
            setflashmsg("No student enrolled to this course within 1 year", 0);
            return redirect()->back();
        }*/
        // $students = \App\Models\Student::whereIn('id', $studentIds)->get();
        /**
         * allow this user to be added in course refresher. But don't add them in TPGateway
         * neither increase the count of registration. But they can get the certification
         * and assessment can be done as well
        **/
        // return view('admin.course.refresher-add', compact('students', 'data'));
        return view('admin.course.refresher-add', compact('data'));
    }

    public function refreshersEdit(Request $request, $id)
    {
        if( $request->method() == 'POST') {
            $record = $this->courseService->updateRefreshersCourse($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.courseRefresherUpdated'), 1);
                return redirect()->route('admin.course.courserunview', $record->course_id);
            }
            setflashmsg('Details not updated', 0);
            return redirect()->back();
            // return redirect()->route('admin.course.courserunview', $id);
        }
        $data = $this->courseService->getRefreshersById($id);
        return view('admin.course.refresher-edit', compact('data'));
    }

    public function refreshersView(Request $request, $id)
    {
        $data = $this->courseService->getRefreshersById($id);
        $singleCourse = true;
        if($data->course->courseMain->course_type_id != 1) {
            $singleCourse = false;
        }
        // dd($data->course);
        return view('admin.course.refresher-view', compact('data', 'singleCourse'));
    }

    public function refresherNotesView(Request $request)
    {
        $main_id = $request->get('id');
        $data = $this->courseService->getRefreshersById($main_id);
        $view = view('admin.partial.view-notes', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    // sync from Tp Gateway
    public function syncAttendanceTpGateway($id, Request $request)
    {
        // check if we have course session data
        // if not then get it
        // loop through sessions and get session attendance
        $course = Course::with(['session', 'courseMain'])->where('id', $id)->first();
        // dd($course);
        // $result = $this->courseService->getCourseByIdStudentEnrolment($id);
        // dd($result);
        $req_data = [];
        $req_data = [
            "uen" => config('settings.tpgateway_uenno'),
            "referenceNumber" => $course->courseMain->reference_number
            // "referenceNumber" => "TGS-2020505256",
            // "sessionId" => "TGS-2020505256-407522-S1"
        ];
        $tpgatewayReq = new TPGatewayService;
        // $attendRes = $tpgatewayReq->getCourseAttendance($course->tpgateway_id, $req_data);
        /*$attendRes = $tpgatewayReq->getCourseAttendance(407522, $req_data);
        // dd($attendRes);
        if( isset($attendRes->status) && $attendRes->status == 200 ) {
            // attendance taken status, (Confirmed, Unconfirmed, Rejected, TP Voided)
            foreach( $attendRes->data->courseRun->sessions as $resSession ) {
                dd($resSession);
                $sessionId = $resSession->id;
                $attendance = $resSession->attendance;
                foreach( $attendance as $attn ) {
                    // search for student enrolment

                }
            }
            dd($attendRes);
        }*/

        // below code check
        foreach( $course->session as $sess ) {
            // dd($sess);
            $req_data['sessionId'] = $sess->tpgateway_id;
            $tpgatewayReq = new TPGatewayService;
            $attendRes = $tpgatewayReq->getCourseAttendance($course->tpgateway_id, $req_data);
            if( isset($attendRes->status) && $attendRes->status == 200 ) {
                // attendance taken status, (Confirmed, Unconfirmed, Rejected, TP Voided)
                foreach( $attendRes->data->courseRun->sessions as $resSession ) {
                    $sessionId = $resSession->id;
                    $attendance = $resSession->attendance;
                    foreach( $attendance as $attn ) {
                        if( $attn->status == "Confirmed" ) {
                            // search for student enrolment
                            $student = Student::where('nric', $attn->nric)->first();
                            if( $student ) {
                                $stuEnrol = StudentEnrolment::where('student_id', $student->id)->where('course_id', $course->id)->where('status', 0)->first();
                                if( $stuEnrol ) {
                                    // update the attendance for this session in attendance field
                                    $attendanceData = json_decode($stuEnrol->attendance);
                                    // dump($attendanceData);
                                    if( !is_array($attendanceData) ) {
                                        continue;
                                        // dd($stuEnrol);
                                        // dd($attendanceData);
                                    }
                                    foreach( $attendanceData as $key => $att ) {
                                        if( $attendanceData[$key]->tpgId == $sess->tpgateway_id ) {
                                            // if(!empty($att->att_sync) && $att->att_sync == 1) {
                                            //     continue;
                                            // }
                                            $attendanceData[$key]->tpgId = $sess->tpgateway_id;
                                            $attendanceData[$key]->start_date = convertFromTPDate($resSession->startDate);
                                            $attendanceData[$key]->end_date = convertFromTPDate($resSession->endDate);
                                            $attendanceData[$key]->start_time = $resSession->startTime;
                                            $attendanceData[$key]->ispresent = "1";
                                            $attendanceData[$key]->remark = null;
                                            $attendanceData[$key]->att_sync = 1;
                                            $stuEnrol->attendance = json_encode($attendanceData);
                                            // update the student enrolment data
                                            $stuEnrol->save();

                                            StudentCourseAttendance::updateOrCreate(
                                                [
                                                    'session_id' => $sess->id,
                                                    'student_enrolment_id' => $stuEnrol->id,
                                                ],
                                                [
                                                    'session_id' => $sess->id,
                                                    'student_enrolment_id' => $stuEnrol->id,
                                                    'course_id' => $course->id,
                                                    'is_present' => 1,
                                                    'attendance_sync' => 1,
                                                ]
                                            );
                                            
                                        }
                                    }
                                }
                            }
                        }
                    } // end foreach
                }
            }
        }
        setflashmsg(trans('msg.attendancetpgSuccess'), 1);
        return redirect()->back();
    }

    public function syncAssessmentTpGateway($id, Request $request)
    {
        $isSuccess = true;
        $course = Course::with(['session', 'courseMain'])->where('id', $id)->first();
        $activeEnrolments = $course->courseActiveEnrolmentsWithStudent;
        
        $req_data = [];

        $req_data['meta'] = [
            "lastUpdateDateFrom" => Carbon::createFromDate($course->course_start_date)->startOfYear()->format('Y-m-d'),
            "lastUpdateDateTo" => Carbon::createFromDate($course->course_start_date)->endOfYear()->format('Y-m-d'),
        ];
        $req_data['sortBy'] = [
            "field" => "updatedOn",
            "order" => "asc"
        ];
        $req_data['parameters'] = [
            "page" => 0,
            "pageSize" => 100
        ];

        foreach($activeEnrolments as $enrolment){

            $student = Student::find($enrolment->student_id);

            $req_data['assessments'] = [
                "course" => [
                    "run" => [ 'id' => $course->tpgateway_id ],
                    "referenceNumber" => $course->courseMain->reference_number,
                ],
                "trainee" => [  "id" => $student->nric],
                "enrolment" => [
                    "referenceNumber" => $enrolment->tpgateway_refno,
                ],
                "skillCode" => $course->courseMain->skill_code,
                'trainingPartner' => [
                    "code" => config('settings.tpgateway_code'),
                    "uen" => config('settings.tpgateway_uenno')
                ],
               
            ];

            $tpgatewayReq = new TPGatewayService;
            $assessRes = $tpgatewayReq->getCourseAssessments($req_data);

            if( isset($assessRes->status) && $assessRes->status == 200 ) {
                foreach($assessRes->data as $resAsses){
                    $student = Student::where('nric', $resAsses->trainee->id)->first();
                    if($student){
                        $stuEnrol = StudentEnrolment::where('student_id', $student->id)->where('course_id', $course->id)->where('status', 0)->first();
                        if($stuEnrol){
                            if($resAsses->result == 'Pass'){
                                $result = 'c';
                            }
                            else if($resAsses->result == 'Fail'){
                                $result = 'nyc';
                            }
                            else{
                                $result = 'void';
                            }
                            $stuEnrol->assessment = $result;
                            $stuEnrol->assessment_ref_no = $resAsses->referenceNumber;
                            $stuEnrol->assessment_sync = 1;
                            $stuEnrol->isAssessmentError = 0;
                            $stuEnrol->update();
                        }
                        
                    }
                }   
            }
            else{
                if($enrolment){
                    $enrolment->assessment_sync = 2;
	                $enrolment->isAssessmentError = 1;
                }
                $isSuccess = false;
            }
        }

        if($isSuccess){
            setflashmsg(trans('msg.assessmenttpgSyncSuccess'), 1);
            return redirect()->back();
        }
        else{
            setflashmsg(trans('msg.assessmenttpgSyncFailed'), 0);
            return redirect()->back();
        }
        
    }

    public function courseRunCancel(Request $request)
    {
        \Log::info("Cancel Course Run Controller function Call");
        $courserun_id = $request->get('id');
        $data = $this->courseService->cancelCourseRun($courserun_id, $request);
        return response()->json($data);
        \Log::info("Cancel Course Run Controller function End");
    }

    public function generateDocuments(Request $request)
    {
        $courseId = $request->get('id');
        $userId = Auth::id();
        MakeTrainerFolderJob::dispatch($courseId, $userId);
        $res = [ 'status' => true, 'data' => [], 'msg' => 'Document Generated Job added in queue successfully. You will be notified via email once it will complete!', 'success' => true ];
        return response()->json($res);
    }

    public function voidStudentAssessment(Request $request)
    {
        
        $tpgatewayReq = new TPGatewayService;
        $studentService = new \App\Services\StudentService;

        $enrolement_id = $request->get('id');

        $tpgatewayReq = new TPGatewayService;
        
        $studentEnrol = $studentService->getStudentEnrolmentById($enrolement_id);

        if($studentEnrol){

            $aseesRefNo = $studentEnrol->assessment_ref_no;

            $req_data['assessment'] = [
                "action" => "void",
            ];

            if(!empty($aseesRefNo)){
                \Log::info("Assessment Number: ".$aseesRefNo);
                $voidAssessRes = $tpgatewayReq->updateVoidCourseAssessment($aseesRefNo, $req_data);
                if( isset($voidAssessRes->status) && $voidAssessRes->status == 200 ) {
                    $data = [ 'status' => true, 'msg' => 'Assessment Void Successfully' ];
                }
                else{
                    $data = [ 'status' => false, 'msg' => 'Assessment Void failed. Please try again later' ];
                } 
            }
            else{
                $data = [ 'status' => false, 'msg' => 'No Assessment Reference Number found' ];
            }
        }
        else{
            $data = [ 'status' => false, 'msg' => 'No Student Enrolment Found' ];
        }

        return response()->json($data);
    }
    
    public function getPaymentTpGateway($id, Request $request){
        $isSuccess = true;
        $course = Course::with(['session', 'courseMain'])->where('id', $id)->first();
        $activeEnrolments = $course->courseActiveEnrolmentsWithStudent;
        $tpgatewayReq = new TPGatewayService;

        foreach($activeEnrolments as $enrolment){
            if($enrolment->tpgateway_refno){
                $statusData = $tpgatewayReq->getStudentEnrolmentPaymentStatus($enrolment->tpgateway_refno);
                if(isset($statusData->status) && $statusData->status == 200) {
                    $enrolPaymentStatus = $statusData->data->enrolment->trainee->fees->collectionStatus;
                    $paymentTpgStatus = getPaymentStatusFormTPG($enrolPaymentStatus);
                    $studentEnrolment = StudentEnrolment::find($enrolment->id);
                    $studentEnrolment->payment_tpg_status = $paymentTpgStatus;
                    $studentEnrolment->tpg_payment_sync = 1;
                } else {
                    $studentEnrolment->tpg_payment_sync = 2;
                    $isSuccess = false;
                }
                $studentEnrolment->update();
            } else {
                $isSuccess = false;
            }
        }

        if($isSuccess){
            setflashmsg(trans('msg.paymenttpgSyncSuccess'), 1);
            return redirect()->back();
        }
        else{
            setflashmsg(trans('msg.paymenttpgSyncFailed'), 0);
            return redirect()->back();
        }
    }

    public function getPaymentTpGatewayByIds(Request $request){
        if(isset($request->ids)) {
            $isSuccess = true;
            $enrolIds = explode(',', $request->ids);
            $tpgatewayReq = new TPGatewayService;
            foreach($enrolIds as $id) {
                $studentEnrolment = StudentEnrolment::find($id);
                
                if($studentEnrolment->tpgateway_refno){
                    $statusData = $tpgatewayReq->getStudentEnrolmentPaymentStatus($studentEnrolment->tpgateway_refno);
                    if(isset($statusData->status) && $statusData->status == 200) {
                        $enrolPaymentStatus = $statusData->data->enrolment->trainee->fees->collectionStatus;
                        $paymentTpgStatus = getPaymentStatusFormTPG($enrolPaymentStatus);
                        $studentEnrolment->payment_tpg_status = $paymentTpgStatus;
                        $studentEnrolment->tpg_payment_sync = 1;
                    } else {
                        $studentEnrolment->tpg_payment_sync = 2;
                        $isSuccess = false;
                    }
                    $studentEnrolment->update();
                } else {
                    $isSuccess = false;
                }

                if($isSuccess){
                    $data = ['status' => TRUE, 'msg' => 'Payment Status Synced From TPGateway'];
                }
                else{
                    $data = ['status' => FALSE, 'msg' => 'Payment Status Synced From TPGateway Failed. Please try again later.'];
                }
                return response()->json($data);
            }
        } else {
            $data = ['status' => FALSE, 'msg' => 'Please select atleast one enrolment'];
            return response()->json($data);
        }
    }

}
