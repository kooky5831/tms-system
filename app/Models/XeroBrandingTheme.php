<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroBrandingTheme extends Model
{
    use HasFactory;

    const SELF = "self-sponsored";
    const COMPANY = "comany-sponsored";

    protected  $fillable = [
        'branding_theme_id',
        'name',
        'logo_url',
        'type',
        'sort_order',
        'created_date_utc',
        'applied_on',
    ];
}
