<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'role' => 'super_admin',
                'password' => Hash::make('12345678'),
                'account_status' => 'disetujui',
                'verified_at' => now(),
            ]
        );
    }
}
