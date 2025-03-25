<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentSubmissionAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "assessment_submission_attachments";

    protected $fillable = [
        'assessment_id',
        'question_id',
        'student_enrol_id',
        'submission_attchment',
        'attachment_size',
    ];

    public function question(){
        return $this->belongsTo('Assessments\Student\Models\AssessmentQuestions', 'id', 'question_id');
    }

}