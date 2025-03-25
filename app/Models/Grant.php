<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grant extends Model
{
    use HasFactory, SoftDeletes;
   
    const PAGINATION_COUNT = 10;

    protected $table = 'grants';

    protected $fillable = ['student_enrolment_id','grant_refno','grant_status','scheme_code','scheme_description','component_code','component_description','amount_estimated','amount_paid','amount_recovery', 'disbursement_date', 'last_sync' ,'TPG_response', 'created_by','updated_by'];

    public function studentEnrolment()
    {
        return $this->belongsTo('App\Models\StudentEnrolment', 'student_enrolment_id');
    }

    
}
