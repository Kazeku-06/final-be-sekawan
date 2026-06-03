<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed admin default
        Admin::create([
            'admin_username' => 'admin',
            'admin_password' => Hash::make('password123'),
        ]);
    }
}
