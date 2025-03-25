<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Excel;
use DataTables;
use App\Models\Course;
use App\Models\Refreshers;
use App\Jobs\CreateEmailJob;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Services\UserService;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Exports\CourseRunExport;
use App\Models\StudentEnrolment;
use App\Services\ReportsService;
use App\Services\StudentService;
use App\Exports\AssessmentExport;
use App\Services\TPGatewayService;
use App\Services\CourseMainService;
use App\Exports\EachCourseRunExport;
use App\Exports\PaymentReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Exports\StudentDetailsExport;
use App\Exports\CourseRunWithIdExport;
use App\Exports\RefresherReportExport;
use App\Exports\CourseRunTraineeExport;
use App\Exports\CourseRunRefresherExport;
use App\Exports\CourseSignupsCountExport;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Services\AssessmentExamService;

class ReportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReportsService $reportService, CourseService $courseService, AssessmentExamService $assessmentService)
    {
        $this->middleware('auth');
        $this->reportService = $reportService;
        $this->courseService = $courseService;
        $this->assessmentService = $assessmentService;
    }

    /**
     * Show the course Registration report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function courseRegistration(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();
        return view('admin.reports.course-registration', compact('courseMainList','trainers'));
    }

    public function courseRegistrationListDatatable(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        $courseService = new CourseService;
        $courseRuns = $courseService->getAllCompletedCourseReport($request);
        return Datatables::of($courseRuns)
                ->addIndexColumn()
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
                ->addColumn('is_published', function($row) {
                    return isPublishedStatusBadge($row->is_published);
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
                            $btn .= '<li><a href="'.route('admin.course.edit',$row->id).'" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action', 'modeoftraining','is_published'])
                ->make(true);
    }

    public function courseRegistrationExportExcel(Request $request)
    {
        $courseService = new CourseService;
        $courseRuns = $courseService->getAllCompletedCourseReport($request);
        $courseRegistrations = $courseRuns->get();
        return Excel::download(new CourseRunExport($courseRegistrations), 'courseRegistration-list.xlsx');
    }

    /**
     * Show the course student Details report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function studentDetails(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.student-details', compact('courseMainList'));
    }

    public function studentDetailsListDatatable(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        $studentService = new StudentService;
        $records = $studentService->getAllStudentEnrolmentWithFilter($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->addColumn('status', function($row) {
                    return enrollStatusBadge($row->status);
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', StudentEnrolment::STATUS_ENROLLED);
                    }
                    if( (substr('not enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', StudentEnrolment::STATUS_NOT_ENROLLED);
                    }
                    if( (substr('enrolment cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', StudentEnrolment::STATUS_CANCELLED);
                    }
                    if( (substr('holding list', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', StudentEnrolment::STATUS_HOLD);
                    }
                })
                ->addColumn('courseName', function($row) {
                    return $row->courseNameWithTPG;
                })
                ->filterColumn('courseName', function($query, $keyword) {
                    $query->where('tpgateway_id', $keyword);
                })
                // ->filterColumn('students.name', function($query, $keyword) {
                //     $query->where('students.name', 'LIKE', '%'. strtolower($keyword) .'%');
                // })
                ->filterColumn('nric', function($query, $keyword) {
                    $query->where('nric', 'LIKE', '%'. strtolower($keyword) .'%');
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
                ->addColumn('learning_mode', function($row) {
                    $mode = $row->courseRun->courseMain->course_mode_training;
                    return $mode;
                })
                ->addColumn('payment_status', function($row) {
                    $payment_status = getPaymentStatus($row->payment_status);
                    return $payment_status;
                })
                ->addColumn('amount', function($row) {
                    $amount = '$'.$row->amount_paid.'/$'.$row->amount;
                    return $amount;
                })
                
                ->rawColumns(['action', 'status','payment_status','amount','learning_mode', 'attendedSessions'])
                ->make(true);
    }

    public function studentDetailsExportExcel(Request $request)
    {
        $studentService = new StudentService;
        $recordsQuery = $studentService->getAllStudentEnrolmentWithFilter($request);
        $records = $recordsQuery->get();
        return Excel::download(new StudentDetailsExport($records), 'studentDetails-list.xlsx');
    }

    /**
     * Show the course signups report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function courseSignups(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.course-signups', compact('courseMainList'));
    }

    public function courseSignupsListDatatable(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        $courseService = new CourseService;
        $courseRuns = $courseService->getSignupsForCoursesWithFilter($request);
        return Datatables::of($courseRuns)
                ->addIndexColumn()
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
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.course.courserunview',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.student',$row->id).'"><i class="fas fa-eye font-16"></i>Students List</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.get-attendance-assessment',$row->id).'" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>';
                            $btn .= '<li><a href="'.route('admin.course.edit',$row->id).'" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action', 'modeoftraining'])
                ->make(true);
    }

    public function courseSignupsExportExcel(Request $request)
    {
        $studentService = new StudentService;
        $recordsQuery = $studentService->getAllStudentEnrolmentPerMonth($request);
        $records = $recordsQuery->get();
        return Excel::download(new CourseSignupsCountExport($records), 'courseSignupsCount-list.xlsx');
    }

    /**
     * Show the course runs signups report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function courseRunsSignups(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.course-runs-signups', compact('courseMainList'));
    }

    public function courseRunsSignupsExportExcel(Request $request)
    {
        $courseService = new CourseService;
        $recordsQuery = $courseService->getAllStudentEnrolmentPerMonthCourseRuns($request);
        $records = $recordsQuery->get();
        /*echo '<pre>';
        foreach ($records as $record) {
            print_r($record);
        }
        exit;*/
        dd($records);
        return Excel::download(new CourseSignupsCountExport($records), 'courseSignupsCount-list.xlsx');
    }

    public function courseRunList(Request $request)
    {
        $coursemain_id = $request->get('id');
        $courseService = new CourseService;
        $courseRunsList = $courseService->getCourseRunByCourseMainId($coursemain_id);
        $data = [ 'status' => true, 'list' => $courseRunsList ];
        return response()->json($data);
    }

    // Export course Runs
    /**
     * Show the course Registration report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function courseRunExport(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        // get main course list
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.course-run-export', compact('courseMainList'));
    }

    public function courseRunsExportExcel(Request $request)
    {
        $courseService = new CourseService;
        $courseRuns = $courseService->getAllCompletedCourseReport($request);
        $courseRegistrations = $courseRuns->get();
        return Excel::download(new CourseRunWithIdExport($courseRegistrations), 'course-run-export-list.xlsx');
    }

    public function courseRunTraineeExportExcel($id, Request $request)
    {
        // get details of course run and students for course runs
        $courseService = new CourseService;
        $courseRun = $courseService->getCourseById($id);
        // get list of enrollment with student data
        $studentService = new StudentService;
        $students = $studentService->getStudentEnrollmentsByCourseRunId($id);
        // get list of refreshers
        return Excel::download(new CourseRunTraineeExport($courseRun, $students), 'courseRunTrainee-list.csv');
    }

    public function courseRunRefreshersExportExcel($id, Request $request)
    {
        // get details of course run and students for course runs
        $courseService = new CourseService;
        $courseRun = $courseService->getCourseById($id);
        // get list of refreshers
        $courseRefreshers = $courseRun->courseRefreshers;
        return Excel::download(new CourseRunRefresherExport($courseRun, $courseRefreshers), 'courseRunTrainee-list.xlsx');
    }

    public function paymentReport(Request $request)
    {
        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.payment-report', compact('courseMainList'));
    }
    
    public function PaymentReportlistDatatable(Request $request)
    {
        if (! Gate::allows('studentenrolment-list')) { return abort(403); }
        $studentService = new StudentService;
        $paymentReport = true;
        $records = $studentService->getAllStudentEnrolmentWithFilter($request, $paymentReport);
        // dd($records);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->addColumn('record_id', function($row){
                    return $row->id;
                })
                ->addColumn('status', function($row) {
                    return enrollStatusBadge($row->status);
                })
                ->filterColumn('student_enrolments.status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED);
                    }
                    if( (substr('not enrolled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('student_enrolments.status', StudentEnrolment::STATUS_NOT_ENROLLED);
                    }
                    if( (substr('enrolment cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('student_enrolments.status', StudentEnrolment::STATUS_CANCELLED);
                    }
                    if( (substr('holding list', 0, $len) === strtolower($keyword)) ) {
                        $query->where('student_enrolments.status', StudentEnrolment::STATUS_HOLD);
                    }
                })
                ->addColumn('payment_status', function($row) {
                    return paymentStatusBadge($row->payment_status);
                })
                ->filterColumn('payment_status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', StudentEnrolment::PAYMENT_STATUS_PENDING);
                    }
                    if( (substr('partial', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', StudentEnrolment::PAYMENT_STATUS_PARTIAL);
                    }
                    if( (substr('full', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', StudentEnrolment::PAYMENT_STATUS_FULL);
                    }
                    if( (substr('refunded', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', StudentEnrolment::PAYMENT_STATUS_REFUND);
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
                ->addColumn('payment_remark', function($row){
                    return $row->paymentRemarks ?? "";
                })  
                ->addColumn('full_payment_remark', function($row) {
                    return $row->fullPaymentRemarks ?? "";
                })
                ->addColumn('remaining_amount', function($row) {
                    // dd($row);
                    return "$".$row->remainingAmount;
                })
                ->addColumn('email', function($row) {
                    // dd($row);
                    return $row->email;
                })
                ->addColumn('payment_mode', function($row){
                    return $row->payment_mode ? getModeOfPayment($row->payment_mode) : '-';
                })
                ->editColumn('name', function($row){
                    return '<a href="'.route('admin.studentenrolment.view', $row->student_enroll_id).'" target="_blank">'. $row->name .'</a>';
                })
                ->editColumn('courseName', function($row){
                    return '<a href="'.route('admin.course.courserunview', $row->course_main_id).'" target="_blank"> '. $row->courseNameWithTPG .'</a>';
                })
                ->addColumn('paymenttpg', function($row){
                    return $row->student_enroll_id;
                })
                ->editColumn('payment_tpg_status', function($row) {
                    return tpgPaymentStatusBadge($row->payment_tpg_status);
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.payment.add').'?studentenrollment='.$row->id.'"><i class="fas fa-dollar-sign font-16"></i>Add Payment</a></li>';
                            $btn .= '<li><a href="'.route('admin.reports.paymentreport.sendreminder', $row->id).'"><i class="mdi mdi-email-check-outline font-16"></i>Email Reminder</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'payment_status', 'remaining_amount', 'record_id', 'payment_remark', 'full_payment_remark', 'name','courseName','paymenttpg', 'payment_tpg_status'])
                ->make(true);
    }

    public function paymentReportExportExcel(Request $request)
    {
        $studentService = new StudentService;
        $records = $studentService->getAllStudentEnrolmentWithFilter($request);
        $paymentReport = $records->get();
        return Excel::download(new PaymentReportExport($paymentReport), 'paymentRepoert-list.xlsx');
    }
    
    public function updatePaymentReport(Request $request)
    {
        $id = $request->get('record_id');
        $studentService = new StudentService;
        $studentService->updateOfStudentPaymentRecord($id, $request);
    }

    public function sendReminderEmail($id){
        $studentService     = new StudentService;
        $commonService      = new CommonService;
        $studentDetails     = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        $sessionString      = $commonService->makeSessionString($studentDetails->courseRun->session)."<br>";
        $sessionDateTime    = $commonService->makeSessionDateTime($studentDetails->courseRun->session);
        $sessionTime        = $commonService->makeSessionDateTime($studentDetails->courseRun->session, false, true);
        $courseFees         = $studentDetails->courseRun->courseMain->course_full_fees;
        $grantAmount        = (float)$studentDetails->grantEstimate;
        $totalAmount        = (float)$studentDetails->amount;
        $paidAmount         = (float)$studentDetails->amount_paid;
        $remainingAmount    = $totalAmount-$paidAmount;
        $registraionDate    = $studentDetails->created_at->format('d M Y');
        $dueDate            = $studentDetails->due_date;
        
        $emailTemplate = EmailTemplate::where('slug', 'test-reminder-of-invoice')->first();
        $content = $emailTemplate['template_text'];
        $content = str_ireplace("{studentname}", $studentDetails->student->name, $content);
        $content = str_ireplace("{courseSession}", $sessionString, $content);
        $content = str_ireplace("{coursename}", $studentDetails->courseRun->courseMain->name, $content);
        $content = str_ireplace("{coursedate}", $sessionDateTime, $content);
        $content = str_ireplace("{coursetime}", $sessionTime, $content);
        $content = str_ireplace("{staffname}", Auth::user()->name, $content);
        $content = str_ireplace("{comapanyName}", $studentDetails->company_name ?? $studentDetails->student->name, $content);
        $content = str_ireplace("{registrationDate}", $registraionDate, $content);
        $content = str_ireplace("{remainingAmount}", $remainingAmount, $content);
        $content = str_ireplace("{totalFees}", $totalAmount , $content);
        $content = str_ireplace("{dueDate}", $dueDate, $content);

        return view('admin.reports.send-reminder', compact('studentDetails', 'sessionString', 'content', 'emailTemplate'));
    }

    public function sendReminder($id, Request $request){
        $studentService     = new StudentService;
        $validated = $request->validate([
            'student_name' => 'required',
            'content' => 'required',
            'comapany' => 'required',
        ]);
        $studentDetails  = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        $msg = $request->get('content');
        $certificateAttachment = null;
        CreateEmailJob::dispatch($studentDetails->student->email, $msg, "student", $certificateAttachment);
        setflashmsg('Reminder send successfull');
        return redirect()->route('admin.reports.paymentreport');
    }

    public function refresherDetails(){

        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainListForRuns();
        return view('admin.reports.refrehser-details', compact('courseMainList'));
    }

    public function refresherDetailsListDatatable(Request $request)
    {
        if (! Gate::allows('reports')) { return abort(403); }
        $studentService = new StudentService;
        $records = $studentService->getAllRefresher($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('name', function($row) {
                    return ($row->student->name);
                })
                ->filterColumn('name', function($query, $keyword) {
                    $query->whereHas('students', function($query) use ($keyword){
                         $query->where('students.name', 'LIKE', '%'. strtolower($keyword) .'%');
                    });
                })
                ->addColumn('nric', function($row) {
                    return convertNricToView($row->student->nric);
                })
                ->filterColumn('nric', function($query, $keyword) {
                    $query->whereHas('students', function($query) use ($keyword){
                        $query->where('students.nric', 'LIKE', '%'. strtolower($keyword) .'%');
                   });   
                })
                ->addColumn('email', function($row) {
                    return ($row->student->email);
                })
                ->filterColumn('email', function($query, $keyword) {
                    $query->whereHas('students', function($query) use ($keyword){
                        $query->where('students.email', 'LIKE', '%'. strtolower($keyword) .'%');
                   });   
                })
                ->addColumn('status', function($row) {
                    if($row->status == Refreshers::STATUS_ACCEPTED) {
                        return '<span class="badge badge-soft-success">Accepted</span>';
                    } else if($row->status == Refreshers::STATUS_CANCELLED){
                        return '<span class="badge badge-soft-danger">Cancelled</span>';
                    } else {
                        return '<span class="badge badge-soft-warning">Pending</span>';
                    }
                })
                ->addColumn('courseName', function($row) {
                    return $row->course->tpgateway_id . ' ('. $row->course->course_start_date.') - ' . $row->course->courseMain->name;
                })
                ->filterColumn('courseName', function($query, $keyword) {
                    $query->whereHas('course.courseMain', function($query) use ($keyword){
                        $query->where('course_mains.name', 'LIKE', '%'. strtolower($keyword) .'%');
                   });   
                })
                ->addColumn('maintrainer', function($row){
                    return $row->course->maintrainerUser->name;
                })
                
                ->rawColumns(['courseName','name', 'nric', 'email', 'status','maintrainer'])
                ->make(true);
    }

    public function refresherExportExcel(Request $request)
    {
        $studentService = new StudentService;
        $records = $studentService->getAllRefresher($request);
        $refresherReport = $records->get();
        return Excel::download(new RefresherReportExport($refresherReport), 'refresherRepoert-list.xlsx');
    }

    public function submitPaymentTPG(Request $request){

        $enrollIds = $request->ids;
        if( empty($enrollIds) ) {
            setflashmsg(trans('msg.selectStudent'), 0);
            return redirect()->back();
        }
        $result = $this->courseService->getCourseByIdAndStudentEnrolmentByIdsOnReport($enrollIds);

        $data = $this->reportService->reportSubmitPaymentTpg($result);
        
        return response()->json($data);
    }

    public function assessmentExportExcel(Request $request){

        $assessmentReport = $this->assessmentService->assessmentReport();
        return Excel::download(new AssessmentExport($assessmentReport), 'assessmentReport-list.xlsx');
    }

    public function exportCourseRun(Request $request) {
        $courseRunId = $request->id;
        $courseRunTrainee = StudentEnrolment::with(['student', 'courseRun'])
            ->whereHas('courseRun', function($query) use ($courseRunId) {
                    $query->where('courses.id', $courseRunId);
        })->where('status', '!=', StudentEnrolment::STATUS_CANCELLED);
        $records = $courseRunTrainee->get();
        // dd($records[0]);
        return Excel::download(new EachCourseRunExport($records), 'ActiveCampaign.csv');
    }

}
