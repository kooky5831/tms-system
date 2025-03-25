<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;
use Assessments\Student\Models\ExamAssessment;

class AssessmentExamCourse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tms_exam_assement_course_runs';

    protected $fillable = [
        'assessment_id',
        'course_run_id',
        'is_assigned',
        'started_at',
        'ended_at',
    ];

    public function assessment(){
        return $this->belongsTo(ExamAssessment::class, 'id');
    }

    public function courseRuns(){
        return $this->hasMany(Course::class, 'id', 'course_run_id');
    }
}