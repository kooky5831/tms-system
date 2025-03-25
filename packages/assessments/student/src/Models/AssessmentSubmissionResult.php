<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentSubmissionResult extends Model
{
    use HasFactory;

    protected $table = "tms_student_results";

    protected $fillable = [
        'assessment_id',
        'student_enr_id',
        'is_passed',
        'assessment_recovery',
        'assessment_reschedule_note',
    ];

    public function examQuestionsSubmission()
    {
        return $this->belongsTo('Assessments\Student\Models\AssessmentSubmission', 'id' ,'submission_id');
    }
}

?>