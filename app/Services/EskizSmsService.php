<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EskizSmsService{
    
    protected string $baseUrl;

    public function __construct(){
        $this->baseUrl = config('services.eskiz.base_url');
    }

    public function getToken(): string{
        return Cache::remember('eskiz_token', now()->addHours(20), function () {
            $response = Http::asForm()->post(
                $this->baseUrl . '/auth/login',
                [
                    'email'    => config('services.eskiz.email'),
                    'password' => config('services.eskiz.password'),
                ]
            );
            if (! $response->successful()) {
                Log::error('Eskiz token error', $response->json());
                throw new \Exception('Eskiz token olinmadi');
            }
            return $response['data']['token'];
        });
    }

    public function sendSms(string $phone, string $message): array{
        $token = $this->getToken();
        $response = Http::withToken($token)->asForm()->post($this->baseUrl . '/message/sms/send', [
                'mobile_phone' => $phone,
                'message'      => $message,
                'from'         => config('services.eskiz.from'),
            ]);
        if (! $response->successful()) {
            Log::error('Eskiz SMS error', $response->json());
            throw new \Exception('SMS yuborilmadi');
        }
        return $response->json();
    }
}
