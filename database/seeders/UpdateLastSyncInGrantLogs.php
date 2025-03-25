<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Grant;

class UpdateLastSyncInGrantLogs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $allGrants = Grant::where('grant_status','Grant Processing')->get();
        if(!empty($allGrants)){
            $count = 0;
            foreach($allGrants as $grant){
                $count++;
                if($count <= 2400){
                    $grant->last_sync = Carbon::now()->subDays(2)->format('Y-m-d');
                } else if($count >= 2400 && $count <= 4800) {
                    $grant->last_sync = Carbon::now()->subDays(1)->format('Y-m-d');
                } else if($count >= 4800 && $count <= 7200 ) {
                    $grant->last_sync = Carbon::now()->subDays(0)->format('Y-m-d');
                } else if($count >= 7200 && $count <= 9600) {
                    $grant->last_sync = Carbon::now()->subDays(-1)->format('Y-m-d');
                }
                $grant->update();
            }
        }
    }
}
