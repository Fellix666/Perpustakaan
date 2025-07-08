<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $bukus = $query->paginate(10)->withQueryString();
        return view('buku.index', compact('bukus'));
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

        $data = $request->all();
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = uniqid('buku_').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/buku', $filename);
            $data['cover'] = $filename;
        }
        $data['stok_tersedia'] = $data['stok_total'];

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

        $data = $request->all();
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = uniqid('buku_').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/buku', $filename);
            $data['cover'] = $filename;
        } else {
            unset($data['cover']);
        }
        
        // Update stok tersedia jika stok total berubah
        if ($data['stok_total'] != $buku->stok_total) {
            $selisih = $data['stok_total'] - $buku->stok_total;
            $data['stok_tersedia'] = $buku->stok_tersedia + $selisih;
            $data['stok_tersedia'] = max(0, $data['stok_tersedia']);
        }

        $buku->update($data);
        $buku->updateStok();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy(Buku $buku)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus');
    }

    public function export()
    {
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
        $imported = 0;
        foreach ($rows as $i => $row) {
            if (empty($row['kode_buku']) || empty($row['judul']) || empty($row['pengarang']) || empty($row['penerbit']) || empty($row['tahun_terbit']) || empty($row['stok_total']) || empty($row['kategori_id']) || empty($row['rak_id'])) {
                $errors[] = "Baris ke-".($i+2).": Data wajib tidak lengkap.";
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
                    'kategori_id' => $row['kategori_id'],
                    'rak_id' => $row['rak_id'],
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
}

