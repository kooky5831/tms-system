<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Library\Sms;

class CreateSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Mobile Number
    */
    private $mobileNo;

    /**
     * @var Message
    */
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobileNo, $message)
    {
        $this->mobileNo = $mobileNo;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Sms::send(config('settings.country_code').$this->mobileNo, $this->message);
        } catch (\Exception $e) {
            \Log::info("Error in sms".$e->getMessage(), [$this->mobileNo, $this->message]);
        }
    }
}
