<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use App\Services\XeroService;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Settings;
use App\Models\XeroInvoiceNumber;
use App\Models\XeroBrandingTheme;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\XeroThemeStoreRequest;
use Log;

class XeroController extends Controller
{
    public $apiInstance;
    protected $xeroCredentials;

    function __construct(OauthCredentialManager $xeroCredentials, XeroService $xeroService) {
        $this->xeroCredentials = $xeroCredentials;
        $this->xeroService = $xeroService;
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

    public function index(Request $request, OauthCredentialManager $xeroCredentials)
    {
        try {
            // Check if we've got any stored credentials
            if ($xeroCredentials->exists()) {
                // dd($xeroCredentials->getTenantId());
                // dd($xeroCredentials->getTenants());
                /*
                 * We have stored credentials so we can resolve the AccountingApi,
                 * If we were sure we already had some stored credentials then we could just resolve this through the controller
                 * But since we use this route for the initial authentication we cannot be sure!
                 */
                $xero             = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
                $organisationName = $xero->getOrganisations($xeroCredentials->getTenantId())->getOrganisations()[0]->getName();
                $user             = $xeroCredentials->getUser();
                $username         = "{$user['given_name']} {$user['family_name']} ({$user['username']})";
            }
        } catch (\throwable $e) {
            // This can happen if the credentials have been revoked or there is an error with the organisation (e.g. it's expired)
            $error = $e->getMessage();
        }

        return view('admin.xero', [
            'connected'        => $xeroCredentials->exists(),
            'error'            => $error ?? null,
            'organisationName' => $organisationName ?? null,
            'username'         => $username ?? null
        ]);
    }

    public function createInvoiceXero()
    {

        // $contacts = $this->createContacts($this->xeroCredentials->getTenantId(), true);
        // dd($contacts);
        // $result = $this->apiInstance->getInvoices($this->xeroCredentials->getTenantId());
        // dd($result);

        $str = '';

        $lineitems = [];
        array_push($lineitems, $this->getLineItem());
        array_push($lineitems, $this->getLineItem2());
        // $xero             = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
        // $getContact = $this->apiInstance->getContact($this->xeroCredentials->getTenantId(), "af900f11-8a0d-4ae5-9f99-474081c233fd", true);
        $contactId = "075f524e-933a-4bc8-8f34-df8f7ed2be80";

        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactId($contactId);


        $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;

        $invoice
            // ->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(Carbon::now()->addDays(7)->format('Y-m-d'))
            ->setContact($contact)
            ->setLineItems($lineitems)
            ->setCurrencyCode("SGD")
            ->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
            // ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
            ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC)
            ->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
        $invoices = $this->apiInstance->createInvoices($this->xeroCredentials->getTenantId(),$invoice);
        // $invoices = $this->createInvoices($this->xeroCredentials->getTenantId(), $this->apiInstance);
        dd($invoices);
        return view('admin.xero-invoice', compact('invoices'));
    }

    public function createPaymentXero()
    {
        $str = '';

        // $newInv = $this->createInvoiceAccRec($xeroTenantId,$apiInstance,true);
        // $invoiceId = $newInv->getInvoices()[0]->getInvoiceID();
        $invoiceId = "4a977de9-38fd-451f-be83-07cb39829963";
        $newAcct = $this->getBankAccount();
        $accountId = $newAcct->getAccounts()[0]->getAccountId();

        //[Payments:Create]
        $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $invoice->setInvoiceID($invoiceId);

        $bankaccount = new \XeroAPI\XeroPHP\Models\Accounting\Account;
        $bankaccount->setAccountID($accountId);

        $payment = new \XeroAPI\XeroPHP\Models\Accounting\Payment;
        $payment->setInvoice($invoice)
            ->setAccount($bankaccount)
            ->setAmount("2.00");

        $result = $this->apiInstance->createPayment($this->xeroCredentials->getTenantId(),$payment);
        //[/Payments:Create]
        dd($result);
        $str = $str . "Create Payment ID: " . $result->getPayments()[0]->getPaymentID() . "<br>" ;

        if($returnObj) {
            return $result;
        } else {
            return $str;
        }
    }

    public function getBankAccount()
    {
        // READ only ACTIVE
        $where = 'Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE .'" AND Type=="' .  \XeroAPI\XeroPHP\Models\Accounting\Account::BANK_ACCOUNT_TYPE_BANK . '"';
        $result = $this->apiInstance->getAccounts($this->xeroCredentials->getTenantId(), null, $where);

        return $result;
    }

    public function getBrandingTheme()
    {
        $str = '';

        //[BrandingThemes:Read]
        // READ ALL
        $result = $this->apiInstance->getBrandingThemes($this->xeroCredentials->getTenantId());
        //[/BrandingThemes:Read]
        dd($result);
        $str = $str ."Get BrandingThemes: " . count($result->getBrandingThemes()) . "<br>";

        return $str;
    }

