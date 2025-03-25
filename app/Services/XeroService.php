<?php

namespace App\Services;

use Log;
use Auth;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Settings;
use App\Models\StudentEnrolment;
use App\Services\StudentService;
use App\Models\XeroBrandingTheme;
use Illuminate\Support\Facades\Http;
use App\Models\Payment AS TmsPayment;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\Models\Accounting\Account;

use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\Payment;
use XeroAPI\XeroPHP\Models\Accounting\TaxRate;
use XeroAPI\XeroPHP\Models\Accounting\TaxType;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;
use Webfox\Xero\Oauth2CredentialManagers\FileStore;
use XeroAPI\XeroPHP\Models\Accounting\PaymentDelete;
use XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes;
use XeroAPI\XeroPHP\Models\Accounting\Invoice AS XeroInvoice;

class XeroService
{
    public $apiInstance;
    protected $xeroCredentials;
    const COURSE_FEE = 1;
    const BASELINE_FUNDING = 2;

    function __construct(OauthCredentialManager $xeroCredentials) {
        $this->xeroCredentials = $xeroCredentials;
        $this->init();
    }

    public function init() {
        /* $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->xeroCredentials->getAccessToken() );
        $this->apiInstance = new \XeroAPI\XeroPHP\Api\AccountingApi(
            new \GuzzleHttp\Client(),
            $config
        ); */
        $this->apiInstance = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
        // $apiInstance = $arg;
    }

    public function checkConnection()
    {
        try {
            // Check if we've got any stored credentials
            if ($this->xeroCredentials->exists()) {
                /*
                 * We have stored credentials so we can resolve the AccountingApi,
                 * If we were sure we already had some stored credentials then we could just resolve this through the controller
                 * But since we use this route for the initial authentication we cannot be sure!
                 */
                $xero             = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
                $organisationName = $xero->getOrganisations($this->xeroCredentials->getTenantId())->getOrganisations()[0]->getName();
                $user             = $this->xeroCredentials->getUser();
                $username         = "{$user['given_name']} {$user['family_name']} ({$user['username']})";
                return [
                    'status' => true,
                    'msg' => $organisationName. " is connected.",
                    'data' => [
                        'connected'        => $this->xeroCredentials->exists(),
                        'error'            => $error ?? null,
                        'organisationName' => $organisationName ?? null,
                        'username'         => $username ?? null
                    ]
                ];
            }
            return [ 'status' => false, 'msg' => 'Xero account not connected. Please connect.' ];
        } catch (\throwable $e) {
            // This can happen if the credentials have been revoked or there is an error with the organisation (e.g. it's expired)
            $error = $e->getMessage();
            return [ 'status' => false, 'msg' => $error ];
        }
    }

    public function getCurrencyList()
    {
        $str = '';

        //[Currencies:Read]
        $result = $this->apiInstance->getCurrencies($this->xeroCredentials->getTenantId());
        //[/Currencies:Read]
        return $result->getCurrencies();

        /* $str = $str . "Get Currencies Total: " . count($result->getCurrencies()) . "<br>";

        if($returnObj) {
            return $result->getCurrencies()[0];
        } else {
            return $str;
        } */
    }

