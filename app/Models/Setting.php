<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model{
    protected $fillable = [
        'tkun',
        'password_update',
        'emploes_paymart',
        'visited',
        'payment',
        'debit',
        'davomad',
    ];
}
            