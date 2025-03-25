<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Grant;
use App\Models\StudentEnrolment;
use App\Models\GrantLog;
use Illuminate\Support\Facades\Log;

class DeleteDataGrantLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete-data-grant-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use only one time for delete data of grant logs if student enrolment status canclled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $allLogs = GrantLog::groupBy('student_enrolment_id')->get();
        $message = "";
        $data = [];
        foreach($allLogs as $grantLog){
            $enrolledStudent = StudentEnrolment::find($grantLog->student_enrolment_id);
            if($enrolledStudent){
                if(StudentEnrolment::STATUS_CANCELLED == $enrolledStudent->status){
                    $delEnrolmentOnLogs = GrantLog::where('student_enrolment_id', $enrolledStudent->id)->delete();
                    $data[] = $enrolledStudent->id ."==". "deleted";
                } else {
                    $message = "No record found !";
                }
            } else {
                $delEnrolmentOnLogs = GrantLog::where('student_enrolment_id', $grantLog->student_enrolment_id)->delete();
                $message = "Enrolment not found";
            }
        }
        Log::info(print_r($data, true));
    }
}
