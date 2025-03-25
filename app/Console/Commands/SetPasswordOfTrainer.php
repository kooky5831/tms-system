<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\User;

class SetPasswordOfTrainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:set-password-of-trainer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $users = User::where('role', 'trainer')->get();
        $postFix = "!99";
        if($users){
            foreach($users as $trainer){
                $user = User::find($trainer->id);
                $trainerName = explode(' ', $user->name);
                $password = $trainerName[0] . $postFix;
                $user->password = Hash::make($password);
                $user->update();
            }
            echo "done";
        }
    }
}
