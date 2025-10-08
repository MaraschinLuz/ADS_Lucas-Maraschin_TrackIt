<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@trackit.local'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('SenhaForte123!'),
                'role'     => User::ROLE_ADMIN,
            ]
        );
    }
}
