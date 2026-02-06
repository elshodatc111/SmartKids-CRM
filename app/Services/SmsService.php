<?php
namespace App\Services;

use App\Jobs\SendSmsJob;

class SmsService{
    public function send(string $phone, string $message): void{
        dispatch(new SendSmsJob($phone, $message));
    }
}
