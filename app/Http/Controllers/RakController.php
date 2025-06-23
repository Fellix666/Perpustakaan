<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    public function index()
    {
        $raks = Rak::withCount('bukus')->orderBy('nama_rak')->paginate(10);
        return view('rak.index', compact('raks'));
    }

    public function create()
    {
        return view('rak.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak|max:10',
            'nama_rak' => 'required|max:100',
            'lokasi' => 'required|max:100',
            'kapasitas' => 'required|integer|min:1'
        ]);

        Rak::create($request->all());

        return redirect()->route('rak.index')->with('success', 'Rak berhasil ditambahkan');
    }

    public function show(Rak $rak)
    {
        $rak->load(['bukus.kategori']);
        return view('rak.show', compact('rak'));
    }

    public function edit(Rak $rak)
    {
        return view('rak.edit', compact('rak'));
    }

    public function update(Request $request, Rak $rak)
    {
        $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak,' . $rak->id . '|max:10',
            'nama_rak' => 'required|max:100',
            'lokasi' => 'required|max:100',
            'kapasitas' => 'required|integer|min:1'
        ]);

        $rak->update($request->all());

        return redirect()->route('rak.index')->with('success', 'Rak berhasil diperbarui');
    }

    public function destroy(Rak $rak)
    {
        if ($rak->bukus()->count() > 0) {
            return redirect()->route('rak.index')->with('error', 'Rak tidak dapat dihapus karena masih memiliki buku');
        }

        $rak->delete();
        return redirect()->route('rak.index')->with('success', 'Rak berhasil dihapus');
    }
}
