<?php

namespace App\Exceptions;

use AdsMurai\Domain\SafeBox\InvalidSafeBoxTokenException;
use AdsMurai\Domain\SafeBox\SafeBoxBlockedException;
use AdsMurai\Domain\SafeBox\SafeBoxExistsException;
use AdsMurai\Domain\SafeBox\SafeBoxNotExistsException;
use AdsMurai\Domain\SafeBox\WrongPasswordException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        if ($request->wantsJson()) {
            // Define the response
            $response = $exception->getMessage();
            $status = 422;

            switch ($exception) {
                case ($this->isHttpException($exception)):
                    $status = $exception->getStatusCode();
                    break;
                case ($exception instanceof SafeBoxExistsException):
                    $status = $exception->getCode();//409
                    break;
                case ($exception instanceof InvalidSafeBoxTokenException):
                    $status = $exception->getCode();//401
                    break;
                case ($exception instanceof WrongPasswordException):
                    $status = $exception->getCode();//401
                    break;
                case ($exception instanceof SafeBoxBlockedException):
                    $status = $exception->getCode();//423
                    break;
                case ($exception instanceof SafeBoxNotExistsException):
                    $status = $exception->getCode();//404
                    break;
                case ($exception instanceof ModelNotFoundException):
                    $status = 404;
                    break;
            }

            return response()->json($response, $status);
        }
        return parent::render($request, $exception);
    }


}
