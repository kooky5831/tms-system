<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use Webfox\Xero\OauthCredentialManager;
use App\Services\PaymentService;
use App\Services\StudentService;
use App\Services\XeroService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DataTables;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PaymentService $paymentService, XeroService $xeroService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
        $this->xeroService = $xeroService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('payment-list')) { return abort(403); }
        return view('admin.payment.list');
    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('payment-list')) { return abort(403); }
        $admins = $this->paymentService->getAllPaymentWithStudentEnrolment($request);
        return Datatables::of($admins)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    return $row->studentEnrolment->student->name;
                })
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->studentEnrolment->student->nric);
                })
                ->editColumn('email', function($row) {
                    return $row->studentEnrolment['email'];
                })
                ->addColumn('xero_invoice_number', function($row){
                    if($row->xero_invoice_number){
                        return $row->xero_invoice_number;
                    } else {
                        return "-";
                    }
                })
                ->editColumn('status', function($row) {
                    if( $row->status == 1 ) { return '<span class="badge badge-soft-danger">Cancelled</span>'; }
                    else { return '<span class="badge badge-soft-success">Paid</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('paid', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                    if( (substr('cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 1);
                    }
                })
                ->editColumn('payment_mode', function($row) {
                    return getModeOfPayment($row->payment_mode);
                })
                ->filterColumn('payment_mode', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('cheque', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 1);
                    }
                    if( (substr('others (e.g vendors@gov)', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 2);
                    }
                    if( (substr('ibanking', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 3);
                    }
                    if( (substr('cash', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 4);
                    }
                    if( (substr('paypal', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 5);
                    }
                    if( (substr('credit card', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 6);
                    }
                    if( (substr('debit card', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_mode', 7);
                    }
                })
                ->filterColumn('courseName', function($query, $keyword) {
                    $query->where('tpgateway_id', 'LIKE', '%'.strtolower($keyword).'%')
                        ->orWhere('course_mains.name', 'LIKE', '%'.strtolower($keyword).'%');
                })
                ->addColumn('courseName', function($row) {
                    return $row->courseNameWithTPG;
                })
               /* ->editColumn('payment_status', function($row) {
                    if( $row->payment_status == 1 ) { return '<span class="badge badge-soft-success">'.getModeOfPaymentStatus($row->payment_status).'</span>'; }
                    else { return '<span class="badge badge-soft-danger">'.getModeOfPaymentStatus($row->payment_status).'</span>'; }
                })
                ->filterColumn('payment_status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('Paid', 1, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 'Paid');
                    }
                    if( (substr('Pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('payment_status', 'Pending');
                    }
                })*/
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                    if( Gate::allows('payment-view') ) {
                        $btn .= '<li><a href="'.route('admin.payment.view', $row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                    }
                    $btn .= '<li><a href="'.route('admin.payment.edit',$row->id).'"><i class="fas fa-edit font-16"></i>Edit</a></li>';
                    if( $row->status != 1 ) {
                        $btn .= '<li><a class="cancelpayment" href="javascript:void(0)" payment_id="'.$row->id.'" ><i class="far fa-trash-alt font-16"></i>Cancel Payment</a></li>';
                    }
                    $btn .= '</ul>
                        </div>';
                    return $btn;
                })
                /*->filter(function($query) use ($request) {
                    $thiscourseRunId = $request->get('studentEnrolment');
                    if( $thiscourseRunId > 0 ) {
                        $query->where('student_enrolments_id', $thiscourseRunId);
                    }
                })*/
                ->rawColumns(['action', 'status', 'xero_invoice_number'])
                ->make(true);
    }

    // public function paymentAdd(PaymentStoreRequest $request, OauthCredentialManager $xeroCredentials)
    public function paymentAdd(PaymentStoreRequest $request, OauthCredentialManager $xeroCredentials)
    {
        if (! Gate::allows('payment-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            // $trainer = $this->paymentService->registerPayment($request, $xeroCredentials);
            $trainer = $this->paymentService->registerPayment($request, $xeroCredentials);
            if( $trainer ) {
                setflashmsg(trans('msg.paymentCreated'), 1);
                return redirect()->route('admin.payment.list');
            }
        }

        /*$courseService = new CourseService;
        $courseList = $courseService->getAllCourseListWithRelation(['courseMain']);
        return view('admin.payment.add', compact('courseList'));*/

        /*$xeroSer = new XeroService($xeroCredentials);
        $connection = $xeroSer->checkConnection();
        $xero = [
            'connection' => $connection,
            'bankaccounts' => []
        ];
        if( $connection['status'] && !$connection['data']['error'] ) {
            // get bank accounts lists
            $xero['bankaccounts'] = $xeroSer->getBankAccounts();
        }*/
        $enrollmentData = NULL;
        if( $request->has('studentenrollment') && $request->get('studentenrollment') ) {
            $id = $request->get('studentenrollment');
            $studentService = new StudentService;
            $enrollmentData = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        }
        return view('admin.payment.add', compact('enrollmentData'));
    }

    public function paymentEdit($id, PaymentStoreRequest $request)
    {
        if (! Gate::allows('payment-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $allCourses = $this->paymentService->updatePayment($id, $request);
            if( $allCourses ) {
                setflashmsg(trans('msg.paymentUpdated'), 1);
                return redirect()->route('admin.payment.list');
            }
        }

        $data = $this->paymentService->getPaymentById($id);
        // $studentService = new StudentService;
        // $studentEnrolmentList = $studentService->getAllStudentEnrolment()->get();
        // return view('admin.payment.edit', compact('data','studentEnrolmentList'));
        return view('admin.payment.edit', compact('data'));
    }

    public function paymentView($id)
    {
        if (! Gate::allows('payment-view')) { return abort(403); }
        $data = $this->paymentService->getPaymentByIdWithRelationData($id);
        return view('admin.payment.view', compact('data'));
    }

    public function paymentCancel(Request $request , OauthCredentialManager $xeroCredentials)
    {
        $payment_id = $request->get('id');
        $data = $this->paymentService->cancelPaymentbyID($payment_id, $xeroCredentials);
        return response()->json($data);
    }

    // public function payPaymentXero(){
    //     $invoiceId = "13417422-f31d-4bf4-8e07-413578d04348";
    //     $bankaccount = "200";
    //     $amount = "50";
    //     $data = $this->xeroService->payFeesOnXero($invoiceId, $bankaccount, $amount);
    //     // dd($data);
    // }
}
