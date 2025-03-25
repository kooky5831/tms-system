<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\StudentEnrolment;
use App\Models\AssessmentExamCourse;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\ExamAssessment;

class Course extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    const PAGINATION_COUNT = 10;
    const STATUS_UNPUBLISHED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_CANCELLED = 2;

    protected $auditStrict = true;
    protected $auditExclude = [
        'course_main_id',
        'course_main_parent_id',
        'course_run_parent_id',
        'tpgateway_id',
        'schinfotype_code',
        'schinfotype_desc',
        'sch_info',
        'registeredusercount',
        'cancelusercount',
        'coursevacancy_desc',
        'coursefileimage',
        'courseRunResponse',
        'isAttendanceSubmitedTPG',
        'isAssessmentSubmitedTPG',
        'course_folder_id',
        'trainer_job_status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    public function venue()
    {
        return $this->belongsTo('App\Models\Venue', 'venue_id');
    }

    public function session()
    {
        return $this->hasMany('App\Models\CourseSessions', 'course_id');
    }

    public function courseMain()
    {
        return $this->belongsTo('App\Models\CourseMain', 'course_main_id');
    }

    public function courseSoftBooking()
    {
        // return $this->hasMany('App\Models\CourseSoftBooking', 'course_id')->whereDate('deadline_date', '>=', date('Y-m-d'));
        return $this->hasMany('App\Models\CourseSoftBooking', 'course_id');
    }

    public function courseDocuments()
    {
        return $this->hasMany('App\Models\CourseDocuments', 'course_id');
    }

    public function courseWaitingList()
    {
        return $this->hasMany('App\Models\WaitingList', 'course_id');
    }

    public function courseEnrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->with('student');
    }

    public function courseTasks()
    {
        return $this->hasMany('App\Models\AdminTasks', 'course_id');
    }

    public function courseEnrolledNotEnrolledEnrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->whereIn('status', [StudentEnrolment::STATUS_ENROLLED, StudentEnrolment::STATUS_NOT_ENROLLED]);
    }

    public function courseActiveEnrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->where('status', 0);
    }

    public function courseActiveEnrolmentsWithStudent()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->where('status', 0)->with(['student']);
    }

    public function courseCancelledEnrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->where('status', 1);
    }

    public function courseHoldingEnrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->where('status', 2);
    }

    public function courseRefreshers()
    {
        return $this->hasMany('App\Models\Refreshers', 'course_id');
    }

    public function courseActiveRefreshers()
    {
        return $this->hasMany('App\Models\Refreshers', 'course_id')->where('status', 1);
    }

    public function getCourseImageAttribute()
    {
        return env('APP_URL').'/'.config('uploadpath.course').'/'.$this->coursefileimage;
    }

    public function maintrainerUser()
    {
        return $this->belongsTo('App\Models\User', 'maintrainer');
    }

    /**
     * The trainers that belong to the course.
    */
    public function trainers()
    {
        return $this->belongsToMany('App\Models\User', 'course_trainer', 'course_id', 'trainer_id', 'id', 'id');
            // ->withPivot(['course_id', 'trainer_id']);
            // ->withTimestamps();
    }

    
    public function courseResource(){
        return $this->belongsToMany('App\Models\CourseResource', 'course_main_id');
    }
    
    public function examCourse(){
        return $this->belongsTo(AssessmentExamCourse::class, 'course_run_id', 'id');
    }

    public function assessmentCourseRun(){
        return $this->hasMany(AssessmentExamCourse::class, 'course_run_id');
    }

    public function courseActiveCampaing()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'course_id')->with(['student']);
    }

}
