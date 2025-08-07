<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengunjungController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengunjung::with('anggota');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter berdasarkan tujuan
        if ($request->filled('tujuan')) {
            $query->where('tujuan_kunjungan', $request->tujuan);
        }

        $pengunjungs = $query->orderBy('tanggal', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        return view('pengunjung.index', compact('pengunjungs'));
    }

    public function create(Request $request)
    {
        // Jika ada anggota_id yang dikirim dari parameter (misal dari peminjaman)
        $selectedAnggotaId = $request->get('anggota_id');
        
        return view('pengunjung.create', compact('selectedAnggotaId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'tujuan_kunjungan' => 'required|in:pinjam,baca',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pengunjung = Pengunjung::create([
            'tanggal' => now()->toDateString(),
            'anggota_id' => $request->anggota_id,
            'tujuan_kunjungan' => $request->tujuan_kunjungan,
            'keterangan' => $request->keterangan
        ]);

        // Redirect berdasarkan tujuan kunjungan
        switch ($request->tujuan_kunjungan) {
            case 'pinjam':
                return redirect()->route('peminjaman.create', ['anggota_id' => $request->anggota_id])
                               ->with('success', 'Data kunjungan berhasil dicatat. Silakan lanjutkan dengan peminjaman buku.');
            
            case 'baca':
                return redirect()->route('pengunjung.index')
                               ->with('success', 'Data kunjungan berhasil dicatat. Anggota sedang membaca di perpustakaan.');
        }
    }

    public function show(Pengunjung $pengunjung)
    {
        return view('pengunjung.show', compact('pengunjung'));
    }

    public function edit(Pengunjung $pengunjung)
    {
        return view('pengunjung.edit', compact('pengunjung'));
    }

    public function update(Request $request, Pengunjung $pengunjung)
    {
        $request->validate([
            'tujuan_kunjungan' => 'required|in:pinjam,baca',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pengunjung->update([
            'tujuan_kunjungan' => $request->tujuan_kunjungan,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('pengunjung.index')
                        ->with('success', 'Data kunjungan berhasil diperbarui.');
    }

    public function destroy(Pengunjung $pengunjung)
    {
        $pengunjung->delete();
        return redirect()->route('pengunjung.index')
                        ->with('success', 'Data kunjungan berhasil dihapus.');
    }

    // Method untuk mencari anggota
    public function searchAnggota(Request $request)
    {
        $search = $request->get('search');
        
        if (empty($search)) {
            return response()->json([]);
        }
        
        $anggotas = Anggota::where('status', 'aktif')
                           ->where(function($query) use ($search) {
                               $query->where('nomor_anggota', 'like', "%{$search}%")
                                     ->orWhere('nama_lengkap', 'like', "%{$search}%")
                                     ->orWhere('kelas', 'like', "%{$search}%");
                           })
                           ->orderBy('nama_lengkap')
                           ->limit(10)
                           ->get(['id', 'nomor_anggota', 'nama_lengkap', 'kelas']);

        return response()->json($anggotas);
    }

    // Method untuk menyelesaikan kunjungan


    // Method untuk laporan kunjungan tahunan (format seperti gambar)
    public function laporan(Request $request)
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
        
        // Dapatkan data pengunjung per bulan per kelas
        $summaryData = $this->getPengunjungSummary($startDate, $endDate);
        
        return view('pengunjung.laporan', compact(
            'summaryData', 'tahunAjaran', 'startDate', 'endDate', 'availableYears'
        ));
    }

    /**
     * Print laporan pengunjung
     */
    public function printLaporan(Request $request)
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
        
        // Dapatkan data pengunjung per bulan per kelas
        $summaryData = $this->getPengunjungSummary($startDate, $endDate);
        
        return view('pengunjung.print.laporan', compact(
            'summaryData', 'tahunAjaran', 'startDate', 'endDate'
        ));
    }

    /**
     * Mendapatkan tahun ajaran yang tersedia (5 tahun ajaran terakhir)
     */
    private function getAvailableAcademicYears()
    {
        // Ambil tahun ajaran saat ini (tahun ini)
        $currentYear = date('Y');
        
        // Buat 5 tahun ajaran terakhir
        $academicYears = collect();
        for ($i = 4; $i >= 0; $i--) {
            $tahun = $currentYear - $i;
            $academicYears->push($tahun);
        }
        
        return $academicYears;
    }

    /**
     * Mendapatkan summary pengunjung per kelas dan bulan
     */
    private function getPengunjungSummary($startDate, $endDate)
    {
        $summary = [];
        
        // Ambil data pengunjung per kelas per bulan
        $pengunjungData = Pengunjung::join('anggotas', 'pengunjungs.anggota_id', '=', 'anggotas.id')
            ->select(
                'anggotas.kelas',
                DB::raw('MONTH(pengunjungs.tanggal) as bulan'),
                DB::raw('YEAR(pengunjungs.tanggal) as tahun'),
                DB::raw('COUNT(*) as total_pengunjung')
            )
            ->whereBetween('pengunjungs.tanggal', [$startDate, $endDate])
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
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun'
        ];
        
        // Inisialisasi semua bulan dengan data kosong
        foreach ($bulanList as $bulan => $namaBulan) {
            $tahun = ($bulan >= 7) ? $startYear : $endYear;
            $bulanKey = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
            
            $summary[$bulanKey] = [
                'bulan' => $namaBulan . ' ' . $tahun,
                'kelas' => [
                    'VII A' => 0, 'VII B' => 0, 'VII C' => 0, 'VII D' => 0, 'VII E' => 0,
                    'VIII A' => 0, 'VIII B' => 0, 'VIII C' => 0, 'VIII D' => 0,
                    'IX A' => 0, 'IX B' => 0, 'IX C' => 0, 'IX D' => 0, 'IX E' => 0
                ]
            ];
        }
        
        // Isi data yang ada
        foreach ($pengunjungData as $item) {
            $bulanKey = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            if (isset($summary[$bulanKey])) {
                $summary[$bulanKey]['kelas'][$item->kelas] = $item->total_pengunjung;
            }
        }
        
        return collect($summary);
    }
}
