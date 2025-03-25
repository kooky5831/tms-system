<?php

// app\Traits\UserTimezoneAware.php
namespace App\Traits;

use Carbon\Carbon;
use Auth;

trait UserTimezoneAware
{
    /**
     * Return the passed date in the user's timezone (or default to the app timezone)
     *
     * @return string
     */
    public function getDateToUserTimezone($date, $format = null)
    {
        if ( Auth::check() ) {
            $timezone = Auth::user()->timezone;
        } else {
            $timezone = config('app.timezone');
        }
        // $datetime = new DateTime($date);
        // $datetime->setTimezone(new datetimezone($timezone));
        // return $datetime->format('c');

        // on a date column
        // $user->created_at->timezone('Asia/Kolkata')->toDateTimeString();

        // Directly on a Carbon instance
        if( $format == null ) {
            return Carbon::parse($date)->timezone($timezone)->toDateTimeString();
        } else {
            return Carbon::parse($date)->timezone($timezone)->format($format);
        }
    }

    public function getMessageTime($timestamp, $timezone = null)
    {
        if ($timezone == null) {
            if ( Auth::check() ) {
                $timezone = Auth::user()->timezone;
            } else {
                $timezone = config('app.timezone');
            }
        }

        // return Carbon::createFromTimestamp($timestamp)->timezone($timezone)->toDateTimeString();
        // return Carbon::createFromTimestamp($timestamp)->timezone($timezone)->toTimeString();
        $d = Carbon::createFromTimestamp($timestamp)->timezone($timezone);
        if( !$d->isCurrentYear() ) {
            return Carbon::createFromTimestamp($timestamp)->timezone($timezone)->format('d M Y h:i A');
        }
        if( !$d->isToday() ) {
            return Carbon::createFromTimestamp($timestamp)->timezone($timezone)->format('d M h:i A');
        }
        return Carbon::createFromTimestamp($timestamp)->timezone($timezone)->format('h:i A');
    }

}
