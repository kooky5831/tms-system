<?php

namespace App\Jobs;

use Illuminate\Console\Command;
use DB;
use App\Models\CourseSoftBooking;
use Carbon\Carbon;

class SoftBookingExpiredJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:soft-booking-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Status of soft booking which are expired';

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            $today = Carbon::now();
            CourseSoftBooking::where('status', CourseSoftBooking::STATUS_PENDING)
                ->whereDate('deadline_date', '<', $today)
                ->chunkById(1000, function ($softBookings) {
                    foreach ($softBookings as $softBooking) {
                        //
                        try {
                            $booking = CourseSoftBooking::where('id', $softBooking->id)->first();
                            $booking->status = CourseSoftBooking::STATUS_EXPIRED;
                            $booking->save();
                        } catch (Exception $e) {
                            \Log::info("Error in soft booking expired job", [$softBooking]);
                        }
                    }
                });
        });
    }
}
