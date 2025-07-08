<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_anggota',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas',
        'alamat',
        'telepon',
        'tanggal_daftar',
        'status',
        'foto',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date'
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function peminjamanAktif()
    {
        return $this->hasMany(Peminjaman::class)->where('status', 'dipinjam');
    }
}

