<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(array('success'=>false,'status_code' => 0, 'error' => 'Unauthorized', 'message' => 'Token expired'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(array('success'=>false,'status_code' => 0, 'error' => 'Unauthorized', 'message' => 'Token invalid'), 401);
            } else {
                return response()->json(array('success'=>false,'status_code' => 0, 'error' => 'Unauthorized', 'message' => 'Token absent'), 401);
            }
        }
        
        return $next($request);
    }
}
