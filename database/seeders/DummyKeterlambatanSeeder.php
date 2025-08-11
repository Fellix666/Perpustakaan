<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use Carbon\Carbon;

class DummyKeterlambatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa anggota dan buku yang sudah ada
        $anggotas = Anggota::where('status', 'aktif')->get();
        $bukus = Buku::where('stok_tersedia', '>', 0)->get();
        
        if ($anggotas->isEmpty() || $bukus->isEmpty()) {
            $this->command->error('Tidak ada anggota aktif atau buku tersedia untuk membuat data dummy!');
            $this->command->error('Anggota tersedia: ' . $anggotas->count());
            $this->command->error('Buku tersedia: ' . $bukus->count());
            return;
        }
        
        $now = Carbon::now();
        $anggota = $anggotas->first();
        $buku = $bukus->first();
        
        $this->command->info('Menggunakan anggota: ' . $anggota->nama_lengkap);
        $this->command->info('Menggunakan buku: ' . $buku->judul);
        
        // Data 1: Terlambat 5 hari
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-001',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(10),
            'tanggal_kembali_rencana' => $now->copy()->subDays(5),
            'tanggal_kembali_aktual' => null,
            'status' => 'dipinjam',
            'keterangan' => 'Data dummy terlambat 5 hari'
        ]);
        
        // Data 2: Terlambat 3 hari
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-002',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(8),
            'tanggal_kembali_rencana' => $now->copy()->subDays(3),
            'tanggal_kembali_aktual' => null,
            'status' => 'dipinjam',
            'keterangan' => 'Data dummy terlambat 3 hari'
        ]);
        
        // Data 3: Terlambat 1 hari (masih dalam toleransi)
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-003',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(6),
            'tanggal_kembali_rencana' => $now->copy()->subDays(1),
            'tanggal_kembali_aktual' => null,
            'status' => 'dipinjam',
            'keterangan' => 'Data dummy terlambat 1 hari (dalam toleransi)'
        ]);
        
        // Data 4: Masih dalam waktu (belum terlambat)
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-004',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(3),
            'tanggal_kembali_rencana' => $now->copy()->addDays(2),
            'tanggal_kembali_aktual' => null,
            'status' => 'dipinjam',
            'keterangan' => 'Data dummy masih dalam waktu'
        ]);
        
        // Data 5: Sudah dikembalikan tepat waktu
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-005',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(10),
            'tanggal_kembali_rencana' => $now->copy()->subDays(5),
            'tanggal_kembali_aktual' => $now->copy()->subDays(5),
            'status' => 'dikembalikan',
            'keterangan' => 'Data dummy dikembalikan tepat waktu'
        ]);
        
        // Data 6: Dikembalikan terlambat
        Peminjaman::create([
            'kode_peminjaman' => 'PINJ-' . date('Ymd') . '-006',
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => $now->copy()->subDays(15),
            'tanggal_kembali_rencana' => $now->copy()->subDays(8),
            'tanggal_kembali_aktual' => $now->copy()->subDays(5),
            'status' => 'dikembalikan',
            'keterangan' => 'Data dummy dikembalikan terlambat'
        ]);
        
        $this->command->info('Data dummy keterlambatan berhasil dibuat!');
        $this->command->info('Total data yang dibuat: 6 peminjaman');
        $this->command->info('Data terlambat: 3 peminjaman');
        $this->command->info('Data dalam waktu: 1 peminjaman');
        $this->command->info('Data dikembalikan: 2 peminjaman');
    }
}
