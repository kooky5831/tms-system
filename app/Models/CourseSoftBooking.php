<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSoftBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;


    const STATUS_PENDING = 0;
    const STATUS_BOOKED = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_EXPIRED = 3;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deadline_date' => 'datetime',
    ];

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
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
