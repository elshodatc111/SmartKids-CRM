<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;


class UserService{

    public function getAllEmployeesExceptAuth(): Collection{
        return User::where('id', '!=', auth()->id())->select(['id', 'name', 'phone', 'salary_amount','birth', 'series', 'image', 'type','is_active', 'created_at'])->latest()->get();
    }

    public function store(array $data): User{
        return User::create([
            'name'          => $data['name'],
            'phone'         => $data['phone'],
            'salary_amount' => $data['salary_amount'],
            'birth'         => $data['birth'],
            'series'        => $data['series'] ?? null,
            'type'          => $data['type'],
            'is_active'     => true,        
            'password'      => Hash::make('password'),
            'image'         => null,
        ]);
    }


}