<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_peminjaman', 'anggota_id', 'buku_id', 'tanggal_pinjam', 
        'tanggal_kembali_rencana', 'tanggal_kembali_aktual', 
        // 'denda',
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

    /**
     * PERBAIKAN: Mengubah nama relasi untuk menghindari konflik dengan kolom 'denda'.
     */
    public function dendaRecord()
    {
        return $this->hasOne(Denda::class);
    }
}