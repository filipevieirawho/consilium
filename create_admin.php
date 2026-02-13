<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::firstOrCreate(
    ['email' => 'admin@consilium.eng.br'],
    ['name' => 'Admin', 'password' => Hash::make('password')]
);

echo "User created/found: " . $user->email . PHP_EOL;
