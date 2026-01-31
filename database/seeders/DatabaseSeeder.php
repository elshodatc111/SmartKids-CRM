<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder{
    public function run(): void{
        User::factory()->create([
            'name' => 'Asosiy Admin',
            'phone' => '998901234567',
            'type' => 'admin',
            'password' => Hash::make('admin123'), // Parol: admin123
        ]);
        User::factory(20)->create();
    }
}