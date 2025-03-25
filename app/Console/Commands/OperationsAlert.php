<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use App\Notifications\OperationNotification;
use App\Notifications\MarketingNotification;


class OperationsAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:operationsalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send Operation & Marketing Alert to Slack channel';

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
        $sevenDaysLater = Carbon::now()->addDays(7)->format('Y-m-d');
        $twoWeeks = Carbon::now()->addDays(14)->format('Y-m-d');
        $oneMonth = Carbon::now()->addDays(28)->format('Y-m-d');
        
        /*$allCourses = Course::with('courseMain')->whereHas(
            'courseMain', function ($query) {
                $query->where('course_type_id', '=', 3);
            }
        )->where('course_start_date' , '>=' , $today)->where('is_published', 1)->get();*/

        $allCourses = Course::with('courseMain')->whereHas(
            'courseMain', function ($query) {
                $query->where('course_type_id', '=', 3);
            }
        )->where(function($q2) use($sevenDaysLater, $twoWeeks, $oneMonth ) {
            
            $q2->where('course_start_date' , '=' , $sevenDaysLater)
            ->orWhere('course_start_date' , '=' , $twoWeeks)
            ->orWhere('course_start_date' , '=' , $oneMonth);

        })->where('course_start_date' , '>=' , $today)
        ->whereIn('is_published', array(1, 0))->get();

        foreach($allCourses as $course) {
            $notifyData = [];
            $courseDetails = $course->courseMain->name;
            $courseType = $course->courseMain->course_type_id;
            $startDate = $course->course_start_date;
            $endDate = $course->course_end_date;
            $intakeSize = $course->courseActiveEnrolments()->count();
            //if($courseType == 3){
                if($startDate == $sevenDaysLater && $intakeSize == 0){
                    $notifyData = array(
                        'name' => $courseDetails,
                        'start_date' => date("d M Y",strtotime($startDate)),
                        'end_date' => date("d M Y",strtotime($endDate)),
                    );
                    if(!empty($notifyData))
                    {
                        Notification::route('slack', getenv('SLACK_HOOK_LOW_REG'))->notify(new OperationNotification($notifyData));
                    }
                }
                else if($startDate == $twoWeeks && $intakeSize == 0){
                    $notifyData = array(
                        'name' => $courseDetails,
                        'start_date' => date("d M Y",strtotime($startDate)),
                        'end_date' => date("d M Y",strtotime($endDate)),
                        'intake' => $intakeSize,
                    );
                    if(!empty($notifyData))
                    {
                        Notification::route('slack', getenv('SLACK_HOOK_LOW_REG'))->notify(new MarketingNotification($notifyData));
                    } 
                }
                else if($startDate == $oneMonth && $intakeSize <= 4){
                    $notifyData = array(
                        'name' => $courseDetails,
                        'start_date' => date("d M Y",strtotime($startDate)),
                        'end_date' => date("d M Y",strtotime($endDate)),
                        'intake' => $intakeSize,
                    );
                    if(!empty($notifyData))
                    {
                        Notification::route('slack', getenv('SLACK_HOOK_LOW_REG'))->notify(new MarketingNotification($notifyData));
                    } 
                } 
            //}
        }

        $this->info('Successfully sent alert to slack channel.');
    }
}
