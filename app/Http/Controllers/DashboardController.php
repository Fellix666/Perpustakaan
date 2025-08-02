<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Denda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        // Ambil 5 peminjaman terbaru yang unik
        $peminjamanTerbaru = Peminjaman::with(['anggota', 'buku'])
            ->orderBy('id', 'desc')
            ->limit(10) // Ambil lebih banyak untuk memastikan tidak ada duplikasi
            ->get()
            ->unique(function ($item) {
                return $item->anggota_id . '-' . $item->buku_id . '-' . $item->tanggal_pinjam;
            })
            ->take(5);
            
        $bukuTerpopuler = Buku::select('bukus.*')
            ->selectRaw('(SELECT COUNT(*) FROM peminjamans WHERE peminjamans.buku_id = bukus.id) as total_peminjaman')
            ->orderBy('total_peminjaman', 'desc')
            ->limit(5)
            ->get();

        // Data untuk grafik trend peminjaman 7 hari terakhir
        $trendPeminjaman = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Peminjaman::whereDate('tanggal_pinjam', $date)->count();
            $trendPeminjaman[] = [
                'date' => $date->format('d/m'),
                'count' => $count
            ];
        }

        // Data untuk grafik status peminjaman
        $statusPeminjaman = [
            'Dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'Dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'Terlambat' => Peminjaman::where('status', 'terlambat')->count()
        ];
        
        // Fallback jika tidak ada data
        if (empty($trendPeminjaman)) {
            $trendPeminjaman = [
                ['date' => '01/01', 'count' => 0],
                ['date' => '02/01', 'count' => 0],
                ['date' => '03/01', 'count' => 0],
                ['date' => '04/01', 'count' => 0],
                ['date' => '05/01', 'count' => 0],
                ['date' => '06/01', 'count' => 0],
                ['date' => '07/01', 'count' => 0]
            ];
        }
        
        if (empty($statusPeminjaman)) {
            $statusPeminjaman = [
                'Dipinjam' => 0,
                'Dikembalikan' => 0,
                'Terlambat' => 0
            ];
        }
        
        // Debug: Log data untuk memastikan terkirim
        Log::info('Dashboard Data:', [
            'trendPeminjaman' => $trendPeminjaman,
            'statusPeminjaman' => $statusPeminjaman,
            'bukuTerpopuler' => $bukuTerpopuler->map(function($buku) {
                return [
                    'id' => $buku->id,
                    'judul' => $buku->judul,
                    'total_peminjaman' => $buku->total_peminjaman
                ];
            })
        ]);
        
        // Pastikan data tidak null
        if (is_null($trendPeminjaman)) {
            $trendPeminjaman = [];
        }
        
        if (is_null($statusPeminjaman)) {
            $statusPeminjaman = [];
        }
            
        return view('dashboard.index', compact(
            'totalAnggota', 'totalBuku', 'bukuTersedia', 'bukuDipinjam',
            'peminjamanHariIni', 'pengembalianHariIni', 'terlambat', 'totalDenda',
            'peminjamanTerbaru', 'bukuTerpopuler', 'trendPeminjaman', 'statusPeminjaman'
        ));
    }
    
    public function laporanPeminjaman(Request $request)
    {
        $query = Peminjaman::with(['anggota', 'buku']);
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan anggota
        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
        }
        
        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->paginate(20);
        
        $anggotaList = Anggota::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        
        // Statistik
        $totalPeminjaman = $query->count();
        $totalDipinjam = $query->where('status', 'dipinjam')->count();
        $totalDikembalikan = $query->where('status', 'dikembalikan')->count();
        $totalTerlambat = $query->where('status', 'terlambat')->count();
        
        return view('laporan.peminjaman', compact(
            'peminjamans', 'anggotaList', 'totalPeminjaman', 
            'totalDipinjam', 'totalDikembalikan', 'totalTerlambat'
        ));
    }
    
    public function laporanDenda(Request $request)
    {
        $query = Denda::with(['peminjaman.anggota', 'peminjaman.buku']);
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereHas('peminjaman', function($q) use ($request) {
                $q->whereDate('tanggal_kembali_aktual', '>=', $request->tanggal_mulai);
            });
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereHas('peminjaman', function($q) use ($request) {
                $q->whereDate('tanggal_kembali_aktual', '<=', $request->tanggal_selesai);
            });
        }
        
        // Filter berdasarkan status bayar
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }
        
        $dendas = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistik
        $totalDenda = $query->sum('total_denda');
        $dendaBelumBayar = $query->where('status_bayar', 'belum-dibayar')->sum('total_denda');
        $dendaSudahBayar = $query->where('status_bayar', 'sudah-dibayar')->sum('total_denda');
        
        return view('laporan.denda', compact(
            'dendas', 'totalDenda', 'dendaBelumBayar', 'dendaSudahBayar'
        ));
    }
    
    public function laporanAnggota(Request $request)
    {
        $query = Anggota::withCount(['peminjamans', 'peminjamans as peminjaman_aktif_count' => function($q) {
            $q->where('status', 'dipinjam');
        }]);
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        
        $anggotas = $query->orderBy('nama_lengkap')->paginate(20);
        
        // Data untuk filter
        $kelasList = Anggota::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        
        // Statistik
        $totalAnggota = Anggota::count();
        $anggotaAktif = Anggota::where('status', 'aktif')->count();
        $anggotaNonAktif = Anggota::where('status', 'non-aktif')->count();
        
        return view('laporan.anggota', compact(
            'anggotas', 'kelasList', 'totalAnggota', 'anggotaAktif', 'anggotaNonAktif'
        ));
    }
    
    public function laporanBuku(Request $request)
    {
        $query = Buku::with(['kategori', 'rak'])->withCount(['peminjamans', 'peminjamans as peminjaman_aktif_count' => function($q) {
            $q->where('status', 'dipinjam');
        }]);
        
        // Filter berdasarkan kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Filter berdasarkan rak
        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $bukus = $query->orderBy('judul')->paginate(20);
        
        // Data untuk filter
        $kategoriList = \App\Models\Kategori::orderBy('nama_kategori')->get();
        $rakList = \App\Models\Rak::orderBy('nama_rak')->get();
        
        // Statistik
        $totalBuku = Buku::count();
        $bukuTersedia = Buku::where('status', 'tersedia')->count();
        $bukuRusak = Buku::where('status', 'rusak')->count();
        $bukuHilang = Buku::where('status', 'hilang')->count();
        $totalStok = Buku::sum('stok_total');
        $stokTersedia = Buku::sum('stok_tersedia');
        
        return view('laporan.buku', compact(
            'bukus', 'kategoriList', 'rakList', 'totalBuku', 'bukuTersedia', 
            'bukuRusak', 'bukuHilang', 'totalStok', 'stokTersedia'
        ));
    }
}