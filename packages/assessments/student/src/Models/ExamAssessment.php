<?php

namespace Assessments\Student\Models;

use App\Models\Course;
use App\Models\AssessmentExamCourse;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Model;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentQuestions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tms_exam_assessments';

    const STARTDATE = 1;
    const ENDDATE = 2;
    
    protected $fillable = [
        'exam_id',
        'title',
        'assessment_time',
        'assessment_duration',
        'type',
        'date_option',
        'trainee_view_access'
    ];

    public function exams(){
        return $this->belongsTo(AssessmentMainExam::class, 'exam_id', 'id');
    }

    public function assessmentCourseRuns(){
        return $this->hasMany(AssessmentExamCourse::class, 'assessment_id');
    }

    public function questions(){
        return $this->hasMany(AssessmentQuestions::class, 'assessment_id')->with('questionImages');
    }
}
