<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\TokenController as Token;
use Closure;

class AuthMiddle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tokenObject = new Token();
        $token = $tokenObject->getToken();
        if (!$token) {
            return response()->json([
                'code'  => 4000,
                'msg'   => '没有登录'
            ]);
        }
        $ret = $tokenObject->checkToken($token);
        if (isset($ret) && $ret == 'token invalid') {
            return response()->json([
                'code'  => 4001,
                'msg'   => 'token不合法'
            ]);
        } else if (isset($ret) && $ret == 'timeout') {
            return response()->json([
                'code'  => 4002,
                'msg'   => 'token过期'
            ]);
        }
        return $next($request);
    }
}
