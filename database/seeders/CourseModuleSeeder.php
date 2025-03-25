<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseType;

class CourseModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moduleNames = [ 'Single Course', 'Modular Course', 'Booster Sessions' ];
        foreach ($moduleNames as $mname) {
            $record = new CourseType;
            $record->name                   = $mname;
            $record->created_by             = 1;
            $record->updated_by             = 1;
            $record->save();
        }
    }
}
