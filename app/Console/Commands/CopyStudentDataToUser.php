<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\User;

class CopyStudentDataToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:copy-student-data-to-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is copy student data to user table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $allStudent = Student::whereNotNull('user_id')
                                ->whereNotNull('email')
                                ->whereNotNull('nric')
                                ->where('is_updated', 0)
                                ->limit(1000)
                                ->get();
        // dd(count($allStudent));
        foreach($allStudent as $student){
            $user = User::find($student->user_id);
            $user->email        = $student->email;
            $user->phone_number = $student->mobile_no;
            $user->address      = $student->billing_address;
            $user->username     = strtoupper($student->nric);
            $user->role         = 'student';
            $user->status       = 1;
            $user->password     = Hash::make(strtoupper($student->nric));
            $user->dob          = $student->dob;
            $user->update();
            
            //this student is stored as user
            $student->is_updated = 1;
            $student->update();
        }
        // foreach($allStudent as $student) {
        //     $email = explode('@', $student->email)[0] . "+student@" . explode('@', $student->email)[1];
        //     $user = User::where('email', $email)->first();
        //     if($user){
        //         if($user->role != 'student'){
        //             $user = new User;
        //             $user->name         = $student->name;
        //             $user->email        = explode('@', $student->email)[0] . rand(1,999) . "+student@" . explode('@', $student->email)[1];
        //             $user->phone_number = $student->mobile_no;
        //             $user->address      = $student->billing_address;
        //             $user->role         = 'student';
        //             $user->status       = 1;
        //             $user->password     = Hash::make($student->nric);
        //             $user->dob          = $student->dob;
        //             $user->save();
        //             $updateStudent = Student::where('nric', $student->nric)->update(['user_id' => $user->id]);
        //         } else {
        //             $user = new User;
        //             $user->name         = $student->name;
        //             $user->email        = explode('@', $student->email)[0] . rand(1,999) . "+student@"  . explode('@', $student->email)[1];
        //             $user->phone_number = $student->mobile_no;
        //             $user->address      = $student->billing_address;
        //             $user->role         = 'student';
        //             $user->status       = 1;
        //             $user->password     = Hash::make($student->nric);
        //             $user->dob          = $student->dob;
        //             $user->save();
        //             $updateStudent = Student::where('nric', $student->nric)->update(['user_id' => $user->id]);
        //         }
        //     } else {
        //         $user = User::where('email', $student->email)->first();
        //         if($user) {
        //             if($user->role != 'student'){
        //                 $user = new User;
        //                 $user->name         = $student->name;
        //                 $user->email        = explode('@', $student->email)[0] . "+student@" . explode('@', $student->email)[1];
        //                 $user->phone_number = $student->mobile_no;
        //                 $user->address      = $student->billing_address;
        //                 $user->role         = 'student';
        //                 $user->status       = 1;
        //                 $user->password     = Hash::make($student->nric);
        //                 $user->dob          = $student->dob;
        //                 $user->save();
        //                 $updateStudent = Student::where('nric', $student->nric)->update(['user_id' => $user->id]);
        //             } else {
        //                 $user = new User;
        //                 $user->name         = $student->name;
        //                 $user->email        = explode('@', $student->email)[0] . "+student@" . explode('@', $student->email)[1];
        //                 $user->phone_number = $student->mobile_no;
        //                 $user->address      = $student->billing_address;
        //                 $user->role         = 'student';
        //                 $user->status       = 1;
        //                 $user->password     = Hash::make($student->nric);
        //                 $user->dob          = $student->dob;
        //                 $user->save();
        //                 $updateStudent = Student::where('nric', $student->nric)->update(['user_id' => $user->id]);
        //             }
        //         } else {
        //             $user = new User;
        //             $user->name         = $student->name;
        //             $user->email        = $student->email;
        //             $user->phone_number = $student->mobile_no;
        //             $user->address      = $student->billing_address;
        //             $user->role         = 'student';
        //             $user->status       = 1;
        //             $user->password     = Hash::make($student->nric);
        //             $user->dob          = $student->dob;
        //             $user->save();
        //             $updateStudent = Student::where('nric', $student->nric)->update(['user_id' => $user->id]);
        //         }
        //     }
        // }
    }
}