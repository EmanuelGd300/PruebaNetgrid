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
        if (!$this->secretKey) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if ($response->failed()) {
            return false;
        }

        $data = $response->json();
        
        return $data['success'] ?? false;
    }
}