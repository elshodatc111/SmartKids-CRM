<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
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

}