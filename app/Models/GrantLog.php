<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentEnrolment;

class GrantLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grant_logs';

    protected $fillable = ['student_enrolment_id','grant_id', 'grant_refno', 'notes', 'event', 'grant_notify', 'created_by','updated_by'];
    
}
