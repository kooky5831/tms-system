<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ProgramType extends Model
{
    use HasFactory;
    use SoftDeletes;
    

    const PAGINATION_COUNT = 10;

    protected $table = 'program_types';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function courseMainProgramType() {

        return $this->belongsToMany('App\Models\CourseMainPlatformType')->withPivot('course_main_id','platform_type_id');
    }
}
