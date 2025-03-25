<?php

namespace App\Exceptions;

use Throwable;
use App\Models\ErrorException;
use App\Jobs\JobDevNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Create Notification Data
            $exception = [
                "name" => get_class($e),
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ];

            // Create a Job for Notification which will run after 5 seconds.
            //$job = (new JobDevNotification($exception))->delay(5);

            // Dispatch Job and continue
            //dispatch($job);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if( $request->wantsJson() ) {
            if($exception instanceof \Illuminate\Auth\AuthenticationException ){
                $response = [
                    'code'      => 401,
                    'success'   => false,
                    'response'  => new \stdClass,
                    'message'   => 'Unauthorized',
                ];

                return response()->json($response, 401);
            }
        }
        return parent::render($request, $exception);
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $msg = "The given data is invalid";
        // get first message and send to response
        if( count($exception->errors()) > 0 ) {
            foreach ($exception->errors() as $k => $error) {
                $msg = $error[0];
                break;
            }
        }
        return response()->json([
            'code'      => 601,
            'success'   => false,
            'response' => new \stdClass,
            'message' => $msg,
            ], $exception->status);
    }

    public function report(Throwable $exception)
    {
        $data = array(
            'datetime' => date("Y-m-d H:i:s"),
            'name' => get_class($exception),
            'code' => $exception->getCode(),
            'filepath' => $exception->getFile().":".$exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        );

        ErrorException::create($data);

        parent::report($exception);
    }
}
