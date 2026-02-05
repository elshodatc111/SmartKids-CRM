<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'phone',
        'salary_amount',
        'birth',
        'series',
        'image',
        'type', // ['admin','manager','tarbiyachi','oshpaz','hodim']
        'is_active',
        'password',
    ];

    protected $hidden = ['password','remember_token',];

    protected function casts(): array{
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birth' => 'date',
        ];
    }
    public function isAdmin(): bool {return $this->type === 'admin';}
    public function isManager(): bool {return $this->type === 'manager';}
    public function isOshpaz(): bool {return $this->type === 'oshpaz';}
    public function isTarbiyachi(): bool {return $this->type === 'tarbiyachi';}

    public function financeHistories(){return $this->hasMany(FinanceHistory::class);}
    public function addedFinanceHistories(){return $this->hasMany(FinanceHistory::class, 'admin_id');}

    protected function image(): Attribute{
        return Attribute::make(
            get: function ($value) {
                if (!$value || $value === 'storage' || $value === 'profiles/') {
                    return asset('images/default-avatar.png');
                }
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                return asset('storage/' . $value);
            },
        );
    }

    public function kids(): HasMany{
        return $this->hasMany(Kids::class, 'user_id');
    }
    public function kassirPayments(){
        return $this->hasMany(Payment::class, 'kassir_user_id');
    }
    public function approvedPayments(){
        return $this->hasMany(Payment::class, 'success_admin_id');
    }
    public function groupUsers(){
        return $this->hasMany(GroupUser::class);
    }
    public function addedGroupUsers(){
        return $this->hasMany(GroupUser::class, 'add_admin_id');
    }
    public function deletedGroupUsers(){
        return $this->hasMany(GroupUser::class, 'delete_admin_id');
    }
    public function addedGroupKids(){
        return $this->hasMany(GroupKids::class, 'add_admin_id');
    }
    public function deletedGroupKids(){
        return $this->hasMany(GroupKids::class, 'delete_admin_id');
    }
    public function kidsHistories(){
        return $this->hasMany(KidsHistory::class);
    }

}