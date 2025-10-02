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

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

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
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }

        $selectedAnggotaId = $request->get('anggota_id');
        
        return view('pengunjung.create', compact('selectedAnggotaId'));
    }

    public function store(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
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
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        return view('pengunjung.edit', compact('pengunjung'));
    }

    public function update(Request $request, Pengunjung $pengunjung)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
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
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $pengunjung->delete();
        return redirect()->route('pengunjung.index')
                        ->with('success', 'Data kunjungan berhasil dihapus.');
    }

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

    public function laporan(Request $request)
    {
        $availableYears = $this->getAvailableAcademicYears();

        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);

        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }

        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';

        $summaryData = $this->getPengunjungSummary($startDate, $endDate);
        
        return view('pengunjung.laporan', compact(
            'summaryData', 'tahunAjaran', 'startDate', 'endDate', 'availableYears'
        ));
    }

    public function printLaporan(Request $request)
    {

        $availableYears = $this->getAvailableAcademicYears();

        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : Carbon::now()->format('Y');
        $tahunAjaran = $request->get('tahun_ajaran', $defaultYear);

        if (is_string($tahunAjaran) && strpos($tahunAjaran, '/') !== false) {
            $tahunAjaran = (int) explode('/', $tahunAjaran)[0];
        } else {
            $tahunAjaran = (int) $tahunAjaran;
        }

        $startDate = $tahunAjaran . '-07-01';
        $endDate = ($tahunAjaran + 1) . '-06-30';

        $summaryData = $this->getPengunjungSummary($startDate, $endDate);
        
        return view('pengunjung.print.laporan', compact(
            'summaryData', 'tahunAjaran', 'startDate', 'endDate'
        ));
    }

    private function getAvailableAcademicYears()
    {

        $currentYear = date('Y');

        $academicYears = collect();
        for ($i = 2; $i >= 0; $i--) {
            $tahun = $currentYear - $i;
            $academicYears->push($tahun);
        }
        
        return $academicYears;
    }

    private function getPengunjungSummary($startDate, $endDate)
    {
        $summary = [];

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

        $startYear = (int) date('Y', strtotime($startDate));
        $endYear = (int) date('Y', strtotime($endDate));

        $bulanList = [
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun'
        ];

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

        foreach ($pengunjungData as $item) {
            $bulanKey = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            if (isset($summary[$bulanKey])) {
                $summary[$bulanKey]['kelas'][$item->kelas] = $item->total_pengunjung;
            }
        }
        
        return collect($summary);
    }
}
