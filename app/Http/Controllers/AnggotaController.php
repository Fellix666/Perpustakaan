<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class AnggotaController extends Controller
{
    // ... method index() dan create() Anda tetap sama ...
    public function index(Request $request)
    {
        $query = Anggota::query();
        if ($request->filled('tahun_daftar')) { $query->whereYear('tanggal_daftar', $request->tahun_daftar); }
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
        $tahunDaftarList = Anggota::selectRaw('YEAR(tanggal_daftar) as tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        return view('anggota.index', compact('anggotas', 'kelasList', 'tahunDaftarList'));
    }

    public function create()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        return view('anggota.create');
    }

    /**
     * PERBAIKAN: Menyeragamkan cara menyimpan file.
     */
    public function store(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota',
            'nama_lengkap' => 'required|max:100',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date|before_or_equal:today',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $data = $request->except('foto'); // Ambil semua data kecuali foto
        $data['status'] = 'aktif';

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = uniqid('anggota_') . '.' . $file->getClientOriginalExtension();
            // Simpan file menggunakan metode yang sama dengan ZIP upload
            $file->storeAs('anggota', $filename, 'public');
            $data['foto'] = $filename;
        }
        
        Anggota::create($data);
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Anggota $anggota)
    {
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

    /**
     * PERBAIKAN: Menyeragamkan cara menyimpan file.
     */
    public function update(Request $request, Anggota $anggota)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota,' . $anggota->id,
            'nama_lengkap' => 'required|max:100',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date|before_or_equal:today',
            'status' => 'required|in:aktif,non-aktif',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($anggota->foto && Storage::disk('public')->exists('anggota/' . $anggota->foto)) {
                Storage::disk('public')->delete('anggota/' . $anggota->foto);
            }
            $file = $request->file('foto');
            $filename = uniqid('anggota_') . '.' . $file->getClientOriginalExtension();
            // Simpan file menggunakan metode yang sama dengan ZIP upload
            $file->storeAs('anggota', $filename, 'public');
            $data['foto'] = $filename;
        }

        $anggota->update($data);
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui');
    }

    // ... sisa method Anda (destroy, card, dll.) tetap sama ...
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
        return view('anggota.card', compact('anggota'));
    }

    public function generateNomorAnggota()
    {
        $currentYear = date('Y');
        $lastAnggota = Anggota::where('nomor_anggota', 'like', "AGT{$currentYear}%")
            ->orderBy('nomor_anggota', 'desc')
            ->first();
        $newNumber = $lastAnggota ? str_pad((int) substr($lastAnggota->nomor_anggota, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        return response()->json(['nomor_anggota' => "AGT{$currentYear}{$newNumber}"]);
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
    
    public function import(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate(['file' => 'required|file|mimes:xlsx']);
        $file = $request->file('file');
        $rows = [];
        $errors = [];
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $header = [];
            foreach ($sheet->getRowIterator() as $i => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                if ($i == 1) {
                    $header = $cells;
                } else {
                    $rows[] = array_combine($header, $cells);
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
        $imported = 0;
        foreach ($rows as $i => $row) {
            if (empty($row['nomor_anggota']) || empty($row['nama_lengkap']) || empty($row['jenis_kelamin']) || empty($row['kelas']) || empty($row['alamat']) || empty($row['tanggal_daftar'])) {
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
                    'status' => $row['status'] ?? 'aktif',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris ke-".($i+2).": Gagal import (".$e->getMessage().")";
            }
        }
        $msg = $imported > 0 ? "$imported data anggota berhasil diimport." : 'Tidak ada data yang berhasil diimport.';
        if ($errors) $msg .= " Sebagian gagal: ".implode(' | ', $errors);
        return back()->with($imported > 0 ? 'success' : 'error', $msg);
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

    /**
     * Memproses upload foto massal dari file ZIP.
     */
    public function prosesUploadFotoZip(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }

        $request->validate([
            'zip_file' => 'required|file|mimetypes:application/zip,application/x-zip-compressed|max:20480',
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

        // =================================================================
        // <<<--- PERBAIKAN FINAL: EKSTRAKSI MANUAL ---<<<
        // =================================================================
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileName = $zip->getNameIndex($i);
            $fileInfo = pathinfo($fileName);

            // Lewati folder atau file tersembunyi
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
        // =================================================================

        $pesan = "$berhasil foto anggota berhasil diupdate.";
        if ($gagal > 0) {
            $pesanGagal = " $gagal foto gagal diproses karena nomor anggota tidak ditemukan: " . implode(', ', $logGagal);
            $status = ($berhasil > 0) ? 'warning' : 'error';
            return back()->with($status, $pesan . $pesanGagal);
        }

        return redirect()->route('anggota.index')->with('success', $pesan);
    }
}