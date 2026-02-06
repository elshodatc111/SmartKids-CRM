<?php
namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Kids;
use App\Models\User;
use App\Models\FinanceHistory;

class SmsService{
    
    public function send(string $phone, string $message): void{
        dispatch(new SendSmsJob($phone, $message));
    }


}
