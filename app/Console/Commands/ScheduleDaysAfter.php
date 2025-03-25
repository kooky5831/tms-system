<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CourseTriggersService;
use App\Models\Course;
use App\Models\AdminTasks;
use App\Models\CourseRunTriggers;
use App\Jobs\CreateEmailJob;
use Illuminate\Support\Carbon;

class ScheduleDaysAfter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:scheduledaysafter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for creating task for Event Type Days After';

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
        $courseMains = $courseTriggersService->getAllCourseTriggersForEventWhen(4, NULL);

        // loop through it and get course run for specific date
        foreach ($courseMains as $courseMain) {
            $courses = $courseMain->courseMains->pluck('id')->toArray();
            $tag_courses = $courseMain->courseTags->pluck('courseMains.*.id')->flatten()->toArray();
            $all_courses = array_unique(array_merge($tag_courses, $courses));
            
            $searchDate = \Carbon\Carbon::now()->addDays($courseMain->no_of_days);
            // check if is there any course for today for this main course
            $courseRuns = Course::whereIn('course_main_id', $all_courses)
                            ->where('is_published', '!=',  Course::STATUS_CANCELLED)
                            ->whereDate('course_end_date', $searchDate)
                            ->with(['courseActiveEnrolmentsWithStudent'])
                            ->get();
            // loop through course run if found any
            if( !$courseRuns->isEmpty() ) {
                // dd($courseMain);
                // dd($courseRuns);
                foreach($courseRuns as $courseRun) {
                    $adminTask = new AdminTasks;
                    $adminTask->course_id = $courseRun->id;
                    $adminTask->task_type = $courseMain->event_type;

                    // check for task Type
                    if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_EMAIL ) {
                        $adminTask->template_name = $courseMain->template_name;
                        $adminTask->template_slug = $courseMain->template_slug;
                        // split Template name and slug
                    } else if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_SMS ) {
                        // template for SMS
                        $adminTask->sms_template_id = $courseMain->sms_template_id;
                    } else if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_TEXT ) {
                        $adminTask->task_text = $courseMain->task_text;
                    }
                    $adminTask->priority = $courseMain->priority;
                    $adminTask->created_by = 1;
                    $adminTask->updated_by = 1;
                    $adminTask->save();
                }
            }
        }
    }
}
