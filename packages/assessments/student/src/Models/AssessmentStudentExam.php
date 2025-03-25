<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentStudentExam extends Model
{
    use HasFactory;

    const NOT_STARTED = 0;
    const STARTED = 1;
    const COMPLETED = 2;
    const MARKED = 3;
    
    protected $guarded = 'webassessment_students';

    protected $table = "tms_student_assessment";

    protected $fillable = [
        'assessment_run_id',
        'student_enrol_id',
        'is_finished',
        'finished_time',
        'is_completed',
        'is_reviewed',
        'reviewed_time',
        'is_started',
        'started_time',
        'assessment_duration',
        'is_reschedule',
        'is_reschedule_time',
    ];

}