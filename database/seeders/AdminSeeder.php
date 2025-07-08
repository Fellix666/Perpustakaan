<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Administrator',
            'email' => 'admin@perpustakaan.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        Admin::create([
            'name' => 'Kepala Perpustakaan',
            'email' => 'kepala@perpustakaan.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala_perpus',
        ]);
    }
}