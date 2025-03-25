<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use App\Notifications\CourseReminderNotification;
use App\Notifications\CourseStartedNotification;

class CourseReminderAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:coursereminderalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send Course Reminder Alert to Slack channel';

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
        
        /*$allCourses = Course::with('courseMain')
            ->where('course_start_date' , '<=' , $today)
            ->where('course_end_date' , '>=' , $today)
            ->where('is_published', 1)
            ->get();*/

        $allCourses = Course::with('courseMain')->whereHas(
            'courseMain', function ($query) {
                $query->where('course_mode_training', '=', 'online');
            }
        )->where('course_start_date' , '<=' , $today)
            ->where('course_end_date' , '>=' , $today)
            ->whereIn('is_published', array(1, 0))
            ->get();
        
        foreach($allCourses as $course) {
            $notifyData = [];
            $courseDetails = $course->courseMain->name;
            $courseMode = $course->courseMain->course_mode_training;
            $startDate = $course->course_start_date;
            $endDate = $course->course_end_date;
           
            //if($courseMode == 'online') {
                $notifyData = array(
                    'name' => $courseDetails,
                    'today' => date("d M Y",strtotime($today)),
                    'start_date' => date("d M Y",strtotime($startDate)),
                    'end_date' => date("d M Y",strtotime($endDate)),
                );

                if(!empty($notifyData)) {
                    Notification::route('slack', getenv('SLACK_HOOK_OPERATION'))->notify(new CourseReminderNotification($notifyData));
                    Notification::route('slack', getenv('SLACK_HOOK_OPERATION'))->notify(new CourseStartedNotification());
                } 
            //}
        }

        $this->info('Successfully sent alert to slack channel.');
    }
}
