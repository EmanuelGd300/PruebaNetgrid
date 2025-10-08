<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = env('RECAPTCHA_SECRET_KEY');
    }

    public function verify($token, $ip = null)
    {
        if (!$this->secretKey || empty($token)) {
            return false;
        }

        try {
            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if ($response->failed()) {
                \Log::error('reCAPTCHA API request failed', ['status' => $response->status()]);
                return false;
            }

            $data = $response->json();
            
            if (!($data['success'] ?? false)) {
                \Log::warning('reCAPTCHA verification failed', ['errors' => $data['error-codes'] ?? []]);
            }
            
            return $data['success'] ?? false;
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}