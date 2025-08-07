<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pengunjung extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'anggota_id',
        'tujuan_kunjungan',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }



    public function getTujuanKunjunganTextAttribute()
    {
        $tujuan = [
            'pinjam' => 'Pinjam Buku',
            'baca' => 'Baca di Tempat'
        ];
        return $tujuan[$this->tujuan_kunjungan] ?? $this->tujuan_kunjungan;
    }
}
