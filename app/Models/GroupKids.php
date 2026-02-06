<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupKids extends Model{
    protected $table = 'group_kids';
    protected $fillable = [
        'group_id',
        'kids_id',
        'status',
        'add_data',
        'add_admin_id',
        'delete_data',
        'delete_admin_id',
        'payment_month',
    ];
    protected $dates = [
        'add_data',
        'delete_data',
    ];
    public function group(){
        return $this->belongsTo(Group::class);
    }
    public function kid(){
        return $this->belongsTo(Kids::class, 'kids_id');
    }
    public function addedBy(){
        return $this->belongsTo(User::class, 'add_admin_id');
    }
    public function deletedBy(){
        return $this->belongsTo(User::class, 'delete_admin_id');
    }
    
}
