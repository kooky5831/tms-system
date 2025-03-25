<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSessions extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PAGINATION_COUNT = 10;

    protected $table = 'course_sessions';

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function venue()
    {
        return $this->belongsTo('App\Models\Venue', 'venue_id');
    }
}
