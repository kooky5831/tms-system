<?php

namespace App\Services;

use App\Models\AdminTasksMailLog;
use Illuminate\Support\Facades\Log;


class AdminTaskMailLogService
{

    public function __construct(){
        $this->adminTaskMail_model = new AdminTasksMailLog;
    }

    public function createAdminTaskMailLog($adminTaskData){

        Log::info("----Start add admin task mail log----"); 
        $adminTaskData = AdminTasksMailLog::create($adminTaskData);

        if($adminTaskData){
            Log::info("Data Added in Admin Task mail log table");
            return true;
        }else{
            Log::info("Data doesn't Added in Admin Task mail log table");
            return false;
        }
    }

    public function getAllEmailLogsData($request){

        $taskMail =  $this->adminTaskMail_model->select('*');

        $name = $request->get('name');
        if( !empty($name) ){
            $taskMail->Where('mail_logs_subject', $name);
        }

        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        if( $startDate ) {
            $taskMail->whereDate('created_at', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $taskMail->whereDate('created_at', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $subject = $request->get('subject');
        if( !empty($subject) ){
            $taskMail->Where('mail_logs_subject', $subject);
        }

        $email_address = $request->get('email_address');
        if( !empty($email_address) ){
            $taskMail->Where('mail_logs_to', 'LIKE' , '%'. $email_address .'%');
        }

        return $taskMail;
    }

}