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

    public function getStatusRealtimeAttribute()
    {

        if ($this->attributes['status'] === 'dikembalikan') {
            return 'dikembalikan';
        }

        if ($this->tanggal_kembali_aktual) {
            return 'dikembalikan';
        }

        $now = Carbon::now();
        $tanggalPinjam = $this->tanggal_pinjam;

        $thirtyDaysAgo = $now->copy()->subDays(30);
        if ($tanggalPinjam && $tanggalPinjam->lt($thirtyDaysAgo)) {

            return $this->attributes['status'];
        }

        if ($this->tanggal_kembali_rencana) {
            $now = Carbon::now()->startOfDay();
            $tglKembali = $this->tanggal_kembali_rencana->startOfDay();

            if ($now->gt($tglKembali) && !$this->tanggal_kembali_aktual) {
                return 'terlambat';
            }
        }

        return 'dipinjam';
    }

    public function getStatusAtDate($date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }
        
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }

        if ($this->attributes['status'] === 'dikembalikan') {
            return 'dikembalikan';
        }

        if ($this->tanggal_kembali_aktual) {
            return 'dikembalikan';
        }

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