<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentEnrolment;

class UpdateMasterInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-master-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for updating master invoice field on student enrollment table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentEnrol = StudentEnrolment::where('master_invoice', 1)->take(200)->get();

        foreach($studentEnrol as $enrol){            
            $updateMaster = StudentEnrolment::find($enrol->id);
            $updateMaster->master_invoice = 0;
            $updateMaster->update();
        }
        dd('done-200');
    }
}
