<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseType extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PAGINATION_COUNT = 10;

    protected $table = 'course_types';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function courseMains()
    {
        return $this->hasMany('App\Models\CourseMain', 'course_type_id');
    }
}
