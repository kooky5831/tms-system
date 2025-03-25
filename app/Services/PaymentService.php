<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\StudentEnrolment;
use Illuminate\Support\Str;
use App\Services\XeroService;
use Auth;

class PaymentService
{
    protected $payment_model;

    public function __construct()
    {
        $this->payment_model = new Payment;
    }

    public function getAllPayment()
    {
        return $this->payment_model->select();
    }

    public function getAllPaymentWithStudentEnrolment($request)
    {
        // return $this->payment_model->with(['studentEnrolment'])->latest();
        $s = $this->payment_model->select('payments.*','students.name','students.nric','student_enrolments.email', 'student_enrolments.xero_invoice_number','courses.tpgateway_id', 'courses.course_start_date', 'courses.course_end_date', 'course_mains.name as coursemainname')
        ->join('student_enrolments', 'student_enrolments.id', '=', 'payments.student_enrolments_id')
        ->join('courses', 'courses.id', '=', 'student_enrolments.course_id')
        ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
        ->join('students', 'students.id', '=', 'student_enrolments.student_id');
        

        //payment_remark
        $payment_remark = $request->get('payment_remark');
        if(!empty($payment_remark))
        {
            $s->where('payments.payment_remark', 'LIKE', '%' . $payment_remark . '%');
        }
        
        //due_date
        if($request->has('dueDatestartDate') && !empty($request->get('dueDatestartDate'))){
            $dueStartDate = $request->get('dueDatestartDate');
        }
        if($request->has('dueDateendDate') && !empty($request->get('dueDateendDate'))){
            $dueDateendDate = $request->get('dueDateendDate');
        }
        if(!empty($dueStartDate) && !empty($dueDateendDate)){
            $s->whereBetween('student_enrolments.due_date', [$dueStartDate, $dueDateendDate]);
        }
    
        //sponsored_by_company
        $sponsored_by_company = $request->get('sponsored_by_company');
        if(!empty($sponsored_by_company))
        {
            $s->where('student_enrolments.sponsored_by_company', 'LIKE', $sponsored_by_company);  
        }
        
        //payment_status
        $payment_status = $request->get('payment_status');
        if(!empty($payment_status))
        {
            $s->where('payment_status', $payment_status);
        }
        
        //company_name
        $company_name = $request->get('company_name');
        if(!empty($company_name))
        {
            $s->where('student_enrolments.company_name', 'LIKE', '%'.$company_name.'%');
        }
        
        //company_uen
        $company_uen = $request->get('company_uen');
        if(!empty($company_uen))
        {
            $s->where('student_enrolments.company_uen', 'LIKE', '%'.$company_uen.'%');
        }
        
        
        //remainingAmount
        $remainingAmount = $request->get('remaining_amount');
        if(isset($remainingAmount)){
            $s->whereRaw('(student_enrolments.amount - student_enrolments.amount_paid) <=?', [$remainingAmount])->get();
        }

        //status
        $enrollStatus = $request->get('status');
        if( is_array($enrollStatus) ) {
            if(in_array(StudentEnrolment::REFRESHER_STATUS, $enrollStatus)){
                $s->join('refreshers' , function($query){
                    $query->on('refreshers.student_id', '=', 'students.id');
                    $query->on('refreshers.course_id', '=', 'courses.id');
                    $query->where('refreshers.status', '=', Refreshers::STATUS_ACCEPTED);
                });
            } else {
                $s->whereIn('student_enrolments.status', $enrollStatus);
            }
        } elseif(!is_array($enrollStatus) && !is_null($enrollStatus)) {
            if( $enrollStatus == StudentEnrolment::STATUS_ENROLLED ) {
                $s->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED);
            } else if( $enrollStatus == StudentEnrolment::STATUS_CANCELLED ) {
                $s->where('student_enrolments.status', StudentEnrolment::STATUS_CANCELLED);
            } else if( $enrollStatus == StudentEnrolment::STATUS_HOLD ) {
                $s->where('student_enrolments.status', StudentEnrolment::STATUS_HOLD);
            } else if( $enrollStatus == StudentEnrolment::STATUS_NOT_ENROLLED ) {
                $s->where('student_enrolments.status', StudentEnrolment::STATUS_NOT_ENROLLED);
            }
        }
        
