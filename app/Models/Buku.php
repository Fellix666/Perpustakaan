<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_buku',
        'isbn',
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'jumlah_halaman',
        'deskripsi',
        'stok_total',
        'stok_tersedia',
        'kategori_id',
        'rak_id',
        'status'
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

    public function peminjamanAktif()
    {
        return $this->hasMany(Peminjaman::class)->where('status', 'dipinjam');
    }

    public function updateStok()
    {
        $dipinjam = $this->peminjamanAktif()->count();
        $this->stok_tersedia = $this->stok_total - $dipinjam;
        $this->status = $this->stok_tersedia > 0 ? 'tersedia' : 'tidak-tersedia';
        $this->save();
    }
}