<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin Nodus
        User::updateOrCreate(
            ['email' => 'admin@nodus.com'],
            [
                'name' => 'Admin Nodus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'trust_score' => 5.0,
            ]
        );
    }
}
