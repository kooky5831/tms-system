<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use App\Models\Grant;
use App\Services\TPGatewayService;
use App\Services\StudentService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\StudentEnrolment;
use Illuminate\Support\Carbon;
use App\Models\GrantLog;
use Log;
use Auth;

class CheckGrantStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     /**
     * @var TPGateway Service
    */
    private $tpgatewayReq;

    private $studentService;

    public $timeout = 0;

    public $tries = 1;


    public function __construct()
    {
        $this->tpgatewayReq = new TPGatewayService;
        $this->studentService = new StudentService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("Called Grant Status Check Job Start");

        $lastThreeDays = Carbon::now()->subDays(3)->format('Y-m-d');
        $allGrants = Grant::where('grant_status','Grant Processing')->whereNotNull('grant_refno')->where('last_sync', $lastThreeDays)->groupBy('student_enrolment_id')->limit(50)->get();
        $notifyGrants = array();
        foreach($allGrants as $grant) {
            
            \Log::info("Student Enrolment Id: ".$grant->student_enrolment_id);
            $studentEnrolment = $this->studentService->getStudentEnrolmentByIdWithRealtionData($grant->student_enrolment_id);
            if(!empty($studentEnrolment)){

                if($studentEnrolment->status != StudentEnrolment::STATUS_CANCELLED){
                    $courseRun = $studentEnrolment->courseRun;

                    if(!empty($courseRun)){

                        \Log::info("Course Run Id: ".$courseRun->id);

                        $courseFee = $courseRun->courseMain->course_full_fees;

                        $gstFromYear = getCourseGSTRate($studentEnrolment);

                        $gstRate = $courseRun->courseMain->gst ?? $gstFromYear;
                        
                        $req_data_grant = $this->tpgatewayReq->createGrantRequest($courseRun, $studentEnrolment);
                        $grantRes = $this->tpgatewayReq->checkGrantCalculator($req_data_grant);
                        $paidAmount = $studentEnrolment->amount ?? 0;
                        $calculatedNetFee = 0;

                        \Log::info("TPG Response Status: ".$grantRes->status);

                        if( isset($grantRes->status) && $grantRes->status == 200 ) {
                            if( !empty($grantRes->data)) {

                                $grantResData = collect($grantRes->data);

                                $completeTotal = $grantResData->filter(function (object $grant) {
                                    return $grant->status == 'Completed';
                                });

                                $cancelTotal = $grantResData->filter(function (object $grant) {
                                    return $grant->status == 'Cancelled';
                                });


                                $completeGrantTotal = $completeTotal->sum('grantAmount.paid');
                                $cancelGrantTotal = $cancelTotal->sum('grantAmount.estimated');


                                $grantTotalCount = count($grantRes->data);
                                $grantTotalCancelCount = 0;
                                $grantTotalCompleteCount = 0;
                                $grantCount = 0;
                                foreach( $grantRes->data as $tpggrant ){

                                    if($tpggrant->status == 'Completed'){
                                        $disbursedDate = $tpggrant->disbursementDate;
                                        Grant::where('grant_refno', $tpggrant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                    }
                                    else{
                                        $grantResByRef = $this->tpgatewayReq->checkGrantStatus($tpggrant->referenceNumber);
                                        if( isset($grantResByRef->status) && $grantResByRef->status == 200 ) {
                                            $disbursedDate = isset($grantResByRef->data->disbursementDate) ? $grantResByRef->data->disbursementDate : Carbon::parse($grantResByRef->meta->updatedOn)->format('Y-m-d');
                                            Grant::where('grant_refno', $tpggrant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                        }
                                    }
                                    

                                    $grantCount++;
                                    $skip = false;
                                    
                                    $isExist = Grant::where('grant_refno', $tpggrant->referenceNumber)->first();
                                    
                                    $grantRecord = Grant::updateOrCreate(
                                        [
                                            'grant_refno'   => $tpggrant->referenceNumber,
                                        ],
                                        [
                                            'student_enrolment_id' => $grant->student_enrolment_id,
                                            'grant_refno' => $tpggrant->referenceNumber,
                                            'grant_status' => $tpggrant->status,
                                            'scheme_code' => $tpggrant->fundingScheme->code,
                                            'scheme_description' => $tpggrant->fundingScheme->description,
                                            'component_code' => $tpggrant->fundingComponent->code,
                                            'component_description' => $tpggrant->fundingComponent->description,
                                            'amount_estimated' => round($tpggrant->grantAmount->estimated,2),
                                            'amount_paid' => round($tpggrant->grantAmount->paid,2),
                                            'amount_recovery' => round($tpggrant->grantAmount->recovery,2),
                                            //'disbursement_date' => $tpggrant->disbursementDate ?? NULL,
                                            'last_sync' => date('Y-m-d'),
                                            'TPG_response' => 1,
                                            'created_by' => 25,
                                            'updated_by' => 25
                                        ]
                                    );

                                    if($tpggrant->status == 'Grant Processing'){
                                        if(empty($isExist)){
                                            $skip = false;
                                        }else{
                                            $skip = true;
                                        }
                                    }

                                    if($tpggrant->status == 'Completed' && $grantTotalCount > 1 && $cancelGrantTotal != 0){
                                        $skip = true;
                                    }

                                    if(!$skip){
                                        if($tpggrant->status == 'Cancelled'){
                                            $grantTotalCancelCount++;
                                            $gst = ($courseFee * $gstRate) / 100;
                                            $totalFee = round(($courseFee + $gst),2);
                                            $calculatedNetFee = round(($paidAmount + $cancelGrantTotal),2);

                                            if($grantTotalCount == $grantCount){
                                                if($paidAmount != $calculatedNetFee){
                                                    $notifyGrants[$grantRecord->id] = $grantRecord;
                                                }
                                            }
                                            elseif($grantCount == $grantTotalCancelCount){
                                                if($paidAmount != $calculatedNetFee){
                                                    $notifyGrants[$grantRecord->id] = $grantRecord;
                                                }
                                            }

                                        }
                                        else if($tpggrant->status == 'Completed'){
                                            
                                            $grantTotalCompleteCount++;
                                            $calculatedNetFee = round(($paidAmount + $completeGrantTotal),2);
                                            $gst = ($courseFee * $gstRate) / 100;
                                            $totalFee = round(($courseFee + $gst),2);

                                            if($grantTotalCount == $grantCount){
                                                if($totalFee != $calculatedNetFee) 
                                                {
                                                    $notifyGrants[$grantRecord->id] = $grantRecord;
                                                }
                                            }
                                            elseif($grantCount == $grantTotalCompleteCount){
                                                
                                                if($totalFee != $calculatedNetFee) 
                                                {
                                                    $notifyGrants[$grantRecord->id] = $grantRecord;
                                                }
                                            }
                                        }
                                        else if($tpggrant->status == 'Grant Processing'){
                                            $notifyGrants[$grantRecord->id] = $grantRecord;
                                        }
                                    }
                                }
                            }
                        }
                        else if(isset($grantRes->status) && $grantRes->status == 404 || $grantRes->status == 400 ){
                            Grant::where('student_enrolment_id', $grant->student_enrolment_id)->update(['TPG_response' => 0, 'last_sync' => date('Y-m-d')]);
                        }
                    }
                }

            }
            
        }
        
        foreach($notifyGrants as $notify){
            $createdDate = date("Y-m-d", strtotime($notify->created_at));
            $todayDate = Carbon::now()->format('Y-m-d');
            $event = ($createdDate == $todayDate) ? "Created" : "Updated";
            GrantLog::updateOrCreate(
                [
                    'grant_refno'   => $notify->grant_refno,
                ],
                [
                    'student_enrolment_id' => $notify->student_enrolment_id,
                    'grant_id' => $notify->id,
                    'grant_refno' => $notify->grant_refno,
                    'notes' => '',
                    'event' => $event,
                    'grant_notify'=> 0,
                    'created_by' => 1,
                    'updated_by' => 1
                ]
            );
        }

        \Log::info("Called Grant Status Check Job End");
    }
}


