<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * The courses that belong to the trainer.
    */
    public function courses()
    {
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'course_id', 'trainer_id', 'user_id', 'id');
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'course_id', 'trainer_id', 'id', 'user_id');
        return $this->belongsToMany('App\Models\Course', 'course_trainer', 'trainer_id', 'course_id', 'user_id', 'id');
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'trainer_id', 'course_id', 'id', 'user_id');
        // return $this->belongsToMany('App\Models\Course');
    }

    /**
     * The courses that belong to the trainer.
    */
    public function coursemains()
    {
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'course_id', 'trainer_id', 'user_id', 'id');
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'course_id', 'trainer_id', 'id', 'user_id');
        return $this->belongsToMany('App\Models\CourseMain', 'coursemain_trainer', 'trainer_id', 'coursemain_id', 'id', 'id');
        // return $this->belongsToMany('App\Models\Course', 'course_trainer', 'trainer_id', 'course_id', 'id', 'user_id');
        // return $this->belongsToMany('App\Models\Course');
    }
}
