<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "tms_student_submitted_assessments";

    protected $fillable = [
        'question_id',
        'assessment_id',
        'student_enr_id',
        'submitted_answer',
        'is_pass',
        'is_reviewed',
        'edited_count',
        'remarks',
        'assessment_type',
        'answer_image',
    ];

    public function examQuestionsSubmission()
    {
        return $this->belongsTo('Assessments\Student\Models\AssessmentSubmission', 'id' ,'question_id');
    }

    public function studentExamCourse(){
        return $this->hasOne('Assessments\Student\Models\AssessmentMainExam', 'id');
    }

    public function examQuestion(){
        return $this->belongsTo('Assessments\Student\Models\AssessmentQuestions', 'question_id');
    }

    public function getExam() {
        return $this->belongsTo('Assessments\Student\Models\ExamAssessment', 'assessment_id');
    }

}

?>