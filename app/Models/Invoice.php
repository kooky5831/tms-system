<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    const SYNC_XERO_TRUE = 1;
    const SYNC_XERO_FALSE = 0;

    const COMAPNY_SPONSORED = 1;
    const SELF_SPONSORED = 0;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        "courserun_id",
        "student_enroll_id",
        "is_comapany",
        "xero_sync",
        "invoice_name",
        'invoice_number',
        "invoice_id",
        "invoice_type",
        "invoice_status",
        "amount_due",
        "amount_paid",
        "sub_total",
        "tax",
        "total_discount",
        "line_items",
        "invoice_date",
        'invoice_gst',
        "due_date"
    ];
}
