<?php

namespace Assessments\Student\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentQuestions;
use App\Models\CourseMain;

class AssessmentMainExam extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = 'webassessment_students';

    protected $table = "tms_exams";

    protected $fillable = [
        'main_exam',
        'created_by',
        'updated_by',
    ];

    public function assessment(){
        return $this->hasMany(ExamAssessment::class, 'exam_id');
    }

    public function courseMain(){
        return $this->belongsToMany(CourseMain::class, 'tms_exam_course_mains', 'exam_id', 'course_main_id');
    }

}