<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\ErrorException;

class ScheduleDeleteExceptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-72hours-exception-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for removing 72 hourse older entries of Exception logs';

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
        ErrorException::where('created_at', '<=', Carbon::now()->subHours(72))->delete();
        $this->info('Successfully deleted older exception logs.');
    }
}
