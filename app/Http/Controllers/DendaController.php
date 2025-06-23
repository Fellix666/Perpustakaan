<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;

class DendaController extends Controller
{
    public function index()
    {
        $dendas = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        return view('denda.index', compact('dendas'));
    }

    public function bayar($id)
    {
        $denda = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])->findOrFail($id);
        return view('denda.bayar', compact('denda'));
    }

    public function prosesBayar(Request $request, $id)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date'
        ]);

        $denda = Denda::findOrFail($id);
        $denda->status_bayar = 'dibayar';
        $denda->tanggal_bayar = $request->tanggal_bayar;
        $denda->save();

        return redirect()->route('denda.index')->with('success', 'Pembayaran denda berhasil dicatat');
    }
}
