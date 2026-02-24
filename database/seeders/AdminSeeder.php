<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\TursoSync;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'filipevieirawho@gmail.com'],
            [
                'name' => 'Filipe Vieira',
                'password' => Hash::make('123'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'active' => true,
            ]
        );

        // Only sync to Turso if user was just created (first-ever deploy)
        if ($user->wasRecentlyCreated) {
            TursoSync::upsertUser($user);
        }
    }
}
