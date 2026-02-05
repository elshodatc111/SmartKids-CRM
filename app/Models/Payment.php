<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model{
    protected $fillable = [
        'kid_id',
        'group_id',
        'amount',
        'payment_type',
        'status',
        'description',
        'kassir_user_id',
        'success_admin_id',
    ];

    public function kid(){
        return $this->belongsTo(Kids::class, 'kid_id');
    }
    
    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function kassir(){
        return $this->belongsTo(User::class, 'kassir_user_id');
    }

    public function successAdmin(){
        return $this->belongsTo(User::class, 'success_admin_id');
    }

    
}
