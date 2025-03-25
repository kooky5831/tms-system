<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\CommonService;
use App\Services\CourseService;
use App\Services\CourseMainService;
use App\Services\StudentService;

use App\Services\XeroService;
use Webfox\Xero\OauthCredentialManager;
use App\Services\GrantCalculationService;
use App\Models\StudentEnrolment;
use App\Models\Invoice;
use App\Models\Settings;
use App\Models\Grant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use DataTables;
use Auth;
use Log;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StudentService $studentService, CommonService $commonService, XeroService $xeroService, GrantCalculationService $grantCalculationService, OauthCredentialManager $xeroCredentials)
    {
        $this->middleware('auth');
        $this->studentService = $studentService;
        $this->commonService = $commonService;
        $this->xeroService = $xeroService;
        $this->grantCalculationService = $grantCalculationService;
        $this->xeroCredentials = $xeroCredentials;
    }

    public function previewInvoice($id){
        
        $invoiceData = Invoice::where('student_enroll_id', $id)->first();
        $studentData = StudentEnrolment::findOrfail($id);
        
        //data wil be empty on invoice and also not sync with xero
        if(empty($invoiceData) && $studentData->xero_invoice_number == null) {
            $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
            if($studentData->master_invoice == StudentEnrolment::TMS_SYNC) {
                if($grantOfStudnet) {
                    $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $this->xeroCredentials);
                    if($invoiceData){
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        setflashmsg("Sorry your course setting is missing", 0);
                        return redirect()->back();
                    }
                } else {
                    $invoiceData = $this->grantCalculationService->generateInvoice($id);
                    if($invoiceData){
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        setflashmsg("Sorry your course setting is missing", 0);
                        return redirect()->back();
                    }
                }
            } elseif($studentData->master_invoice == StudentEnrolment::XERO_SYNC) {
                $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                if($grantOfStudnet) {
                    $returnType = false;
                    $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $this->xeroCredentials);
                    $invoiceData = $this->xeroService->createInvoiceFromXero($invoiceData, $returnType);
                    $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                } else {
                    $returnType = false;
                    $invoiceData = $this->grantCalculationService->generateInvoice($id);
                    $invoiceData = $this->xeroService->createInvoiceFromXero($invoiceData, $returnType);
                    $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                }
            }

        } else if (!empty($invoiceData) && $studentData->xero_invoice_number == null) {
            $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
            if($studentData->master_invoice == StudentEnrolment::TMS_SYNC) {
                if($grantOfStudnet) {
                    if($invoiceData->xero_sync) {
                        $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $this->xeroCredentials);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        $invoiceData = $invoiceData;
                    }
                } else {
                    if($invoiceData->xero_sync) {
                        $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        $invoiceData = $invoiceData;
                    }
                }
            } elseif($studentData->master_invoice == StudentEnrolment::XERO_SYNC) {
                $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                if($grantOfStudnet) {
                    if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                        $invoiceData = $invoiceData;
                    } else {
                        $returnType = false;
                        $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                        $invoiceData = $this->xeroService->createInvoiceFromXero($invoiceData, $returnType);
                        $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                    }
                } else {
                    if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                        $invoiceData = $invoiceData;
                    } else {
                        $returnType = false;
                        $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                        $invoiceData = $this->xeroService->createInvoiceFromXero($invoiceData, $returnType);
                        $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                    }
                }
            }
        } else if (!empty($invoiceData) && $studentData->xero_invoice_number != null){
            $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
            if($studentData->master_invoice == StudentEnrolment::TMS_SYNC) {
                if($grantOfStudnet) {
                    if($invoiceData->xero_sync == 1 && !empty($invoiceData->xero_invoice_id)) {
                        $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $this->xeroCredentials);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        $invoiceData = $invoiceData;
                    }
                } else {
                    if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                        $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        $invoiceData = $invoiceData;
                    }
                }
            } elseif($studentData->master_invoice == StudentEnrolment::XERO_SYNC) {
                if($grantOfStudnet) {
                    if($studentData->xero_invoice_number == $invoiceData->invoice_number){
                        if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                            // $invoiceData = $invoiceData;
                            $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                            if($getUUID) {
                                $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID); 
                                $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Your invoice will be not available on xero", 0);
                                return redirect()->back();
                            }
                        } else {
                            $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                            if($getUUID) {
                                $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID); 
                                $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Your invoice will be not available on xero", 0);
                                return redirect()->back();
                            }
                        }
                    } else {
                        $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                        if($getUUID) {
                            $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID); 
                            $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                            $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                        } else {
                            setflashmsg("Your invoice will be not available on xero", 0);
                            return redirect()->back();
                        }
                    } 
                } else {
                    if($studentData->xero_invoice_number == $invoiceData->invoice_number){
                        if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                            $invoiceData = $invoiceData;
                        } else {
                            $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                            if($getUUID) {
                                $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID); 
                                $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Your invoice will be not available on xero", 0);
                                return redirect()->back();
                            }
                        }
                    }else{
                        $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                        if($getUUID) {
                            $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID); 
                            $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                            $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                        } else {
                            setflashmsg("Your invoice will be not available on xero", 0);
                            return redirect()->back();
                        }
                    }
                }
            }
        } else if (empty($invoiceData) && $studentData->xero_invoice_number != null) {
            $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
            if($studentData->master_invoice == StudentEnrolment::TMS_SYNC) {
                
                if($grantOfStudnet) {
                    $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $this->xeroCredentials);
                    if($invoiceData){
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        setflashmsg("Sorry your course setting is missing", 0);
                        return redirect()->back();
                    }
                } else {
                    $invoiceData = $this->grantCalculationService->generateInvoice($id);
                    if($invoiceData){
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        setflashmsg("Sorry your course setting is missing", 0);
                        return redirect()->back();
                    }
                }

                // $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                // $invoiceData = $this->grantCalculationService->generateInvoice($id);
                // if($invoiceData){
                //     if($getUUID) {
                //         $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID);
                //         $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                //         $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                //     } else {
                //         setflashmsg("Your invoice will be not available on xero", 0);
                //         return redirect()->back();
                //     }
                // } else {
                //     setflashmsg("Your invoice will be not available on xero", 0);
                //     return redirect()->back();
                // }

            } else if ($studentData->master_invoice == StudentEnrolment::XERO_SYNC) {
                $getUUID = $this->xeroService->getInvoiceFromXero($studentData->xero_invoice_number);
                $invoiceData = $this->grantCalculationService->generateInvoice($id);
                if($invoiceData){
                    if($getUUID) {
                        $xeroInvoiceData = $this->xeroService->saveInvoiceFromTheXero($getUUID);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                        $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                    } else {
                        setflashmsg("Your invoice will be not available on xero", 0);
                        return redirect()->back();
                    }
                } elseif(empty($invoiceData) && !empty($getUUID)) {
                    $invoiceData = $this->xeroService->getInvoiceFromXeroAndSave($getUUID, $studentData->id);
                }
            }
        }

        if(strtolower($studentData->sponsored_by_company) == "yes"){
            $companyName    = $studentData->company_name;
            $companyUen     = $studentData->company_uen;
            $companyEmail   = $studentData->billing_email;
            $billingAddress = $studentData->billing_address;
        }else{
            $companyName    = $studentData->student->name;
            $companyEmail   = $studentData->email;
            $companyUen     = "";
            $billingAddress = "";
        }
        $companydetails =  [
            "comapany_name" => $companyName,
            "comapany_uen"  => $companyUen,
            "billing_address" => $billingAddress,
            "company_email" => $companyEmail,
        ];
        
        $invoiceSettings = Settings::where('group', 'invoice')->get();
        $pdfData = $this->commonService->getPdfdata($invoiceData, $companydetails, $invoiceSettings);
        return view('admin.invoices.generate-invoice', compact('pdfData', 'id'));
        
        // if(!empty($invoiceData->invoice_number)){
        //     $xeroData = $this->xeroService->getInvoiceFromXero($invoiceData->invoice_number);
        //     $invoiceData->xero_invoice_id = $xeroData;
        //     $invoiceData->update();
        //     $updatedInvoice = $this->xeroService->getInvoiceFromXeroAndUpdate($invoiceData->xero_invoice_id);
        // } else {
        //     $getInvNo = StudentEnrolment::findOrfail($id);
        //     $xeroData = $this->xeroService->getInvoiceFromXero($getInvNo->xero_invoice_number);
        //     if($xeroData){
        //         $invoiceData = $this->xeroService->getInvoiceFromXeroAndSave($xeroData, $getInvNo->id);
        //         if($invoiceData) {
        //             $invoiceData = $invoiceData;
        //         } else {
        //             setflashmsg("Sorry invoice will be not available from the xero", 0);
        //             return redirect()->back();                    
        //         }
        //     } else {
        //         setflashmsg("Sorry invoice will be not available from the xero", 0);
        //         return redirect()->back();
        //     }
        // } 
        // if(isset($updatedInvoice)) {
        //     $invoiceData = Invoice::where('student_enroll_id', $updatedInvoice->student_enroll_id)->first();
        // } else {
        //     if($invoiceData){
        //         $invoiceData = Invoice::where('student_enroll_id', $invoiceData->student_enroll_id)->first();
        //     }
        // }
        // $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->first();
        // $studentData = StudentEnrolment::findOrfail($id);
        // if(strtolower($studentData->sponsored_by_company) == "yes"){
        //     $companyName    = $studentData->company_name;
        //     $companyUen     = $studentData->company_uen;
        //     $billingAddress = $studentData->billing_address;
        // }else{
        //     $companyName    = $studentData->student->name;
        //     $companyUen     = "";
        //     $billingAddress = "";
        // }
        // $companydetails =  [
        //     "comapany_name" => $companyName,
        //     "comapany_uen"  => $companyUen,
        //     "billing_address" => $billingAddress,
        // ];
        // $invoiceSettings = Settings::where('group', 'invoice')->get();
        // if(!empty($invoiceData) && empty($grantOfStudnet)) {
        //     $pdfData = $this->commonService->getPdfdata($invoiceData, $companydetails, $invoiceSettings);
        //     return view('admin.invoices.generate-invoice', compact('pdfData', 'id'));
        // } elseif(!empty($grantOfStudnet) && empty($invoiceData)) {
        //     $tpGatewayGrants = $this->grantCalculationService->AddUpdateOldInvoices($grantOfStudnet->student_enrolment_id, $this->xeroCredentials);
        //     $invoiceData = Invoice::where('invoice_number', $tpGatewayGrants)->first();
        //     $pdfData = $this->commonService->getPdfdata($invoiceData, $companydetails, $invoiceSettings);
        //     return view('admin.invoices.generate-invoice', compact('pdfData', 'id'));
        // } elseif(!empty($grantOfStudnet) && !empty($invoiceData)) {
        //     $tpGatewayGrants = $this->grantCalculationService->AddUpdateOldInvoices($grantOfStudnet->student_enrolment_id, $this->xeroCredentials);
        //     if(!empty($tpGatewayGrants)) {
        //         $invoiceData = Invoice::where('invoice_number', $tpGatewayGrants)->first();
        //         $pdfData = $this->commonService->getPdfdata($invoiceData, $companydetails, $invoiceSettings);
        //         return view('admin.invoices.generate-invoice', compact('pdfData', 'id'));
        //     } else {
        //         setflashmsg("Sorry something went wrong", 0);
        //         return redirect()->back();
        //     }
        // } else {
        //     setflashmsg("Sorry invoice will be available only new registred student", 0);
        //     return redirect()->back();
        // }
    }

    public function createInvoicePdf($id){
        $invoiceData = Invoice::findOrFail($id);
        $companyData = $this->studentService->getStudentEnrolmentById($invoiceData->student_enroll_id);
        $invoiceSettings = Settings::where('group', 'invoice')->get();
        if(strtolower($companyData->sponsored_by_company) == "yes"){
            $companydetails =  [
                "comapany_name" => $companyData->company_name,
                "comapany_uen"  => $companyData->company_uen,
                "billing_address" => $companyData->billing_address,
            ];
        }else{
            $companydetails =  [
                "comapany_name" => $companyData->student->name,
                "comapany_uen"  => "",
                "billing_address" => "",
            ];
        }

        $pdfData = $this->commonService->getPdfdata($invoiceData, $companydetails, $invoiceSettings);
        $pdf = PDF::loadView('admin.invoices.create-invoice', $pdfData, [], [
            'format' => 'A4',
        ]);
        $invoiceFolder = Storage::path('public/invoices/');
            if( !File::exists($invoiceFolder) ) {
                File::makeDirectory($invoiceFolder, 0755, true, true);
        }
        $fileName = $invoiceData->invoice_number . "_" . time() . '.pdf';
        $fullFilePath = $invoiceFolder . $fileName;
        $pdf->save($fullFilePath);
        // $pdf->stream($fullFilePath);
        $pdf->download($fullFilePath);
    }

    public function grantCalculation($id){
        //Get Student Data
        $studentData = $this->studentService->getStudentEnrolmentByIdWithRealtionData($id);

        //Get course run data
        $courseService  = new CourseService;
        $getCourseRun   = $courseService->getCourseById($studentData->course_id);
        
        //Get Main course data
        $courseMainService  = new CourseMainService;
        $getCourseMain      = $courseMainService->getCourseMainById($getCourseRun->course_main_id);

        $grantCalculationService = new GrantCalculationService;
        $calculation = $grantCalculationService->grantCalculation($studentData, $getCourseMain);
        return $calculation;
    }
}
