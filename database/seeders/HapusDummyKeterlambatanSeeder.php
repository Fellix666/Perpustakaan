<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peminjaman;

class HapusDummyKeterlambatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deletedCount = Peminjaman::where('kode_peminjaman', 'like', 'DUMMY-%')->delete();
        
        $this->command->info('Data dummy keterlambatan berhasil dihapus!');
        $this->command->info('Total data yang dihapus: ' . $deletedCount . ' peminjaman');
    }
}
