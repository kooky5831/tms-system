<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourseAttendance extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'student_enrolment_id', 'course_id', 'is_present', 'attendance_sync'];  

    public function studentEnrolment()
    {
        return $this->belongsTo('App\Models\StudentEnrolment', 'id');
    }
}
