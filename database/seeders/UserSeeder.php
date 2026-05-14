<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Utiliza firstOrCreate para garantizar la idempotencia (no duplicará registros)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'other@example.com'],
            [
                'name' => 'Other User',
                'password' => Hash::make('password'),
            ]
        );
    }
}