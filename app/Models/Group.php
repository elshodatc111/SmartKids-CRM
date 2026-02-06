<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'user_id'
    ];

    public function kids(){
        return $this->hasMany(GroupKids::class);
    }

    public function users(){
        return $this->hasMany(GroupUser::class, 'group_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function groupKids(){
        return $this->hasMany(GroupKids::class, 'group_id');
    }

    public function groupUsers(){
        return $this->hasMany(GroupUser::class, 'group_id');
    }
    
}