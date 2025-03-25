<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SoftBookingExpiredJob;
use App\Console\Commands\LowIntakeAlert;
use App\Console\Commands\RatSlackAlert;
use App\Console\Commands\OperationsAlert;
use App\Console\Commands\CourseReminderAlert;
use App\Console\Commands\ScheduleDaysAfter;
use App\Console\Commands\ScheduleTimeOfMonth;
use App\Console\Commands\ScheduleDaysBefore;
use App\Console\Commands\ScheduleDayOfWeek;
use App\Console\Commands\ScheduleDeleteExceptions;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\DispatchGrantStatusJob;
use App\Console\Commands\TpgCallForGrants;
use App\Console\Commands\CopyStudentDataToUser;
use App\Console\Commands\GenerateAssessmentForCourses;
use App\Console\Commands\GetCompletedCourserunEnrolments;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SoftBookingExpiredJob::class,
        LowIntakeAlert::class,
        RatSlackAlert::class,
        OperationsAlert::class,
        CourseReminderAlert::class,
        ScheduleDayOfWeek::class,
        ScheduleDaysBefore::class,
        ScheduleTimeOfMonth::class,
        ScheduleDaysAfter::class,
        DispatchGrantStatusJob::class,
        ScheduleDeleteExceptions::class,
        TpgCallForGrants::class,
        CopyStudentDataToUser::class,
        GenerateAssessmentForCourses::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(SoftBookingExpiredJob::class)->dailyAt('01:00');
        $schedule->command('send:adminnotification')->dailyAt('03:00');
        // testing
        // $schedule->command(SoftBookingExpiredJob::class)->everyMinute();

        // Task Creation from Course Triggers
        $schedule->command(ScheduleDayOfWeek::class)->dailyAt('06:00');
        $schedule->command(ScheduleDaysBefore::class)->dailyAt('06:00');
        $schedule->command(ScheduleTimeOfMonth::class)->dailyAt('06:00');
        $schedule->command(ScheduleDaysAfter::class)->dailyAt('06:00');
        
        // Testing of Slack Notifications on #test channel
        $schedule->command(LowIntakeAlert::class)->dailyAt('08:00');
        //$schedule->command(RatSlackAlert::class)->dailyAt('08:00');
        $schedule->command(OperationsAlert::class)->dailyAt('08:00');
        $schedule->command(CourseReminderAlert::class)->dailyAt('08:00');

        // Daily Database Backup 
        $schedule->command('backup:run --only-db')->dailyAt('01:30');

        // Remove 72 hours older entries of Exception Logs
        $schedule->command(ScheduleDeleteExceptions::class)->daily();

        // Dispatch Grant status check job
        $schedule->command(DispatchGrantStatusJob::class)->everyThirtyMinutes();

        //Fetch all grant response
        $schedule->command(TpgCallForGrants::class)->everyFiveMinutes();

        //
        $schedule->command(GenerateAssessmentForCourses::class)->everyMinute();

        //Add users as a student role
        // $schedule->command(CopyStudentDataToUser::class)->everyFifteenMinutes();

        //ActiveCampaings cron job
        $schedule->command(GetCompletedCourserunEnrolments::class)->dailyAt('00:15');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}