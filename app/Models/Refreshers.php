<?php

namespace App\Models;

use App\Models\Refreshers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refreshers extends Model
{
    use HasFactory, SoftDeletes;

    const PAGINATION_COUNT = 10;

    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_CANCELLED = 2;

    /**
     * Add query scope to get only active records
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 0);
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id')->with(['courseMain', 'maintrainerUser']);
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'id', 'student_id'); // Assuming Student is the related model
    }

}
