<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XeroService;
use App\Models\XeroInvoiceNumber;

class GetXeroInvoiceNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-xero-invoice-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all invoice number and insert into the table';

    /**
     * Execute the console command.
     */
    public function handle(XeroService $xeroService)
    {
        //
        // $xeroNumber = $xeroService->getAllInvoiceNumbersFromXero();
        // foreach($xeroNumber as $invoiceNumber){
        //     XeroInvoiceNumber::updateOrCreate(
        //         ['xero_invoice_number' => $invoiceNumber],
        //         ['xero_invoice_number' => $invoiceNumber],
        //     );
        // }
    }
}
