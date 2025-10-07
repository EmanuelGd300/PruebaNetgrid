<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        // Verificar reCAPTCHA si está presente
        if (isset($data['g-recaptcha-response']) && !empty($data['g-recaptcha-response'])) {
            $this->verifyRecaptcha($data['g-recaptcha-response']);
        }

        $sessionToken = Str::random(60);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'session_token' => $sessionToken,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'session_token' => $sessionToken,
        ];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Invalidar sesiones anteriores (solo una sesión activa)
        $user->tokens()->delete();
        
        $sessionToken = Str::random(60);
        $user->update(['session_token' => $sessionToken]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'session_token' => $sessionToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
        $user->update(['session_token' => null]);
    }

    private function verifyRecaptcha(string $recaptchaResponse): void
    {
        $secretKey = config('services.recaptcha.secret_key');
        
        if (!$secretKey) {
            return; // Si no hay clave secreta configurada, omitir verificación
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
        ]);

        $result = $response->json();

        if (!$result['success']) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['La verificación reCAPTCHA falló.'],
            ]);
        }
    }
}