<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseRunTriggers extends Model
{
    use HasFactory, SoftDeletes;

    const EVENT_TYPE_EMAIL = 1;
    const EVENT_TYPE_SMS = 2;
    const EVENT_TYPE_TEXT = 3;

    const EVENT_WHEN_DAYS_BEFORE = 1;
    const EVENT_WHEN_TIME_OF_MONTH = 2;
    const EVENT_WHEN_DAY_OF_WEEK = 3;
    const EVENT_WHEN_DAYS_AFTER = 4;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function courseMains()
    {
        return $this->belongsToMany(CourseMain::class, 'course_run_triggers_course_mains', 'course_run_trigger_id', 'course_mains_id')->with('course');
    }

    public function smsTemplate()
    {
        return $this->belongsTo('App\Models\SMSTemplates', 'sms_template_id');
    }

    public function courseTags()
    {
        return $this->belongsToMany(CourseTags::class, 'course_run_triggers_tags', 'course_run_trigger_id', 'course_tag_id');
    }
}
