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
use Illuminate\Support\Facades\DB;

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



    public function transaksi(Request $request)
    {
        // Hanya izinkan laporan peminjaman
        $type = 'peminjaman';
        
        // Dapatkan tahun ajaran yang tersedia (yang sudah ada datanya)
        $availableYears = $this->getAvailableAcademicYears();
        
        // Set tahun ajaran default ke tahun terbaru yang tersedia
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            // Jika format "2023/2024", ambil tahun pertama
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        $data = [];
        $summaryData = [];
        $dendaData = [];
        
        // Tentukan periode tahun ajaran (Juli - Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        // Hanya laporan peminjaman
        $data = Peminjaman::with(['anggota', 'buku'])
                  ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                  ->orderBy('tanggal_pinjam', 'desc')
                  ->get();
        
        // Data summary per kelas
        $summaryData = $this->getPeminjamanSummary($startDate, $endDate);
        
        // Data denda untuk pengumuman
        $dendaData = $this->getDendaData();
        
        return view('laporan.transaksi', compact(
            'data', 'tahunAjaran', 'summaryData', 'dendaData', 
            'startDate', 'endDate', 'availableYears'
        ));
    }

    /**
     * Mendapatkan summary peminjaman per kelas dan bulan
     */
    private function getPeminjamanSummary($startDate, $endDate)
    {
        $summary = [];
        
        // Ambil data peminjaman per kelas per bulan
        $peminjamanData = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
            ->select(
                'anggotas.kelas',
                DB::raw('MONTH(peminjamans.tanggal_pinjam) as bulan'),
                DB::raw('YEAR(peminjamans.tanggal_pinjam) as tahun'),
                DB::raw('COUNT(*) as total_peminjaman')
            )
            ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('anggotas.kelas', 'bulan', 'tahun')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('anggotas.kelas')
            ->get();
        
        // Buat struktur data lengkap untuk semua bulan (Juli-Juni)
        $startYear = (int) date('Y', strtotime($startDate));
        $endYear = (int) date('Y', strtotime($endDate));
        
        // Daftar semua bulan dalam tahun ajaran (Juli-Juni)
        $bulanList = [
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun'
        ];
        
        // Inisialisasi semua bulan dengan data kosong
        foreach ($bulanList as $bulan => $namaBulan) {
            $tahun = ($bulan >= 7) ? $startYear : $endYear;
            $bulanKey = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
            
            $summary[$bulanKey] = [
                'bulan' => $namaBulan . ' ' . $tahun,
                'kelas' => [
                    'VII A' => 0, 'VII B' => 0, 'VII C' => 0, 'VII D' => 0, 'VII E' => 0,
                    'VIII A' => 0, 'VIII B' => 0, 'VIII C' => 0, 'VIII D' => 0, 'VIII E' => 0,
                    'IX A' => 0, 'IX B' => 0, 'IX C' => 0, 'IX D' => 0, 'IX E' => 0
                ]
            ];
        }
        
        // Isi data yang ada
        foreach ($peminjamanData as $item) {
            $bulanKey = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            if (isset($summary[$bulanKey])) {
                $summary[$bulanKey]['kelas'][$item->kelas] = $item->total_peminjaman;
            }
        }
        
        return collect($summary);
    }

    /**
     * Mendapatkan tahun ajaran yang tersedia (5 tahun ajaran terakhir)
     */
    public function getAvailableAcademicYears()
    {
        // Ambil tahun ajaran saat ini (tahun ini)
        $currentYear = date('Y');
        
        // Buat 3 tahun ajaran terakhir (tahun sekarang + 2 tahun sebelumnya)
        $academicYears = collect();
        for ($i = 2; $i >= 0; $i--) {
            $tahun = $currentYear - $i;
            $academicYears->push($tahun);
        }
        
        return $academicYears;
    }

    /**
     * Mendapatkan data denda untuk pengumuman
     */
    private function getDendaData()
    {
        // Data denda yang belum dibayar
        $dendaBelumBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
            ->where('status_bayar', 'belum-dibayar')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Data peminjaman terlambat (konsisten dengan status_realtime)
        $peminjamanTerlambat = Peminjaman::with(['anggota', 'buku'])
            ->where('status', 'dipinjam')
            ->whereNull('tanggal_kembali_aktual')
            ->where(function($query) {
                $query->where('tanggal_kembali_rencana', '<', Carbon::now()->subDay()) // Toleransi 1 hari seperti status_realtime
                      ->where('tanggal_pinjam', '>=', Carbon::now()->subDays(30)); // Hanya data non-historis
            })
            ->orderBy('tanggal_kembali_rencana', 'asc')
            ->get();
        
        // Hitung total denda
        $totalDendaBelumBayar = $dendaBelumBayar->sum('total_denda');
        $totalDendaTerlambat = $peminjamanTerlambat->sum(function($peminjaman) {
            $hariTerlambat = $this->hitungHariTerlambat($peminjaman->tanggal_kembali_rencana);
            return $hariTerlambat * 1000; // Denda per hari Rp 1.000, minimal 0
        });
        
        return [
            'dendaBelumBayar' => $dendaBelumBayar,
            'peminjamanTerlambat' => $peminjamanTerlambat,
            'totalDendaBelumBayar' => $totalDendaBelumBayar,
            'totalDendaTerlambat' => $totalDendaTerlambat
        ];
    }



    /**
     * Menghitung hari terlambat (hari kalender biasa)
     */
    private function hitungHariTerlambat($tanggalKembaliRencana)
    {
        $tanggalSekarang = Carbon::now()->startOfDay();
        $tanggalKembali = $tanggalKembaliRencana->startOfDay();
        return max(0, $tanggalKembali->diffInDays($tanggalSekarang, false));
    }

    /**
     * Laporan denda dan keterlambatan dengan filter sederhana
     */
    public function laporanDenda(Request $request)
    {
        $jenisLaporan = $request->get('jenis_laporan', 'pengumuman');
        $availableYears = $this->getAvailableAcademicYears();
        
        // Set tahun ajaran default
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        // Tentukan periode tahun ajaran (Juli - Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        if ($jenisLaporan == 'pengumuman') {
            // Laporan pengumuman - data yang perlu ditindaklanjuti
            $dendaBelumBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'belum-dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $peminjamanTerlambat = Peminjaman::with(['anggota', 'buku'])
                ->where('status', 'dipinjam')
                ->whereNull('tanggal_kembali_aktual')
                ->where('tanggal_kembali_rencana', '<', Carbon::now()->subDay())
                ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                ->orderBy('tanggal_kembali_rencana', 'asc')
                ->get();
                
            $dendaSudahBayar = collect();
            
        } else {
            // Laporan tahunan - data lengkap
            $dendaSudahBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $dendaBelumBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'belum-dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $peminjamanTerlambat = collect();
        }
        
        // Hitung total
        $totalDendaSudahBayar = $dendaSudahBayar->sum('total_denda');
        $totalDendaBelumBayar = $dendaBelumBayar->sum('total_denda');
        $totalDendaTerlambat = $peminjamanTerlambat->sum(function($peminjaman) {
            $hariTerlambat = $this->hitungHariTerlambat($peminjaman->tanggal_kembali_rencana);
            return $hariTerlambat * 1000;
        });
        
        // Summary per bulan
        $summaryData = $this->getDendaSummary($startDate, $endDate, $tahunAjaran);
        
        return view('laporan.denda', compact(
            'dendaBelumBayar', 
            'dendaSudahBayar',
            'peminjamanTerlambat', 
            'totalDendaBelumBayar', 
            'totalDendaSudahBayar',
            'totalDendaTerlambat',
            'startDate', 
            'endDate',
            'jenisLaporan',
            'summaryData',
            'tahunAjaran',
            'availableYears'
        ));
    }

    /**
     * Print laporan denda dan keterlambatan (untuk pengumuman)
     */
    public function printDenda(Request $request)
    {
        $jenisLaporan = $request->get('jenis_laporan', 'pengumuman');
        $availableYears = $this->getAvailableAcademicYears();
        
        // Set tahun ajaran default
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        // Tentukan periode tahun ajaran (Juli - Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        if ($jenisLaporan == 'pengumuman') {
            // Print laporan pengumuman - data yang perlu ditindaklanjuti
            $dendaBelumBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'belum-dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $peminjamanTerlambat = Peminjaman::with(['anggota', 'buku'])
                ->where('status', 'dipinjam')
                ->whereNull('tanggal_kembali_aktual')
                ->where('tanggal_kembali_rencana', '<', Carbon::now()->subDay())
                ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                ->orderBy('tanggal_kembali_rencana', 'asc')
                ->get();
                
            $dendaSudahBayar = collect();
            
        } else {
            // Print laporan tahunan - data lengkap
            $dendaSudahBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $dendaBelumBayar = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                ->where('status_bayar', 'belum-dibayar')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $peminjamanTerlambat = collect();
        }
        
        // Hitung total
        $totalDendaSudahBayar = $dendaSudahBayar->sum('total_denda');
        $totalDendaBelumBayar = $dendaBelumBayar->sum('total_denda');
        $totalDendaTerlambat = $peminjamanTerlambat->sum(function($peminjaman) {
            $hariTerlambat = $this->hitungHariTerlambat($peminjaman->tanggal_kembali_rencana);
            return $hariTerlambat * 1000;
        });
        
        // Summary per bulan
        $summaryData = $this->getDendaSummary($startDate, $endDate, $tahunAjaran);
        
        return view('laporan.print.denda', compact(
            'dendaBelumBayar', 
            'dendaSudahBayar',
            'peminjamanTerlambat', 
            'totalDendaBelumBayar', 
            'totalDendaSudahBayar',
            'totalDendaTerlambat',
            'startDate', 
            'endDate',
            'jenisLaporan',
            'summaryData',
            'tahunAjaran'
        ));
    }



    /**
     * Mendapatkan summary denda per bulan
     */
    private function getDendaSummary($startDate, $endDate, $tahunAjaran = null)
    {
        $summary = [];
        
        // Ambil data denda per bulan
        $dendaData = Denda::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('status_bayar'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(total_denda) as total_nominal')
            )
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        // TIDAK menggunakan filter tahun ajaran anggota untuk summary
        
        $dendaData = $dendaData->groupBy('bulan', 'tahun', 'status_bayar')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
        
        // Format data untuk view
        foreach ($dendaData as $item) {
            $bulanKey = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            if (!isset($summary[$bulanKey])) {
                $summary[$bulanKey] = [
                    'bulan' => Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y'),
                    'sudah_dibayar' => ['transaksi' => 0, 'nominal' => 0],
                    'belum_dibayar' => ['transaksi' => 0, 'nominal' => 0]
                ];
            }
            
            if ($item->status_bayar == 'dibayar') {
                $summary[$bulanKey]['sudah_dibayar']['transaksi'] = $item->total_transaksi;
                $summary[$bulanKey]['sudah_dibayar']['nominal'] = $item->total_nominal;
            } else {
                $summary[$bulanKey]['belum_dibayar']['transaksi'] = $item->total_transaksi;
                $summary[$bulanKey]['belum_dibayar']['nominal'] = $item->total_nominal;
            }
        }
        
        return collect($summary);
    }

    /**
     * Mendapatkan data top peminjam untuk program hadiah
     */
    private function getTopPeminjam($startDate, $endDate)
    {
        // Top 10 siswa dengan peminjaman terbanyak
        $topPeminjam = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
            ->select(
                'anggotas.id',
                'anggotas.nomor_anggota',
                'anggotas.nama_lengkap',
                'anggotas.kelas',
                DB::raw('COUNT(*) as total_peminjaman')
            )
            ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('anggotas.id', 'anggotas.nomor_anggota', 'anggotas.nama_lengkap', 'anggotas.kelas')
            ->orderBy('total_peminjaman', 'desc')
            ->limit(10)
            ->get();

        // Top 3 per kelas
        $topPerKelas = [];
        $kelasList = ['VII A', 'VII B', 'VII C', 'VII D', 'VII E', 'VIII A', 'VIII B', 'VIII C', 'VIII D', 'IX A', 'IX B', 'IX C', 'IX D', 'IX E'];
        
        foreach ($kelasList as $kelas) {
            $topKelas = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
                ->select(
                    'anggotas.id',
                    'anggotas.nomor_anggota',
                    'anggotas.nama_lengkap',
                    'anggotas.kelas',
                    DB::raw('COUNT(*) as total_peminjaman')
                )
                ->where('anggotas.kelas', $kelas)
                ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
                ->groupBy('anggotas.id', 'anggotas.nomor_anggota', 'anggotas.nama_lengkap', 'anggotas.kelas')
                ->orderBy('total_peminjaman', 'desc')
                ->limit(3)
                ->get();
            
            if ($topKelas->count() > 0) {
                $topPerKelas[$kelas] = $topKelas;
            }
        }

        // Top 5 per bulan
        $topPerBulan = [];
        for ($month = 7; $month <= 12; $month++) {
            $year = (int) $startDate;
            $bulanKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            
            $topBulan = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
                ->select(
                    'anggotas.id',
                    'anggotas.nomor_anggota',
                    'anggotas.nama_lengkap',
                    'anggotas.kelas',
                    DB::raw('COUNT(*) as total_peminjaman')
                )
                ->whereMonth('peminjamans.tanggal_pinjam', $month)
                ->whereYear('peminjamans.tanggal_pinjam', $year)
                ->groupBy('anggotas.id', 'anggotas.nomor_anggota', 'anggotas.nama_lengkap', 'anggotas.kelas')
                ->orderBy('total_peminjaman', 'desc')
                ->limit(5)
                ->get();
            
            if ($topBulan->count() > 0) {
                $topPerBulan[$bulanKey] = [
                    'bulan' => Carbon::createFromDate($year, $month, 1)->format('M Y'),
                    'data' => $topBulan
                ];
            }
        }
        
        // Semester 2 (Jan-Jun tahun berikutnya)
        for ($month = 1; $month <= 6; $month++) {
            $year = (int) $startDate + 1;
            $bulanKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            
            $topBulan = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
                ->select(
                    'anggotas.id',
                    'anggotas.nomor_anggota',
                    'anggotas.nama_lengkap',
                    'anggotas.kelas',
                    DB::raw('COUNT(*) as total_peminjaman')
                )
                ->whereMonth('peminjamans.tanggal_pinjam', $month)
                ->whereYear('peminjamans.tanggal_pinjam', $year)
                ->groupBy('anggotas.id', 'anggotas.nomor_anggota', 'anggotas.nama_lengkap', 'anggotas.kelas')
                ->orderBy('total_peminjaman', 'desc')
                ->limit(5)
                ->get();
            
            if ($topBulan->count() > 0) {
                $topPerBulan[$bulanKey] = [
                    'bulan' => Carbon::createFromDate($year, $month, 1)->format('M Y'),
                    'data' => $topBulan
                ];
            }
        }

        return [
            'topPeminjam' => $topPeminjam,
            'topPerKelas' => $topPerKelas,
            'topPerBulan' => $topPerBulan
        ];
    }

    /**
     * Mendapatkan statistik peminjaman
     */
    private function getStatistikPeminjaman($startDate, $endDate)
    {
        // Total peminjaman
        $totalPeminjaman = Peminjaman::whereBetween('tanggal_pinjam', [$startDate, $endDate])->count();
        
        // Rata-rata peminjaman per bulan
        $bulanCount = 12; // Juli-Juni
        $rataRataPerBulan = $totalPeminjaman > 0 ? round($totalPeminjaman / $bulanCount) : 0;
        
        // Kelas dengan peminjaman tertinggi
        $kelasTertinggi = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
            ->select('anggotas.kelas', DB::raw('COUNT(*) as total'))
            ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('anggotas.kelas')
            ->orderBy('total', 'desc')
            ->first();
        
        // Bulan dengan peminjaman tertinggi
        $bulanTertinggi = Peminjaman::select(
                DB::raw('MONTH(tanggal_pinjam) as bulan'),
                DB::raw('YEAR(tanggal_pinjam) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('bulan', 'tahun')
            ->orderBy('total', 'desc')
            ->first();
        
        // Persentase per tingkat
        $persentaseTingkat = [];
        $tingkatList = ['VII', 'VIII', 'IX'];
        
        foreach ($tingkatList as $tingkat) {
            // Gunakan query yang lebih spesifik untuk menghindari masalah LIKE
            $totalTingkat = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
                ->where(function($query) use ($tingkat) {
                    $query->where('anggotas.kelas', 'like', $tingkat . ' A')
                          ->orWhere('anggotas.kelas', 'like', $tingkat . ' B')
                          ->orWhere('anggotas.kelas', 'like', $tingkat . ' C')
                          ->orWhere('anggotas.kelas', 'like', $tingkat . ' D')
                          ->orWhere('anggotas.kelas', 'like', $tingkat . ' E');
                })
                ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
                ->count();
            
            $persentase = $totalPeminjaman > 0 ? round(($totalTingkat / $totalPeminjaman) * 100, 1) : 0;
            $persentaseTingkat[$tingkat] = [
                'total' => $totalTingkat,
                'persentase' => $persentase
            ];
        }
        
        // Kategori buku favorit
        $kategoriFavorit = Peminjaman::join('bukus', 'peminjamans.buku_id', '=', 'bukus.id')
            ->join('kategoris', 'bukus.kategori_id', '=', 'kategoris.id')
            ->select('kategoris.nama_kategori', DB::raw('COUNT(*) as total'))
            ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('kategoris.id', 'kategoris.nama_kategori')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Anggota dengan peminjaman terbanyak
        $anggotaTerbanyak = Peminjaman::join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
            ->select(
                'anggotas.nama_lengkap',
                'anggotas.kelas',
                DB::raw('COUNT(*) as total_peminjaman')
            )
            ->whereBetween('peminjamans.tanggal_pinjam', [$startDate, $endDate])
            ->groupBy('anggotas.id', 'anggotas.nama_lengkap', 'anggotas.kelas')
            ->orderBy('total_peminjaman', 'desc')
            ->limit(10)
            ->get();
        
        return [
            'totalPeminjaman' => $totalPeminjaman,
            'rataRataPerBulan' => $rataRataPerBulan,
            'kelasTertinggi' => $kelasTertinggi,
            'bulanTertinggi' => $bulanTertinggi,
            'persentaseTingkat' => $persentaseTingkat,
            'kategoriFavorit' => $kategoriFavorit,
            'anggotaTerbanyak' => $anggotaTerbanyak
        ];
    }

    /**
     * Mendapatkan statistik tambahan untuk laporan tahunan denda
     */
    private function getStatistikDendaTahunan($startDate, $endDate)
    {
        $dendaSudahBayar = Denda::where('status_bayar', 'dibayar')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $dendaBelumBayar = Denda::where('status_bayar', 'belum-dibayar')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalSudahBayar = $dendaSudahBayar->sum('total_denda');
        $totalBelumBayar = $dendaBelumBayar->sum('total_denda');
        $countSudahBayar = $dendaSudahBayar->count();
        $countBelumBayar = $dendaBelumBayar->count();
        
        $totalTransaksi = $countSudahBayar + $countBelumBayar;
        $persentasePembayaran = $totalTransaksi > 0 ? 
            round(($countSudahBayar / $totalTransaksi) * 100, 2) : 0;
        
        $rataRataDenda = $countSudahBayar > 0 ? 
            round($totalSudahBayar / $countSudahBayar, 0) : 0;
        
        return [
            'total_denda_dibayar' => $totalSudahBayar,
            'total_denda_belum_dibayar' => $totalBelumBayar,
            'total_transaksi_dibayar' => $countSudahBayar,
            'total_transaksi_belum_dibayar' => $countBelumBayar,
            'persentase_pembayaran' => $persentasePembayaran,
            'rata_rata_denda_per_transaksi' => $rataRataDenda,
            'bulan_tertinggi_denda' => $this->getBulanTertinggiDenda($startDate, $endDate),
            'kelas_tertinggi_denda' => $this->getKelasTertinggiDenda($startDate, $endDate)
        ];
    }
    
    /**
     * Mendapatkan bulan dengan denda tertinggi
     */
    private function getBulanTertinggiDenda($startDate, $endDate)
    {
        $bulanTertinggi = Denda::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(total_denda) as total_denda')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('bulan', 'tahun')
            ->orderBy('total_denda', 'desc')
            ->first();
        
        if ($bulanTertinggi) {
            $namaBulan = Carbon::createFromDate($bulanTertinggi->tahun, $bulanTertinggi->bulan, 1)->format('F Y');
            return [
                'bulan' => $namaBulan,
                'total_denda' => $bulanTertinggi->total_denda
            ];
        }
        
        return null;
    }
    
    /**
     * Mendapatkan kelas dengan denda tertinggi
     */
    private function getKelasTertinggiDenda($startDate, $endDate)
    {
        $kelasTertinggi = Denda::join('peminjamans', 'dendas.peminjaman_id', '=', 'peminjamans.id')
            ->join('anggotas', 'peminjamans.anggota_id', '=', 'anggotas.id')
            ->select(
                'anggotas.kelas',
                DB::raw('SUM(dendas.total_denda) as total_denda'),
                DB::raw('COUNT(*) as total_transaksi')
            )
            ->whereBetween('dendas.created_at', [$startDate, $endDate])
            ->groupBy('anggotas.kelas')
            ->orderBy('total_denda', 'desc')
            ->first();
        
        if ($kelasTertinggi) {
            return [
                'kelas' => $kelasTertinggi->kelas,
                'total_denda' => $kelasTertinggi->total_denda,
                'total_transaksi' => $kelasTertinggi->total_transaksi
            ];
        }
        
        return null;
    }





    public function analisisPeminjaman(Request $request)
    {
        // Dapatkan tahun ajaran yang tersedia
        $availableYears = $this->getAvailableAcademicYears();
        
        // Set tahun ajaran default ke tahun terbaru yang tersedia
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        // Tentukan periode tahun ajaran (Juli-Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        // Dapatkan data top peminjam
        $topPeminjamData = $this->getTopPeminjam($startDate, $endDate);
        
        // Dapatkan statistik peminjaman
        $statistikData = $this->getStatistikPeminjaman($startDate, $endDate);
        
        return view('laporan.analisis-peminjaman', compact(
            'topPeminjamData', 'statistikData', 'tahunAjaran', 'startDate', 'endDate', 'availableYears'
        ));
    }

    public function printAnalisisPeminjaman(Request $request)
    {
        // Dapatkan tahun ajaran yang tersedia
        $availableYears = $this->getAvailableAcademicYears();
        
        // Set tahun ajaran default ke tahun terbaru yang tersedia
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        // Tentukan periode tahun ajaran (Juli-Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        // Dapatkan data top peminjam
        $topPeminjamData = $this->getTopPeminjam($startDate, $endDate);
        
        // Dapatkan statistik peminjaman
        $statistikData = $this->getStatistikPeminjaman($startDate, $endDate);
        
        return view('laporan.print.analisis-peminjaman', compact(
            'topPeminjamData', 'statistikData', 'tahunAjaran', 'startDate', 'endDate'
        ));
    }





    public function printTransaksi(Request $request)
    {
        // Hanya laporan peminjaman
        $type = 'peminjaman';
        
        $tahunAjaran = $request->get('tahun_ajaran', Carbon::now()->format('Y'));
        
        // Pastikan tahun ajaran adalah integer
        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            // Jika format "2023/2024", ambil tahun pertama
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }
        
        // Tentukan periode tahun ajaran (Juli - Juni)
        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';
        
        // Hanya laporan peminjaman
        $data = Peminjaman::with(['anggota', 'buku'])
                  ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                  ->orderBy('tanggal_pinjam', 'desc')
                  ->get();
        $summaryData = $this->getPeminjamanSummary($startDate, $endDate);
        
        return view('laporan.print.transaksi', compact(
            'data', 'tahunAjaran', 'summaryData', 'startDate', 'endDate', 'type'
        ));
    }


}