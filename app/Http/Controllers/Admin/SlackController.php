<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Notification;
use App\Notifications\SlackResponse;
use Illuminate\Support\Facades\Log;
// use DataTables;

class SlackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth', ['except' => ['slackResponse']]);
    }

    public function slackResponse()
    {
        if(!empty($_POST['payload']))
        {
            //Log::info(print_r($_POST['payload'], true));
            $json = json_decode($_POST['payload']);
            $type = $json->type;
            if(!empty($json) && $type == 'block_actions' )
            {
                $resp_url = $json->response_url;
                Notification::route('slack', $resp_url)->notify(new SlackResponse($_POST));
            }
        }
    }
}
