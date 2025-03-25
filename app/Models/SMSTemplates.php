<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSTemplates extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sms_templates';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }
}
