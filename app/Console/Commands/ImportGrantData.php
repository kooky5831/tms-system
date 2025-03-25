<?php

namespace App\Console\Commands;

use Auth;
use App\Models\Grant;
use Illuminate\Console\Command;
use App\Models\StudentEnrolment;
use DB;

class ImportGrantData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:grant_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import all grant data from existing grantResponse column from Student Enrolments.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allEnrolments = StudentEnrolment::whereNotNull('grantResponse')->get();
        $grantRecord = new Grant;
        DB::table('grants')->truncate();
        if(!empty($allEnrolments)){
            foreach($allEnrolments as $enrolment) {
                $grants = json_decode($enrolment->grantResponse);
                if(!empty($grants->data)){
                    foreach( $grants->data as $grant ){
                        $grantRecord = new Grant;
                        $grantRecord->student_enrolment_id      = $enrolment->id;
                        $grantRecord->grant_refno               = $grant->referenceNumber;
                        $grantRecord->grant_status              = $grant->status;
                        $grantRecord->scheme_code               = $grant->fundingScheme->code;
                        $grantRecord->scheme_description        = $grant->fundingScheme->description;
                        $grantRecord->component_code            = $grant->fundingComponent->code;
                        $grantRecord->component_description     = $grant->fundingComponent->description;
                        $grantRecord->amount_estimated          = $grant->grantAmount->estimated;
                        $grantRecord->amount_paid               = $grant->grantAmount->paid;
                        $grantRecord->amount_recovery           = $grant->grantAmount->recovery;
                        $grantRecord->created_by                = 25;
                        $grantRecord->updated_by                = 25;
                        $grantRecord->save();
                    }
                }
            }
        }
    }
}
