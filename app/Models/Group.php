<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model{
    protected $fillable = [
        'name',
        'description',
        'amount',
    ];

    public function kids(){
        return $this->hasMany(GroupKids::class);
    }

    public function users(){
        return $this->hasMany(GroupUser::class);
    }
}
