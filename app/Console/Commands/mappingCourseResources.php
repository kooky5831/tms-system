<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CourseResourceCourseMain;
use Auth;

class mappingCourseResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tms:mapping-course-resources';

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
        $resources = DB::select('SELECT * FROM course_resources_old');
        
        if(!empty($resources)){
            foreach($resources as $resource) {
                $courseResource = new CourseResourceCourseMain;
                $courseResource->course_main_id =  $resource->course_main_id;
                $courseResource->course_resource_id     = $resource->id;
                $courseResource->created_by     = $resource->created_by;
                $courseResource->updated_by     =  $resource->updated_by;
                $courseResource->save();
            }
        }
    }
}