    public function getItem()
    {
        $str = '';

        //[Items:Read]
        // READ ALL
        $result = $this->apiInstance->getItems($this->xeroCredentials->getTenantId());
        //[/Items:Read]
        dd($result->getItems());
        $str = $str . "Get Items total: " . count($result->getItems()) . "<br>";

        if($returnObj) {
            return $result->getItems()[0];
        } else {
            return $str;
        }
    }

    public function createInvoices($xeroTenantId,$apiInstance,$returnObj=false)
    {
        $str = '';

        $lineitems = [];
        array_push($lineitems, $this->getLineItem());

        // $getContact = $this->getContact($xeroTenantId,$apiInstance,true);
        // $contactId = $getContact->getContacts()[0]->getContactId();
        $contactId = "075f524e-933a-4bc8-8f34-df8f7ed2be80";

        //[Invoices:Create]
        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactId($contactId);
        // $contact->setContactId("075f524e-933a-4bc8-8f34-df8f7ed2be80");

        $arr_invoices = [];

        $invoice_1 = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $invoice_1->setReference('Ref-' . $this->getRandNum())
                ->setDueDate(new \DateTime('2021-08-18'))
                ->setContact($contact)
                ->setLineItems($lineitems)
                ->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
                ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
                ->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
        array_push($arr_invoices, $invoice_1);

        $invoice_2 = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $invoice_2->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(new \DateTime('2021-05-07'))
            ->setContact($contact)
            ->setLineItems($lineitems)
            ->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED)
            ->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCPAY)
            ->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);
        array_push($arr_invoices, $invoice_2);

        $invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $invoices->setInvoices($arr_invoices);

        $result = $apiInstance->createInvoices($xeroTenantId,$invoices);
        //[/Invoices:Create]

        $str = $str ."Create Invoice 1 total amount: " . $result->getInvoices()[0]->getTotal() ." and Create Invoice 2 total amount: " . $result->getInvoices()[1]->getTotal() . "<br>" ;

        if($returnObj) {
            return $result;
        } else {
            return $str;
        }
    }

    public function getRandNum()
    {
        $randNum = strval(rand(1000,100000));

        return $randNum;
    }

    public function getLineItem()
    {
        $lineitem = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
        $lineitem->setDescription('SSG Training Grants')
            ->setQuantity(1)
            ->setUnitAmount(-799.2)
            ->setItemCode("303")
            ->setAccountCode("200");

        return $lineitem;
    }

    public function getLineItem2()
    {
        $lineitem = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
        $lineitem->setDescription('{Dates} - WSQ Digital Marketing Analytics Course')
            ->setQuantity(1)
            ->setUnitAmount(888.0)
            ->setItemCode("105")
            ->setAccountCode("200");

        return $lineitem;
    }

    public function getContact($xeroTenantId,$returnObj=false)
    {
        $str = '';

        $new = $this->createContacts($xeroTenantId, true);
        $contactId = $new->getContacts()[0]->getContactId();
        //[Contact:Read]
        $result = $this->apiInstance->getContacts($xeroTenantId, $contactId);
        //[/Contact:Read]

        $str = $str . "Get specific Contact name: " . $result->getContacts()[0]->getName() . "<br>";

        if($returnObj) {
            return $result;
        } else {
            return $str;
        }
    }

    public function updateContact($xeroTenantId)
    {
        $str = '';

        $new = $this->createContacts($xeroTenantId,true);
        $contactId = $new->getContacts()[0]->getContactId();

        //[Contact:Update]
        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setName("Goodbye" . $this->getRandNum());
        $result = $this->apiInstance->updateContact($xeroTenantId,$contactId,$contact);
        //[/Contact:Update]

        $str = $str . "Update Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

        return $str;
    }

    public function archiveContact($xeroTenantId)
    {
        $str = '';

        $new = $this->createContacts($xeroTenantId,true);
        $contactId = $new->getContacts()[0]->getContactId();

        //[Contact:Archive]
        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactStatus(\XeroAPI\XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ARCHIVED);
        $result = $this->apiInstance->updateContact($xeroTenantId,$contactId,$contact);
        //[/Contact:Archive]

        $str = $str . "Archive Contacts: " . $result->getContacts()[0]->getName() . "<br>" ;

        return $str;
    }

    public function getContacts($xeroTenantId,$returnObj=false)
    {
        $str = '';

        //[Contacts:Read]
        // read all contacts
        $result = $this->apiInstance->getContacts($xeroTenantId);

        // filter by contacts by status
        $where = 'ContactStatus=="ACTIVE"';
        $result2 = $this->apiInstance->getContacts($xeroTenantId, null, $where);
        //[/Contacts:Read]

        $str = $str . "Get Contacts Total: " . count($result->getContacts()) . "<br>";
        $str = $str . "Get ACTIVE Contacts Total: " . count($result2->getContacts()) . "<br>";

        if($returnObj) {
            return $result2;
        } else {
            return $str;
        }
    }

    public function createContacts($xeroTenantId,$returnObj=false)
    {
        $str = '';

        //[Contacts:Create]
        $arr_contacts = [];

        $contact_1 = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact_1->setName('FooBar' . $this->getRandNum())
                ->setFirstName("Foo" . $this->getRandNum())
                ->setLastName("Bar" . $this->getRandNum())
                ->setIsCustomer(true)
                ->setEmailAddress("ben.bowden@24locks.com");
        array_push($arr_contacts, $contact_1);

        $contact_2 = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact_2->setName('FooBar' . $this->getRandNum())
            ->setFirstName("Foo" . $this->getRandNum())
            ->setLastName("Bar" . $this->getRandNum())
            ->setEmailAddress("ben.bowden@24locks.com");
        array_push($arr_contacts, $contact_2);

        $contacts = new \XeroAPI\XeroPHP\Models\Accounting\Contacts;
        $contacts->setContacts($arr_contacts);

        $result = $this->apiInstance->createContacts($xeroTenantId,$contacts);
        //[/Contacts:Create]

        $str = $str ."Create Contact 1: " . $result->getContacts()[0]->getName() ." --- Create Contact 2: " . $result->getContacts()[0]->getName() . "<br>";

        if($returnObj) {
            return $result;
        } else {
            return $str;
        }
    }

    public function createContactsXero()
    {
        $str = '';

        //[Contacts:Create]
        $arr_contacts = [];

        $contact_1 = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact_1->setName('Salmankhan' . $this->getRandNum())
                ->setFirstName("Salman" . $this->getRandNum())
                ->setLastName("Khan" . $this->getRandNum())
                ->setIsCustomer(true)
                ->setEmailAddress("salman@mail.com");
        array_push($arr_contacts, $contact_1);

        // $contact_2 = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        // $contact_2->setName('FooBar' . $this->getRandNum())
        //     ->setFirstName("Foo" . $this->getRandNum())
        //     ->setLastName("Bar" . $this->getRandNum())
        //     ->setEmailAddress("ben.bowden@24locks.com");
        // array_push($arr_contacts, $contact_2);

        $contacts = new \XeroAPI\XeroPHP\Models\Accounting\Contacts;
        $contacts->setContacts($arr_contacts);

        $result = $this->apiInstance->createContacts($this->xeroCredentials->getTenantId(), $contacts);
        //[/Contacts:Create]
        dd($result->getContacts()[0]->getContactId());
        dd($result);
        $str = $str ."Create Contact 1: " . $result->getContacts()[0]->getName();

        if($returnObj) {
            return $result;
        } else {
            return $str;
        }
    }

    public function getCurrencyXero()
    {
        $str = '';

        //[Currencies:Read]
        $result = $this->apiInstance->getCurrencies($this->xeroCredentials->getTenantId());
        dd($result);
        //[/Currencies:Read]

        $str = $str . "Get Currencies Total: " . count($result->getCurrencies()) . "<br>";

        if($returnObj) {
            return $result->getCurrencies()[0];
        } else {
            return $str;
        }

    }

    //New Implementation xero start here 26-06
    public function createXeroContact(){
        // $contactDetail['name'] = "Test Company 1";
        // $contactDetail['contact_no'] = "83393959";
        // $contactDetail['email'] = "dustin@equinetacademy.com";
        // $contactDetail['company_sponsored'] = "yes";
        $this->xeroService->createContactFromXero();
    }
    
    public function createXeroInvoice($invoiceId){
        Log::info("Create XeroController IN");
        $responseData = $this->xeroService->createInvoiceFromXero($invoiceId, true);
        Log::info("Create XeroController IN Response Data");
        if($responseData == true){
            Log::info("Create XeroController IN Response True");
            setflashmsg("Invoice successfully created", 1);
            return redirect()->back();
            // return true;
        }else{
            if(auth()->user()->role == 'superadmin') {
                Log::info("Create XeroController IN Response False");
                setflashmsg("Invoice not sync please check contact details and other details", 0);
                return redirect()->back();
            }
            Log::info("Create XeroController IN Response False");
            return false;
        }
    }
    
    public function editXeroInvoice($id){
        // $invoiceData = $this->xeroService->getInvoiceDataById($id);
        // $updatedInvoice = $this->xeroService->updateInvoiceFromXeroById($invoiceData);
        // $data = $this->courseService->getCourseRunFullDetailsById($invoiceData->courserun_id);
        // $courseMainService = new \App\Services\CourseMainService;
        // $courseMainData = $courseMainService->getCourseMainById($data->course_main_id);
        // $allEnrolledStudent =  $data->courseActiveEnrolments;
        // // dd(json_decode($invoiceData->line_items, true));
        // $invoiceStudentList = json_decode($invoiceData->line_items, true);
        // foreach($invoiceStudentList as $value){
        //     if($value['quantity'] == 0){
        //         $studentList = explode(",", $value['description']);
        //     }elseif($value['unit_amount'] < 0){
        //         $grantAmount = str_replace('-','',$value['unit_amount']);
        //     }
        // }
        // return view('admin.invoice.edit-invoice', compact('invoiceData', 'allEnrolledStudent', 'studentList', 'courseMainData', 'grantAmount'));
    }

    public function updateXeroInvoice($uuid){
        $updatedInvoice = $this->xeroService->updateInvoiceFromXeroById($uuid);
        if($updatedInvoice == true){
            setflashmsg("Successfully updated from the xero", 1);
            return redirect()->back();
        }else{
            setflashmsg("Something went wrong", 0);
            return redirect()->back();
        }
    }
    
    public function getXeroInvoice($invoiceNumber){
        $this->xeroService->getInvoiceFromXero($invoiceNumber);
    }
    
    public function voidXeroInvoice(){
        $this->xeroService->voidInvoiceFromXero();
    }

    //New Implementation xero end here 26-06 

    public function getInvoices(Request $req){
        // $xeroNumber = $this->xeroService->getAllInvoiceNumbersFromXero();
        // foreach ($xeroNumber as $key => $invoice) {
            //     $results[] = ['id' => $invoice, 'text' => $invoice];
            // }
        $query = $req->get('q');
        $results = [];
        $ret = XeroInvoiceNumber::select('xero_invoice_number AS text')->where('xero_invoice_number', 'like' ,'%'.$query.'%')->get();
        foreach ($ret as $invoice) {
            $results[] = ['id' => $invoice->text, 'text' => $invoice->text];
        }
        // return $ret;
        return $results;
    }

    public function getAllAccounts(){
        $allAccounts = $this->xeroService->getAllAccountsFromXero();
        return $allAccounts;
    }

    public function getAllTaxRates(){
        $taxRates = $this->xeroService->getAllTaxRatesFromXero();
        return $taxRates;
    }

    public function setXeroCodes(Request $request){
        foreach($request->all() as $key => $value){
            if($key != "_token"){
                Settings::updateOrCreate(
                    [
                        "name" => $key,
                    ],[
                        "val" => $value,
                        "group" => "xero",
                    ]
                );
            }
        }
        return true;
    }

    public function getAllSavedCode(){
        $allXeroCodes = Settings::where('group', 'xero')->get()->toArray();
        return $allXeroCodes;
    }

    public function getAllBrandingTheme(){
        $getThemes = $this->xeroService->getBrandingThemesListFromXero();
        foreach($getThemes as $theme){
            $getData = XeroBrandingTheme::updateOrCreate([
                'branding_theme_id' => $theme->getBrandingThemeId()
            ],[
                'branding_theme_id' => $theme->getBrandingThemeId(),
                'name' => $theme->getName(),
                'logo_url' => $theme->getLogoUrl(),
                'type' => $theme->getType(),
                'sort_order' => $theme->getSortOrder(),
                'created_date_utc' => Carbon::createFromTimestamp(substr($this->dateFormate($theme->getCreatedDateUtc(),'(',')'), 0, 10))->format('Y-m-d'),
            ]);
        }
        
        if($getData){
            setflashmsg(trans('msg.getThemeSuccess'), 1);
            return redirect()->back();
        } else {
            setflashmsg(trans('msg.getThemeFail'), 0);
            return redirect()->back();
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

    public function settingTheme(Request $request){
        if (! Gate::allows('xero-theme-setting')) { return abort(403); }
        $brandingThemes = XeroBrandingTheme::get();
        // $themeSetting   = XeroThemeSetting::get();
        if( $request->method() == 'POST') {
            $storeSettings = $request->get('brand_theme_data');
            if(is_array($storeSettings)){
                foreach($storeSettings as $store) {
                    $updateSetting = XeroBrandingTheme::updateOrCreate([
                        'branding_theme_id' => $store['branding_theme_id'],
                    ],[
                        'applied_on' => $store['applied_on'],
                    ]);
                }
                if($updateSetting){
                    setflashmsg(trans('msg.getThemeUpdateSuccess'), 1);
                    return redirect()->back();
                } else {
                    setflashmsg(trans('msg.getThemeUpdateFail'), 0);
                    return redirect()->back();
                }
            } else {
                setflashmsg(trans('msg.getThemeUpdateFail'), 0);
                return redirect()->back();
            }
        }
        return view('admin.xerothemesettings.index', compact('brandingThemes'));
    }

}
