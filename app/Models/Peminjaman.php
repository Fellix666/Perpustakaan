<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // <-- TAMBAHKAN INI

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

        // Jika belum dikembalikan, cek apakah sudah lewat batas waktu.
        if (Carbon::now()->startOfDay()->isAfter($this->attributes['tanggal_kembali_rencana'])) {
            return 'terlambat';
        }

        // Jika tidak keduanya, berarti masih 'dipinjam'.
        return 'dipinjam';
    }
}