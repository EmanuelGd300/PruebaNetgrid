<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'recaptcha_token' => 'nullable|string',
        ]);

        // Validar reCAPTCHA solo si está configurado y se envía token
        if (env('RECAPTCHA_SECRET_KEY')) {
            if (!$request->recaptcha_token) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['Debes completar la verificación reCAPTCHA.'],
                ]);
            }
            
            $recaptchaService = new RecaptchaService();
            if (!$recaptchaService->verify($request->recaptcha_token, $request->ip())) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['La verificación reCAPTCHA falló. Inténtalo de nuevo.'],
                ]);
            }
        }

        $sessionToken = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'session_token' => $sessionToken,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
                'session_token' => $sessionToken,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'recaptcha_token' => 'nullable|string',
        ]);

        // Validar reCAPTCHA solo si está configurado y se envía token
        if (env('RECAPTCHA_SECRET_KEY')) {
            if (!$request->recaptcha_token) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['Debes completar la verificación reCAPTCHA.'],
                ]);
            }
            
            $recaptchaService = new RecaptchaService();
            if (!$recaptchaService->verify($request->recaptcha_token, $request->ip())) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['La verificación reCAPTCHA falló. Inténtalo de nuevo.'],
                ]);
            }
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Invalidar sesiones anteriores
        $user->tokens()->delete();
        
        $sessionToken = Str::random(60);
        $user->update(['session_token' => $sessionToken]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
                'session_token' => $sessionToken,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->update(['session_token' => null]);

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}