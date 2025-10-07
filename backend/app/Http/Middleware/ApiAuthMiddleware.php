<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json(['error' => 'Token de autorización requerido'], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        
        $user = User::where('session_token', $token)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $request->merge(['user' => $user]);
        
        return $next($request);
    }
}