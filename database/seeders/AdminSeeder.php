<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password'  => Hash::make('Admin@gcw123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
