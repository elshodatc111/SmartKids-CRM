<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Laravel\Sanctum\HasApiTokens;

class Finance extends Model{
    use HasFactory;
    protected $table = 'finances';
    protected $fillable = [
        'cash',
        'card',
        'bank',
        'donation_percent',
        'donation',
    ];
    protected $casts = [
        'cash'             => 'integer',
        'card'             => 'integer',
        'bank'             => 'integer',
        'donation'         => 'integer',
        'donation_percent' => 'integer',
    ];
}