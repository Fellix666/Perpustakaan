<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_peminjaman', 'anggota_id', 'buku_id', 'tanggal_pinjam', 
        'tanggal_kembali_rencana', 'tanggal_kembali_aktual', 
        'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function dendaRecord()
    {
        return $this->hasOne(Denda::class);
    }

    // =================================================================
    // <<<--- TAMBAHAN: Accessor untuk status real-time ---<<<
    // =================================================================
    public function getStatusRealtimeAttribute()
    {
        // Jika status di database sudah 'dikembalikan', maka final.
        if ($this->attributes['status'] === 'dikembalikan') {
            return 'dikembalikan';
        }

        // Jika ada tanggal_kembali_aktual, berarti sudah dikembalikan
        if ($this->tanggal_kembali_aktual) {
            return 'dikembalikan';
        }

        // Untuk data historis (peminjaman lama), gunakan status database
        // Jika peminjaman lebih dari 30 hari yang lalu, anggap sebagai data historis
        $now = Carbon::now();
        $tanggalPinjam = $this->tanggal_pinjam;
        
        // Cek apakah data lebih dari 30 hari yang lalu
        $thirtyDaysAgo = $now->copy()->subDays(30);
        if ($tanggalPinjam && $tanggalPinjam->lt($thirtyDaysAgo)) {
            // Data historis - gunakan status database
            return $this->attributes['status'];
        }

        // Untuk data realtime (peminjaman baru), cek keterlambatan
        if ($this->tanggal_kembali_rencana) {
            $now = Carbon::now()->startOfDay();
            $tglKembali = $this->tanggal_kembali_rencana->startOfDay();
            
            // Tampilkan terlambat jika sudah lewat batas waktu DAN belum dikembalikan
            // TIDAK ADA TOLERANSI - langsung terlambat jika lewat tanggal
            if ($now->gt($tglKembali) && !$this->tanggal_kembali_aktual) {
                return 'terlambat';
            }
        }

        // Jika tidak keduanya, berarti masih 'dipinjam'.
        return 'dipinjam';
    }

    /**
     * Mendapatkan status berdasarkan tanggal tertentu (untuk laporan historis)
     */
    public function getStatusAtDate($date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }
        
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }

        // Jika status di database sudah 'dikembalikan', maka final.
        if ($this->attributes['status'] === 'dikembalikan') {
            return 'dikembalikan';
        }

        // Jika ada tanggal_kembali_aktual, berarti sudah dikembalikan
        if ($this->tanggal_kembali_aktual) {
            return 'dikembalikan';
        }

        // Cek keterlambatan berdasarkan tanggal yang diberikan
        if ($this->tanggal_kembali_rencana) {
            $checkDate = $date->startOfDay();
            $tglKembali = $this->tanggal_kembali_rencana->startOfDay();
            
            if ($checkDate->gt($tglKembali) && !$this->tanggal_kembali_aktual) {
                return 'terlambat';
            }
        }

        return 'dipinjam';
    }
}