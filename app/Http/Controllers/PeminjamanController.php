<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $anggotas = Anggota::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bukus = Buku::where('status', 'tersedia')->where('stok_tersedia', '>', 0)
                    ->orderBy('judul')->get();
        return view('peminjaman.create', compact('anggotas', 'bukus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam'
        ]);

        $buku = Buku::find($request->buku_id);
        
        if ($buku->stok_tersedia <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia');
        }

        $kodePeminjaman = 'PJM' . date('Ymd') . sprintf('%04d', Peminjaman::count() + 1);

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => $kodePeminjaman,
            'anggota_id' => $request->anggota_id,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => 'dipinjam'
        ]);

        $buku->updateStok();

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dicatat');
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['anggota', 'buku', 'denda']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function pengembalian($id)
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        
        if ($peminjaman->status == 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan');
        }

        return view('peminjaman.pengembalian', compact('peminjaman'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
        $peminjaman->status = 'dikembalikan';
        $peminjaman->keterangan = $request->keterangan;

        // Hitung denda jika terlambat
        $hariTerlambat = Carbon::parse($request->tanggal_kembali_aktual)
                            ->diffInDays($peminjaman->tanggal_kembali_rencana, false);
        
        if ($hariTerlambat > 0) {
            $totalDenda = $hariTerlambat * 1000;
            $peminjaman->denda = $totalDenda;

            Denda::create([
                'peminjaman_id' => $peminjaman->id,
                'hari_terlambat' => $hariTerlambat,
                'denda_per_hari' => 1000,
                'total_denda' => $totalDenda,
                'status_bayar' => 'belum-dibayar'
            ]);
        }

        $peminjaman->save();
        $peminjaman->buku->updateStok();

        return redirect()->route('peminjaman.index')->with('success', 'Pengembalian berhasil diproses');
    }

    public function edit(Peminjaman $peminjaman)
    {
        $anggotas = \App\Models\Anggota::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bukus = \App\Models\Buku::where('status', 'tersedia')->orWhere('id', $peminjaman->buku_id)->orderBy('judul')->get();
        return view('peminjaman.edit', compact('peminjaman', 'anggotas', 'bukus'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam'
        ]);
        $peminjaman->update($request->only(['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali_rencana', 'keterangan']));
        $peminjaman->buku->updateStok();
        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status == 'dikembalikan') {
            return back()->with('error', 'Tidak dapat menghapus peminjaman yang sudah dikembalikan');
        }
        $peminjaman->delete();
        $peminjaman->buku->updateStok();
        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus');
    }
}
