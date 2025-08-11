<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {name} {email} {password} {role=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $role = $this->argument('role');

        // Validasi role
        if (!in_array($role, ['admin', 'kepala_perpus'])) {
            $this->error('Role harus admin atau kepala_perpus');
            return 1;
        }

        // Cek apakah email sudah ada
        if (Admin::where('email', $email)->exists()) {
            $this->error('Email sudah terdaftar!');
            return 1;
        }

        // Buat admin baru
        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'status' => 'aktif',
        ]);

        $this->info("Admin berhasil dibuat!");
        $this->info("Email: {$email}");
        $this->info("Role: {$role}");
        
        return 0;
    }
}
