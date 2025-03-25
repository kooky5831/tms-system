<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Student extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;

    protected $auditEvents = [
        'updated',
    ];

    protected $auditExclude = [
        'xero_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $fillable = [
        'xero_id',
        'user_id',
        'is_updated',
        'name',
        'nirc_name',
        'nric',
        'nationality',
        'email',
        'mobile_no',
        'dob',
        'company_sme',
        'company_name',
        'company_uen',
        'company_contact_person',
        'company_contact_person_email',
        'company_contact_person_number',
        'billing_address',
        'meal_restrictions',
        'meal_restrictions_type',
        'meal_restrictions_other',
        'application_fee',
        'payment_type',
        'created_by',
        'updated_by',       
    ];


    public function enrolments()
    {
        return $this->hasMany('App\Models\StudentEnrolment', 'student_id');
    }
}
