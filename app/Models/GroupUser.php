<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model{
    protected $table = 'group_users';
    protected $fillable = [
        'group_id',
        'user_id',
        'status',
        'add_data',
        'add_admin_id',
        'delete_data',
        'delete_admin_id',
    ];
    protected $dates = [
        'add_data',
        'delete_data',
    ];
    public function group(){
        return $this->belongsTo(Group::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function addedBy(){
        return $this->belongsTo(User::class, 'add_admin_id');
    }
    public function deletedBy(){
        return $this->belongsTo(User::class, 'delete_admin_id');
    }
}
