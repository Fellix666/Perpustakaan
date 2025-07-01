<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        // Ambil data peminjaman yang belum dikembalikan
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
                        ->whereIn('status', ['dipinjam', 'terlambat'])
                        ->orderBy('tanggal_kembali_rencana', 'asc')
                        ->paginate(10);
        
        return view('pengembalian.index', compact('peminjamans'));
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        
        if ($peminjaman->status == 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan');
        }
        
        return view('pengembalian.show', compact('peminjaman'));
    }

    public function create($id)
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        
        if ($peminjaman->status == 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan');
        }
        
        // Hitung denda jika terlambat
        $hariTerlambat = 0;
        $totalDenda = 0;
        $today = Carbon::now();
        
        if ($today->gt($peminjaman->tanggal_kembali_rencana)) {
            $hariTerlambat = $today->diffInDays($peminjaman->tanggal_kembali_rencana);
            $totalDenda = $hariTerlambat * 1000; // Rp 1000 per hari
        }
        
        return view('pengembalian.create', compact('peminjaman', 'hariTerlambat', 'totalDenda'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        
        // Update data pengembalian
        $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
        $peminjaman->status = 'dikembalikan';
        $peminjaman->keterangan = $request->keterangan;

        // Hitung denda jika terlambat
        $hariTerlambat = Carbon::parse($request->tanggal_kembali_aktual)
                          ->diffInDays($peminjaman->tanggal_kembali_rencana, false);

        if ($hariTerlambat > 0) {
            $totalDenda = $hariTerlambat * 1000;
            $peminjaman->denda = $totalDenda;

            // Buat record denda
            Denda::create([
                'peminjaman_id' => $peminjaman->id,
                'hari_terlambat' => $hariTerlambat,
                'denda_per_hari' => 1000,
                'total_denda' => $totalDenda,
                'status_bayar' => 'belum-dibayar'
            ]);
        }

        $peminjaman->save();

        // Update stok buku
        $peminjaman->buku->updateStok();

        return redirect()->route('pengembalian.index')
                        ->with('success', 'Pengembalian berhasil diproses');
    }
}