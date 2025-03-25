<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminTasks extends Model
{
    use HasFactory, SoftDeletes;

    const PAGINATION_COUNT = 10;

    const STATUS_CREATED = 1;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;

    const TASK_TYPE_EMAIL = 1;
    const TASK_TYPE_SMS = 2;
    const TASK_TYPE_TEXT = 3;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function smsTemplate()
    {
        return $this->belongsTo('App\Models\SMSTemplates', 'sms_template_id');
    }

    public function completedByUser()
    {
        return $this->belongsTo('App\Models\User', 'completed_by');
    }
}
