<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CourseTriggersService;
use App\Models\Course;
use App\Jobs\CreateSMSJob;
use Illuminate\Support\Carbon;

class ScheduleSMSWeekBefore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:courseremindersmsweekbefore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for sending Course Reminder SMS One Week Before';

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
        $courseTriggersService = new CourseTriggersService;
        $courseMains = $courseTriggersService->getAllCourseTriggersForSMS(1);
        $weekBefore = Carbon::now()->addDays(7)->format('Y-m-d');
        // dd($weekBefore);
        // loop through it and get course run for specific date
        foreach ($courseMains as $courseMain) {
            $courses = $courseMain->courseMains->pluck('id')->toArray();
            $tag_courses = $courseMain->courseTags->pluck('courseMains.*.id')->flatten()->toArray();
            $all_courses = array_unique(array_merge($tag_courses, $courses));

            // get SMS Template
            $smsTemplateMsg = $courseMain->smsTemplate->content;
            // dd($smsTemplateMsg);
            // get course run with enrolments
            $courseRuns = Course::whereIn('course_main_id', $all_courses)
                            ->whereDate('course_start_date', $weekBefore)->with(['courseActiveEnrolmentsWithStudent', 'courseMain'])->get();

            // loop through course run if found any
            if( !$courseRuns->isEmpty() ) {
                foreach($courseRuns as $courseRun) {
                    $courseName = $courseRun->courseMain->name;
                    $staffName = $courseRun->courseMain->trainers->pluck('name');
                    // now check if course run has any active enrollment
                    if( count($courseRun->courseActiveEnrolmentsWithStudent) ) {
                        // loop through every enrolment and add sms as job
                        foreach($courseRun->courseActiveEnrolmentsWithStudent as $enrolment) {
                            // dd($enrolment);
                            if( !empty($enrolment->mobile_no) ) {
                                // then create message
                                $msg = $smsTemplateMsg;
                                $msg = str_ireplace("{studentname}", $enrolment->student->name, $msg);
                                $msg = str_ireplace("{coursedate}", $courseRun->course_start_date, $msg);
                                $msg = str_ireplace("{coursename}", $courseName, $msg);
                                $msg = str_ireplace("{staffname}", $staffName, $msg);
                                $msg = str_ireplace("{coursemeetinglink}", $courseRun->course_link, $msg);
                                $msg = str_ireplace("{coursemeetingId}", $courseRun->meeting_id, $msg);
                                $msg = str_ireplace("{coursemeetingPwd}", $courseRun->meeting_pwd, $msg);
                                CreateSMSJob::dispatch($enrolment->mobile_no, $msg);
                            }
                        }
                    }
                }
            }
        }

        // $this->info('Successfully sent sms to students.');
    }
}
