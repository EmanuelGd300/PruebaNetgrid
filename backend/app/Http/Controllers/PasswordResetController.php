<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Intentar enviar correo
        $mailSent = false;
        $startTime = time();
        
        try {
            // Verificar configuración de correo
            if (!env('MAIL_USERNAME') || !env('MAIL_PASSWORD')) {
                throw new \Exception('Mail credentials not configured');
            }
            
            Mail::raw(
                "Hola,\n\nRecibimos una solicitud para restablecer tu contraseña.\n\n" .
                "Tu token de recuperación es: {$token}\n\n" .
                "Ingresa este token en la página de recuperación de contraseña.\n\n" .
                "Si no solicitaste este cambio, ignora este correo.\n\n" .
                "Saludos,\nEmanuel Gómez Díaz",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Recuperación de Contraseña - Pokemon API');
                }
            );
            
            $mailSent = true;
            \Log::info("Password reset email sent successfully to {$request->email} in " . (time() - $startTime) . " seconds");
            
        } catch (\Exception $e) {
            \Log::error("Failed to send password reset email to {$request->email}: " . $e->getMessage());
            \Log::error("Mail configuration - Host: " . env('MAIL_HOST') . ", Port: " . env('MAIL_PORT') . ", Username: " . env('MAIL_USERNAME'));
        }
        
        // SIEMPRE loguear el token para recuperación manual
        \Log::info("=== PASSWORD RESET TOKEN ===");
        \Log::info("Email: {$request->email}");
        \Log::info("Token: {$token}");
        \Log::info("Mail sent: " . ($mailSent ? 'YES' : 'NO'));
        \Log::info("===========================");

        return response()->json([
            'message' => 'Se ha enviado un enlace de recuperación a tu correo electrónico.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['El token de recuperación es inválido o ha expirado.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'session_token' => null
        ]);

        // Invalidar todas las sesiones
        $user->tokens()->delete();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.'
        ]);
    }
}