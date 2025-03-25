<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseAssessments extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PAGINATION_COUNT = 10;

    protected $table = 'course_assessments';

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
