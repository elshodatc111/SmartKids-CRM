<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model{
    protected $fillable = [
        'phone',
        'message',
        'provider',
        'status',
        'provider_message_id',
        'response',
    ];
}
