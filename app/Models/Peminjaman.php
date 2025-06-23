<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_peminjaman',
        'anggota_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'denda',
        'status',
        'keterangan'
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

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }

    public function hitungDenda()
    {
        if ($this->status == 'dikembalikan' && $this->tanggal_kembali_aktual) {
            $hariTerlambat = $this->tanggal_kembali_aktual->diffInDays($this->tanggal_kembali_rencana, false);
            if ($hariTerlambat > 0) {
                return $hariTerlambat * 1000; // Rp 1000 per hari
            }
        } elseif ($this->status == 'dipinjam' || $this->status == 'terlambat') {
            $hariTerlambat = Carbon::now()->diffInDays($this->tanggal_kembali_rencana, false);
            if ($hariTerlambat > 0) {
                $this->status = 'terlambat';
                $this->save();
                return $hariTerlambat * 1000;
            }
        }
        return 0;
    }

    public function getHariTerlambatAttribute()
    {
        if ($this->tanggal_kembali_aktual) {
            return max(0, $this->tanggal_kembali_aktual->diffInDays($this->tanggal_kembali_rencana, false));
        }
        return max(0, Carbon::now()->diffInDays($this->tanggal_kembali_rencana, false));
    }
}
