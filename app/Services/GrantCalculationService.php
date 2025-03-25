<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentEnrolment;
use App\Models\Course;
use App\Models\Grant;
use App\Services\StudentService;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Services\CourseMainService;
use App\Services\XeroService;
use Carbon\Carbon;
use Webfox\Xero\OauthCredentialManager;
use Log;
use App\Models\ProgramType;

class GrantCalculationService
{
    const SUBSIDY_FIRST = 95; 
    const SUBSIDY_SECOND = 90;
    const SUBSIDY_THIRD = 70;
    const SUBSIDY_FUORTH = 50;
    
    const SINGAPOREAN           = "singaporean";
    const SINGAPOR_CITIZEN      = "singapore citizen";
    const SINGAPOR_PR           = "singapore permanent resident";
    const NON_SINGAPOR_PR       = "non-singapore citizen/pr (fin)";
    const NON_SINGAPOR_PR_ONE   = "non-singapore citizen/pr";
    const NON_SINGAPOR_RESIDENT = "non-singapore resident (foreign passport)";
    const LONG_TERM_VISITOR     = "long term visitor pass+ (ltvp+)";

    const COURSE_FEE = 1;
    const BASELINE_FUNDING = 2;

    const APPLICATION_FEES = 10;

    public function generateInvoice($id, $isCourseProgramType = false, $EntryId = null)
    {
        $itemOne = $itemTwo = $itemThree = $itemFour = [];
        //Get Student Data
        $studentService = new StudentService;
        $studentData = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        
        //Get course run data
        $courseService  = new CourseService;
        $getCourseRun   = $courseService->getCourseById($studentData->course_id);

        $paidAmount = 0;
        if (strtolower($studentData->payment_mode_company) == 'credit card' || strtolower($studentData->payment_mode_company) == 'credit card'){
            $paidAmount = $studentData->amount;
        }
        
        //Get Main course data
        $courseMainService  = new CourseMainService;
        $getCourseMain      = $courseMainService->getCourseMainById($getCourseRun->course_main_id);
        
        $checkInvoiceGenerate = StudentEnrolment::where(['entry_id' => $EntryId, 'is_invoice_generated' => 1])->get();

        if($isCourseProgramType) {
            Log::info('isCourseProgramType True');
            $programDiscount =  ProgramType::where('id', $studentData->program_type_id)->first(); // $studentData->program_type_id;
        }

        $netCourseFee = $studentData->amount;
        $mainCourseFees     = (float)$getCourseMain->course_full_fees;
        if( !empty($getCourseMain->enhanced_funding)  && !empty($getCourseMain->baseline_funding) && !empty($getCourseMain->gst)){
            $commonService = new CommonService;
            $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);

            if(!empty($getCourseRun->tpgateway_id)){
                $gateWayId = "- (" . $getCourseRun->tpgateway_id . ")";
            } else {
                $gateWayId = "";
            }
            
            if(isset($programDiscount) && $programDiscount->is_discount == 1) {
                Log::info('if E/B funding program discount is 1');
                $calculation = $this->grantCalculation($studentData, $getCourseMain, $programDiscount, $isCourseProgramType);

                $calculation['program_discount'] = $programDiscount->discount_percentage;                                        
                $programCourseFeeDiscount =  ($mainCourseFees * $programDiscount->discount_percentage) / 100;
                $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;

                $studentData->discountAmount = $programCourseFeeDiscount;
                $studentData->save();
            } else {
                Log::info('if E/B funding program discount is 0');
                $calculation = $this->grantCalculation($studentData, $getCourseMain, 0, $isCourseProgramType);
                if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount) && !$isCourseProgramType){
                    Log::info('if E/B funding course discount is 1 with singular course');
                    $calculation['course_discount'] = $getCourseMain->discount_amount;
                    $studentData->discountAmount = $getCourseMain->discount_amount;
                    $studentData->save();
                }   
            }

            $itemOne[]      = $CourseRunSessionString . " : " . $getCourseMain->reference_number . " " . $getCourseMain->name . $gateWayId;
            $itemTwo[]      = $calculation['baseline_funding'];
            $itemThree[]    = $calculation['enhanced_funding'];
            $itemFour[]     = $studentData->student->name;
            $applied_gst    = $calculation['gst_applied_on'];
            $updateItem     = null;
            $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
            $updatedXeroItemList = [];
            foreach($xeroItemList['itemlist'] as $key => $value){
                if (strtolower($value['description']) == "ssg training grant (enhanced subsidy)" && $value['line_amount'] != 0) {
                    Log::info("1 condition 1 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) == "ssg training grant (baseline funding)" && $value['line_amount'] != 0) {
                    Log::info("1 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) != "ssg training grant (enhanced subsidy)" && strtolower($value['description']) != "ssg training grant (baseline funding)") {
                    Log::info("1 condition 3 true");
                    $updatedXeroItemList[] = $value;
                }
            }
            
            if (strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)") {
                Log::info("1 condition 4 true");
                $updatedXeroItemList[] = $this->addXeroItemListCmpyNo($netCourseFee, $applied_gst);
            }

            // check if Prgram type application added to first invoice
            if( $isCourseProgramType ){
                if($programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                    Log::info('if E/B funding program application is 1');
                    if($checkInvoiceGenerate->count() >= 1 ){ Log::info('if E/B funding skip program application on other invoice'); }
                    else {
                        Log::info('if E/B funding program application on first invoice');

                        $calculation['application_fees_gst'] = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
                        $calculation['application_fees_amount'] = $programDiscount->application_fee;

                        if($programDiscount->is_absorb_gst == 0){
                            $studentData->is_invoice_generated = 1;
                            $studentData->save();
                        }
    
                        $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
                    }
                }
            }

            if($studentData->application_fee){
                if(strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)"){
                    if($studentData->payment_type == 'Pay Now') {
                        $calculation['application_fees_gst'] = 0;
                        $calculation['application_fees_amount'] = 0;
                    } else if ($studentData->payment_type == 'Pay Later') {
                        $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                        $calculation['application_fees_amount'] = $studentData->application_fee;
                    }
                } else {
                    $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                    $calculation['application_fees_amount'] = $studentData->application_fee;
                }
                $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
            }

            if($isCourseProgramType) {
                if($programDiscount->is_absorb_gst == 1){
                    Log::info('if E/B funding course absorb on bundle course');
                    if($programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                        Log::info('if E/B funding course absorb program application is 1');
                        if($checkInvoiceGenerate->count() >= 1 ){
                            if(isset($programCourseFeeDiscount)){
                                Log::info('if E/B funding course absorb calculate the other invoice with absorb gst with discount (bundle course)');
                                //calculate the other invoice with absorb gst with discount (bundle course)
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            } else {
                                Log::info('if E/B funding course absorb calculate the other invoice with absorb gst without discount (bundle course)');
                                //calculate the other invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount    = ($mainCourseFees * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                        else {
                            if(isset($programCourseFeeDiscount)){
                                Log::info('if E/B funding course absorb calculate the application on first invoice with absorb gst with discount (bundle course)');
                                //calculate the application on first invoice with absorb gst with discount (bundle course)
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
        
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();
                            } else {
                                Log::info('if E/B funding course absorb calculate the application on first invoice with absorb gst without discount (bundle course)');
                                //calculate the application on first invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount         = ($mainCourseFees * $getCourseMain->gst) / 100;        
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
        
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                    } else {
                        Log::info('if E/B funding course absorb program application is 0');
                        if(isset($programCourseFeeDiscount)){
                            Log::info('if E/B funding course absorb calculate the other invoice with absorb gst with discount with no application fees (bundle course)');
                            //calculate the other invoice with absorb gst with discount with no application fees (bundle course)
                            $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                            $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                        } else {
                            Log::info('if E/B funding course absorb calculate the other invoice with absorb gst without discount with no application fees (bundle course)');
                            //calculate the other invoice with absorb gst without discount with no application fees (bundle course)
                            $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                            $calculation['absorb_gst'] = $absorbUnitPrice;
                        }                     
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }
                }
            } else {
                if($getCourseMain->is_absorb_gst == 1) {
                    Log::info('if E/B funding course absorb on singular course');
                    if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount)){
                        Log::info('if E/B funding course absorb calculate the other invoice with absorb gst with discount (singular course)');
                        //calculate the other invoice with absorb gst with discount (singular course)
                        $afterDiscountCourseFee   = $mainCourseFees - $getCourseMain->discount_amount;
                        $absorbUnitPrice = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    } else {
                        Log::info('if E/B funding course absorb calculate the other invoice with absorb gst without discount (singular course)');
                        //calculate the other invoice with absorb gst without discount (singular course)
                        $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }                    
                }
            }

            Log::info("======== generateInvoice function data =========");
            Log::info(print_r($updatedXeroItemList, true));
            Log::info("======== generateInvoice function data =========");
            $xeroItemList['itemlist'] = $updatedXeroItemList;            
            $oldInvoice = Invoice::where('student_enroll_id', $studentData->id)->first();

            if(empty($oldInvoice)){
                $updateInvoice =  Invoice::Create([
                    "student_enroll_id" => $studentData->id,
                    'invoice_number' => $studentData->xero_invoice_number,
                    "student_id" => $studentData->id,
                    "courserun_id" => $studentData->course_id,
                    "is_comapany" => strtolower($studentData->sponsored_by_company) == "yes" ? 1 : 0,
                    "invoice_name" => implode(',', $itemOne),
                    "invoice_type" => "Pending",
                    "invoice_status" =>  $calculation['total_fess'] == 0 ? "paid" : "pending",
                    "amount_due" => $this->amountDue($calculation, $getCourseMain),
                    "amount_paid" => 0,
                    "sub_total" => $this->subTotal($calculation, $getCourseMain),
                    "tax" => ($getCourseMain->application_fees != 0) ? ($calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount']) + $calculation['application_fees_gst'] : $calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount'],
                    "invoice_gst" => $getCourseMain->gst ?? getCourseGSTRate($studentData),
                    "total_discount" => 0,
                    "line_items" => json_encode($xeroItemList['itemlist']),
                    "invoice_date" => Carbon::now()->format('Y-m-d'),
                    "due_date" => Carbon::now()->format('Y-m-d'),
                ]);
                // $pdfData = $this->commonService->getPdfdata($updateInvoice, $companydetails);
                Log::info("Return New Student Enroll iD for invoice syncing xero");
                $invoiceData = $updateInvoice->student_enroll_id; 
    
            }else{
                Log::info("Return OLD Student Enroll iD for invoice syncing xero");
                // $pdfData = $this->commonService->getPdfdata($oldInvoice, $companydetails);
                $invoiceData = $oldInvoice->student_enroll_id; 
            }
            Log::info("Return Student Enroll iD for invoice syncing xero");
            return $invoiceData;
            // return view('admin.pdf.generate-invoice', compact('pdfData', 'id'));
        }else{
            $commonService = new CommonService;
            $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);

            if(!empty($getCourseRun->tpgateway_id)){
                $gateWayId = "- (" . $getCourseRun->tpgateway_id . ")";
            } else {
                $gateWayId = "";
            }

            if(isset($programDiscount) && $programDiscount->is_discount == 1) {
                Log::info('else program discount is 1');
                $calculation = $this->grantCalculation($studentData, $getCourseMain, $programDiscount, $isCourseProgramType);
                $calculation['program_discount'] = $programDiscount->discount_percentage;
                                            
                $programCourseFeeDiscount =  ($mainCourseFees * $programDiscount->discount_percentage) / 100;

                $studentData->discountAmount = $programCourseFeeDiscount;
                $studentData->save();
            } else {
                Log::info('else program discount is 0');
                $calculation = $this->grantCalculation($studentData, $getCourseMain, 0, $isCourseProgramType);
                if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount)){
                    Log::info('else course discount is 1 with singular course');
                    $calculation['course_discount'] = $getCourseMain->discount_amount;
                    $studentData->discountAmount = $getCourseMain->discount_amount;
                    $studentData->save();
                }
            }

            $itemOne[]      = $CourseRunSessionString . " : " . $getCourseMain->reference_number . " " . $getCourseMain->name . $gateWayId;
            $itemTwo[]      = $calculation['baseline_funding'];
            $itemThree[]    = $calculation['enhanced_funding'];
            $itemFour[]     = $studentData->student->name;
            $applied_gst    = $calculation['gst_applied_on'];
            $updateItem     = null;
            $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
            $updatedXeroItemList = [];
            foreach($xeroItemList['itemlist'] as $key => $value){
                if (strtolower($value['description']) == "ssg training grant (enhanced subsidy)" && $value['line_amount'] != 0) {
                    Log::info("2 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) == "ssg training grant (baseline funding)" && $value['line_amount'] != 0) {
                    Log::info("2 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) != "ssg training grant (enhanced subsidy)" && strtolower($value['description']) != "ssg training grant (baseline funding)") {
                    Log::info("2 condition 3 true");
                    $updatedXeroItemList[] = $value;
                }
            }
            if (strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)") {
                Log::info("2 condition 4 true");
                $updatedXeroItemList[] = $this->addXeroItemListCmpyNo($netCourseFee, $applied_gst);
            }
            // check if Prgram type application added to first invoice
            if( $isCourseProgramType ){
                Log::info('else program application is 1');
                if($programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                    if($checkInvoiceGenerate->count() >= 1 ){ Log::info('else skip program application on other invoice'); }
                    else {
                        Log::info('else program application on first invoice');
                        $calculation['application_fees_gst'] = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
                        $calculation['application_fees_amount'] = $programDiscount->application_fee;

                        if($programDiscount->is_absorb_gst == 0){
                            $studentData->is_invoice_generated = 1;
                            $studentData->save();
                        }
    
                        $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
                    }
                }
            }

            if($studentData->application_fee){
                if(strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)"){
                    if($studentData->payment_type == 'Pay Now') {
                        $calculation['application_fees_gst'] = 0;
                        $calculation['application_fees_amount'] = 0;
                    } else if ($studentData->payment_type == 'Pay Later') {
                        $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                        $calculation['application_fees_amount'] = $studentData->application_fee;
                    }
                } else {
                    $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                    $calculation['application_fees_amount'] = $studentData->application_fee;
                }
                $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
            }


            if($isCourseProgramType) {
                if($programDiscount->is_absorb_gst == 1) {
                    Log::info('else course absorb on bundle course - program type gst absorb true');
                    if($programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                        if($checkInvoiceGenerate->count() >= 1 ){
                            Log::info('else course absorb program application is 1');
                            if(isset($programCourseFeeDiscount)){
                                Log::info('else course absorb calculate the other invoice with absorb gst with discount (bundle course)');
                                //calculate the other invoice with absorb gst with discount (bundle course)
                                $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            } else {
                                Log::info('else course absorb calculate the other invoice with absorb gst without discount (bundle course)');
                                //calculate the other invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount    = ($mainCourseFees * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                        else {
                            if(isset($programCourseFeeDiscount)){
                                Log::info('else course absorb calculate the application on first invoice with absorb gst with discount (bundle course)');
                                //calculate the application on first invoice with absorb gst with discount (bundle course)
                                $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
        
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();

                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                            } else {
                                Log::info('else course absorb calculate the application on first invoice with absorb gst without discount (bundle course)');
                                //calculate the application on first invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount         = ($mainCourseFees * $getCourseMain->gst) / 100;        
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();

                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                    } else {
                        Log::info('else course absorb program application is 0');
                        if(isset($programCourseFeeDiscount)){
                            Log::info('else course absorb calculate the other invoice with absorb gst with discount with no application fees (bundle course)');
                            //calculate the other invoice with absorb gst with discount with no application fees (bundle course)
                            $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                            $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                            $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                        } else {
                            Log::info('else course absorb calculate the other invoice with absorb gst without discount with no application fees (bundle course)');
                            //calculate the other invoice with absorb gst without discount with no application fees (bundle course)
                            $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                            $calculation['absorb_gst'] = $absorbUnitPrice;
                        }                     
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }
                }
            } else {
                if($getCourseMain->is_absorb_gst == 1) {
                    Log::info('else course absorb on singular course');
                    if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount)){
                        Log::info('else course absorb calculate the other invoice with absorb gst with discount (singular course)');
                        //calculate the other invoice with absorb gst with discount (singular course)
                        $afterDiscountCourseFee   = $mainCourseFees - $getCourseMain->discount_amount;
                        $absorbUnitPrice = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    } else {
                        Log::info('else course absorb calculate the other invoice with absorb gst without discount (singular course)');
                        //calculate the other invoice with absorb gst without discount (singular course)
                        $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }                    
                }
            }

            Log::info("======== generateInvoice else function data =========");
            Log::info(print_r($updatedXeroItemList, true));
            Log::info("======== generateInvoice else function data =========");
            $xeroItemList['itemlist'] = $updatedXeroItemList;
    
            $oldInvoice = Invoice::where('student_enroll_id', $studentData->id)->first();
            if(empty($oldInvoice)){
                $updateInvoice =  Invoice::Create([
                    "student_enroll_id" => $studentData->id,
                    'invoice_number' => $studentData->xero_invoice_number,
                    "student_id" => $studentData->id,
                    "courserun_id" => $studentData->course_id,
                    "is_comapany" => strtolower($studentData->sponsored_by_company) == "yes" ? 1 : 0,
                    "invoice_name" => implode(',', $itemOne),
                    "invoice_type" => "Pending",
                    "invoice_status" =>  $calculation['total_fess'] == 0 ? "paid" : "pending",
                    "amount_due" => $this->amountDue($calculation, $getCourseMain),
                    "amount_paid" => 0,
                    "sub_total" => $this->subTotal($calculation, $getCourseMain),
                    "tax" => ($getCourseMain->application_fees != 0) ? ($calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount']) + $calculation['application_fees_gst'] : $calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount'],
                    "invoice_gst" => $getCourseMain->gst ?? getCourseGSTRate($studentData),
                    "total_discount" => 0,
                    "line_items" => json_encode($xeroItemList['itemlist']),
                    "invoice_date" => Carbon::now()->format('Y-m-d'),
                    "due_date" => Carbon::now()->format('Y-m-d'),
                ]);
                // $pdfData = $this->commonService->getPdfdata($updateInvoice, $companydetails);
                Log::info("Return New Student Enroll iD for invoice syncing xero");
                $invoiceData = $updateInvoice->student_enroll_id; 
    
            } else{
                Log::info("Return OLD Student Enroll iD for invoice syncing xero");
                // $pdfData = $this->commonService->getPdfdata($oldInvoice, $companydetails);
                $invoiceData = $oldInvoice->student_enroll_id; 
            }
            Log::info("Return Student Enroll iD for invoice syncing xero");
            return $invoiceData;
        }
    }

    public function grantCalculation($studentData, $getCourseMain, $programDiscount = 0, $isCourseProgramType){
        $studentDob = Carbon::parse($studentData->dob);
        $toDay =  Carbon::now();
        
        $invoiceGst = 0;
        if($studentData->xero_invoice_number){
            $invoice = Invoice::where([ 'student_enroll_id' => $studentData->id, 'invoice_number' => $studentData->xero_invoice_number])->first();
            if($invoice){
                $invoiceGst = $invoice->invoice_gst;
            }
        }
        
        $studnetNric        = $studentData->student->nric; 
        $nationality        = strtolower($studentData->nationality);
        $studnetAge         = $toDay->diffInYears($studentDob);
        $mainCourseFees     = (float)$getCourseMain->course_full_fees;
        $sponserByCompany   = strtolower($studentData->sponsored_by_company);
        $companyIsSme       = strtolower($studentData->company_sme);
        
        $grantResponse = [];
        $grantResponse['age']                   = $studnetAge;
        $grantResponse['nationality']           = $nationality;
        $grantResponse['sponser_by_company']    = $sponserByCompany;
        $grantResponse['company_is_sme']        = $companyIsSme;
        $grantResponse['course_amount']         = $mainCourseFees;
        $grantResponse['gst']                   = $getCourseMain->gst;

        $no_funding         = $getCourseMain->no_funding;
        $enhanced_funding   = ($getCourseMain->enhanced_funding) ? $getCourseMain->enhanced_funding : 0; 
        $baseline_funding   = ($getCourseMain->baseline_funding) ? $getCourseMain->baseline_funding : 0;
        $gst                = ($invoiceGst != 0) ? $invoiceGst : $getCourseMain->gst;
        $gst_applied_on     = ($getCourseMain->gst_applied_on) ? $getCourseMain->gst_applied_on : 'baseline';

        if($gst_applied_on == self::BASELINE_FUNDING) {
            Log::info("GST on baseline funding");

            if(!empty($programDiscount)){
                Log::info("programDiscount true grant calculation base funding");
                Log::info($programDiscount->discount_percentage);
                $programCourseFeeDiscount =  ($mainCourseFees * $programDiscount->discount_percentage) / 100;
                $afterDiscountCourseFee   = $mainCourseFees - $programCourseFeeDiscount;
                $gstOnCourseFee           = $afterDiscountCourseFee * $gst / 100; 
                $baseLineCalculation      = $afterDiscountCourseFee * $baseline_funding / 100;
                $enhancedCalculation      = $afterDiscountCourseFee * $enhanced_funding / 100; 
            } else {
                if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount) && !$isCourseProgramType) {
                    Log::info("course Discount true grant calculation base funding");
                    Log::info($getCourseMain->discount_amount);
                    $afterDiscountCourseFee = $mainCourseFees - $getCourseMain->discount_amount; 
                    $gstOnCourseFee         = $afterDiscountCourseFee * $gst / 100;
                    $baseLineCalculation    = $afterDiscountCourseFee * $baseline_funding / 100;
                    $enhancedCalculation    = $afterDiscountCourseFee * $enhanced_funding / 100; 
                } else {
                    Log::info("no Discount grant calculation base funding");
                    $gstOnCourseFee         = $mainCourseFees * $gst / 100;
                    $baseLineCalculation    = $mainCourseFees * $baseline_funding / 100;
                    $enhancedCalculation    = $mainCourseFees * $enhanced_funding / 100;
                }
            }

            $gstOnBaseline          = $baseLineCalculation * $gst / 100;
            $gstOnEnhanced          = 0;

            if($nationality == self::NON_SINGAPOR_PR) {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'baseline';
            } elseif($nationality == self::NON_SINGAPOR_RESIDENT) {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'baseline';
            } elseif($nationality == self::SINGAPOR_CITIZEN || $nationality == self::SINGAPOREAN) {
                //old condition if ($studnetAge > 40)
                if($studnetAge >= 40) {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                    $grantResponse['baseline_funding']      = $baseLineCalculation;
                    $grantResponse['enhance_gst_amount']    = $gstOnEnhanced;
                    $grantResponse['enhanced_funding']      = $enhancedCalculation;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'baseline';
                //old condition elseif($studnetAge <= 40 && $studnetAge != 0)
                } elseif($studnetAge < 40 && $studnetAge != 0) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhanced;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany != "yes" && $studnetAge >= 21) {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany != "yes" && $studnetAge < 21) {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = 0;
                        $grantResponse['baseline_funding']      = 0;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    }
                } elseif($studnetAge == 0) {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'baseline';
                }
            } elseif($nationality == self::SINGAPOR_PR) {
                //added condition new condition student age >= 21
                if($studnetAge >= 21) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhanced;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    }
                } else {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'baseline';
                }
            } elseif($nationality == "") {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'baseline';
            } elseif($nationality == self::NON_SINGAPOR_PR_ONE){
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'baseline';
            } elseif($nationality == self::LONG_TERM_VISITOR){
                //added condition new condition student age >= 21
                if($studnetAge >= 21) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhanced;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    } elseif($sponserByCompany != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'baseline';
                    }
                } else {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'baseline';
                }
            }
        } else if($gst_applied_on == self::COURSE_FEE) {
            Log::info("GST on Course fee");

            if(!empty($programDiscount)){
                Log::info("programDiscount true grant calculation course fee");
                $programCourseFeeDiscount =  ($mainCourseFees * $programDiscount->discount_percentage) / 100;
                $afterDiscountCourseFee   = $mainCourseFees - $programCourseFeeDiscount;
                $gstOnCourseFee           = $afterDiscountCourseFee * $gst / 100;
                $baseLineCalculation      = $afterDiscountCourseFee * $baseline_funding / 100;
                $enhancedCalculation      = $afterDiscountCourseFee * $enhanced_funding / 100;     
            } else {
                if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount) && !$isCourseProgramType){
                    Log::info("course Discount true grant calculation course fee");
                    Log::info($getCourseMain->discount_amount);
                    $afterDiscountCourseFee = $mainCourseFees - $getCourseMain->discount_amount;
                    $gstOnCourseFee         = $afterDiscountCourseFee * $gst / 100;
                    $baseLineCalculation    = $afterDiscountCourseFee * $baseline_funding / 100;
                    $enhancedCalculation    = $afterDiscountCourseFee * $enhanced_funding / 100;
                } else {
                    Log::info("no Discount grant calculation course fee");
                    $gstOnCourseFee         = $mainCourseFees * $gst / 100;
                    $baseLineCalculation    = $mainCourseFees * $baseline_funding / 100;
                    $enhancedCalculation    = $mainCourseFees * $enhanced_funding / 100;
                }
            }

            // $baseLineCalculation    = $mainCourseFees * ($baseline_funding) / 100;
            $gstOnBaseline          = 0;
            // $enhancedCalculation    = $mainCourseFees * $enhanced_funding / 100;
            $gstOnEnhance           = 0;
            
            if($nationality == self::NON_SINGAPOR_PR) {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'course_fee';
            } elseif($nationality == self::NON_SINGAPOR_RESIDENT) {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'course_fee';
            } elseif($nationality == self::SINGAPOR_CITIZEN || $nationality == self::SINGAPOREAN) {
                //old condition if ($studnetAge > 40)
                if($studnetAge >= 40) {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                    $grantResponse['baseline_funding']      = $baseLineCalculation;
                    $grantResponse['enhance_gst_amount']    = $gstOnEnhance;
                    $grantResponse['enhanced_funding']      = $enhancedCalculation;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'course_fee';
                //old condition elseif($studnetAge <= 40 && $studnetAge != 0)
                } elseif($studnetAge < 40 && $studnetAge != 0) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhance;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany != "yes" && $studnetAge >= 21) {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany != "yes" && $studnetAge < 21) {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = 0;
                        $grantResponse['baseline_funding']      = 0;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    }
                } elseif($studnetAge == 0){
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'course_fee';
                }
            } elseif($nationality == self::SINGAPOR_PR) {
                //added condition new condition student age >= 21
                if($studnetAge >= 21) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhance;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    }
                } else {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'course_fee';
                }
            } elseif($nationality == "") {
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'course_fee';
            } elseif($nationality == self::NON_SINGAPOR_PR_ONE){
                $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                $grantResponse['baseline_gst_amount']   = 0;
                $grantResponse['baseline_funding']      = 0;
                $grantResponse['enhance_gst_amount']    = 0;
                $grantResponse['enhanced_funding']      = 0;
                $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                $grantResponse['gst_applied_on']        = 'course_fee';
            } elseif($nationality == self::LONG_TERM_VISITOR){
                //added condition new condition student age >= 21
                if($studnetAge >= 21) {
                    if($sponserByCompany == "yes" && $companyIsSme == "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = $gstOnEnhance;
                        $grantResponse['enhanced_funding']      = $enhancedCalculation;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany == "yes" && $companyIsSme != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    } elseif($sponserByCompany != "yes") {
                        $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                        $grantResponse['baseline_gst_amount']   = $gstOnBaseline;
                        $grantResponse['baseline_funding']      = $baseLineCalculation;
                        $grantResponse['enhance_gst_amount']    = 0;
                        $grantResponse['enhanced_funding']      = 0;
                        $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                        $grantResponse['gst_applied_on']        = 'course_fee';
                    }
                } else {
                    $grantResponse['course_gst_amount']     = $gstOnCourseFee;
                    $grantResponse['baseline_gst_amount']   = 0;
                    $grantResponse['baseline_funding']      = 0;
                    $grantResponse['enhance_gst_amount']    = 0;
                    $grantResponse['enhanced_funding']      = 0;
                    $grantResponse['total_fess']            = $mainCourseFees + $gstOnCourseFee;
                    $grantResponse['gst_applied_on']        = 'course_fee';
                }
            }
        }
        Log::info($grantResponse);
        return $grantResponse; 
    }


    public function AddUpdateOldInvoices($id, $xeroCredentials){
        $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();

        $studentService = new StudentService;
        $studentData = $studentService->getStudentEnrolmentByIdWithRealtionData($grantOfStudnet[0]['student_enrolment_id']);
        
        //Get course run data
        $courseService  = new CourseService;
        $getCourseRun   = $courseService->getCourseById($studentData->course_id);
        
        //Get Main course data
        $courseMainService  = new CourseMainService;
        $getCourseMain      = $courseMainService->getCourseMainById($getCourseRun->course_main_id);
        $commonService = new CommonService;
        $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);

        $calculation = $this->tpGatewayGrantCalculation($grantOfStudnet, $studentData, $getCourseMain);

        if(!empty($getCourseRun->tpgateway_id)){
            $gateWayId = "- (" . $getCourseRun->tpgateway_id . ")";
        } else {
            $gateWayId = "";
        }
        

        $itemOne[]      = $CourseRunSessionString . " : " . $getCourseMain->reference_number . " " . $getCourseMain->name . $gateWayId;
        $itemTwo[]      = $calculation['baseline_funding'];
        $itemThree[]    = $calculation['enhanced_funding'];
        $itemFour[]     = $studentData->student->name;
        $applied_gst = ($getCourseMain->gst_applied_on == 1) ? 'course_fee' : 'baseline';
        $updateItem = null;
        $netCourseFee = $studentData->amount;

        if($studentData->master_invoice == StudentEnrolment::TMS_SYNC) {
            
            $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
            $updatedXeroItemList = [];
            foreach($xeroItemList['itemlist'] as $key => $value){
                if (strtolower($value['description']) == "ssg training grant (enhanced subsidy)" && $value['line_amount'] != 0) {
                    Log::info("3 condition 1 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) == "ssg training grant (baseline funding)" && $value['line_amount'] != 0) {
                    Log::info("3 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) != "ssg training grant (enhanced subsidy)" && strtolower($value['description']) != "ssg training grant (baseline funding)") {
                    Log::info("3 condition 3 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)") {
                    Log::info("3 condition 4 true");
                    $updatedXeroItemList[] = $this->addXeroItemListCmpyNo($netCourseFee, $applied_gst);
                }
            }

            Log::info("========  addUpdateOldInvoices function data =========");
            Log::info(print_r($updatedXeroItemList, true));
            Log::info("========  addUpdateOldInvoices function data =========");
            
            $xeroItemList['itemlist'] = $updatedXeroItemList;
            
            $updateItem = $xeroItemList['itemlist'];
            $invoiceLineItems = Invoice::where(['student_enroll_id' => $studentData->id, 'courserun_id' => $getCourseRun->id])->first();
            $syncedOn = "tms";
            $studentEnrolId = $this->commonUpdatefunction($getCourseMain, $studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn);
            return $studentEnrolId;
            // $invoiceLineItems = Invoice::where(['student_enroll_id' => $studentData->id, 'courserun_id' => $getCourseRun->id])->first();
            // $updateItem = $this->commonUpdateLineItems($invoiceLineItems);
            // $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
            // $syncedOn = "tms";
            // $studentEnrolId = $this->commonUpdatefunction($studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn);
            // return $studentEnrolId;

        } else if($studentData->master_invoice == StudentEnrolment::XERO_SYNC) {
            
            $invoiceLineItems = Invoice::where(['student_enroll_id' => $studentData->id, 'courserun_id' => $getCourseRun->id])->first();
            $updateItem = $this->commonUpdateLineItems($invoiceLineItems);
            $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
            $updatedXeroItemList = [];
            foreach($xeroItemList['itemlist'] as $key => $value){
                if (strtolower($value['description']) == "ssg training grant (enhanced subsidy)" && $value['line_amount'] != 0) {
                    Log::info("4 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) == "ssg training grant (baseline funding)" && $value['line_amount'] != 0) {
                    Log::info("4 condition 2 true");
                    $updatedXeroItemList[] = $value;
                } elseif (strtolower($value['description']) != "ssg training grant (enhanced subsidy)" && strtolower($value['description']) != "ssg training grant (baseline funding)") {
                    Log::info("4 condition 3 true");
                    $updatedXeroItemList[] = $value;
                }  elseif (strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)") {
                    Log::info("4 condition 4 true");
                    $updatedXeroItemList[] = $this->addXeroItemListCmpyNo($netCourseFee, $applied_gst);
                }
            }

            Log::info("======== addUpdateOldInvoices else function data =========");
            Log::info(print_r($updatedXeroItemList, true));
            Log::info("======== addUpdateOldInvoices else function data =========");
            $xeroItemList['itemlist'] = $updatedXeroItemList;

            $syncedOn = "xero";
            $studentEnrolId = $this->commonUpdatefunction($getCourseMain, $studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn);
            return $studentEnrolId;
        }
        //Also synced with Xero
        // $xeroService = new XeroService($xeroCredentials);
        // $returnType = false;
        // if(!empty($updateOrCreateInvoice->invoice_number)) {
        //     $getInvoiceId = $xeroService->getInvoiceFromXero($updateOrCreateInvoice->invoice_number);
        //     $updateOrCreateInvoice->xero_invoice_id = $getInvoiceId;
        //     $updateOrCreateInvoice->update();
        //     $updateInvoice = $xeroService->updateInvoiceFromXeroById($getInvoiceId);
        //     if($updateInvoice){
        //         return $updateOrCreateInvoice->invoice_number;
        //     } else {
        //         return false;
        //     }
        // } else {
        //     $createdInvoiceId = $xeroService->createInvoiceFromXero($updateOrCreateInvoice->student_enroll_id, $returnType);
        //     return $createdInvoiceId;
        // }
    }

    public function updateTmsInvoice($id){
        $itemOne = $itemTwo = $itemThree = $itemFour = [];
        //Get Student Data
        $studentService = new StudentService;
        $studentData = $studentService->getStudentEnrolmentByIdWithRealtionData($id);
        
        //Get course run data
        $courseService  = new CourseService;
        $getCourseRun   = $courseService->getCourseById($studentData->course_id);
        
        //Get Main course data
        $courseMainService  = new CourseMainService;
        $getCourseMain      = $courseMainService->getCourseMainById($getCourseRun->course_main_id);

        $commonService = new CommonService;
        $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);
        $mainCourseFees = (float) $getCourseMain->course_full_fees;
        if(!empty($getCourseRun->tpgateway_id)){
            $gateWayId = "- (" . $getCourseRun->tpgateway_id . ")";
        } else {
            $gateWayId = "";
        }
        if(!empty($studentData->program_type_id)){
            $programDiscount =  ProgramType::where('id', $studentData->program_type_id)->first();
        }
        if($studentData->is_invoice_generated == 0 && !empty($studentData->program_type_id)){
            $checkInvoiceGenerate = StudentEnrolment::where(['entry_id' => $studentData->entry_id, 'is_invoice_generated' => 1])->get();
        }

        if(isset($programDiscount) && $programDiscount->is_discount == 1) {
            $calculation = $this->grantCalculation($studentData, $getCourseMain, $programDiscount, false);
            $calculation['program_discount'] = $programDiscount->discount_percentage;                                        
            $programCourseFeeDiscount =  ($mainCourseFees * $programDiscount->discount_percentage) / 100;

            $studentData->discountAmount = $programCourseFeeDiscount;
            $studentData->save();

        } else {
            $calculation = $this->grantCalculation($studentData, $getCourseMain, 0 , false);
            if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount) && empty($studentData->program_type_id)){
                $calculation['course_discount'] = $getCourseMain->discount_amount;
                $studentData->discountAmount = $getCourseMain->discount_amount;
                $studentData->save();
            }            
        }

        $itemOne[]      = $CourseRunSessionString . " : " . $getCourseMain->reference_number . " " . $getCourseMain->name . $gateWayId;
        $itemTwo[]      = $calculation['baseline_funding'];
        $itemThree[]    = $calculation['enhanced_funding'];
        $itemFour[]     = $studentData->student->name;
        $applied_gst    = $calculation['gst_applied_on'];
        $updateItem     = null;
        $netCourseFee = $studentData->amount;

        $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
        $updatedXeroItemList = [];
        foreach($xeroItemList['itemlist'] as $key => $value){
            if (strtolower($value['description']) == "ssg training grant (enhanced subsidy)" && $value['line_amount'] != 0) {
                Log::info("5 condition 1 true");
                $updatedXeroItemList[] = $value;
            } elseif (strtolower($value['description']) == "ssg training grant (baseline funding)" && $value['line_amount'] != 0) {
                Log::info("5 condition 2 true");
                $updatedXeroItemList[] = $value;
            } elseif (strtolower($value['description']) != "ssg training grant (enhanced subsidy)" && strtolower($value['description']) != "ssg training grant (baseline funding)") {
                Log::info("5 condition 3 true");
                $updatedXeroItemList[] = $value;
            }   elseif (strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)") {
                Log::info("5 condition 4 true");
                $updatedXeroItemList[] = $this->addXeroItemListCmpyNo($netCourseFee, $applied_gst);
            }
        }

        // check if Prgram type application added to first invoice
        if( isset($programDiscount) ){
            if($programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                if(isset($checkInvoiceGenerate) && $checkInvoiceGenerate->count() >= 1 ){}
                else {
                    $calculation['application_fees_gst'] = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
                    $calculation['application_fees_amount'] = $programDiscount->application_fee;

                    if($programDiscount->is_absorb_gst == 0){
                        $studentData->is_invoice_generated = 1;
                        $studentData->save();
                    }

                    $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
                }
            }
        }

        if($studentData->application_fee){
            if(strtolower($studentData->sponsored_by_company) == "no (i'm signing up as an individual)"){
                if($studentData->payment_type == 'Pay Now') {
                    $calculation['application_fees_gst'] = 0;
                    $calculation['application_fees_amount'] = 0;
                } else if ($studentData->payment_type == 'Pay Later') {
                    $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                    $calculation['application_fees_amount'] = $studentData->application_fee;
                }
            } else {
                $calculation['application_fees_gst'] = ($studentData->application_fee * $getCourseMain->gst) / 100;
                $calculation['application_fees_amount'] = $studentData->application_fee;
            }
            $updatedXeroItemList[] = $this->addXeroLineItemApplicationFees($calculation, $applied_gst);
        }

        
            if(!empty($studentData->program_type_id)) {
                if($programDiscount->is_absorb_gst == 1){
                    if(!empty($programDiscount) && $programDiscount->is_application_fee == 1 && !empty($programDiscount->application_fee)) {
                        if(isset($checkInvoiceGenerate) && $checkInvoiceGenerate->count() >= 1 ){
                            if(isset($programCourseFeeDiscount)){
                                //calculate the other invoice with absorb gst with discount (bundle course)
                                $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            } else {
                                //calculate the other invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount    = ($mainCourseFees * $getCourseMain->gst) / 100; 
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                        else {
                            if(isset($programCourseFeeDiscount)){
                                //calculate the application on first invoice with absorb gst with discount (bundle course)
                                $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                                $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
        
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
        
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();
                            } else {
                                //calculate the application on first invoice with absorb gst without discount (bundle course)
                                $gstOnCourseFeeDiscount         = ($mainCourseFees * $getCourseMain->gst) / 100;        
                                $apllicationGST = ($programDiscount->application_fee * $getCourseMain->gst) / 100;
        
                                $calculation['absorb_gst'] = $gstOnCourseFeeDiscount + $apllicationGST;
                                $studentData->is_invoice_generated = 1;
                                $studentData->save();
                            }
                            $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                        }
                    } else {
                        //calculate the other invoice with absorb gst with discount with no application fees (bundle course)
                        if(isset($programCourseFeeDiscount)){
                            $afterDiscountCourseFee = $mainCourseFees - $programCourseFeeDiscount;
                            $gstOnCourseFeeDiscount         = ($afterDiscountCourseFee * $getCourseMain->gst) / 100; 
                            $calculation['absorb_gst'] = $gstOnCourseFeeDiscount;
                        } else {
                            //calculate the other invoice with absorb gst without discount with no application fees (bundle course)
                            $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                            $calculation['absorb_gst'] = $absorbUnitPrice;
                        }                     
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }
                }
            } else {
                if($getCourseMain->is_absorb_gst == 1) {
                    if(!empty($getCourseMain->is_discount) && !empty($getCourseMain->discount_amount)){
                        //calculate the other invoice with absorb gst with discount (singular course)
                        $afterDiscountCourseFee   = $mainCourseFees - $getCourseMain->discount_amount;
                        $absorbUnitPrice = ($afterDiscountCourseFee * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    } else {
                        //calculate the other invoice with absorb gst without discount (singular course)
                        $absorbUnitPrice = ($mainCourseFees * $getCourseMain->gst) / 100;
                        $calculation['absorb_gst'] = $absorbUnitPrice;
                        $updatedXeroItemList[] = $this->addXeroLineItemAbsorbGst($calculation, $applied_gst);
                    }                    
                }
            }

        \Log::info($getCourseMain->application_fees . ": 5application_fees");
        Log::info("========  updateTmsInvoice function data =========");
        Log::info(print_r($updatedXeroItemList, true));
        Log::info("======== updateTmsInvoice function data =========");
        $xeroItemList['itemlist'] = $updatedXeroItemList;
        
        $invoiceLineItems = Invoice::where(['student_enroll_id' => $studentData->id, 'courserun_id' => $getCourseRun->id])->first();
        $syncedOn = "tms";
        $studentEnrolId = $this->commonUpdatefunction($getCourseMain, $studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn);
        
        // $updateItem = $this->commonUpdateLineItems($invoiceLineItems);
        // $xeroItemList = $this->setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst);
        // $syncedOn = "tms";
        // $studentEnrolId = $this->commonUpdatefunction($studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn);
        
        return $studentEnrolId;
    }

    public function xeroToTmsInvoice($xeroInvoiceData, $invoiceData){

        $studentService = new StudentService;
        $studentData = $studentService->getStudentEnrolmentByIdWithRealtionData($invoiceData->student_enroll_id);
        
        //Get course run data
        $courseService  = new CourseService;
        $getCourseRun   = $courseService->getCourseById($studentData->course_id);
        
        //Get Main course data
        $courseMainService  = new CourseMainService;
        $getCourseMain      = $courseMainService->getCourseMainById($getCourseRun->course_main_id);

        $commonService = new CommonService;
        $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);

        $studnetEnrolment = StudentEnrolment::find($invoiceData->student_enroll_id);

        if(!empty($getCourseRun->tpgateway_id)){
            $gateWayId = "- (" . $getCourseRun->tpgateway_id . ")";
        } else {
            $gateWayId = "";
        }
        
        $invoiceName  = $CourseRunSessionString . " : " . $getCourseMain->reference_number . " " . $getCourseMain->name . $gateWayId;
        foreach($xeroInvoiceData as $invoiceXeroData){
            if( strtolower($invoiceXeroData->getStatusAttributeString()) == null){
                $invoiceData->invoice_name = $invoiceName;
                $invoiceData->invoice_number = $invoiceXeroData->getInvoiceNumber();
                $invoiceData->xero_invoice_id = $invoiceXeroData->getInvoiceId();
                $invoiceData->invoice_type = $invoiceXeroData->getType(); 
                $invoiceData->invoice_status = $invoiceXeroData->getStatus();
                $invoiceData->amount_due = $invoiceXeroData->getAmountDue();
                $invoiceData->amount_paid = $invoiceXeroData->getAmountPaid();
                $invoiceData->sub_total = $invoiceXeroData->getSubTotal();
                $invoiceData->tax = $invoiceXeroData->getTotalTax();
                $invoiceData->total_discount = $invoiceXeroData->getTotalDiscount();
                $invoiceData->invoice_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceXeroData->getDate(),'(',')'), 0, 10))->format('Y-m-d');
                $invoiceData->due_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceXeroData->getDueDate(),'(',')'), 0, 10))->format('Y-m-d');

                $studnetEnrolment->xero_invoice_id      = $invoiceXeroData->getInvoiceId();
                $studnetEnrolment->xero_invoice_number  = $invoiceXeroData->getInvoiceNumber();
                $studnetEnrolment->xero_amount          = ($invoiceXeroData->getSubTotal() + $invoiceXeroData->getTotalTax());
                $studnetEnrolment->xero_due_amount	    = $invoiceXeroData->getAmountDue();
                $studnetEnrolment->xero_paid_amount     = $invoiceXeroData->getAmountPaid();

                foreach($invoiceXeroData->getLineItems() as $key => $lineItems){
                    $invoiceLineItems['line_items'][$key]['line_item_id'] =  $lineItems->getLineItemId();
                    $invoiceLineItems['line_items'][$key]['description']  =  $lineItems->getDescription();
                    $invoiceLineItems['line_items'][$key]['quantity']     =  $lineItems->getQuantity();
                    $invoiceLineItems['line_items'][$key]['unit_amount']  =  $lineItems->getUnitAmount();
                    $invoiceLineItems['line_items'][$key]['tax_amount']   =  $lineItems->getTaxAmount();
                    $invoiceLineItems['line_items'][$key]['line_amount']  =  $lineItems->getLineAmount();
                    $invoiceLineItems['line_items'][$key]['gst_applied_on']  =  json_decode($invoiceData->line_items, true)[1]['gst_applied_on'];
                }
                $invoiceData->line_items = json_encode($invoiceLineItems['line_items']);
                $invoiceData->xero_sync = Invoice::SYNC_XERO_TRUE;
            }
        }
        if($invoiceData->update()){
            $studnetEnrolment->update();
            return $invoiceData->student_enroll_id;
        }else{
            return false;
        }
    }
    
    public function dateFormate($string, $start, $end){
        $startCount = strpos($string, $start)+strlen($start);
        $firstdateFormate = substr($string, $startCount, strlen($string));
        $endDateFormate = strpos($firstdateFormate, $end);
        if($endDateFormate == 0){
            $endDateFormate = strlen($firstdateFormate);
        }
        return substr($firstdateFormate, 0, $endDateFormate);
    }

    public function commonUpdatefunction($getCourseMain,$studentData, $getCourseRun, $invoiceLineItems, $calculation, $itemOne, $xeroItemList, $syncedOn){
        $application_fees = $getCourseMain->application_fees;
        if($invoiceLineItems){
            $updateOrCreateInvoice = Invoice::updateOrCreate([
                'student_enroll_id' => $studentData->id,
                'courserun_id'      => $getCourseRun->id,
            ],[
                // 'invoice_number' => "",
                'invoice_number' => $studentData->xero_invoice_number ? $studentData->xero_invoice_number : null,
                'xero_invoice_id' => $invoiceLineItems->xero_invoice_id ? $invoiceLineItems->xero_invoice_id : null,
                "is_comapany" => strtolower($studentData->sponsored_by_company) == "yes" ? 1 : 0,
                "invoice_name" => implode(',', $itemOne),
                "invoice_type" => "Pending",
                "invoice_status" =>  $calculation['total_fess'] == 0 ? "paid" : "pending",
                "amount_due" => $this->amountDue($calculation, $getCourseMain),
                "amount_paid" => 0,
                "sub_total" => $this->subTotal($calculation, $getCourseMain),
                "tax" => ($application_fees != 0) ? ($calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount']) + $calculation['application_fees_gst'] : $calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount'],
                "total_discount" => 0,
                "line_items" => json_encode($xeroItemList['itemlist']),
                "invoice_date" => Carbon::now()->format('Y-m-d'),
                "due_date" => Carbon::now()->format('Y-m-d'),
                "xero_sync" => $syncedOn == "tms" ? Invoice::SYNC_XERO_FALSE : Invoice::SYNC_XERO_TRUE,
            ]);
        } else {
            $updateOrCreateInvoice = Invoice::updateOrCreate([
                'student_enroll_id' => $studentData->id,
                'courserun_id'      => $getCourseRun->id,
            ],[
                'invoice_number' => $studentData->xero_invoice_number ? $studentData->xero_invoice_number : null,
                'xero_invoice_id' => null,
                "is_comapany" => strtolower($studentData->sponsored_by_company) == "yes" ? 1 : 0,
                "invoice_name" => implode(',', $itemOne),
                "invoice_type" => "Pending",
                "invoice_status" =>  $calculation['total_fess'] == 0 ? "paid" : "pending",
                "amount_due" => $this->amountDue($calculation, $getCourseMain),
                "amount_paid" => 0,
                "sub_total" => $this->subTotal($calculation, $getCourseMain),
                "tax" => ($application_fees != 0) ? ($calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount']) + $calculation['application_fees_gst'] : $calculation['course_gst_amount'] - $calculation['enhance_gst_amount'] - $calculation['baseline_gst_amount'],
                "total_discount" => 0,
                "line_items" => json_encode($xeroItemList['itemlist']),
                "invoice_date" => Carbon::now()->format('Y-m-d'),
                "due_date" => Carbon::now()->format('Y-m-d'),
                "xero_sync" => $syncedOn == "tms" ? Invoice::SYNC_XERO_FALSE : Invoice::SYNC_XERO_TRUE,
            ]);
        }
        return $updateOrCreateInvoice->student_enroll_id;
    }
    
    public function commonUpdateLineItems($invoiceLineItems){
        if($invoiceLineItems) {
            $lineItems = json_decode($invoiceLineItems->line_items, true);
            $updateItem = [];
            foreach($lineItems as $key => $value){
                if($key > 3){
                    $updateItem[] = $value;
                }
            }
            $updateItem = isset($updateItem) ? $updateItem : null;
        } else {
            $updateItem = null;
        }
        return $updateItem;
    }

    public function commonUpdateTmsLineItems($invoiceLineItems){
        if($invoiceLineItems) {
            $lineItems = json_decode($invoiceLineItems['line_items'], true);
            $updateItem = [];
            foreach($lineItems as $key => $value){
                if($key >= 3){
                    $updateItem[] = $value;
                }
            }
            $updateItem = isset($updateItem) ? $updateItem : null;
        } else {
            $updateItem = null;
        }
        return $updateItem;
    }

    public function tpGatewayGrantCalculation($grantOfStudnet, $studentData, $getCourseMain){

        $studentDob = Carbon::parse($studentData->dob);
        $toDay =  Carbon::now();
        $studnetNric        = $studentData->student->nric; 
        $nationality        = strtolower($studentData->nationality);
        $studnetAge         = $toDay->diffInYears($studentDob);
        $mainCourseFees     = (float)$getCourseMain->course_full_fees;
        $sponserByCompany   = strtolower($studentData->sponsored_by_company);
        $companyIsSme       = strtolower($studentData->company_sme);
        
        $invoiceGst = 0;
        if($studentData->xero_invoice_number){
            $invoice = Invoice::where([ 'student_enroll_id' => $studentData->id, 'invoice_number' => $studentData->xero_invoice_number])->first();
            if($invoice){
                $invoiceGst = $invoice->invoice_gst;
            }
        }
        $gst = ($invoiceGst != 0) ? $invoiceGst : $getCourseMain->gst;

        $grantResponse = [];
        $grantResponse['age']                   = $studnetAge;
        $grantResponse['nationality']           = $nationality;
        $grantResponse['sponser_by_company']    = $sponserByCompany;
        $grantResponse['company_is_sme']        = $companyIsSme;
        $grantResponse['course_amount']         = $mainCourseFees;
        $grantResponse['gst']                   = $gst ?? 8;
        $applied_gst = ($getCourseMain->gst_applied_on == 1) ? 'course_fee' : 'baseline'; 

        $grantResponse['course_gst_amount']     = $mainCourseFees * $grantResponse['gst'] / 100;
        $grantCount = count($grantOfStudnet);
        foreach($grantOfStudnet as $value){
            if($grantCount > 1){
                if(strtolower($value['scheme_code']) == "baseline") {
                    $grantResponse['baseline_gst_amount']  = 0;
                    $grantResponse['baseline_funding']     = $value['amount_estimated'];
                    $grantResponse['gst_applied_on']       = $applied_gst;
                } else  {
                    $grantResponse['enhance_gst_amount']   = 0;
                    $grantResponse['enhanced_funding']     = $value['amount_estimated'];
                    $grantResponse['gst_applied_on']       = $applied_gst;
                }
            } elseif ($grantCount == 1) {
                if(strtolower($value['scheme_code']) == "baseline") {
                    $grantResponse['baseline_gst_amount']  = 0;
                    $grantResponse['baseline_funding']     = $value['amount_estimated'];
                    $grantResponse['gst_applied_on']       = $applied_gst;
                    $grantResponse['enhance_gst_amount']   = 0;
                    $grantResponse['enhanced_funding']     = 0;
                } else  {
                    $grantResponse['baseline_gst_amount']  = 0;
                    $grantResponse['baseline_funding']     = 0;
                    $grantResponse['gst_applied_on']       = $applied_gst;
                    $grantResponse['enhance_gst_amount']   = 0;
                    $grantResponse['enhanced_funding']     = $value['amount_estimated'];
                }
            }
        }
        $grantResponse['total_fess']  = $mainCourseFees + $grantResponse['course_gst_amount'];
        return $grantResponse;
    }

    public function setupXeroItemList($itemOne, $itemTwo, $itemThree, $itemFour, $updateItem, $calculation, $applied_gst){
        $xeroItemList = [
            "itemlist" => [
                [
                    "line_item_id"      => "",
                    "description"       => implode(',', $itemOne),
                    "quantity"          => count($itemOne),
                    "unit_amount"       => $calculation['course_amount'],
                    "discount"          => isset($calculation['program_discount']) ? $calculation['program_discount'] : "",
                    "tax_amount"        => $calculation['course_gst_amount'],
                    "line_amount"       => $calculation['course_amount'],
                    "gst_applied_on"    => $applied_gst,
                    "discount_amount"   => isset($calculation['course_discount']) ? $calculation['course_discount'] : "",
                ],
                [
                    "line_item_id"      => "",
                    "description"       => "SSG Training Grant (Baseline Funding)",
                    "quantity"          => count($itemTwo),
                    "unit_amount"       => -$calculation['baseline_funding'],
                    "discount"          => "",
                    "tax_amount"        => -$calculation['baseline_gst_amount'],
                    "line_amount"       => -$calculation['baseline_funding'],
                    "gst_applied_on"    => $applied_gst,
                    "discount_amount"   => "",
                ],
                [
                    "line_item_id"      => "",
                    "description"       => "SSG Training Grant (Enhanced Subsidy)",
                    "quantity"          => count($itemThree),
                    "unit_amount"       => -$calculation['enhanced_funding'],
                    "discount"          => "",
                    "tax_amount"        => 0,
                    "line_amount"       => -$calculation['enhanced_funding'],
                    "gst_applied_on"    => $applied_gst,
                    "discount_amount"   => "",
                ],
                [
                    "line_item_id"      => "",
                    "description"       => "Course Applicant: " . implode(',', $itemFour),
                    "quantity"          => count($itemFour),
                    "unit_amount"       => 0,
                    "discount"          => "",
                    "tax_amount"        => 0,
                    "line_amount"       => 0,
                    "gst_applied_on"    => $applied_gst,
                    "discount_amount"   => "",
                ]
            ],
        ];
        if($updateItem){
            foreach($updateItem as $value){
                array_push($xeroItemList['itemlist'], $value);
            }
        }
        return $xeroItemList;
    }

    public function addXeroItemListCmpyNo($netCourseFee, $applied_gst){
        $xeroLineItemCmpyNo =   [
            "line_item_id"      => "",
            "description"       => "SkillsFuture Credit Claimable: $" . $netCourseFee,
            "quantity"          => 0,
            "unit_amount"       => 0,
            "discount"          => "",
            "tax_amount"        => 0,
            "line_amount"       => 0,
            "gst_applied_on"    => $applied_gst,
            "discount_amount"   => "",
        ];

        return $xeroLineItemCmpyNo;
    }

    public function addXeroLineItemApplicationFees($calculation, $applied_gst)
    {

        $xeroLineItemApplicationFee = [
            "line_item_id"      => "",
            "description"       => "Application Fees: $" . (isset($calculation['application_fees_amount']) ? $calculation['application_fees_amount'] : 0),
            "quantity"          => 1,
            "unit_amount"       => $calculation['application_fees_amount'],
            "discount"          => "",
            "tax_amount"        => $calculation['application_fees_gst'],
            "line_amount"       => isset($calculation['application_fees_amount']) ? $calculation['application_fees_amount'] : 0,
            "gst_applied_on"    => $applied_gst,
            "inculde_account_code"  => 'course_fee_account',
            "discount_amount"   => "",
        ];

        return $xeroLineItemApplicationFee;
    }

    public function addXeroLineItemAbsorbGst($calculation, $applied_gst)
    {
        $xeroLineItemAbsorbGst = [
            "line_item_id"      => "",
            "description"       => "GST Absorption",
            "quantity"          => 1,
            "unit_amount"       => -$calculation['absorb_gst'],
            "discount"          => "",
            "tax_amount"        => 0,
            "line_amount"       => -$calculation['absorb_gst'],
            "gst_applied_on"    => $applied_gst,
            "inculde_account_code"  => 'gst_absorption',
            "discount_amount"   => "",
        ];
        return $xeroLineItemAbsorbGst;
    }

    public function amountDue($calculation, $getCourseMain)
    {
        if(isset($calculation['absorb_gst'])) {
            return (($calculation['course_amount'] - $calculation['baseline_funding'] - $calculation['enhanced_funding']) + ($calculation['course_gst_amount'] - $calculation['baseline_gst_amount'])) - $calculation['absorb_gst'];
            // (990.0 - 495.00  - 0) + (89.10 - 0) = 584.1
        }

        return ($calculation['course_amount'] - $calculation['baseline_funding'] - $calculation['enhanced_funding']) + ($calculation['course_gst_amount'] - $calculation['baseline_gst_amount']);
    }

    public function subTotal($calculation, $getCourseMain)
    { 
        if(isset($calculation['absorb_gst'])) {
            return $calculation['course_amount'] - $calculation['baseline_funding'] - $calculation['enhanced_funding'] - $calculation['absorb_gst'];
            // (975.0- 0 - 0 - 85.05) =  889.95
        }

        return $calculation['course_amount'] - $calculation['baseline_funding'] - $calculation['enhanced_funding'];
    }
}
