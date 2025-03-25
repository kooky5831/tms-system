<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    const DEFAULT_TIMEZONE = "Asia/Singapore";

    protected $attributes = [
        'timezone' => self::DEFAULT_TIMEZONE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'datetime'
    ];

    /**
     * Set the email.
     *
     * @param  string  $value
     * @return string
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function getProfileImageAttribute()
    {
        return Storage::url(config('uploadpath.user_profile_storage') . '/' . $this->profile_avatar);
    }

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

    /**
     * Add query scope to get only sales persons records
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUserTrainer(Builder $query): Builder
    {
        return $query->where('role', 'trainer');
    }


    public function isAdmin()
    {
        if( $this->role == 'superadmin' || $this->role == 'staff' )
            return true;
        return false;
    }

    public function isTrainer()
    {
        if( $this->role == 'trainer' )
            return true;
        return false;
    }

    public function isStudent()
    {
        if( $this->role == 'student' )
            return true;
        return false;
    }

    public function hasAssociatedStudentData()
    {
        // if ($this->isStudent()) {
        //     $accessibleStudent = AssessmentStudent::where('user_id', $this->email)->first();
        //     return $accessibleStudent !== null;
        // }
        return true;
    }

    public function trainer()
    {
        return $this->hasOne('App\Models\Trainer', 'user_id');
    }

    /* Get Trainer Signature */
    public function getTrainerSignAttribute()
    {
        return Storage::url(config('uploadpath.trainer_sign_storage') . '/' . $this->trainer_signature);
    }

    public function getNricOfUser(){
        // $this->email;
        return $this->username;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
    */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }
}
