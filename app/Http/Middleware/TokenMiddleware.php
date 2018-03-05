<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;

class TokenMiddleware
{
    private $tokenService;
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('token'))
            return response()->json([
                'code' => 6013,
                'message' => '未提交token'
            ]);
        $checkRes = $this->tokenService->verifyToken($request->header('token'));
        if($checkRes == -1)
            return response()->json([
                'code' => 6011,
                'message' => '该token不存在'
            ]);
        else if($checkRes == 0)
            return response()->json([
                'code' => 6012,
                'message' => 'token已过期，请重新登录'
            ]);
        else
        {
            $userInfo = $this->tokenService->getUserByToken($request->header('token'));
            $request->user = $userInfo;
            return $next($request);
        }
    }
}
