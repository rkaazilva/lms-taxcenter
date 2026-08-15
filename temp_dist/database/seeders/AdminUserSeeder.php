<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $admin = User::where('email', 'admin@taxcenter.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Admin Pusat',
                'email' => 'admin@taxcenter.com',
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
