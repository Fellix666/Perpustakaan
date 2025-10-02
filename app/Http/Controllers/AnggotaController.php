<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class AnggotaController extends Controller
{
    private function getKelasList()
    {
        return [
            'VII A', 'VII B', 'VII C', 'VII D', 'VII E',
            'VIII A', 'VIII B', 'VIII C', 'VIII D', 'VIII E',
            'IX A', 'IX B', 'IX C', 'IX D', 'IX E'
        ];
    }

    private function getKelasFilterList()
    {
        return ['VII', 'VIII', 'IX'];
    }

    private function getTahunAjaranList()
    {
        $currentYear = date('Y');
        
        $tahunAjaranList = [];
        for ($i = 4; $i >= 0; $i--) {
            $tahun = $currentYear - $i;
            $tahunAjaranList[] = $tahun . '/' . ($tahun + 1);
        }
        
        return $tahunAjaranList;
    }

    public function index(Request $request)
    {
        $query = Anggota::query();
        
        if ($request->filled('tahun_daftar')) { $query->where('tahun_ajaran_masuk', $request->tahun_daftar); }
        if ($request->filled('kelas')) { $query->where('kelas', $request->kelas); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%$q%")
                    ->orWhere('nomor_anggota', 'like', "%$q%")
                    ->orWhere('kelas', 'like', "%$q%");
            });
        }
        $anggotas = $query->orderBy('nomor_anggota')->paginate(10)->withQueryString();
        $kelasList = Anggota::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $tahunDaftarList = $this->getTahunAjaranList();
        return view('anggota.index', compact('anggotas', 'kelasList', 'tahunDaftarList'));
    }

    public function create()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota|regex:/^\d{1,7}-PPUS-\d{4}$/',
            'nama_lengkap' => 'required|max:100',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|in:' . implode(',', $this->getKelasList()),
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date|before_or_equal:today',
            'tahun_ajaran_masuk' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $data = $request->except('foto');
        $data['status'] = 'aktif';

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = uniqid('anggota_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('anggota', $filename, 'public');
            $data['foto'] = $filename;
        }
        
        Anggota::create($data);
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Anggota $anggota)
    {
        if (request()->expectsJson()) {
            return response()->json($anggota);
        }
        $anggota->load(['peminjamans.buku', 'peminjamans.dendaRecord']);
        return view('anggota.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota,' . $anggota->id . '|regex:/^\d{1,7}-Perpus-\d{4}$/',
            'nama_lengkap' => 'required|max:100',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|in:' . implode(',', $this->getKelasList()),
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date|before_or_equal:today',
            'tahun_ajaran_masuk' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'status' => 'required|in:aktif,non-aktif',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($anggota->foto && Storage::disk('public')->exists('anggota/' . $anggota->foto)) {
                Storage::disk('public')->delete('anggota/' . $anggota->foto);
            }
            $file = $request->file('foto');
            $filename = uniqid('anggota_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('anggota', $filename, 'public');
            $data['foto'] = $filename;
        }

        $anggota->update($data);
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui');
    }

    public function destroy(Anggota $anggota)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        if ($anggota->peminjamanAktif()->count() > 0) {
            return redirect()->route('anggota.index')->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif');
        }
        if ($anggota->foto && Storage::disk('public')->exists('anggota/' . $anggota->foto)) {
            Storage::disk('public')->delete('anggota/' . $anggota->foto);
        }
        $anggota->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }

    public function card(Anggota $anggota)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $color = request('color', 'blue');
        
        return view('anggota.card', compact('anggota', 'color'));
    }

    public function printCards(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $kelasFilterList = $this->getKelasFilterList();
        $tahunAjaranList = $this->getTahunAjaranList();
        
        $selectedKelas = $request->get('kelas');
        $selectedTahun = $request->get('tahun_daftar');
        
        if ($selectedKelas || $selectedTahun) {
            $query = Anggota::where('status', 'aktif');
            
            if ($selectedKelas) {
                $query->where('kelas', 'like', $selectedKelas . ' %');
            }
            
            if ($selectedTahun) {
                $tahun = explode('/', $selectedTahun)[0];
                $query->where('tahun_ajaran_masuk', $tahun);
            }
            
            $anggotas = $query->orderBy('kelas')->orderBy('nama_lengkap')->get();
        } else {
            $anggotas = collect();
        }
        
        return view('anggota.print-cards', compact(
            'anggotas', 'kelasFilterList', 'tahunAjaranList', 
            'selectedKelas', 'selectedTahun'
        ));
    }
    public function printCardsView(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $selectedKelas = $request->get('kelas');
        $selectedTahun = $request->get('tahun_daftar');
        $cardColor = $request->get('color', 'blue');
        
        $query = Anggota::where('status', 'aktif');
        
        if ($selectedKelas) {
            $query->where('kelas', 'like', $selectedKelas . ' %');
        }
        
        if ($selectedTahun) {
            $tahun = explode('/', $selectedTahun)[0];
            $query->where('tahun_ajaran_masuk', $tahun);
        }
        
        $anggotas = $query->orderBy('kelas')->orderBy('nama_lengkap')->get();
        
        return view('anggota.print-cards-view', compact(
            'anggotas', 'selectedKelas', 'selectedTahun', 'cardColor'
        ));
    }

    public function generateNomorAnggota()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $currentYear = date('Y');
        $lastAnggota = Anggota::where('nomor_anggota', 'like', "%PPUS-{$currentYear}")
            ->orderBy('nomor_anggota', 'desc')
            ->first();
        
        if ($lastAnggota) {
            $lastNumber = (int) substr($lastAnggota->nomor_anggota, 0, 2);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }
        
        return response()->json(['nomor_anggota' => "{$newNumber}-PPUS-{$currentYear}"]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $anggotas = Anggota::where('nama_lengkap', 'like', "%{$query}%")
            ->orWhere('nomor_anggota', 'like', "%{$query}%")
            ->orWhere('kelas', 'like', "%{$query}%")
            ->limit(10)
            ->get();
        return response()->json($anggotas);
    }

    public function export()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->fromArray([
            ['No', 'Nomor Anggota', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Kelas', 'Alamat', 'Telepon', 'Tanggal Daftar', 'Tahun Ajaran Masuk', 'Status']
        ], null, 'A1');
        
        $rowNum = 2;
        foreach ($anggotas as $index => $anggota) {
            $sheet->fromArray([
                $index + 1,
                $anggota->nomor_anggota,
                $anggota->nama_lengkap,
                $anggota->tempat_lahir,
                $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('d/m/Y') : '',
                $anggota->jenis_kelamin,
                $anggota->kelas,
                $anggota->alamat,
                $anggota->telepon,
                $anggota->tanggal_daftar ? $anggota->tanggal_daftar->format('d/m/Y') : '',
                $anggota->tahun_ajaran_masuk,
                $anggota->status
            ], null, 'A'.$rowNum);
            $rowNum++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'data_anggota_'.date('Y-m-d_H-i-s').'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save('php://output');
        exit;
    }
    
    public function import(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate(['file' => 'required|file|mimes:xlsx|max:102400']);
        $file = $request->file('file');
        $rows = [];
        $errors = [];
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $header = [];
            $rowCount = 0;
            
            $chunkSize = 100;
            $imported = 0;
            $totalRows = $sheet->getHighestRow();
            
            foreach ($sheet->getRowIterator() as $i => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                
                if ($i == 1) {
                    $header = $cells;
                    continue;
                }
                
                $rows[] = array_combine($header, $cells);
                $rowCount++;
                
                if ($rowCount % $chunkSize === 0 || $i === $totalRows) {
                    $imported += $this->processImportChunk($rows, $errors);
                    $rows = [];
                    
                    if ($totalRows > 1000) {
                        $progress = round(($i / $totalRows) * 100, 1);
                    }
                }
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
        
        $msg = $imported > 0 ? "$imported data anggota berhasil diimport." : 'Tidak ada data yang berhasil diimport.';
        if ($errors) $msg .= " Sebagian gagal: ".implode(' | ', array_slice($errors, 0, 10)); // Batasi error yang ditampilkan
        
        return back()->with($imported > 0 ? 'success' : 'error', $msg);
    }
    
    private function processImportChunk($rows, &$errors)
    {
        $imported = 0;
        
        foreach ($rows as $i => $row) {
            if (empty($row['nomor_anggota']) || empty($row['nama_lengkap']) || empty($row['jenis_kelamin']) || empty($row['kelas']) || empty($row['alamat']) || empty($row['tanggal_daftar']) || empty($row['tahun_ajaran_masuk'])) {
                $errors[] = "Baris ke-".($i+2).": Data wajib tidak lengkap.";
                continue;
            }
            if (Anggota::where('nomor_anggota', $row['nomor_anggota'])->exists()) {
                $errors[] = "Baris ke-".($i+2).": Nomor anggota sudah ada.";
                continue;
            }
            try {
                Anggota::create([
                    'nomor_anggota' => $row['nomor_anggota'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $this->parseExcelDate($row['tanggal_lahir'] ?? null),
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'kelas' => $row['kelas'],
                    'alamat' => $row['alamat'],
                    'telepon' => $row['telepon'] ?? null,
                    'tanggal_daftar' => $this->parseExcelDate($row['tanggal_daftar'] ?? null),
                    'tahun_ajaran_masuk' => $this->parseTahunAjaran($row['tahun_ajaran_masuk']),
                    'status' => $row['status'] ?? 'aktif',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris ke-".($i+2).": Gagal import (".$e->getMessage().")";
            }
        }
        
        return $imported;
    }
    
    private function parseExcelDate($value)
    {
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) { return null; }
        }
        if ($value) {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) { return null; }
        }
        return null;
    }

    private function parseTahunAjaran($value)
    {
        if (empty($value)) {
            return null;
        }
        
        if (strpos($value, '/') !== false) {
            return $value;
        }
        
        if (is_numeric($value)) {
            $year = (int) $value;
            return $year . '/' . ($year + 1);
        }
        
        return null;
    }

    public function prosesUploadFotoZip(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }

        $request->validate([
            'zip_file' => 'required|file|mimetypes:application/zip,application/x-zip-compressed|max:102400',
        ]);

        $zip = new ZipArchive;
        $file = $request->file('zip_file');
        
        if ($zip->open($file->getRealPath()) !== TRUE) {
            return back()->with('error', 'Gagal membuka file ZIP. Pastikan file tidak rusak.');
        }

        $berhasil = 0;
        $gagal = 0;
        $logGagal = [];

        $semuaAnggota = Anggota::all()->keyBy('nomor_anggota');

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileName = $zip->getNameIndex($i);
            $fileInfo = pathinfo($fileName);

            if (substr($fileName, -1) == '/' || str_starts_with(basename($fileName), '._') || str_contains($fileName, '__MACOSX')) {
                continue;
            }

            $nomorAnggotaDariFile = trim($fileInfo['filename']);

            if ($semuaAnggota->has($nomorAnggotaDariFile)) {
                $anggota = $semuaAnggota->get($nomorAnggotaDariFile);

                if ($anggota->foto && Storage::disk('public')->exists('anggota/' . $anggota->foto)) {
                    Storage::disk('public')->delete('anggota/' . $anggota->foto);
                }

                $newFileName = uniqid('anggota_') . '.' . $fileInfo['extension'];
                $fileContent = $zip->getFromIndex($i);
                Storage::disk('public')->put('anggota/' . $newFileName, $fileContent);
                
                $anggota->foto = $newFileName;
                $anggota->save();
                $berhasil++;
            } else {
                $gagal++;
                $logGagal[] = basename($fileName);
            }
        }
        $zip->close();

        $pesan = "$berhasil foto anggota berhasil diupdate.";
        if ($gagal > 0) {
            $pesanGagal = " $gagal foto gagal diproses karena nomor anggota tidak ditemukan: " . implode(', ', $logGagal);
            $status = ($berhasil > 0) ? 'warning' : 'error';
            return back()->with($status, $pesan . $pesanGagal);
        }

        return redirect()->route('anggota.index')->with('success', $pesan);
    }
}