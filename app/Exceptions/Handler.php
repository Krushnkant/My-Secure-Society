<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('admin.404', [], 404);
        }

        // try {
        //     $user = JWTAuth::parseToken()->authenticate();
        // } catch (\Exception $e) {
        //     if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        //         return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token expired'), 401);
        //     } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        //         return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token invalid'), 401);
        //     } else {
        //         return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token absent'), 401);
        //     }
        // }

        return parent::render($request, $exception);
    }
}
