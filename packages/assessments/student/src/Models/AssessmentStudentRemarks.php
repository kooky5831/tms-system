<?php 

namespace Assessments\Student\Models;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentStudentRemarks extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = 'webassessment_students';

    protected $table = "tms_student_assessment_remarks";

    protected $fillable = [
        'assessment_id',
        'student_enrol_id',
        'student_exam_remarks'
    ];


    public function studentEnrol(){
        return $this->hasOne('App\Models\StudentEnrolment', 'id', 'student_enrol_id')->with('student');
    }
}

?>