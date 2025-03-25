<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTags extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_tags';

    const WSQ_F2F_COURSE = 1;
    const WSQ_ONLINE_COURSE = 2;
    const NON_WSQ_F2F_COURSE = 3;

    public function courseMains()
    {
        return $this->belongsToMany(CourseMain::class, 'course_mains_tags', 'course_tag_id', 'course_mains_id');
    }

    public function courseRunTriggers()
    {
        return $this->belongsToMany(CourseRunTriggers::class, 'course_run_triggers_tags', 'course_tag_id', 'course_run_trigger_id');
    }
}
