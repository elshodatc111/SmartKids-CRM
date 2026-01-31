<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory{
    protected static ?string $password;
    public function definition(): array{
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('998#########'), // O'zbekiston formati: 998 + 9 ta raqam
            'salary_amount' => fake()->randomFloat(2, 2000000, 10000000), // 2 mln dan 10 mln gacha
            'birth' => fake()->date('Y-m-d', '2007-01-01'),
            'series' => strtoupper(fake()->bothify('??######')), // Masalan: AA123456
            'image' => null, // Factoryda rasm yo'lini null qoldiramiz
            'type' => fake()->randomElement(['admin', 'manager', 'tarbiyachi', 'oshpaz', 'hodim']),
            'is_active' => true,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}