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
        'tempat_lahir',
        'tanggal_lahir',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_lahir' => 'date',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function peminjamanAktif()
    {
        return $this->hasMany(Peminjaman::class)->where('status', 'dipinjam');
    }

    public function getStatusRealtimeAttribute()
    {
        // Jika status database sudah non-aktif, langsung non-aktif
        if ($this->status === 'non-aktif') return 'non-aktif';
        // Jika status database aktif, cek masa berlaku 3 tahun
        if (!$this->tanggal_daftar) return $this->status;
        $expired = $this->tanggal_daftar->copy()->addYears(3);
        return now()->lessThanOrEqualTo($expired) ? 'aktif' : 'non-aktif';
    }
}

