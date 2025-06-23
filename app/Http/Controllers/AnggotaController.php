<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->paginate(10);
        return view('anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota',
            'nama_lengkap' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date'
        ]);

        Anggota::create($request->all());

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load(['peminjamans.buku', 'peminjamans.denda']);
        return view('anggota.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota,' . $anggota->id,
            'nama_lengkap' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date',
            'status' => 'required|in:aktif,non-aktif'
        ]);

        $anggota->update($request->all());

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui');
    }

    public function destroy(Anggota $anggota)
    {
        $anggota->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }
}

