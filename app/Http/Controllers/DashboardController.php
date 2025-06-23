<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Denda;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalBuku = Buku::sum('stok_total');
        $bukuTersedia = Buku::sum('stok_tersedia');
        $bukuDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        
        $peminjamanHariIni = Peminjaman::whereDate('tanggal_pinjam', Carbon::today())->count();
        $pengembalianHariIni = Peminjaman::whereDate('tanggal_kembali_aktual', Carbon::today())->count();
        $terlambat = Peminjaman::where('status', 'terlambat')
                               ->orWhere(function($query) {
                                   $query->where('status', 'dipinjam')
                                         ->where('tanggal_kembali_rencana', '<', Carbon::today());
                               })->count();
        
        $totalDenda = Denda::where('status_bayar', 'belum-dibayar')->sum('total_denda');
        
        $peminjamanTerbaru = Peminjaman::with(['anggota', 'buku'])
                                      ->orderBy('created_at', 'desc')
                                      ->limit(5)
                                      ->get();
        
        $bukuTerpopuler = Buku::withCount('peminjamans')
                             ->orderBy('peminjamans_count', 'desc')
                             ->limit(5)
                             ->get();

        return view('dashboard.index', compact(
            'totalAnggota', 'totalBuku', 'bukuTersedia', 'bukuDipinjam',
            'peminjamanHariIni', 'pengembalianHariIni', 'terlambat', 'totalDenda',
            'peminjamanTerbaru', 'bukuTerpopuler'
        ));
    }
}

