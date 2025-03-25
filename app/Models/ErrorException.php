<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorException extends Model
{
    use HasFactory;

    const PAGINATION_COUNT = 10;

    const UPDATED_AT = null;

    const PENDING = 1;
    const RESOLVED = 2;

    protected $fillable = [
        'datetime',
        'name',
        'code',
        'filepath',
        'message',
        'trace'
    ];
    
}
