<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Repositories\AppRepository;

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
     *
     * @throws \Exception
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException)
        {
            return response()->json(['code' => 429, 'message' => __('Too Many Attempts.')]);
        }

        if ($exception instanceof \Illuminate\Routing\Exceptions\InvalidSignatureException)
        {
            return response()->json(['code' => 428, 'message' => __('Invalid signature. 签名无效')]);
        }

        return parent::render($request, $exception);
    }

}