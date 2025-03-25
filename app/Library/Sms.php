<?php

namespace App\Library;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;
use Log;

class Sms
{
    /**
     * @param string|int $to
     * @param string|int $message
     */
    public static function send($to, $message)
    {
        if (config('settings.sms_on')) {
            if( config('settings.test_sms') ) {
                $to = config('settings.test_sms_no');
            }
            try {
                $account_sid = config("settings.twilio_sid");
                $auth_token = config("settings.twilio_token");
                $twilio_number = config("settings.twilio_from");

                $client = new Client($account_sid, $auth_token);
                $client->messages->create($to, [
                    'from' => $twilio_number,
                    'body' => $message
                ]);
                return true;
            } catch (Exception $e) {
                Log::info($e->getMessage());
                return true;
            }
        } else {
            Log::info([$to, $message]);
            return true;
        }
    }
}
