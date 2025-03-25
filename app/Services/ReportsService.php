<?php

namespace App\Services;

use App\Models\Payment;
use Auth;

class ReportsService
{

    public function getAllPayment()
    {
        return $this->payment_model->select();
    }

    public function getAllPaymentWithStudentEnrolment()
    {
        // return $this->payment_model->with(['studentEnrolment'])->latest();
        $s = $this->payment_model->select('payments.*','students.name','students.nric','student_enrolments.email')
        ->join('student_enrolments', 'student_enrolments.id', '=', 'payments.student_enrolments_id')
        ->join('students', 'students.id', '=', 'student_enrolments.student_id');
        return $s;
    }

    public function reportSubmitPaymentTpg($result){
        $isError = false;
        $studentService = new \App\Services\StudentService;
        $tpgatewayReq = new TPGatewayService;

        foreach ($result as $key => $record) {
            if( !empty($record->isPaymentError) && $record->isPaymentError == 0 ) {
               continue;
            }
            $stuEnrol = $studentService->getStudentEnrolmentById($record->id);
            $payment_status = getPaymentStatusForTPG($record->payment_status);
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

            if( $isError ) {
                return ['status' => FALSE, 'msg' => 'Payment Status Not Submited to TPGateway'];
            } else {
                return ['status' => TRUE, 'msg' => 'Payment Status Submited to TPGateway'];
            }
        }
    }
}
