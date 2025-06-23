<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id',
        'hari_terlambat',
        'denda_per_hari',
        'total_denda',
        'status_bayar',
        'tanggal_bayar'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}

