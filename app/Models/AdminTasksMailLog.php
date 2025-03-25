<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTasksMailLog extends Model
{
    use HasFactory;

    protected  $fillable = ['mail_logs_subject','mail_logs_from', 'mail_logs_to', 'mail_logs_cc', 'mail_logs_bcc', 'mail_logs_content', 'mail_logs_time'];
}
