<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    const TRIGGER_TYPE_ADMIN = 1;
    const TRIGGER_TYPE_COURSE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'description', 'subject', 'slug', 'template_for', 'template_text', 'keywords', 'status'
    ];

    public function setKeywordsAttribute($value)
    {
        $this->attributes['keywords'] = serialize($value);
    }

    public function getKeywordsAttribute($value)
    {
        return unserialize($value);
    }
}
