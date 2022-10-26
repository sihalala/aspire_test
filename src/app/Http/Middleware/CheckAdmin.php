<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if($request->user()->is_admin === 1){
            return $next($request);
        }
        return response()->json('Unauthorized.',401);
    }
}
