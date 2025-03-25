<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroCourseLineItems extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function coursemain()
    {
        return $this->belongsTo('App\Models\CourseMain', 'course_main_id');
    }
}
