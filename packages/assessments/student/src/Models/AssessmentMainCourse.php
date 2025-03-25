<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentMainCourse extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = 'webassessment_students';

    protected $table = "tms_exam_course_mains";

    protected $fillable = [
        'exam_id',
        'course_main_id'
    ];

}