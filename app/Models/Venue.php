<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $revisionCreationsEnabled = true;

    const PAGINATION_COUNT = 10;

    /**
     * Add query scope to get only active records
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'venue_id');
    }

    public function session()
    {
        return $this->belongsTo('App\Models\CourseSessions', 'venue_id');
    }
}