    public function getItemsList()
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }
        $str = '';

        //[Items:Read]
        // READ ALL
        $result = $this->apiInstance->getItems($this->xeroCredentials->getTenantId());
        //[/Items:Read]
        return $result->getItems();
        // $str = $str . "Get Items total: " . count($result->getItems()) . "<br>";

        /* if($returnObj) {
            return $result->getItems()[0];
        } else {
            return $str;
        } */
    }

    public function getBrandingThemesList()
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }
        $str = '';

        //[BrandingThemes:Read]
        // READ ALL
        $result = $this->apiInstance->getBrandingThemes($this->xeroCredentials->getTenantId());
        //[/BrandingThemes:Read]

        return $result->getBrandingThemes();
        // $str = $str ."Get BrandingThemes: " . count($result->getBrandingThemes()) . "<br>";

        // return $str;
    }

    public function createContactsXero($name, $contactno, $email)
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }
        //[Contacts:Create]
        $arr_contacts = [];

        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setName($name)
                // ->setFirstName($fname)
                // ->setLastName($lname)
                ->setContactNumber($contactno)
                ->setIsCustomer(true)
                // ->setIsSupplier(false)
                ->setDefaultCurrency("SGD")
                ->setEmailAddress($email);
        array_push($arr_contacts, $contact);

        $contacts = new \XeroAPI\XeroPHP\Models\Accounting\Contacts;
        $contacts->setContacts($arr_contacts);

        $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
        //[/Contacts:Create]

        // $str = $str ."Contact Created : " . $result->getContacts()[0]->getName();

        return $result;
    }

    public function getBankAccounts()
    {
        // READ only ACTIVE
        $where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\Account::BANK_ACCOUNT_TYPE_BANK . '"';
        $result = $this->apiInstance->getAccounts($this->xeroCredentials->getTenantId(), null, $where);

        return $result;
    }

    public function createLineItem($item)
    {
        $lineitem = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
        $lineitem->setDescription($item->description)
            ->setQuantity(1)
            ->setUnitAmount($item->amount)
            // ->setTaxType("NONE")
            ->setItemCode($item->code)
            ->setAccountCode($item->account_code);

        return $lineitem;
    }

    public function createInvoiceXero($contactId, $lineitems, $brandingThemeId = NULL)
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }
        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactId($contactId);

        $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;

        $invoice->setDueDate(Carbon::now()->addDays(7)->format('Y-m-d'))
            ->setContact($contact)
            ->setLineItems($lineitems)
            ->setCurrencyCode("SGD")
            ->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
            // ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
            ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC)
            ->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
        if( !is_null($brandingThemeId) ) {
            $invoice->setBrandingThemeId($brandingThemeId);
        }
        $result = $this->apiInstance->createInvoices($this->xeroCredentials->getTenantId(), $invoice);

        return $result;
    }

    public function createPaymentXero($invoiceId, $amount, $bankaccountId)
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }

        // $newAcct = $this->getBankAccounts();
        // $accountId = $newAcct->getAccounts()[0]->getAccountId();

        //[Payments:Create]
        $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $invoice->setInvoiceID($invoiceId);
        // dd($invoice);
        // dd($bankaccountId);
        $bankaccount = new \XeroAPI\XeroPHP\Models\Accounting\Account;
        $bankaccount->setAccountID($bankaccountId);

        $payment = new \XeroAPI\XeroPHP\Models\Accounting\Payment;
        $payment->setInvoice($invoice)
            ->setAccount($bankaccount)
            ->setDate(new \DateTime('now'))
            ->setAmount($amount);

        $result = $this->apiInstance->createPayment($this->xeroCredentials->getTenantId(),$payment);
        return $result;

        dd($result);
        $str = $str . "Create Payment ID: " . $result->getPayments()[0]->getPaymentID() . "<br>" ;
    }

    //Xero new services start here 26-06
    public function createContactFromXero($contactDetail){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }

        $arr_contacts = [];
        $contact_1 = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact_1->setName($contactDetail['name'])
                // ->setFirstName()
                // ->setLastName()
                ->setContactNumber($contactDetail['contact_no'])
                ->setIsCustomer(true)
                ->setEmailAddress($contactDetail['email']);
        
                array_push($arr_contacts, $contact_1);

        $contacts = new \XeroAPI\XeroPHP\Models\Accounting\Contacts;
        $contacts->setContacts($arr_contacts);
        $includeArchived = true;
        
        $result = $this->apiInstance->getContacts($this->xeroCredentials->getTenantId(), "", "", "", "", "", $includeArchived, "", $contactDetail['email']);

        if(!empty($result->getContacts())) {
            if($contactDetail['company_sponsored'] == "yes"){
                $contactId = null;
                foreach($result->getContacts() as $contacts){
                    if(strtolower($contactDetail['name']) == strtolower($contacts->getName())){
                        $contactId = $contacts->getContactId();
                    }
                }
                if(empty($contactId)){
                    $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
                    $contactId = $result->getContacts()[0]->getContactId();
                    if($contactId == "00000000-0000-0000-0000-000000000000"){
                        Log::info("New Contact" . $contactId);
                        Log::info("Error for contact creation");
                        Log::info(print_r($result->getContacts()[0]->getValidationErrors(), true));
                        return false;
                    } else {
                        $contactId = $contactId; 
                    }
                }
            } else {
                $contactId = $result->getContacts()[0]->getContactId();
            }
            Log::info("Old" . $contactId);
        } else {
            $result = $this->apiInstance->getContacts($this->xeroCredentials->getTenantId(), "", "", "", "", "", $includeArchived, "", $contactDetail['contact_no']);
            if(!empty($result->getContacts())) {
                if($contactDetail['company_sponsored'] == "yes"){
                    $contactId = null;
                    foreach($result->getContacts() as $contacts){
                        if(strtolower($contactDetail['contact_no']) == strtolower($contacts->getContactNumber())){
                            $contactId = $contacts->getContactId();
                        }
                    }
                    if(empty($contactId)){
                        $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
                        $contactId = $result->getContacts()[0]->getContactId();
                        if($contactId == "00000000-0000-0000-0000-000000000000"){
                            Log::info("New Contact" . $contactId);
                            Log::info("Error for contact creation");
                            Log::info(print_r($result->getContacts()[0]->getValidationErrors(), true));
                            return false;
                        } else {
                            $contactId = $contactId; 
                        }
                    }
                    Log::info("Old" . $contactId);
                } else {
                    $contactId = $result->getContacts()[0]->getContactId();
                }
            } else {
                $result = $this->apiInstance->getContacts($this->xeroCredentials->getTenantId(), "", "", "", "", "", $includeArchived, "", $contactDetail['name']);
                if(!empty($result->getContacts())){
                    if($contactDetail['company_sponsored'] == "yes"){
                        $contactId = null;
                        foreach($result->getContacts() as $contacts){
                            if(strtolower($contactDetail['contact_no']) == strtolower($contacts->getContactNumber())){
                                $contactId = $contacts->getContactId();
                            }
                        }
                        if(empty($contactId)){
                            $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
                            $contactId = $result->getContacts()[0]->getContactId();
                            if($contactId == "00000000-0000-0000-0000-000000000000"){
                                Log::info("New Contact" . $contactId);
                                Log::info("Error for contact creation");
                                Log::info(print_r($result->getContacts()[0]->getValidationErrors(), true));
                                return false;
                            } else {
                                $contactId = $contactId;
                            }
                        }
                        Log::info("Old" . $contactId);
                    } else {
                        $contactId = $result->getContacts()[0]->getContactId();
                    }    
                } else {
                    $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
                    $contactId = $result->getContacts()[0]->getContactId();
                    if($contactId == "00000000-0000-0000-0000-000000000000"){
                        Log::info("New Contact" . $contactId);
                        Log::info("Error for contact creation");
                        Log::info(print_r($result->getContacts()[0]->getValidationErrors(), true));
                        return false;
                    } else {
                        $contactId = $contactId; 
                    }
                }
            }
            $contactId = $contactId;
        }
        return $contactId;
    }

    public function createInvoiceFromXero($invoiceId, $returnType, $checkApi = false, $isProgramApplicationFees = false)
    {
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }
        Log::info("Create createInvoiceFromXero IN");
        $invoiceData = Invoice::where('student_enroll_id', $invoiceId)->first();

        //Dynamic Contact creation from xero
        $studentService =  new StudentService;
        $courseService  =  new CourseMainService;
        $courseRunService = new CourseService;
        $commonService = new CommonService;
        
        $studentDetails = $studentService->getStudentEnrolmentByIdWithRealtionData($invoiceData->student_enroll_id);
        $courseDeatils  = $courseService->getCourseMainById($studentDetails->courseRun->course_main_id);
        $getCourseRun   = $courseRunService->getCourseById($studentDetails->course_id);
        $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);
        $invoiceName  = $CourseRunSessionString . " : " . $courseDeatils->reference_number . " " . $courseDeatils->name . " - (" . $getCourseRun->tpgateway_id . ")";
        
        $setContactDetails = [];
        if(strtolower($studentDetails->sponsored_by_company) == "yes"){
            Log::info("if condition");
            Log::info("if " . $studentDetails->billing_email);
            $setContactDetails['name']              = $studentDetails->company_name;
            $setContactDetails['first_name']        = $studentDetails->company_name;
            $setContactDetails['last_name']         = $studentDetails->company_uen;
            $setContactDetails['contact_no']        = $studentDetails->company_contact_person_number;
            $setContactDetails['email']             = $studentDetails->billing_email;
            $setContactDetails['company_sponsored'] = "yes";
        }else{
            Log::info("else condition");
            Log::info("else " . $studentDetails->billing_email);
            $setContactDetails['name']              = $studentDetails->student->name;
            $setContactDetails['first_name']        = $studentDetails->student->name;
            $setContactDetails['last_name']         = "";
            $setContactDetails['contact_no']        = $studentDetails->mobile_no;
            $setContactDetails['email']             = $studentDetails->email;
            $setContactDetails['company_sponsored'] = "no";
        }
        Log::info("Result");
        Log::info($setContactDetails);
        $contectId = $this->createContactFromXero($setContactDetails);
        Log::info("New or old contact id" . $contectId);


        $invoice = $this->invoiceSetup($invoiceData, $contectId);
        $studnetEnrolment = StudentEnrolment::find($invoiceData->student_enroll_id);
        $createdInvoices = $this->apiInstance->createInvoices($this->xeroCredentials->getTenantId(),$invoice);

        foreach($createdInvoices as $invoiceResonse){
            if( strtolower($invoiceResonse->getStatusAttributeString()) == "ok") {
                Log::info("Create createInvoiceFromXero IN");
                // $invoiceData->invoice_name = $invoiceResonse->getReference();
                $invoiceData->invoice_name = $invoiceName;
                $invoiceData->invoice_number = $invoiceResonse->getInvoiceNumber(); 
                $invoiceData->xero_invoice_id = $invoiceResonse->getInvoiceId();                
                $invoiceData->invoice_type = $invoiceResonse->getType(); 
                $invoiceData->invoice_status = $invoiceResonse->getStatus();
                $invoiceData->amount_due = $invoiceResonse->getAmountDue();
                $invoiceData->amount_paid = $invoiceResonse->getAmountPaid();
                $invoiceData->sub_total = $invoiceResonse->getSubTotal();
                $invoiceData->tax = $invoiceResonse->getTotalTax();
                $invoiceData->total_discount = $invoiceResonse->getTotalDiscount();
                $invoiceData->invoice_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDate(),'(',')'), 0, 10))->format('Y-m-d');
                $invoiceData->due_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDueDate(),'(',')'), 0, 10))->format('Y-m-d');
                
                $studnetEnrolment->xero_invoice_id      = $invoiceResonse->getInvoiceId();
                $studnetEnrolment->xero_invoice_number  = $invoiceResonse->getInvoiceNumber();
                $studnetEnrolment->xero_amount          = ($invoiceResonse->getSubTotal() + $invoiceResonse->getTotalTax());
                $studnetEnrolment->xero_due_amount	    = $invoiceResonse->getAmountDue();
                $studnetEnrolment->xero_paid_amount     = $invoiceResonse->getAmountPaid();
                
                foreach($invoiceResonse->getLineItems() as $key => $lineItems){
                    Log::info("Create createInvoiceFromXero lineitems if IN");
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
            } else {
                Log::info(print_r($createdInvoices, true));
                Log::info("Xero invoice creation Fail");
                return false;    
            }
        }
        if($invoiceData->update()){
            $studnetEnrolment->update();

            if($checkApi){
                $paidAmount = 0;
                if (strtolower($studnetEnrolment->payment_mode_company) == 'credit card' || strtolower($studnetEnrolment->payment_mode_company) == 'credit card'){
                    $paidAmount = $studnetEnrolment->amount;
                    $dateValue = Carbon::today()->format('Y-m-d');
                    $resObj = $this->payFeesOnXero($invoiceData->xero_invoice_id, $studnetEnrolment->amount, $studnetEnrolment->id, "CC payment", 6, $dateValue);
                    /* if( $resObj ) {
                        $studnetEnrolment->xero_fee_amount = $studnetEnrolment->amount;
                        $studnetEnrolment->xero_pay_id = $resObj->getPayments()[0]->getPaymentID();
                        $studnetEnrolment->save();
                        // $xeroId = $resObj->getContacts()[0]->getContactId();
                    } */
                    $studentService->updateXeroAmountPaidByStudentEnrolmentID($studnetEnrolment->id, $studnetEnrolment->amount);
                } else{
                    $paidAmount = $paidAmount;
                }
            }

            if($returnType) {
                Log::info("Update success and return type is true");
                return true;
            } else {
                Log::info("Update success " . $invoiceData->invoice_number);
                return $invoiceData->invoice_number;
            }
        }else{
            Log::info("Update Fail");
            return false;
        }
        Log::info("End");
    }
    
    public function updateInvoiceFromXeroById($uuid){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }
        $getInvoiceData = Invoice::where('xero_invoice_id', $uuid)->first();
        
        //Dynamic Contact creation from xero
        $studentService =  new StudentService;
        $studentDetails = $studentService->getStudentEnrolmentByIdWithRealtionData($getInvoiceData->student_enroll_id);
        $setContactDetails = [];
        if(strtolower($studentDetails->sponsored_by_company) == "yes"){
            $setContactDetails['name']              = $studentDetails->company_name;
            $setContactDetails['first_name']        = $studentDetails->company_name;
            $setContactDetails['last_name']         = $studentDetails->company_uen;
            $setContactDetails['contact_no']        = $studentDetails->company_contact_person_number;
            $setContactDetails['email']             = $studentDetails->billing_email;
            $setContactDetails['company_sponsored'] = "yes";
        }else{
            $setContactDetails['name']              = $studentDetails->student->name;
            $setContactDetails['first_name']        = $studentDetails->student->name;
            $setContactDetails['last_name']         = "";
            $setContactDetails['contact_no']        = $studentDetails->mobile_no;
            $setContactDetails['email']             = $studentDetails->email;
            $setContactDetails['company_sponsored'] = "no";
        }
        
        $contectId = $this->createContactFromXero($setContactDetails);
        $invoice = $this->invoiceSetup($getInvoiceData, $contectId);
        
        try {
            $updatedInvoices = $this->apiInstance->updateInvoice($this->xeroCredentials->getTenantId(), $getInvoiceData->xero_invoice_id, $invoice);
        } catch (\Exception $e) { 
            return redirect()->back();
            Log::info($e->getMessage());
        }

        foreach($updatedInvoices as $invoiceResonse){

            if( strtolower($invoiceResonse->getStatusAttributeString()) == null){
                $getInvoiceData->invoice_name = $getInvoiceData->invoice_name;
                $getInvoiceData->invoice_number = $invoiceResonse->getInvoiceNumber();
                $getInvoiceData->xero_invoice_id = $invoiceResonse->getInvoiceId();
                $getInvoiceData->invoice_type = $invoiceResonse->getType(); 
                $getInvoiceData->invoice_status = $invoiceResonse->getStatus();
                $getInvoiceData->amount_due = $invoiceResonse->getAmountDue();
                $getInvoiceData->amount_paid = $invoiceResonse->getAmountPaid();
                $getInvoiceData->sub_total = $invoiceResonse->getSubTotal();
                $getInvoiceData->tax = $invoiceResonse->getTotalTax();
                $getInvoiceData->total_discount = $invoiceResonse->getTotalDiscount();
                $getInvoiceData->invoice_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDate(),'(',')'), 0, 10))->format('Y-m-d');
                $getInvoiceData->due_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDueDate(),'(',')'), 0, 10))->format('Y-m-d');
                              
                foreach($invoiceResonse->getLineItems() as $key => $lineItems){
                    $invoiceLineItems['line_items'][$key]['line_item_id'] =  $lineItems->getLineItemId();
                    $invoiceLineItems['line_items'][$key]['description']  =  $lineItems->getDescription();
                    $invoiceLineItems['line_items'][$key]['quantity']     =  $lineItems->getQuantity();
                    $invoiceLineItems['line_items'][$key]['unit_amount']  =  $lineItems->getUnitAmount();
                    $invoiceLineItems['line_items'][$key]['tax_amount']   =  $lineItems->getTaxAmount();
                    $invoiceLineItems['line_items'][$key]['line_amount']  =  $lineItems->getLineAmount();
                    $invoiceLineItems['line_items'][$key]['gst_applied_on']  =  json_decode($getInvoiceData->line_items, true)[1]['gst_applied_on'];
                }
                $getInvoiceData->line_items = json_encode($invoiceLineItems['line_items']);
                $getInvoiceData->xero_sync = Invoice::SYNC_XERO_TRUE;
            }
        }
        if($getInvoiceData->update()){
            return true;
        }else{
            return false;
        }
    }

    public function invoiceSetup($invoiceData, $contactId){
        //Curruntly Id was static need to create function for genrate id
        // $contactId = "6cdf0976-341f-4667-83dc-e8f2b0f0bbc6";
        // $connection = $this->checkConnection();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        //     // return $connection['data']['error'];
        // }
        $contact = new Contact;
        $contact->setContactId($contactId);
        $lineitems = [];
        $count = 0;
        $allLineItems = json_decode($invoiceData->line_items, true);
        foreach($allLineItems as $key => $values){
            switch (true) {
                case ($key == 0):
                    array_push($lineitems, $this->getLineItem($values)); 
                    break;
                
                case ($key == 1):
                    array_push($lineitems, $this->getLineItemTwo($values));
                    break;

                case ($key == 2):
                    array_push($lineitems, $this->getLineItemThree($values));
                    break;

                case ($key == 3):
                    if(isset($values['inculde_account_code'])){
                        array_push($lineitems, $this->getOtherLineItems($values, $values['inculde_account_code']));
                    } else {
                        array_push($lineitems, $this->getLineItemFour($values));   
                    }
                    break;
                
                case ($key > 3):
                    isset($values['inculde_account_code']) ? array_push($lineitems, $this->getOtherLineItems($values, $values['inculde_account_code'])) : array_push($lineitems, $this->getOtherLineItems($values));
                    break;

                default:
                    # code...
                    break;
            }
        }
        $due_amount = (int)$invoiceData->amount_due;
        if($due_amount == 0){
            $status = XeroInvoice::STATUS_DRAFT;
        }else{
            $status = XeroInvoice::STATUS_AUTHORISED;
        }

        Log::info($invoiceData->is_comapany);
        if($invoiceData->is_comapany == Invoice::COMAPNY_SPONSORED) {
            $brandThemeId = XeroBrandingTheme::where('applied_on', XeroBrandingTheme::COMPANY)->first();
        } elseif($invoiceData->is_comapany == Invoice::SELF_SPONSORED) {
            $brandThemeId = XeroBrandingTheme::where('applied_on', XeroBrandingTheme::SELF)->first();
        } else {
            $brandThemeId = XeroBrandingTheme::where('applied_on', XeroBrandingTheme::SELF)->first();
        }

        $invoice = new XeroInvoice;
        if($brandThemeId){
            $invoice->setType(XeroInvoice::TYPE_ACCREC)
                    ->setStatus($status)
                    ->setContact($contact)
                    ->setBrandingThemeId($brandThemeId->branding_theme_id)
                    ->setLineAmountTypes(LineAmountTypes::EXCLUSIVE)
                    ->setDueDate($invoiceData->due_date)
                    ->setCurrencyCode("SGD")
                    ->setReference("")
                    ->setLineItems($lineitems)
                    ->setAmountDue($invoiceData->amount_due)
                    ->setAmountPaid($invoiceData->amount_paid)
                    ->setTotal($invoiceData->amount_paid)
                    ->setTotalTax($invoiceData->tax);
        } else {
            $invoice->setType(XeroInvoice::TYPE_ACCREC)
                    ->setStatus($status)
                    ->setContact($contact)
                    ->setLineAmountTypes(LineAmountTypes::EXCLUSIVE)
                    ->setDueDate($invoiceData->due_date)
                    ->setCurrencyCode("SGD")
                    ->setReference("")
                    ->setLineItems($lineitems)
                    ->setAmountDue($invoiceData->amount_due)
                    ->setAmountPaid($invoiceData->amount_paid)
                    ->setTotal($invoiceData->amount_paid)
                    ->setTotalTax($invoiceData->tax);
        }

        // \Log::info(print_r($invoice, true));
        
        return $invoice;
    }

    //Studentlist
    public function getLineItem($itemOne){
        $accountCode = Settings::select('val')
                    ->where('name', 'course_fee_account')
                    ->first();
                    
        // $connection = $this->checkConnection();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        //     // return $connection['data']['error'];
        // }
        $lineitem = new LineItem;
        $lineitem->setDescription($itemOne['description'])
        ->setQuantity($itemOne['quantity'])
        ->setTaxAmount($itemOne['tax_amount'])
        ->setUnitAmount($itemOne['unit_amount'])
        ->setDiscountRate($itemOne['discount'])
        ->setDiscountAmount($itemOne['discount_amount'])
        // ->setTaxType(8)
        ->setAccountCode($accountCode->val);
        return $lineitem;
    }
    
    //Student Grant
    public function getLineItemTwo($itemTwo){
        $accountCode = Settings::select('val')
                    ->where('name', 'ssg_grant_account')
                    ->first();

        if($itemTwo['gst_applied_on'] == 'course_fee'){
            Log::info("Xero line item Course fee");
            $taxType = TaxRate::REPORT_TAX_TYPE_NONE;
        }else{
            Log::info("Xero line item Base line");
            // $taxType = TaxType::OUTPUTY23;
            $taxType = TaxType::OUTPUTY24;
        }

        // $connection = $this->checkConnection();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        //     // return $connection['data']['error'];
        // }
        $lineitem = new LineItem;
        $lineitem->setDescription($itemTwo['description'])
        // ->setLineItemId("") set uuid
        ->setQuantity($itemTwo['quantity'])
        ->setTaxAmount($itemTwo['tax_amount'])
        ->setUnitAmount($itemTwo['unit_amount'])
        ->setTaxType($taxType)
        // ->setItemCode('ABCD')
        ->setAccountCode($accountCode->val);
        return $lineitem;
    }

    //CourseName
    public function getLineItemThree($itemThree){

        // $connection = $this->checkConnection();
        $accountCode = Settings::select('val')
                    ->where('name', 'ssg_grant_account')
                    ->first();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        $accountCode = Settings::select('val')
                    ->where('name', 'ssg_grant_account')
                    ->first();
        $lineitem = new LineItem;
        $lineitem->setDescription($itemThree['description'])
        // ->setLineItemId("") set uuid
        ->setQuantity($itemThree['quantity'])
        ->setTaxAmount($itemThree['tax_amount'])
        ->setUnitAmount($itemThree['unit_amount'])
        ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE)
        // ->setItemCode('ABCD')
        ->setAccountCode($accountCode->val);
        return $lineitem;
    }

    public function getLineItemFour($itemFour){
        // $connection = $this->checkConnection();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        //     // return $connection['data']['error'];
        // }
        $lineitem = new LineItem;
        $lineitem->setDescription($itemFour['description'])
        // ->setLineItemId("") set uuid
        ->setQuantity($itemFour['quantity'])
        ->setTaxAmount($itemFour['tax_amount'])
        ->setUnitAmount($itemFour['unit_amount'])
        ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE);
        // ->setItemCode('ABCD')
        // ->setAccountCode("200");
        return $lineitem;
    }

    public function getOtherLineItems($items, $inculde_account_code = null){
        // $connection = $this->checkConnection();
        // if( !$connection['status'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        // }
        // if( $connection['data']['error'] ) {
        //     setflashmsg("Connection lost", 0);
        //     return redirect()->back();
        //     // return $connection['data']['error'];
        // }
        
        if($inculde_account_code){
            // dd($inculde_account_code);
            if($inculde_account_code == 'ssg_grant_account') {
                $accountCode = Settings::select('val')
                ->where('name', 'ssg_grant_account')
                ->first();
                $lineitem = new LineItem;
                $lineitem->setDescription($items['description'])
                // ->setLineItemId("") set uuid
                ->setQuantity($items['quantity'])
                ->setTaxAmount($items['tax_amount'])
                ->setUnitAmount($items['unit_amount'])
                // ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE)
                // ->setItemCode($accountCode)
                ->setAccountCode($accountCode->val);
            } elseif($inculde_account_code == 'course_fee_account'){
                $accountCode = Settings::select('val')
                    ->where('name', 'course_fee_account')
                    ->first();
                    $lineitem = new LineItem;
                    $lineitem->setDescription($items['description'])
                    // ->setLineItemId("") set uuid
                    ->setQuantity($items['quantity'])
                    ->setTaxAmount($items['tax_amount'])
                    ->setUnitAmount($items['unit_amount'])
                    // ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE)
                    // ->setItemCode($accountCode)
                    ->setAccountCode($accountCode->val);
            } elseif($inculde_account_code == 'gst_absorption') {
                $accountCode = Settings::select('val')
                ->where('name', 'gst_absorption')
                ->first();
                $lineitem = new LineItem;
                $lineitem->setDescription($items['description'])
                // ->setLineItemId("") set uuid
                ->setQuantity($items['quantity'])
                ->setTaxAmount($items['tax_amount'])
                ->setUnitAmount($items['unit_amount'])
                // ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE)
                // ->setItemCode($accountCode)
                ->setAccountCode($accountCode->val);
            }
        } else {
            $lineitem = new LineItem;
            $lineitem->setDescription($items['description'])
            // ->setLineItemId("") set uuid
            ->setQuantity($items['quantity'])
            ->setTaxAmount($items['tax_amount'])
            ->setUnitAmount($items['unit_amount'])
            ->setTaxType(TaxRate::REPORT_TAX_TYPE_NONE);
            // ->setItemCode('ABCD')
            // ->setAccountCode("200");
        }
        return $lineitem;
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
    //Xero new services end here 26-06

    //getting invoice from xero
    public function getInvoiceFromXero($invoiceNumber){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }
        try {
            $result = $this->apiInstance->getInvoice($this->xeroCredentials->getTenantId(), $invoiceNumber);

            foreach($result as $invoiceResonse){
                if( strtolower($invoiceResonse->getStatusAttributeString()) == null) {
                    $invoiceId = $invoiceResonse->getInvoiceId();
                }
            }
            return $invoiceId;
        } catch (\Exception $e) {
            // echo 'Exception when calling AccountingApi->getContactAttachmentById: ', $e->getMessage(), PHP_EOL;
            // echo $e->getMessage();
            if($e->getCode() == 404){
                return null;
            }
        }
    }

    public function getInvoiceFromXeroAndUpdate($uuid){
        try {
            $getInvoiceFromXero = $this->apiInstance->getInvoice($this->xeroCredentials->getTenantId(), $uuid);
            $getInvoiceData = Invoice::where('xero_invoice_id', $uuid)->first();        
            //Dynamic Contact creation from xero
            
            $studentService =  new StudentService;
            $studentDetails = $studentService->getStudentEnrolmentByIdWithRealtionData($getInvoiceData->student_enroll_id);
            $setContactDetails = [];
            
            if(strtolower($studentDetails->sponsored_by_company) == "yes"){
                $setContactDetails['name'] = $studentDetails->company_name;
                $setContactDetails['first_name'] = $studentDetails->company_name;
                $setContactDetails['last_name'] = $studentDetails->company_uen;
                $setContactDetails['contact_no'] = $studentDetails->company_contact_person_number;
                $setContactDetails['email'] = $studentDetails->billing_email;
            }else{
                $setContactDetails['name'] = $studentDetails->student->name;
                $setContactDetails['first_name'] = $studentDetails->student->name;
                $setContactDetails['last_name'] = "";
                $setContactDetails['contact_no'] = $studentDetails->mobile_no;
                $setContactDetails['email'] = $studentDetails->email;
            }
            foreach($getInvoiceFromXero as $invoiceResonse){
                if( strtolower($invoiceResonse->getStatusAttributeString()) == null){
                    $getInvoiceData->invoice_name = $getInvoiceData->invoice_name;
                    $getInvoiceData->invoice_number = $invoiceResonse->getInvoiceNumber();
                    $getInvoiceData->xero_invoice_id = $invoiceResonse->getInvoiceId();
                    $getInvoiceData->invoice_type = $invoiceResonse->getType(); 
                    $getInvoiceData->invoice_status = $invoiceResonse->getStatus();
                    $getInvoiceData->amount_due = $invoiceResonse->getAmountDue();
                    $getInvoiceData->amount_paid = $invoiceResonse->getAmountPaid();
                    $getInvoiceData->sub_total = $invoiceResonse->getSubTotal();
                    $getInvoiceData->tax = $invoiceResonse->getTotalTax();
                    $getInvoiceData->total_discount = $invoiceResonse->getTotalDiscount();
                    $getInvoiceData->invoice_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDate(),'(',')'), 0, 10))->format('Y-m-d');
                    $getInvoiceData->due_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDueDate(),'(',')'), 0, 10))->format('Y-m-d');
                                  
                    foreach($invoiceResonse->getLineItems() as $key => $lineItems){
                        $invoiceLineItems['line_items'][$key]['line_item_id'] =  $lineItems->getLineItemId();
                        $invoiceLineItems['line_items'][$key]['description']  =  $lineItems->getDescription();
                        $invoiceLineItems['line_items'][$key]['quantity']     =  $lineItems->getQuantity();
                        $invoiceLineItems['line_items'][$key]['unit_amount']  =  $lineItems->getUnitAmount();
                        $invoiceLineItems['line_items'][$key]['tax_amount']   =  $lineItems->getTaxAmount();
                        $invoiceLineItems['line_items'][$key]['line_amount']  =  $lineItems->getLineAmount();
                        $invoiceLineItems['line_items'][$key]['gst_applied_on']  = isset(json_decode($getInvoiceData->line_items, true)[1]['gst_applied_on']) ? json_decode($getInvoiceData->line_items, true)[1]['gst_applied_on'] : "";
                    }
                    $getInvoiceData->line_items = json_encode($invoiceLineItems['line_items']);
                    $getInvoiceData->xero_sync = Invoice::SYNC_XERO_TRUE;
                }
            }
            if($getInvoiceData->update()){
                return $getInvoiceData;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            echo 'Exception when calling AccountingApi->getContactAttachmentById: ', $e->getMessage(), PHP_EOL;
        }
    }
    //saveInvoiceFrom the xero
    public function saveInvoiceFromTheXero($uuid){
        try {
            $getInvoiceFromXero = $this->apiInstance->getInvoice($this->xeroCredentials->getTenantId(), $uuid);
        } catch (\throwable $e) {
            $error = $e->getMessage();
            return [ 'status' => false, 'msg' => $error ];
        }
        return $getInvoiceFromXero;
    }

    //void invoice from xero 
    public function voidInvoiceFromXero(){
        $getInvoiceData = Invoice::where('xero_invoice_id', "6b5601fe-88f5-4d7f-aa0b-5264b4ce3b37")->first();
        
        //Dynamic Contact creation from xero
        $studentService =  new StudentService;
        $studentDetails = $studentService->getStudentEnrolmentByIdWithRealtionData($getInvoiceData->student_enroll_id);
        $setContactDetails = [];
        if(strtolower($studentDetails->sponsored_by_company) == "yes"){
            $setContactDetails['name']              = $studentDetails->company_name;
            $setContactDetails['first_name']        = $studentDetails->company_name;
            $setContactDetails['last_name']         = $studentDetails->company_uen;
            $setContactDetails['contact_no']        = $studentDetails->company_contact_person_number;
            $setContactDetails['email']             = $studentDetails->billing_email;
            $setContactDetails['company_sponsored'] = "yes";
        }else{
            $setContactDetails['name']              = $studentDetails->student->name;
            $setContactDetails['first_name']        = $studentDetails->student->name;
            $setContactDetails['last_name']         = "";
            $setContactDetails['contact_no']        = $studentDetails->mobile_no;
            $setContactDetails['email']             = $studentDetails->email;
            $setContactDetails['company_sponsored'] = "no";
        }
        
        $contectId = $this->createContactFromXero($setContactDetails);
        $invoice = $this->voidInvoiceSetup($getInvoiceData, $contectId);
        
        $updatedInvoices = $this->apiInstance->updateInvoice($this->xeroCredentials->getTenantId(),$getInvoiceData->xero_invoice_id, $invoice);
        dd($updatedInvoices);
    }

    //void invoice setup 
    public function voidInvoiceSetup($invoiceData, $contactId){
        $contact = new Contact;
        $contact->setContactId($contactId);
        $lineitems = [];
        $count = 0;
        $allLineItems = json_decode($invoiceData->line_items, true);
        foreach($allLineItems as $key => $values){
            switch (true) {
                case ($key == 0):
                    array_push($lineitems, $this->getLineItem($values)); 
                    break;
                
                case ($key == 1):
                    array_push($lineitems, $this->getLineItemTwo($values));
                    break;

                case ($key == 2):
                    array_push($lineitems, $this->getLineItemThree($values));
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        $invoice = new XeroInvoice;
        $invoice->setType(XeroInvoice::TYPE_ACCREC)
                ->setStatus(XeroInvoice::STATUS_VOIDED)
                ->setContact($contact)
                ->setLineAmountTypes(LineAmountTypes::EXCLUSIVE)
                ->setDueDate($invoiceData->due_date)
                ->setCurrencyCode("USD")
                ->setReference($invoiceData->invoice_name) 
                ->setLineItems($lineitems)
                ->setAmountDue($invoiceData->amount_due)
                ->setAmountPaid($invoiceData->amount_paid)
                ->setTotal($invoiceData->amount_paid)
                ->setTotalTax($invoiceData->tax);
        
        return $invoice;
    }

    public function getAllInvoiceNumbersFromXero(){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }
        $summaryOnly = true;
        $result = $this->apiInstance->getInvoices($this->xeroCredentials->getTenantId(), TRUE, "", $summaryOnly);
        $invoiceIds = [];
        foreach($result as $key => $value){
            if(substr($value->getInvoiceNumber(),0,3) == 'INV') {
                if($value->getStatus() != "VOIDED"){
                    $invoiceIds[] = $value->getInvoiceNumber();
                }
            }
        }
        return $invoiceIds;
    }

    public function getAllAccountsFromXero(){
        $accountWithCode = [];
        try {
            $allAccounts = $this->apiInstance->getAccounts($this->xeroCredentials->getTenantId());
        } catch (Exception $e) {
            
        }
        foreach($allAccounts as $accounts){
            $accountWithCode[$accounts->getCode()] = $accounts->getName(); 
        }
        return $accountWithCode;
    }

    public function getAllTaxRatesFromXero(){
        $taxRatesWithCode = [];
        try {
            $allTaxRates = $this->apiInstance->getTaxRates($this->xeroCredentials->getTenantId());
        } catch (Exception $e) {
            
        }
        foreach($allTaxRates as $taxrates){
            $taxRatesWithCode[$taxrates->getName()] = $taxrates->getEffectiveRate();
        }

        return $taxRatesWithCode;
    }

    public function getInvoiceFromXeroAndSave($invoiceUuid, $studentEnrollmentID){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
        }
        if( $connection['data']['error'] ) {
            setflashmsg("Connection lost", 0);
            return redirect()->back();
            // return $connection['data']['error'];
        }
        try {
            $result = $this->apiInstance->getInvoice($this->xeroCredentials->getTenantId(), $invoiceUuid);
            
            $studentService =  new StudentService;
            $courseService  =  new CourseMainService;
            $courseRunService = new CourseService;
            $commonService = new CommonService;
            $studentDetails = $studentService->getStudentEnrolmentByIdWithRealtionData($studentEnrollmentID);
            $courseDeatils  = $courseService->getCourseMainById($studentDetails->courseRun->course_main_id);
            $getCourseRun   = $courseRunService->getCourseById($studentDetails->course_id);

            $CourseRunSessionString = $commonService->makeSessionString($getCourseRun->session);
            $invoiceName  = $CourseRunSessionString . " : " . $courseDeatils->reference_number . " " . $courseDeatils->name . " - (" . $getCourseRun->tpgateway_id . ")";
            
            if($courseDeatils->gst_applied_on == self::COURSE_FEE){
                $gstAppliedOn = "course_fee";
            } else{
                $gstAppliedOn = "baseline";
            }

            $invoiceData =  new Invoice();
            $invoiceData->courserun_id = $getCourseRun->id;
            $invoiceData->student_enroll_id  = $studentEnrollmentID;
            $invoiceData->is_comapany  = ($studentDetails->sponsored_by_company == "yes") ? $studentDetails->company_sme : "no";
            foreach($result as $invoiceResonse){
                if( strtolower($invoiceResonse->getStatusAttributeString()) == null){
                    $invoiceData->invoice_name = $invoiceName;
                    $invoiceData->invoice_number = $invoiceResonse->getInvoiceNumber();
                    $invoiceData->xero_invoice_id = $invoiceResonse->getInvoiceId();
                    $invoiceData->invoice_type = $invoiceResonse->getType(); 
                    $invoiceData->invoice_status = $invoiceResonse->getStatus();
                    $invoiceData->amount_due = $invoiceResonse->getAmountDue();
                    $invoiceData->amount_paid = $invoiceResonse->getAmountPaid();
                    $invoiceData->sub_total = $invoiceResonse->getSubTotal();
                    $invoiceData->tax = $invoiceResonse->getTotalTax();
                    $invoiceData->total_discount = $invoiceResonse->getTotalDiscount() ? $invoiceResonse->getTotalDiscount() : 0;
                    $invoiceData->invoice_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDate(),'(',')'), 0, 10))->format('Y-m-d');
                    $invoiceData->due_date = Carbon::createFromTimestamp(substr($this->dateFormate($invoiceResonse->getDueDate(),'(',')'), 0, 10))->format('Y-m-d');
                    
                    foreach($invoiceResonse->getLineItems() as $key => $lineItems){
                        $invoiceLineItems['line_items'][$key]['line_item_id'] =  $lineItems->getLineItemId();
                        $invoiceLineItems['line_items'][$key]['description']  =  $lineItems->getDescription();
                        $invoiceLineItems['line_items'][$key]['quantity']     =  $lineItems->getQuantity();
                        $invoiceLineItems['line_items'][$key]['unit_amount']  =  $lineItems->getUnitAmount();
                        $invoiceLineItems['line_items'][$key]['tax_amount']   =  $lineItems->getTaxAmount();
                        $invoiceLineItems['line_items'][$key]['line_amount']  =  $lineItems->getLineAmount();
                        $invoiceLineItems['line_items'][$key]['gst_applied_on']  = $gstAppliedOn;
                    }
                    $invoiceData->line_items = json_encode($invoiceLineItems['line_items']);
                    $invoiceData->xero_sync = Invoice::SYNC_XERO_TRUE;
                }
            }
            if($invoiceData->save()) {
                return $invoiceData;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            if($e->getCode() == 404){
                return null;
            }
        }
    }

    public function payFeesOnXero($invoiceId, $amount, $student_enrolments_id, $reference = null, $paymentMode = null, $paymentDate = null){

        //Get Bank Accounts
        $where = 'Status=="ACTIVE" AND Type=="BANK"';
        $accounts = $this->apiInstance->getAccounts($this->xeroCredentials->getTenantId(), null, $where);
        // \Log::info(print_r($accounts, true));
        $bankAccount = [];
        foreach($accounts as $account){
            $bankAccount[$account->getBankAccountNumber()] = $account->getAccountID();
        }
        
        // "002" => "--Miscellaneous Account"
        // "0908007006543" => "--Business Bank Account"
        // "001" => "--Stripe Test"
        // switch ($paymentMode) {
        //     case 1:
        //         $bankAccount = $bankAccount["0908007006543"];
        //         break;
        //     case 2:
        //         $bankAccount = $bankAccount["0908007006543"]; 
        //         break;
        //     case 3:
        //         $bankAccount = $bankAccount["0908007006543"];
        //         break;
        //     case 4:
        //         $bankAccount = $bankAccount["002"];
        //         break;
        //     case 5:
        //         $bankAccount = $bankAccount["001"];
        //         break;
        //     case 6:
        //         $bankAccount = $bankAccount["001"];
        //         break;
        //     case 7:
        //         $bankAccount = $bankAccount["001"];
        //         break;
        //     case 8:
        //         $bankAccount = $bankAccount["002"];
        //         break;
        //     case 9:
        //         $bankAccount = $bankAccount["002"];
        //         break;
        //     default:
        //         $bankAccount = "e3fd762e-c6e2-4e40-a64a-15fc1f7919a4";
        // }

        switch ($paymentMode) {
            case 1:
                $bankAccount = $bankAccount["******0240"];
                break;
            case 2:
                $bankAccount = $bankAccount["******0240"]; 
                break;
            case 3:
                $bankAccount = $bankAccount["******0240"];
                break;
            case 4:
                $bankAccount = $bankAccount["PettyCash"];
                break;
            case 5:
                $bankAccount = $bankAccount["accounts@equinetacademy.com"];
                break;
            case 6:
                $bankAccount = $bankAccount["x-acct_1B3fzGIerfLb0aH8"];
                break;
            case 7:
                $bankAccount = $bankAccount["x-acct_1B3fzGIerfLb0aH8"];
                break;
            case 8:
                $bankAccount = $bankAccount["SkillsFutureCredits"];
                break;
            case 9:
                $bankAccount = $bankAccount["PSEA"];
                break;
            default:
                $bankAccount =  $bankAccount["******0240"];
        }
        
        $accountId = $bankAccount;
        if($paymentDate) {
            $dateValue = $paymentDate;
        } else {
            $dateValue = Carbon::today()->format('Y-m-d');
        }
        $invoice = new XeroInvoice;
        $invoice->setInvoiceID($invoiceId);

        // $allInvoice = Invoice::where('xero_invoice_id', $invoiceId)->get();
        // dd($allInvoice);
        $account = new Account;
        $account->setAccountID($accountId);

        $payment = new Payment;
        $payment->setInvoice($invoice);
        $payment->setAccount($account);
        $payment->setAmount($amount);
        $payment->setReference($reference);
        $payment->setDate($dateValue);
        
        try {
            \Log::info("create payment start =====>>>>");
            $result = $this->apiInstance->createPayment($this->xeroCredentials->getTenantId(), $payment);
            // dd($result->getPayments()[0]->getPaymentId());

            Log::info(print_r($result, true));
            foreach($result as $updatedInvoice) {
                Log::info("StudentEnrId ===>>" . $student_enrolments_id);
                Log::info("Xero Id ===>>" . $invoiceId);
                $updateInvoice = Invoice::where(['xero_invoice_id'=> $invoiceId, 'student_enroll_id' => $student_enrolments_id])->first();
                Log::info(print_r($updateInvoice, true));

                if($updateInvoice){
                    $updateInvoice->invoice_status = $updatedInvoice->getInvoice()->getStatus();
                    $updateInvoice->amount_paid    = $updatedInvoice->getInvoice()->getAmountPaid();
                    $updateInvoice->sub_total      = $updatedInvoice->getInvoice()->getSubTotal();
                    $updateInvoice->amount_due     = $updatedInvoice->getInvoice()->getAmountDue();
                    $updateInvoice->save();
                                            // ->update([
                                            //     "invoice_status" => $updatedInvoice->getInvoice()->getStatus(),
                                            //     "amount_paid"    => $updatedInvoice->getInvoice()->getAmountPaid(),
                                            //     "sub_total"      => $updatedInvoice->getInvoice()->getSubTotal(),
                                            //     "amount_due"     => $updatedInvoice->getInvoice()->getAmountDue(),
                                            // ]);
                    $studentEnrolData = StudentEnrolment::find($student_enrolments_id);
                    $studentEnrolData->xero_due_amount  = $updatedInvoice->getInvoice()->getAmountDue();
                    $studentEnrolData->xero_paid_amount = $updatedInvoice->getInvoice()->getAmountPaid();

                    if($studentEnrolData->xero_due_amount == 0){
                        if($studentEnrolData->xero_paid_amount == $studentEnrolData->amount){
                            $studentEnrolData->payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
                            // $studentEnrolData->save();
                        }
                    }
                } else {
                    Log::info("Invoice not found");
                }
                \Log::info("create payment out =====>>>>");
            }
            if($updateInvoice){
                \Log::info("update student enrolment =====>>>>");
                $studentEnrolData->update();
                return $result;
            }
        }  
        catch (Exception $e) { 
            dd($e);
        }
    }

    public function canclePaymentXero($paymentId){
        $paymentDelete = new PaymentDelete;
        $paymentDelete->setStatus('DELETED');

        $paymentDetails = TmsPayment::where('xero_pay_id', $paymentId)->first();
        $studentDetails = StudentEnrolment::find($paymentDetails->student_enrolments_id);
        $invoiceDetails = Invoice::where(['student_enroll_id' => $paymentDetails->student_enrolments_id,
                                          'invoice_number' => $studentDetails->xero_invoice_number])
                                    ->first();

        try {
            $result = $this->apiInstance->deletePayment($this->xeroCredentials->getTenantId(), $paymentId, $paymentDelete);
            $resultResponse = "";
            foreach($result as $updatedInvoice) {
                $resultResponse = $updatedInvoice->getStatusAttributeString();

                //invoice payment updates
                $invoiceDetails->amount_paid    = $updatedInvoice->getInvoice()->getAmountPaid();
                $invoiceDetails->sub_total      = $updatedInvoice->getInvoice()->getSubTotal();
                $invoiceDetails->amount_due     = $updatedInvoice->getInvoice()->getAmountDue();
                $invoiceDetails->save();

                $studentDetails->xero_due_amount  = $updatedInvoice->getInvoice()->getAmountDue();
                $studentDetails->xero_paid_amount = $updatedInvoice->getInvoice()->getAmountPaid();

            }
            if($invoiceDetails){
                $studentDetails->update();
            }

        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->deletePayment: ', $e->getMessage(), PHP_EOL;
        }
        
        if($resultResponse) {
            return false;
        } else {
            return true;
        }
    }

    public function getBrandingThemesListFromXero(){
        $connection = $this->checkConnection();
        if( !$connection['status'] ) {
            return NULL;
        }
        if( $connection['data']['error'] ) {
            return $connection['data']['error'];
        }
        $result = $this->apiInstance->getBrandingThemes($this->xeroCredentials->getTenantId());
        return $result->getBrandingThemes();
    }
}
