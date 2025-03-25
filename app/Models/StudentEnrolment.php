<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Str;
class StudentEnrolment extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use SearchableTrait;
    use \OwenIt\Auditing\Auditable;

    const PAGINATION_COUNT = 10;

    const PAYMENT_STATUS_PENDING = 1;
    const PAYMENT_STATUS_PARTIAL = 2;
    const PAYMENT_STATUS_FULL = 3;
    const PAYMENT_STATUS_REFUND = 4;


    const STATUS_NOT_ENROLLED = 3;
    const STATUS_ENROLLED = 0;
    const STATUS_HOLD = 2;
    const STATUS_CANCELLED = 1;
    // adding refersher for student-detail enrollment status search
    const REFRESHER_STATUS = 4;

    const TPG_STATUS_PENDING = 1;
    const TPG_STATUS_PARTIAL = 2;
    const TPG_STATUS_FULL = 3;
    const TPG_STATUS_CANCELLED = 4;
      
    

    const TPG_ENROLLED = 1;
    const TPG_NOT_ENROLLED = 0;
    const TPG_CANCLLED = 2;


    const XERO_SYNC = 0;
    const TMS_SYNC = 1;

    protected $auditExclude = [
        'student_id',
        'tpgateway_refno',
        'learning_mode',
        'designation',
        'salary',
        'other_paying_by',
        'payment_tpg_status',
        'computer_navigation_skill',
        'course_brochure_determined',
        'gform_id',
        'entry_id',
        'transaction_id',
        'amount_paid',
        'isGrantError',
        'grantEstimated',
        'grantRefNo',
        'grantStatus',
        'isAttendanceError',
        'isAssessmentError',
        'isPaymentError',
        // 'attendance',
        // 'assessment',
        'assessment_sync',
        'tpg_payment_sync',
        'assessment_remark',
        'assessment_date',
        'enrollmentResponse',
        'grantResponse',
        'attendanceResponse',
        'assessmentResponse',
        'tgp_payment_response',
        'reference',
        'due_date',
        'is_feedback_submitted',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    protected $fillable = [
        'xero_invoice_id',
		'course_id',
		'student_id',
		'tpgateway_refno',
		'sponsored_by_company',
		'xero_invoice_number',
		'xero_amount',
		'xero_paid_amount',
		'xero_due_amount',
		'xero_nett_course_fees',
		'master_invoice',
		'company_sme',
		'nationality',
		'age',
		'learning_mode',
		'email',
		'mobile_no',
		'dob',
		'education_qualification',
		'designation',
		'salary',
		'company_name',
		'company_uen',
		'company_contact_person',
		'company_contact_person_email',
		'company_contact_person_number',
		'billing_email',
		'billing_address',
		'billing_zip',
		'billing_country',
		'remarks',
		'payment_mode_company',
		'payment_mode_individual',
		'other_paying_by',
		'amount',
		'discountAmount',
		'payment_tpg_status',
		'payment_status',
		'status',
		'meal_restrictions',
		'meal_restrictions_type',
		'meal_restrictions_other',
		'computer_navigation_skill',
		'course_brochure_determined',
		'entry_id',
		'transaction_id',
		'amount_paid',
		'isGrantError',
		'grantEstimated',
		'grantRefNo',
		'grantStatus',
		'isAttendanceError',
		'isAssessmentError',
		'isPaymentError',
		'attendance',
		'assessment',
		'assessment_ref_no',
		'assessment_sync',
		'tpg_payment_sync',
		'assessment_remark',
		'assessment_date',
		'enrollmentResponse',
		'grantResponse',
		'attendanceResponse',
		'assessmentResponse',
		'tgp_payment_response',
		'payment_remark',
		'reference',
		'due_date',
		'created_by',
		'updated_by',
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'students.name' => 10,
            'students.nric' => 1,
            'student_enrolments.email' => 2,
            'courses.tpgateway_id' => 1,
            'course_mains.name' => 3,
        ],
        'joins' => [
            'students' => ['students.id','student_enrolments.student_id'],
            'courses' => ['courses.id','student_enrolments.course_id'],
            'course_mains' => ['course_mains.id','courses.course_main_id'],
        ],
    ];

    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function scopeHoldListTrainee(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_HOLD);
    }

    public function courseRun()
    {
        return $this->belongsTo('App\Models\Course', 'course_id')->with('courseMain');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'student_enrolments_id');
    }

    public function attendances()
    {
        return $this->hasMany('App\Models\StudentCourseAttendance', 'student_enrolment_id');
    }
    
    protected function getCourseNameWithTPGAttribute()
    {
        return $this->tpgateway_id . " (" . $this->course_start_date . ") - " . $this->coursemainname;
    }

    protected function getRemainingAmountAttribute()
    {
        return $this->amount - $this->amount_paid;
    }

    protected function getPaymentRemarksAttribute()
    {
        return Str::words($this->payment_remark, 10, '...');
    }
    
    protected function getFullPaymentRemarksAttribute()
    {
        return $this->payment_remark;
    }

    public function grants()
    {
        return $this->hasMany('App\Models\Grant', 'student_enrolment_id');
    }

    public function courseRefreshers()
    {
        return $this->hasMany('App\Models\Refreshers', 'course_id');
    }


    public function assessmentStudentExam(){
        return $this->hasOne('Assessments\Student\Models\AssessmentStudentExam','student_enrol_id');
    }  
}
