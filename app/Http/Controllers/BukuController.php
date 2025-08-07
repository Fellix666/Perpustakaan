<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Pastikan ini ada
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive; // <-- Pastikan ini ada

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with(['kategori', 'rak'])->orderBy('judul');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('judul', 'like', "%$q%")
                    ->orWhere('kode_buku', 'like', "%$q%")
                    ->orWhere('pengarang', 'like', "%$q%")
                    ->orWhere('penerbit', 'like', "%$q%");
            });
        }
        if ($request->filled('kategori_id')) { $query->where('kategori_id', $request->kategori_id); }
        if ($request->filled('rak_id')) { $query->where('rak_id', $request->rak_id); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('tahun_terbit')) { $query->where('tahun_terbit', $request->tahun_terbit); }
        
        $bukus = $query->paginate(10)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $raks = Rak::orderBy('nama_rak')->get();
        $tahunTerbitList = Buku::select('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');
        
        return view('buku.index', compact('bukus', 'kategoris', 'raks', 'tahunTerbitList'));
    }

    public function create()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $raks = Rak::orderBy('nama_rak')->get();
        return view('buku.create', compact('kategoris', 'raks'));
    }

    /**
     * PERBAIKAN: Menyeragamkan cara menyimpan file cover.
     */
    public function store(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku',
            'isbn' => 'nullable|max:20',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'required|max:100',
            'tahun_terbit' => 'required|digits:4|integer',
            'jumlah_halaman' => 'nullable|integer',
            'deskripsi' => 'nullable',
            'stok_total' => 'required|integer|min:1',
            'kategori_id' => 'required|exists:kategoris,id',
            'rak_id' => 'required|exists:raks,id',
            'status' => 'required|in:tersedia,tidak-tersedia',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('cover');
        $data['stok_tersedia'] = $request->stok_total;

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = uniqid('buku_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('buku', $filename, 'public'); // Simpan ke public/buku
            $data['cover'] = $filename;
        }

        Buku::create($data);
        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan');
    }

    public function show(Buku $buku)
    {
        $buku->load(['kategori', 'rak', 'peminjamans.anggota']);
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $raks = Rak::orderBy('nama_rak')->get();
        return view('buku.edit', compact('buku', 'kategoris', 'raks'));
    }

    /**
     * PERBAIKAN: Menyeragamkan cara menyimpan file cover dan menghapus yang lama.
     */
    public function update(Request $request, Buku $buku)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku,' . $buku->id,
            'isbn' => 'nullable|max:20',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'required|max:100',
            'tahun_terbit' => 'required|digits:4|integer',
            'jumlah_halaman' => 'nullable|integer',
            'deskripsi' => 'nullable',
            'stok_total' => 'required|integer|min:1',
            'kategori_id' => 'required|exists:kategoris,id',
            'rak_id' => 'required|exists:raks,id',
            'status' => 'required|in:tersedia,tidak-tersedia',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('cover');

        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($buku->cover && Storage::disk('public')->exists('buku/' . $buku->cover)) {
                Storage::disk('public')->delete('buku/' . $buku->cover);
            }
            $file = $request->file('cover');
            $filename = uniqid('buku_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('buku', $filename, 'public');
            $data['cover'] = $filename;
        }
        
        // Update stok tersedia jika stok total berubah
        if ($request->stok_total != $buku->stok_total) {
            $selisih = $request->stok_total - $buku->stok_total;
            $data['stok_tersedia'] = max(0, $buku->stok_tersedia + $selisih);
        }

        $buku->update($data);
        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui');
    }

    /**
     * PERBAIKAN: Menambahkan logika untuk menghapus file cover.
     */
    public function destroy(Buku $buku)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        // Hapus cover jika ada
        if ($buku->cover && Storage::disk('public')->exists('buku/' . $buku->cover)) {
            Storage::disk('public')->delete('buku/' . $buku->cover);
        }
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus');
    }

    public function export()
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $bukus = \App\Models\Buku::with(['kategori', 'rak'])->orderBy('judul')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['kode_buku','isbn','judul','pengarang','penerbit','tahun_terbit','jumlah_halaman','deskripsi','stok_total','stok_tersedia','kategori_id','rak_id','status','cover']
        ], null, 'A1');
        $rowNum = 2;
        foreach ($bukus as $buku) {
            $sheet->fromArray([
                $buku->kode_buku,
                $buku->isbn,
                $buku->judul,
                $buku->pengarang,
                $buku->penerbit,
                $buku->tahun_terbit,
                $buku->jumlah_halaman,
                $buku->deskripsi,
                $buku->stok_total,
                $buku->stok_tersedia,
                $buku->kategori_id,
                $buku->rak_id,
                $buku->status,
                $buku->cover
            ], null, 'A'.$rowNum);
            $rowNum++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_buku_'.date('Y-m-d_H-i-s').'.xlsx';
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
        
        // Ambil data kategori dan rak untuk mapping
        $kategoris = \App\Models\Kategori::all()->keyBy('kode_kategori');
        $raks = \App\Models\Rak::all()->keyBy('kode_rak');
        
        $imported = 0;
        foreach ($rows as $i => $row) {
            if (empty($row['kode_buku']) || empty($row['judul']) || empty($row['pengarang']) || empty($row['penerbit']) || empty($row['tahun_terbit']) || empty($row['stok_total'])) {
                $errors[] = "Baris ke-".($i+2).": Data wajib tidak lengkap.";
                continue;
            }
            
            // Cek apakah menggunakan kode atau ID
            $kategoriId = null;
            $rakId = null;
            
            // Cek kategori
            if (!empty($row['kode_kategori'])) {
                // Menggunakan kode kategori
                if (!$kategoris->has($row['kode_kategori'])) {
                    $errors[] = "Baris ke-".($i+2).": Kode kategori '{$row['kode_kategori']}' tidak ditemukan.";
                    continue;
                }
                $kategoriId = $kategoris->get($row['kode_kategori'])->id;
            } elseif (!empty($row['kategori_id'])) {
                // Menggunakan ID kategori (backward compatibility)
                $kategoriId = $row['kategori_id'];
            } else {
                $errors[] = "Baris ke-".($i+2).": Kode kategori atau kategori_id harus diisi.";
                continue;
            }
            
            // Cek rak
            if (!empty($row['kode_rak'])) {
                // Menggunakan kode rak
                if (!$raks->has($row['kode_rak'])) {
                    $errors[] = "Baris ke-".($i+2).": Kode rak '{$row['kode_rak']}' tidak ditemukan.";
                    continue;
                }
                $rakId = $raks->get($row['kode_rak'])->id;
            } elseif (!empty($row['rak_id'])) {
                // Menggunakan ID rak (backward compatibility)
                $rakId = $row['rak_id'];
            } else {
                $errors[] = "Baris ke-".($i+2).": Kode rak atau rak_id harus diisi.";
                continue;
            }
            
            if (\App\Models\Buku::where('kode_buku', $row['kode_buku'])->exists()) {
                $errors[] = "Baris ke-".($i+2).": Kode buku sudah ada.";
                continue;
            }
            
            try {
                \App\Models\Buku::create([
                    'kode_buku' => $row['kode_buku'],
                    'isbn' => $row['isbn'] ?? null,
                    'judul' => $row['judul'],
                    'pengarang' => $row['pengarang'],
                    'penerbit' => $row['penerbit'],
                    'tahun_terbit' => $row['tahun_terbit'],
                    'jumlah_halaman' => $row['jumlah_halaman'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'stok_total' => $row['stok_total'],
                    'stok_tersedia' => $row['stok_total'],
                    'kategori_id' => $kategoriId,
                    'rak_id' => $rakId,
                    'status' => $row['status'] ?? 'tersedia',
                    // 'cover' => $row['cover'] ?? null, // cover diisi manual lewat aplikasi
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris ke-".($i+2).": Gagal import (".$e->getMessage().")";
            }
        }
        if ($imported > 0) {
            $msg = "$imported data buku berhasil diimport.";
            if ($errors) {
                $msg .= " Sebagian gagal: ".implode(' | ', $errors);
                return back()->with('success', $msg);
            }
            return back()->with('success', $msg);
        } else {
            return back()->with('error', 'Tidak ada data yang berhasil diimport. '.implode(' | ', $errors));
        }
    }
    public function prosesUploadCoverZip(Request $request)
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
            return back()->with('error', 'Gagal membuka file ZIP.');
        }

        $berhasil = 0;
        $gagal = 0;
        $logGagal = [];
        $semuaBuku = Buku::all()->keyBy('kode_buku');

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileName = $zip->getNameIndex($i);
            $fileInfo = pathinfo($fileName);

            if (substr($fileName, -1) == '/' || str_starts_with(basename($fileName), '._') || str_contains($fileName, '__MACOSX')) {
                continue;
            }

            $kodeBukuDariFile = trim($fileInfo['filename']);

            if ($semuaBuku->has($kodeBukuDariFile)) {
                $buku = $semuaBuku->get($kodeBukuDariFile);

                if ($buku->cover && Storage::disk('public')->exists('buku/' . $buku->cover)) {
                    Storage::disk('public')->delete('buku/' . $buku->cover);
                }

                $newFileName = uniqid('buku_') . '.' . $fileInfo['extension'];
                $fileContent = $zip->getFromIndex($i);
                Storage::disk('public')->put('buku/' . $newFileName, $fileContent);
                
                $buku->cover = $newFileName;
                $buku->save();
                $berhasil++;
            } else {
                $gagal++;
                $logGagal[] = basename($fileName);
            }
        }
        $zip->close();

        $pesan = "$berhasil cover buku berhasil diupdate.";
        if ($gagal > 0) {
            $pesanGagal = " $gagal cover gagal diproses karena kode buku tidak ditemukan: " . implode(', ', $logGagal);
            $status = ($berhasil > 0) ? 'warning' : 'error';
            return back()->with($status, $pesan . $pesanGagal);
        }

        return redirect()->route('buku.index')->with('success', $pesan);
    }
}