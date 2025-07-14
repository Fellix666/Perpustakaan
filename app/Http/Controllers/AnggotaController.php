<?php
namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggota::query();

        // Filter berdasarkan tahun daftar
        if ($request->filled('tahun_daftar')) {
            $query->whereYear('tanggal_daftar', $request->tahun_daftar);
        }
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Pencarian umum (opsional, tetap dipertahankan)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%$q%")
                    ->orWhere('nomor_anggota', 'like', "%$q%")
                    ->orWhere('kelas', 'like', "%$q%");
            });
        }
        $anggotas = $query->orderBy('nomor_anggota')->paginate(10)->withQueryString();
        // Data untuk filter dropdown
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

        $data = $request->all();
        $data['status'] = 'aktif';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = uniqid('anggota_').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/anggota', $filename);
            $data['foto'] = $filename;
        }
        Anggota::create($data);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load(['peminjamans.buku', 'peminjamans.denda']);
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

        $data = $request->all();
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = uniqid('anggota_').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/anggota', $filename);
            $data['foto'] = $filename;
        } else {
            unset($data['foto']);
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

        $anggota->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }

    // Perbaikan fungsi card - mengarahkan ke view card yang sudah ada
    public function card(Anggota $anggota)
    {
        return view('anggota.card', compact('anggota'));
    }

    // Tambahan fungsi untuk cetak kartu dalam format print-friendly
    public function printCard(Anggota $anggota)
    {
        return view('anggota.card', compact('anggota'));
    }

    public function generateNomorAnggota()
    {
        $currentYear = date('Y');
        $lastAnggota = Anggota::where('nomor_anggota', 'like', "AGT{$currentYear}%")
            ->orderBy('nomor_anggota', 'desc')
            ->first();

        if ($lastAnggota) {
            $lastNumber = (int) substr($lastAnggota->nomor_anggota, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

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

    public function export()
    {
        // Export Excel saja (CSV dihapus)
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['nomor_anggota','nama_lengkap','jenis_kelamin','kelas','alamat','telepon','tanggal_daftar','status']
        ], null, 'A1');
        $rowNum = 2;
        foreach ($anggotas as $anggota) {
            $sheet->fromArray([
                $anggota->nomor_anggota,
                $anggota->nama_lengkap,
                $anggota->jenis_kelamin,
                $anggota->kelas,
                $anggota->alamat,
                $anggota->telepon,
                $anggota->tanggal_daftar->format('Y-m-d'),
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
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);
        $file = $request->file('file');
        $rows = [];
        $errors = [];
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
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
            if (\App\Models\Anggota::where('nomor_anggota', $row['nomor_anggota'])->exists()) {
                $errors[] = "Baris ke-".($i+2).": Nomor anggota sudah ada.";
                continue;
            }
            try {
                \App\Models\Anggota::create([
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
        if ($imported > 0) {
            $msg = "$imported data anggota berhasil diimport.";
            if ($errors) {
                $msg .= " Sebagian gagal: ".implode(' | ', $errors);
                return back()->with('success', $msg);
            }
            return back()->with('success', $msg);
        } else {
            return back()->with('error', 'Tidak ada data yang berhasil diimport. '.implode(' | ', $errors));
        }
    }

    // Tambahan: fungsi konversi tanggal dari Excel
    private function parseExcelDate($value)
    {
        // Jika numeric (serial Excel)
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        // Jika string, coba parse ke Y-m-d
        if ($value) {
            $formats = [
                'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y', 'd.m.Y', 'Y.m.d'
            ];
            foreach ($formats as $fmt) {
                $date = \DateTime::createFromFormat($fmt, $value);
                if ($date) return $date->format('Y-m-d');
            }
            // Coba parse otomatis
            $date = date_create($value);
            if ($date) return $date->format('Y-m-d');
        }
        return null;
    }
}