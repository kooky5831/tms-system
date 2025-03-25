<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Assessments\Student\Models\AssessmentMainExam;


class CourseMain extends Model implements Auditable
{
    use HasFactory, SoftDeletes;


    protected $revisionCreationsEnabled = true;

    use \OwenIt\Auditing\Auditable;

    // protected $auditEvents = [
    //     'deleted',
    // ];

    protected $auditExclude = [
        'branding_theme_id',
        'cert_cordinates',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    const PAGINATION_COUNT = 10;

    const SINGLE_COURSE = 1;
    const MODULAR_COURSE = 2;
    const BOOSTER_SESSIONS = 3;

    const COURSE_TYPE_WSQ = 1;
    const COURSE_TYPE_NONWSQ = 2;

    const APPLICATION_FEES = 10;

    const APP_FEES_TRUE = 1;
    const APP_FEES_FALSE = 0;

    const ABSORB_GST_TRUE = 1;
    const ABSORB_GST_FALSE = 0;

    const IS_DISCOUNT_TRUE = 1;
    const IS_DISCOUNT_FALSE = 0;

    //new relation start
    public function tmsExams(){
        return $this->belongsToMany(AssessmentMainExam::class, 'tms_exam_course_mains', 'exam_id', 'course_main_id');
    }
    //new relation end


    public function coursetype()
    {
        return $this->belongsTo('App\Models\CourseType', 'course_type_id');
    }

    public function lineItems()
    {
        return $this->hasMany('App\Models\XeroCourseLineItems', 'course_main_id');
    }

    public function courseRunTriggers()
    {
        return $this->belongsToMany(CourseMain::class, 'course_run_triggers_course_mains', 'course_mains_id', 'course_run_trigger_id');
    }

    /**
     * The trainers that belong to the course.
    */
    public function trainers()
    {
        return $this->belongsToMany('App\Models\User', 'coursemain_trainer', 'coursemain_id', 'trainer_id', 'id', 'id');
            // ->withPivot(['course_id', 'trainer_id']);
            // ->withTimestamps();
    }

    public function programTypes()
    {
        return $this->belongsToMany('App\Models\ProgramType')->withPivot('course_main_id','program_type_id');
    }

    public function assessments()
    {
        return $this->hasMany('App\Models\CourseAssessments', 'course_id');
    }

    public function courseTags()
    {
        return $this->belongsToMany(CourseTags::class, 'course_mains_tags', 'course_mains_id', 'course_tag_id');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'id');
    }

    public function courseRun(){
        return $this->hasMany(Course::class, 'course_main_id')->with(['examCourse', 'courseEnrolments']);
    }

    public function courseResources(){
        return $this->hasMany(CourseResource::class, 'course_main_id');
    }
}
