<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Utiliza updateOrCreate para garantizar que el usuario de prueba siempre tenga el rol correcto
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'other@example.com'],
            [
                'name' => 'Other User',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
            ]
        );
    }
}