        //course type
        $courseType = $request->get('course_type');
        if( !empty($courseType) ) {
            $s->where('course_mains.course_type', '=', $courseType);
        }
        return $s;
    }

    public function getPaymentById($id)
    {
        return $this->payment_model->with(['studentEnrolment'])->find($id);
    }

    public function getPaymentByIdWithRelationData($id)
    {
        return $this->payment_model->where('id', $id)->with(['studentEnrolment'])->first();
    }

    public function getPaymentByStudentEnrolmentID($id)
    {
        return $this->payment_model->where('student_enrolments_id', $id)->get();
    }

    public function getPaymentByStudentEnrolmentIds($ids)
    {
        return $this->payment_model->whereIn('student_enrolments_id', $ids)->with(['studentEnrolment'])->get();
    }

    public function addPaymentData($request)
    {
        $record = $this->payment_model;
        $studentService = new StudentService;
        $studentService->updateStudentEnrolmentByEntryID($request);
        $studentEnrolmentData = $studentService->getStudentEnrolmentByEntryId($request->get('entry_id'));

        $student_enrolments_id = $studentEnrolmentData['id'];

        if($request->get('transaction_type') == "") {
            $transaction_type = $request->get('payment_mode');
        } else {
            $transaction_type = $request->get('transaction_type');
        }

        $record->entry_id                   = $request->get('entry_id');
        $record->student_enrolments_id      = $student_enrolments_id;
        $record->payment_mode               = $request->get('payment_mode');
        $record->creditcard_number          = $request->get('creditcard_number');
        $record->creditcard_type            = $request->get('creditcard_type');
        $record->ip_address                 = $request->get('ip_address');
        /*$record->payment_amount             = $request->get('payment_amount');*/
        $record->payment_date               = $request->get('payment_date');
        $record->payment_method             = $request->get('payment_method');
        /*$record->payment_status             = $request->get('payment_status');*/
        $record->transaction_id             = $request->get('transaction_id');
        $record->transaction_type           = $request->get('transaction_type');
        $record->created_by                 = 1;
        $record->updated_by                 = 1;
        $record->save();
        return $record;
    }

    public function updatePayment($id, $request)
    {
        $record = $this->getPaymentById($id);

        if( $record ) {
            $amount_paid = $request->get('fee_amount');
            $studentService = new StudentService;
            $studentService->updateAmountPaidChangeByStudentEnrolmentID($record->student_enrolments_id, $amount_paid, $record->fee_amount);
            // $record->student_enrolments_id  = $request->get('student_enrolments_id');
            $record->payment_mode           = $request->get('payment_mode');
            $record->payment_method         = $request->get('payment_mode');
            // $record->ip_address             = $request->get('ip_address');
            $record->fee_amount             = $amount_paid;
            $record->payment_date           = $request->get('payment_date');
            // $record->payment_method         = $request->get('payment_method');
            $record->payment_remark         = $request->get('payment_remark');

            $record->cheque_no              = $request->get('cheque_no');
            $record->account_number         = $request->get('account_number');
            $record->creditcard_number      = $request->get('creditcard_number');
            $record->creditcard_type        = $request->get('creditcard_type');

            // $record->transaction_id         = $request->get('transaction_id');
            // $record->transaction_type       = $request->get('transaction_type');
            /*$record->payment_status         = $request->get('payment_status');*/
            $record->updated_by             = Auth::Id();
            $record->save();
            return $record;
        }
        return false;
    }

    // public function registerPayment($request, $xeroCredentials)
    public function registerPayment($request, $xeroCredentials)
    {
        // dd($request->all());
        $record = $this->payment_model;
        $student_enrolments_id = $request->get('student_enrolments_id');
        $amount_paid = $request->get('fee_amount');
        // $bankaccountId = $request->get('bankaccount_id');
        $record->student_enrolments_id      = $student_enrolments_id;

        $studentService = new StudentService;
        // $studentService->updateAmountPaidByStudentEnrolmentID($student_enrolments_id, $amount_paid);
        $xero_amount                        = $request->get('xero_fees_amount');
        $xer_paid_amount                    = $xero_amount;
        $record->payment_mode               = $request->get('payment_mode');
        $record->payment_method             = $request->get('payment_mode');
        /*$record->payment_amount             = $request->get('payment_amount');*/
        $record->payment_date               = $request->get('payment_date');

        $record->cheque_no                  = $request->get('cheque_no');
        $record->account_number             = $request->get('account_number');
        $record->creditcard_number          = $request->get('creditcard_number');
        $record->creditcard_type            = $request->get('creditcard_type');

        $record->transaction_id             = Str::random(20);

        // $record->fee_amount                 = $request->get('fee_amount');
        $record->fee_amount                 = $xer_paid_amount;
        $record->payment_remark             = $request->get('payment_remark');
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();
        $record->save();
        
        
        $studentAmountPaid = $studentService->getStudentEnrolmentById($student_enrolments_id);
        $totalAmountPaid = $studentAmountPaid->amount_paid + $request->get('xero_fees_amount');
        
        \Log::info("StudentEnrol ID ===>>> " . $student_enrolments_id);
        \Log::info("Total Paid Amount ===>>> " . $totalAmountPaid);
        $studentAmountPaid->amount_paid = $totalAmountPaid;
        $studentAmountPaid->save();

        if($studentAmountPaid){
            \Log::info("Updated Amount paid field with amount ====>" . $totalAmountPaid);
        }
        /*
        // add payment to XERO
        try {
            // get student enrollment data
            $studentEnrol = $studentService->getStudentEnrolmentById($student_enrolments_id);
            if( !empty($studentEnrol->xero_invoice_id) ) {
                $xeroServiceReq = new XeroService($xeroCredentials);
                $resObj = $xeroServiceReq->createPaymentXero(
                    $studentEnrol->xero_invoice_id,
                    $amount_paid,
                    $bankaccountId);
                if( $resObj ) {
                    $record->xero_pay_id = $resObj->getPayments()[0]->getPaymentID();
                    $record->save();
                    // $xeroId = $resObj->getContacts()[0]->getContactId();
                }
            }
        } catch (Exception $e) {
            // log to error
        }*/

        // add payment to Xero new implementation
        if($request->has('sync_xero')){
            $studentService->updateXeroAmountPaidByStudentEnrolmentID($student_enrolments_id, $xer_paid_amount);
        } else {
            try{
                if(!empty($request->get('xero_invoice_number'))){
                    $xeroServiceReq = new XeroService($xeroCredentials);
                    $getUUID = $xeroServiceReq->getInvoiceFromXero($request->get('xero_invoice_number'));
                    $resObj = $xeroServiceReq->payFeesOnXero($getUUID, $xer_paid_amount, $student_enrolments_id, $record->payment_remark, $record->payment_mode, $record->payment_date);
                    // dd($resObj->getPayments()[0]->getInvoice());
                    if( $resObj ) {
                        $record->xero_fee_amount = $xero_amount;
                        $record->xero_pay_id = $resObj->getPayments()[0]->getPaymentID();
                        $record->save();
                        // $xeroId = $resObj->getContacts()[0]->getContactId();
                    }
                }
                \Log::info("update student enrolment on PaymentService start =====>>>>");
                $studentService->updateXeroAmountPaidByStudentEnrolmentID($student_enrolments_id, $xer_paid_amount);
                \Log::info("update student enrolment on PaymentService end =====>>>>");
            } catch (Exception $e) {
                // log to error
            }
        }
        

        return $record;
    }

    public function cancelPaymentbyID($id, $xeroCredentials)
    {
        $record = $this->getPaymentById($id);
        
        if( $record ) {
            if( $record->status != 0 ) {
                return ['status' => FALSE, 'msg' => 'Payment already cancelled'];
            }
            $record->status = 1;
            $record->save();
            // now change the amount for enrollment
            $studentEnrolment = StudentEnrolment::where('id', $record->student_enrolments_id)->first();

            $paidAmount = ($studentEnrolment->xero_paid_amount + $studentEnrolment->xero_due_amount) - $studentEnrolment->xero_amount;
            $oldPaidAmount = $studentEnrolment->xero_paid_amount;
            $studentEnrolment->xero_paid_amount = $paidAmount;
            $studentEnrolment->xero_due_amount  = $studentEnrolment->xero_due_amount + $oldPaidAmount;
            $studentEnrolment->payment_status  = calculatePaymentStatus($studentEnrolment->xero_paid_amount, $studentEnrolment->xero_due_amount);
            // $studentEnrolment->amount_paid = $studentEnrolment->amount_paid - $record->fee_amount;
            // $studentEnrolment->payment_status  = calculatePaymentStatus($studentEnrolment->amount_paid, $studentEnrolment->amount);

            $payId = $record->xero_pay_id;
            if($payId){
                $xeroServiceReq = new XeroService($xeroCredentials);
                $response = $xeroServiceReq->canclePaymentXero($payId);
            } else {
                $response =  false;
            }
            
            if($response){
                return ['status' => TRUE, 'msg' => 'Payment cancelled Successfully'];
            } else {
                return ['status' => FALSE, 'msg' => 'Payment data not found'];
            }
        }
        return ['status' => FALSE, 'msg' => 'Payment data not found'];
    }
}
