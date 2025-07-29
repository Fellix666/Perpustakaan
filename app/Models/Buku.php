<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_buku', 'isbn', 'judul', 'pengarang', 'penerbit', 'tahun_terbit', 
        'jumlah_halaman', 'deskripsi', 'stok_total', 'stok_tersedia', 
        'kategori_id', 'rak_id', 'status', 'cover'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
    
    // Method peminjamanAktif() tidak lagi diperlukan untuk manajemen stok,
    // tapi bisa dipertahankan untuk keperluan lain jika ada.
    public function peminjamanAktif()
    {
        return $this->hasMany(Peminjaman::class)->whereIn('status', ['dipinjam', 'terlambat']);
    }
}