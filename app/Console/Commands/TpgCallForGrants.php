<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Grant;
use App\Models\StudentEnrolment;
use App\Models\GrantLog;
use App\Services\GrantService;
use Illuminate\Support\Facades\Log;

class TpgCallForGrants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tpg-call-for-grants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is TPG call for grant table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $message = "";
        $notSyncedData = Grant::whereIn('grant_status', ['Completed', 'Cancelled'])
                                ->whereNull('disbursement_date')
                                ->limit('25')
                                ->get();
        $grantService = new GrantService;
        $grants = [];
        if($notSyncedData){
            foreach($notSyncedData as $grantData){
                if($grantData){
                    $tpgCallforGrants = $grantService->fetchGrantStatusFromTPG($grantData->id);
                    $grants[] = $grantData->id . " == ". 'tpg call success';
                } else {
                    $message = "all grants are synced";        
                }
            }
        } else {
            $message = "all grants are synced";
        }
        
        if($grants){
            Log::info(print_r($grants, true));
        } else {
            Log::info($message);
        }
    }
}
