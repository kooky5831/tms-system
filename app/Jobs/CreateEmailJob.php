<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use App\Mail\EmailTriggersForCourse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AdminTaskMailLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $attachment;
    /**
     * @var Send to Email
    */
    private $sendToEmail;

    /**
     * @var Email For (Student or Company)
    */
    private $emailFor;

    /**
     * @var Email Content
    */
    private $emailContent;

    /**
     * @var Student Name
    */
    private $studentName;

    /**
     * @var Course
    */
    private $course;

    public $timeout = 0;

    /**
     * @var Course Name
    */
    private $courseName;

    /**
     * @var Course Id
    */
    private $courseId;

        /**
     * @var AdminTaskMailLogService 
    */
    protected  $adminTaskMailLogService;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sendToEmail, $emailContent, $emailFor, $attachment = null, $courseId = null)
    // public function __construct($sendToEmail, $emailContent, $studentName, $course, $courseName)
    {
        $adminTaskMailLogService = new AdminTaskMailLogService ;
        $this->sendToEmail  = $sendToEmail;
        $this->emailContent = $emailContent;
        $this->emailFor = $emailFor;
        $this->attachment = $attachment;
        $this->courseId = $courseId;
        $this->adminTaskMailLogService = $adminTaskMailLogService;
        // $this->studentName  = $studentName;
        // $this->course       = $course;
        // $this->courseName   = $courseName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // $sendEmailTo = "vishal@equinetacademy.com";
            // $sendEmailTo = "register@equinetacademy.com";
            // Mail::to($this->sendToEmail)
            // Mail::to("vishal@equinetacademy.com")
            $attachmentPath = !empty($this->attachment) ? Storage::path('public') . "/" . $this->attachment : null;
            $bccEmail = 'register@equinetacademy.com';
            $subject = 'Course Reminder';
            if( $this->emailFor == 'company' ) {
                $adminTaskMailLogsData = [
                    'mail_logs_subject' => $subject,
                    'mail_logs_from' => 'System',
                    'mail_logs_to' => $this->emailFor,
                    'mail_logs_cc' => $this->sendToEmail,
                    'mail_logs_bcc' => $bccEmail,
                    'mail_logs_content' => $this->emailContent,
                    'mail_logs_time' => Carbon::now(),
                ];
                $this->adminTaskMailLogService->createAdminTaskMailLog($adminTaskMailLogsData);
                \Log::info("Company Sponser Student email");
                \Log::info($this->sendToEmail);
                Mail::cc($this->sendToEmail)
                // Mail::cc($sendEmailTo)
                ->bcc($bccEmail)
                ->send(new EmailTriggersForCourse($this->emailContent, $attachmentPath));
                Storage::deleteDirectory(config('uploadpath.course_certificate') . $this->courseId);
            } else {
                $adminTaskMailLogsData = [
                    'mail_logs_subject' => $subject,
                    'mail_logs_from' => 'System',
                    'mail_logs_to' => $this->sendToEmail,
                    'mail_logs_cc' => "",
                    'mail_logs_bcc' => $bccEmail,
                    'mail_logs_content' => $this->emailContent,
                    'mail_logs_time' => Carbon::now(),
                ];
                $this->adminTaskMailLogService->createAdminTaskMailLog($adminTaskMailLogsData);
                \Log::info("Self Sponser Student email");
                \Log::info($this->sendToEmail);
                Mail::to($this->sendToEmail)
                ->bcc($bccEmail)
                ->send(new EmailTriggersForCourse($this->emailContent, $attachmentPath));
                // ->send(new EmailTriggersForCourse($this->emailContent, $this->studentName, $this->course, $this->courseName));
                Storage::deleteDirectory(config('uploadpath.course_certificate') . $this->courseId);
            }
        } catch (\Exception $e) {
            \Log::info("Error in email".$e->getMessage(), [$this->emailContent, $this->sendToEmail]);
        }
    }
}
