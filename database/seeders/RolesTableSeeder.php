<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'superadmin'
        ]);

        Role::create([
            'name' => 'admin'
        ]);

        Role::create([
            'name' => 'trainer'
        ]);

        Role::create([
            'name' => 'student'
        ]);

        \Artisan::call('cache:clear');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::create(['name' => 'manage-staff-users']);

        Permission::create(['name' => 'trainer-list']);
        Permission::create(['name' => 'trainer-view']);
        Permission::create(['name' => 'trainer-add']);
        Permission::create(['name' => 'trainer-edit']);

        Permission::create(['name' => 'venue-list']);
        Permission::create(['name' => 'venue-view']);
        Permission::create(['name' => 'venue-add']);
        Permission::create(['name' => 'venue-edit']);

        Permission::create(['name' => 'course-list']);
        Permission::create(['name' => 'course-add']);
        Permission::create(['name' => 'course-edit']);

        Permission::create(['name' => 'coursetype-list']);
        Permission::create(['name' => 'coursetype-view']);
        Permission::create(['name' => 'coursetype-add']);
        Permission::create(['name' => 'coursetype-edit']);

        Permission::create(['name' => 'coursemain-list']);
        Permission::create(['name' => 'coursemain-view']);
        Permission::create(['name' => 'coursemain-add']);
        Permission::create(['name' => 'coursemain-edit']);

        Permission::create(['name' => 'studentenrolment-list']);
        Permission::create(['name' => 'studentenrolment-view']);
        Permission::create(['name' => 'studentenrolment-add']);
        Permission::create(['name' => 'studentenrolment-edit']);

        Permission::create(['name' => 'students-list']);
        Permission::create(['name' => 'students-view']);
        Permission::create(['name' => 'students-add']);
        Permission::create(['name' => 'students-edit']);

        Permission::create(['name' => 'softbooking-list']);
        // Permission::create(['name' => 'softbooking-view']);
        Permission::create(['name' => 'softbooking-add']);
        Permission::create(['name' => 'softbooking-edit']);

        Permission::create(['name' => 'payment-list']);
        Permission::create(['name' => 'payment-view']);
        Permission::create(['name' => 'payment-add']);
        Permission::create(['name' => 'payment-edit']);


        Permission::create(['name' => 'waitinglist-list']);
        // Permission::create(['name' => 'waitinglist-view']);
        Permission::create(['name' => 'waitinglist-add']);
        Permission::create(['name' => 'waitinglist-edit']);

        Permission::create(['name' => 'reports']);

        Permission::create(['name' => 'programtype-list']);
        Permission::create(['name' => 'programtype-add']);
        Permission::create(['name' => 'programtype-edit']);

        Permission::create(['name' => 'coursetriggers-list']);
        Permission::create(['name' => 'coursetriggers-add']);
        Permission::create(['name' => 'coursetriggers-edit']);

        Permission::create(['name' => 'xero-theme-setting']);
    }
}
