<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::with(['kategori', 'rak'])->orderBy('judul')->paginate(10);
        return view('buku.index', compact('bukus'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $raks = Rak::orderBy('nama_rak')->get();
        return view('buku.create', compact('kategoris', 'raks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku',
            'isbn' => 'nullable|max:20',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'required|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'jumlah_halaman' => 'nullable|integer|min:1',
            'stok_total' => 'required|integer|min:1',
            'kategori_id' => 'required|exists:kategoris,id',
            'rak_id' => 'required|exists:raks,id'
        ]);

        $data = $request->all();
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
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $raks = Rak::orderBy('nama_rak')->get();
        return view('buku.edit', compact('buku', 'kategoris', 'raks'));
    }

    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku,' . $buku->id,
            'isbn' => 'nullable|max:20',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'required|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'jumlah_halaman' => 'nullable|integer|min:1',
            'stok_total' => 'required|integer|min:1',
            'kategori_id' => 'required|exists:kategoris,id',
            'rak_id' => 'required|exists:raks,id'
        ]);

        $data = $request->all();
        
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
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus');
    }
}

