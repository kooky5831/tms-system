<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    // protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;

    protected $table = 'payments';

    protected $auditExclude = [
        'entry_id',
        'xero_pay_id',
        'ip_address',
        'transaction_id',
        'transaction_type',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    public function studentEnrolment()
    {
        return $this->belongsTo('App\Models\StudentEnrolment', 'student_enrolments_id');
    }
    
    protected function getCourseNameWithTPGAttribute()
    {
        return $this->tpgateway_id . " (" . $this->course_start_date . ") - " . $this->coursemainname;
    }

    protected function getRemainingAmountAttribute()
    {
        return $this->amount - $this->amount_paid;
    }
}
