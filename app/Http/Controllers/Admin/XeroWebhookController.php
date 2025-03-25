<?php

namespace App\Http\Controllers\Admin;

use Webfox\Xero\Webhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use XeroApi\XeroPHP\Models\Accounting\Contact;
use XeroApi\XeroPHP\Models\Accounting\Invoice;
use App\Http\Controllers\Controller;
use App\Models\XeroInvoiceNumber;
use Illuminate\Support\Facades\Log;
class XeroWebhookController extends Controller
{
    public function __invoke(Request $request, Webhook $webhook)
    {

        if (!$webhook->validate($request->header('x-xero-signature'))) {
            // We can't use abort here, since Xero expects no response body
            return response('', Response::HTTP_UNAUTHORIZED);
        }
        
        foreach ($webhook->getEvents() as $event) {
            if ($event->getEventType() === 'CREATE' && $event->getEventCategory() === 'INVOICE') {
                $this->invoiceCreated($request, $event->getResource());
            } 
            
            // Invoice webhooks call for events 
            // elseif ($event->getEventType() === 'CREATE' && $event->getEventCategory() === 'CONTACT') {
            //     $this->contactCreated($request, $event->getResource());
            // } elseif ($event->getEventType() === 'UPDATE' && $event->getEventCategory() === 'INVOICE') {
            //     // $this->invoiceUpdated($request, $event->getResource());
            // } elseif ($event->getEventType() === 'UPDATE' && $event->getEventCategory() === 'CONTACT') {
            //     $this->contactUpdated($request, $event->getResource());
            // }
        }

        return response('', Response::HTTP_OK);
    }

    protected function invoiceCreated(Request $request, Invoice $invoice)
    {   
        $invoiceNumber = $invoice->getInvoiceNumber();
        // Log::info("======== WebHookCall Start =======");
        // Log::info(print_r($invoice, true));
        // Log::info("======== WebHookCall End =======");
        XeroInvoiceNumber::updateOrCreate([
            'xero_invoice_number'   => $invoiceNumber,
        ],[
            'xero_invoice_number'   => $invoiceNumber,
        ]);
        Log::info("Here is invoice created webhooks one");
        // Log::info($invoice->getInvoiceNumber());
    }

    protected function contactCreated(Request $request, Contact $contact)
    {
    }

    protected function invoiceUpdated(Request $request, Invoice $invoice)
    {
        // Log::info("======== WebHookCall Start =======");
        // Log::info(print_r($invoice, true));
        // Log::info("======== WebHookCall End =======");
        // XeroInvoiceNumber::updateOrCreate([
        //     'xero_invoice_number'   => $invoice->getInvoiceNumber(),
        // ],[
        //     'xero_invoice_number'   => $invoice->getInvoiceNumber(),
        // ]);
        // Log::info("Here is invoice 2");
        // Log::info($invoice->getInvoiceNumber());
    }

    protected function contactUpdated(Request $request, Contact $contact)
    {
    }

}