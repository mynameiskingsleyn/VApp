<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        /*
        * Refered a code here - https://laracasts.com/discuss/channels/laravel/the-page-has-expired-due-to-inactivity-when-logout
        */
/*        if($exception instanceof TokenMismatchException){
             return redirect()->route('inventory');
        }*/
        // If this exception is an instance of HttpException
        if ($this->isHttpException($exception)) {
            $response = array();
            // Grab the HTTP status code from the Exception
            $status = $exception->getStatusCode();
            switch ($status) {
             case 404:
               $response['Message'] = 'Not Found';
               break;
             case 500:
                 $response['Message'] = 'Fatal Error';
                 break;
             case 503:
                $response['Message'] = 'Service Unavailable';
                 break;
             case 400:
                 $response['Message'] = 'Bad Request';
                 break;
             /*case 401:
                 $response['Message'] = 'Unauthorized';
                 break;*/
             case 405:
                 $response['Message'] = 'Method Not Allowed';
                 break;
             case 440:
                 $response['Message'] = 'Session Expired';
                 break;
             default:
                return $this->renderHttpException($exception);
               break;
            }
            $response['StatusCode'] = $status;
            // Return a JSON response with the response array and status code
            return response()->json($response, 200);
        }
        return parent::render($request, $exception);
    }
}
