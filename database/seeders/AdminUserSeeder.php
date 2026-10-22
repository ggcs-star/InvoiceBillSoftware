<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('12345678'),
                'role' => UserRole::Admin,
                'branch_id' => null,
                'is_active' => true,
            ]
        );
    }
}
