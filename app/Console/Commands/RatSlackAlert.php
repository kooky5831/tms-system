<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use App\Notifications\RatSlackNotification;

class RatSlackAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:ratslackalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send Rat Slack Alert to Slack channel';

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
        $today = Carbon::today()->format('Y-m-d');
        $allCourses = Course::with('courseMain')->where('course_start_date' , '>=' , $today)->get();
        foreach($allCourses as $course) {
            $notifyData = [];
            $count = $course->courseActiveEnrolments()->count();
            $courseDetails = $course->courseMain->name;
            $startDate = $course->course_start_date;
            $endDate = $course->course_end_date;
            if($count == 0 ){
                $notifyData = array(
                    'name' => $courseDetails,
                    'start_date' => date("d M Y",strtotime($startDate)),
                    'end_date' => date("d M Y",strtotime($endDate)),
                );
            }

            if(!empty($notifyData))
            {
                Notification::route('slack', getenv('SLACK_HOOK'))->notify(new RatSlackNotification($notifyData));
            }
        }

        $this->info('Successfully sent alert to slack channel.');
    }
}
