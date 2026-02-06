<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KidsHistory extends Model{
    
    protected $table = 'kids_histories';

    protected $fillable = [
        'kids_id',
        'type',
        'amount',
        'payment_id',
        'group_id',
        'description',
        'user_id',
    ];

    public function kid(){
        return $this->belongsTo(Kids::class, 'kids_id');
    }

    public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
