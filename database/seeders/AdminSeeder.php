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
        Admin::firstOrCreate(
            ['email' => 'admin@perpustakaan.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('AdminPerpus2024!'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );
        
        Admin::firstOrCreate(
            ['email' => 'kepala@perpustakaan.com'],
            [
                'name' => 'Kepala Perpustakaan',
                'password' => Hash::make('KepalaPerpus2024!'),
                'role' => 'kepala_perpus',
                'status' => 'aktif',
            ]
        );
    }
}