<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use App\Notifications\LowIntakeNotification;

class LowIntakeAlert extends Command
{

    use Notifiable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:lowintakealert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send Low Intake Alert to Slack channel';

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
        /* 
            Note:
            1- Single Course
            3 - Booster Session
            Booster Session = AMA sessions = Clinic sessions
        */
        $today = Carbon::today()->format('Y-m-d');
        $day14 = Carbon::now()->addDays(14)->format('Y-m-d');
        $day28 = Carbon::now()->addDays(28)->format('Y-m-d');

        /*$allCourses = Course::with('courseMain')->whereHas(
            'courseMain', function ($query) {
                $query->where('course_type_id', '!=', 3);
            }
        )->where('course_start_date' , '>=' , $today)->where('is_published', 1)->get();*/

        $allCourses = Course::with('courseMain')->whereHas(
            'courseMain', function ($query) {
                $query->where('course_type_id', '!=', 3);
            }
        )->where('course_start_date' , '>=' , $today)
        ->where(function($q2) use($day14, $day28) {
            $q2->where('course_start_date' , '=' , $day14)
            ->orwhere('course_start_date' , '=' , $day28);
        })->whereIn('is_published', array(1, 0))->get();

        foreach($allCourses as $course) {
            $notifyData = [];
            $startDate = $course->course_start_date;
            $endDate = $course->course_end_date;
            if($startDate == $day14 || $startDate == $day28 ){
                $intakeSize = $course->courseActiveEnrolments()->count();
                $courseDetails = $course->courseMain->name;
                $courseType = $course->courseMain->course_type_id;
                //if($intakeSize < 16 && courseType != "AMA"){
                if($intakeSize < 16){
                    $notifyData = array(
                        'name' => $courseDetails,
                        'start_date' => date("d M Y",strtotime($startDate)),
                        'end_date' => date("d M Y",strtotime($endDate)),
                        'intake' => $intakeSize
                    );
                }
            }
            if(!empty($notifyData))
            {
                Notification::route('slack', getenv('SLACK_HOOK_LOW_INTAKE'))->notify(new LowIntakeNotification($notifyData));
            }
        }
        

        $this->info('Successfully sent alert to slack channel.');
    }
}
