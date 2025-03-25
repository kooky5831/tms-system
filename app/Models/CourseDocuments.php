<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDocuments extends Model
{
    use HasFactory, SoftDeletes;

    protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;

    const CATEGORY_ATTENDANCE = 1;
    const CATEGORY_ASSESSMENT = 2;

    public function courseRun()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
