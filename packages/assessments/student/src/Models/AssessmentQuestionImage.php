<?php

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class AssessmentQuestionImage extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = 'webassessment_students';

    protected $table = "assessment_question_images";

    protected $fillable = [
        'question_id',
        'question_image',
    ];

    public function question(){
        return $this->belongsTo('Assessments\Student\Models\AssessmentQuestions', 'id', 'question_id');
    }
}