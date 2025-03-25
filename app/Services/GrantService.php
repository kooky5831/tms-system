<?php

namespace App\Services;

use App\Models\Grant;
use App\Models\StudentEnrolment;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Carbon;

class GrantService
{
    protected $grant_model;

    public function __construct()
    {
        $this->grant_model = new Grant;
    }

    public function getAllGrants()
    {
        return $this->grant_model->select();
    }

    public function getAllGrantsWithStudentEnrolment($request)
    {
        $s = $this->grant_model->select('grants.*','students.name','students.nric','student_enrolments.email', 'courses.tpgateway_id', 'courses.course_start_date', 'courses.course_end_date', 'course_mains.name as coursemainname', 'student_enrolments.status as enr_status', 'student_enrolments.tpgateway_refno as enr_ref_no')
        ->join('student_enrolments', 'student_enrolments.id', '=', 'grants.student_enrolment_id')
        ->join('courses', 'courses.id', '=', 'student_enrolments.course_id')
        ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id')
        ->join('students', 'students.id', '=', 'student_enrolments.student_id');

        $grantStatus = $request->get('grant_status');
        if( is_array($grantStatus) ) {
            $s->whereIn('grant_status', $grantStatus);
        } else if( $grantStatus > 0 ) {
            $s->where('grant_status', $grantStatus);
        }

        $startDate = $request->get('from');
        $endDate = $request->get('to');

        if( $startDate ) {
            $s->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $s->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $enrollStatus = $request->get('status');
        if( is_numeric($enrollStatus) ) {
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

        return $s;
    }

    public function getGrantById($id)
    {
        return $this->grant_model->with(['studentEnrolment'])->find($id);
    }

    public function getGrantByIdWithRelationData($id)
    {
        return $this->grant_model->where('id', $id)->with(['studentEnrolment'])->first();
    }

    public function getGrantByStudentEnrolmentID($id)
    {
        return $this->grant_model->where('student_enrolment_id', $id)->get();
    }

    public function getGrantByStudentEnrolmentIds($ids)
    {
        return $this->grant_model->whereIn('student_enrolment_id', $ids)->with(['studentEnrolment'])->get();
    }

    public function fetchGrantStatusFromTPG($id){

        $tpgatewayReq = new TPGatewayService;
        $record = Grant::find($id);
        if( $record ) {
            if( isset($record->grant_refno) && !empty($record->grant_refno) ) {
                $grantRes = $tpgatewayReq->checkGrantStatus($record->grant_refno);
                if( isset($grantRes->status) && $grantRes->status == 200 ) {
                    $record->grant_refno = $grantRes->data->referenceNumber;
                    $record->grant_status = $grantRes->data->status;
                    $record->scheme_code  = $grantRes->data->fundingScheme->code;
                    $record->scheme_description  = $grantRes->data->fundingScheme->description;
                    $record->component_code  = $grantRes->data->fundingComponent->code;
                    $record->component_description  = $grantRes->data->fundingComponent->description;
                    $record->amount_estimated  = round($grantRes->data->grantAmount->estimated,2);
                    $record->amount_paid  = round($grantRes->data->grantAmount->paid,2);
                    $record->amount_recovery  = round($grantRes->data->grantAmount->recovery,2);
                    $record->disbursement_date  = isset($grantRes->data->disbursementDate) ? $grantRes->data->disbursementDate : Carbon::parse($grantRes->meta->updatedOn)->format('Y-m-d');
                    $record->last_sync = date('Y-m-d');
                    $record->TPG_response = 1;

                    $record->save();

                    return ['status' => TRUE, 'msg' => 'Grant details fetched Successfully'];
                }
                else{
                    return ['status' => FALSE, 'msg' => 'Error during fetch grant details. Please try again later.'];
                }
            }
        }
        return ['status' => FALSE, 'msg' => 'Grant data not found'];
        
    }
}
