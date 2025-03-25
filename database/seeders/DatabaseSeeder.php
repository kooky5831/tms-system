<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        \DB::transaction(function () {
            if (User::count() == 0) {
                $user = User::create([
                    'name' => 'Super Admin',
                    'email' => 'superadmin@mail.com',
                    'role' => 'superadmin',
                    'password' => \Hash::make('123456'),
                    'status' => 1,
                    'profile_avatar' => 'admin_default.png'
                ]);
                $user->assignRole('superadmin');

                $user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@mail.com',
                    'role' => 'admin',
                    'password' => \Hash::make('123456'),
                    'status' => 1
                ]);
                $user->assignRole('admin');

                $user = User::create([
                    'name' => 'Trainer',
                    'email' => 'trainer@mail.com',
                    'role' => 'trainer',
                    'password' => \Hash::make('123456'),
                    'status' => 1
                ]);
                $user->assignRole('trainer');

            }
        });
        $this->call(TrainerQualificationsSeeder::class);
        $this->call(CourseModuleSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
