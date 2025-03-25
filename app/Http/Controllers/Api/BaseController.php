<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    protected $validationError  = 1001;
    protected $invalidLogin = 1002;
    protected $invalidToken = 1003;
    protected $unauthorizedRequestError = 1004;

    public $_blankObj;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'code'      => 200,
            'success'   => true,
            'response'  => $result,
            'message'   => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * success response blank data method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendBlankResponse($message)
    {
        $response = [
            'code'      => 200,
            'success'   => true,
            'response'  => new \stdClass,
            'message'   => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 1001, $errorMessages = [])
    {
        $response = [
            'code'      => $code,
            'success'   => false,
            'response'  => new \stdClass,
            'message'   => $error,
        ];

        if(!empty($errorMessages)){
            $response['response'] = $errorMessages;
        }

        return response()->json($response, 200);
    }

    public function sendFirstError($exception, $code = 1001)
    {
        // dd( get_class($exception) );
        $msg = "The given data is invalid";
        // get first message and send to response
        if( count($exception->errors()) > 0 ) {
            $msg = $exception->errors()->first();
        }
        return response()->json([
            'code'      => $code,
            'success'   => false,
            'response' => new \stdClass,
            'message' => $msg,
            ], 200);

    }
}
