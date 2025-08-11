<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $this->error("Admin dengan email {$email} tidak ditemukan!");
            return 1;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info("Password untuk admin {$admin->name} berhasil direset!");
        $this->info("Email: {$email}");
        $this->info("Password baru: {$password}");
        
        return 0;
    }
}
