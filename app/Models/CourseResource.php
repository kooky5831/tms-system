<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['course_main_id', 'resource_title', 'resource_file', 'created_by', 'updated_by'];

    protected $table = 'course_resources';

    public function courseMain()
    {
        return $this->belongsToMany('App\Models\CourseResourceCourseMain', 'course_resources_coursemains','course_resource_id', 'course_main_id');
    }
}
