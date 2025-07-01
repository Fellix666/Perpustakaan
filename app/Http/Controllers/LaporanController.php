<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        // Data statistik untuk halaman utama laporan
        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalBuku = Buku::sum('stok_total');
        $totalKategori = Kategori::count();
        $totalRak = Rak::count();
        
        $totalPeminjaman = Peminjaman::count();
        $totalDenda = Denda::sum('total_denda');
        $dendaBelumBayar = Denda::where('status_bayar', 'belum-dibayar')->sum('total_denda');
        
        // Data bulan ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        
        $peminjamanBulanIni = Peminjaman::whereMonth('tanggal_pinjam', $bulanIni)
                                      ->whereYear('tanggal_pinjam', $tahunIni)
                                      ->count();
        
        $dendaBulanIni = Denda::whereMonth('created_at', $bulanIni)
                             ->whereYear('created_at', $tahunIni)
                             ->sum('total_denda');
        
        return view('laporan.index', compact(
            'totalAnggota', 'totalBuku', 'totalKategori', 'totalRak',
            'totalPeminjaman', 'totalDenda', 'dendaBelumBayar',
            'peminjamanBulanIni', 'dendaBulanIni'
        ));
    }

    public function dataMaster(Request $request)
    {
        $type = $request->get('type', 'anggota');
        $data = [];
        
        switch($type) {
            case 'anggota':
                $data = Anggota::orderBy('nama_lengkap')->get();
                break;
            case 'buku':
                $data = Buku::with(['kategori', 'rak'])->orderBy('judul')->get();
                break;
            case 'kategori':
                $data = Kategori::withCount('bukus')->orderBy('nama_kategori')->get();
                break;
            case 'rak':
                $data = Rak::withCount('bukus')->orderBy('nomor_rak')->get();
                break;
        }
        
        return view('laporan.data-master', compact('data', 'type'));
    }

    public function transaksi(Request $request)
    {
        $type = $request->get('type', 'peminjaman');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $data = [];
        
        switch($type) {
            case 'peminjaman':
                $data = Peminjaman::with(['anggota', 'buku'])
                          ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                          ->orderBy('tanggal_pinjam', 'desc')
                          ->get();
                break;
            case 'pengembalian':
                $data = Peminjaman::with(['anggota', 'buku'])
                          ->where('status', 'dikembalikan')
                          ->whereBetween('tanggal_kembali_aktual', [$startDate, $endDate])
                          ->orderBy('tanggal_kembali_aktual', 'desc')
                          ->get();
                break;
            case 'denda':
                $data = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->get();
                break;
        }
        
        return view('laporan.transaksi', compact('data', 'type', 'startDate', 'endDate'));
    }

    public function semuaData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Data Master
        $anggota = Anggota::orderBy('nama_lengkap')->get();
        $buku = Buku::with(['kategori', 'rak'])->orderBy('judul')->get();
        $kategori = Kategori::withCount('bukus')->orderBy('nama_kategori')->get();
        $rak = Rak::withCount('bukus')->orderBy('nomor_rak')->get();
        
        // Data Transaksi
        $peminjaman = Peminjaman::with(['anggota', 'buku'])
                        ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                        ->orderBy('tanggal_pinjam', 'desc')
                        ->get();
        
        $denda = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                      ->whereBetween('created_at', [$startDate, $endDate])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('laporan.semua', compact(
            'anggota', 'buku', 'kategori', 'rak', 
            'peminjaman', 'denda', 'startDate', 'endDate'
        ));
    }

    public function printDataMaster(Request $request)
    {
        $type = $request->get('type', 'anggota');
        $data = [];
        
        switch($type) {
            case 'anggota':
                $data = Anggota::orderBy('nama_lengkap')->get();
                break;
            case 'buku':
                $data = Buku::with(['kategori', 'rak'])->orderBy('judul')->get();
                break;
            case 'kategori':
                $data = Kategori::withCount('bukus')->orderBy('nama_kategori')->get();
                break;
            case 'rak':
                $data = Rak::withCount('bukus')->orderBy('nomor_rak')->get();
                break;
        }
        
        return view('laporan.print.data-master', compact('data', 'type'));
    }

    public function printTransaksi(Request $request)
    {
        $type = $request->get('type', 'peminjaman');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $data = [];
        
        switch($type) {
            case 'peminjaman':
                $data = Peminjaman::with(['anggota', 'buku'])
                          ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                          ->orderBy('tanggal_pinjam', 'desc')
                          ->get();
                break;
            case 'pengembalian':
                $data = Peminjaman::with(['anggota', 'buku'])
                          ->where('status', 'dikembalikan')
                          ->whereBetween('tanggal_kembali_aktual', [$startDate, $endDate])
                          ->orderBy('tanggal_kembali_aktual', 'desc')
                          ->get();
                break;
            case 'denda':
                $data = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->get();
                break;
        }
        
        return view('laporan.print.transaksi', compact('data', 'type', 'startDate', 'endDate'));
    }

    public function printSemua(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Data Master
        $anggota = Anggota::orderBy('nama_lengkap')->get();
        $buku = Buku::with(['kategori', 'rak'])->orderBy('judul')->get();
        $kategori = Kategori::withCount('bukus')->orderBy('nama_kategori')->get();
        $rak = Rak::withCount('bukus')->orderBy('nomor_rak')->get();
        
        // Data Transaksi
        $peminjaman = Peminjaman::with(['anggota', 'buku'])
                        ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                        ->orderBy('tanggal_pinjam', 'desc')
                        ->get();
        
        $denda = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                      ->whereBetween('created_at', [$startDate, $endDate])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('laporan.print.semua', compact(
            'anggota', 'buku', 'kategori', 'rak', 
            'peminjaman', 'denda', 'startDate', 'endDate'
        ));
    }
}