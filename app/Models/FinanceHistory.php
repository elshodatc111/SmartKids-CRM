<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceHistory extends Model{

    use HasFactory;
    protected $table = 'finance_histories';
    protected $fillable = [
        'type',
        'reason',
        'amount',
        'donation',
        'description',
        'user_id',
        'admin_id',
        'start_at',
        'end_date',
    ];
    protected $casts = [
        'start_at' => 'datetime',
        'end_date' => 'date',
        'amount'   => 'integer',
        'donation' => 'integer',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

}
