<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaitingList extends Model
{
    use HasFactory, SoftDeletes;

    protected $revisionCreationsEnabled = true;

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
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
