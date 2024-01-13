<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'user_name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => \Hash::make('password'),
            'is_super_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
