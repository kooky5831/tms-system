<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\ExamAssessment;
use Illuminate\Database\Eloquent\SoftDeletes;


class AssessmentQuestions extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = 'webassessment_students';

    protected $table = "tms_questions";

    protected $fillable = [
        'assessment_id',
        'question',
        'question_weightage',
        'answer_format',
        'created_by',
        'updated_by',
    ];

    public function examQuestions(){
        return $this->belongsTo(ExamAssessment::class, 'id');
    }
    
    public function questionImages(){
        return $this->hasMany('Assessments\Student\Models\AssessmentQuestionImage', 'question_id');
    }

    public function questionStudentAttachments(){
        return $this->hasMany('Assessments\Student\Models\AssessmentSubmissionAttachment', 'question_id');
    }

    public function submitedSubmission()
    {
        return $this->belongsTo('Assessments\Student\Models\AssessmentSubmission', 'id' ,'question_id');
    }

}