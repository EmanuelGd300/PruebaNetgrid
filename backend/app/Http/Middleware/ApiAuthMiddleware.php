<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json(['error' => 'Token de autorización requerido'], 401);
        }

        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'Token inválido'], 401);
        }
        
        return $next($request);
    }
}