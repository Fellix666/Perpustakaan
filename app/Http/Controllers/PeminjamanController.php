<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['anggota', 'buku'])->orderBy('created_at', 'desc');
        // ... (kode filter Anda sudah benar) ...
        $peminjamans = $query->paginate(10)->withQueryString();
        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        
        // Jika ada anggota_id dari parameter (dari pengunjung), set sebagai selected
        $selectedAnggotaId = $request->get('anggota_id');
        
        return view('peminjaman.create', compact('selectedAnggotaId'));
    }

    public function store(Request $request)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
        ]);

        $buku = Buku::find($request->buku_id);
        if ($buku->stok_tersedia <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $kodePeminjaman = 'PJM' . date('Ymd') . sprintf('%04d', Peminjaman::count() + 1);
        Peminjaman::create([
            'kode_peminjaman' => $kodePeminjaman,
            'anggota_id' => $request->anggota_id,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => 'dipinjam',
        ]);

        // PERBAIKAN FINAL: Gunakan decrement untuk update stok yang andal
        $buku->decrement('stok_tersedia');
        if ($buku->fresh()->stok_tersedia <= 0) {
            $buku->status = 'tidak-tersedia';
            $buku->save();
        }

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dicatat.');
    }
    
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['anggota', 'buku', 'dendaRecord']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        if ($peminjaman->status === 'dikembalikan') {
            return back()->with('error', 'Tidak dapat menghapus peminjaman yang sudah dikembalikan.');
        }
        
        $buku = $peminjaman->buku;
        $peminjaman->delete();

        // PERBAIKAN FINAL: Gunakan increment untuk update stok yang andal
        if ($buku) {
            $buku->increment('stok_tersedia');
            $buku->status = 'tersedia';
            $buku->save();
        }
        
        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }
    
    // Method edit dan update Anda sudah benar
    public function edit(Peminjaman $peminjaman) 
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $anggotas = Anggota::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $bukus = Buku::where('status', 'tersedia')->orWhere('id', $peminjaman->buku_id)->orderBy('judul')->get();
        return view('peminjaman.edit', compact('peminjaman', 'anggotas', 'bukus'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
        ]);

        $buku = Buku::find($request->buku_id);
        if ($buku->stok_tersedia <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $peminjaman->update([
            'anggota_id' => $request->anggota_id,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    // Method untuk mencari anggota
    public function searchAnggota(Request $request)
    {
        $search = $request->get('search');
        $exactId = $request->get('exact_id');
        
        if (empty($search) && empty($exactId)) {
            return response()->json([]);
        }
        
        $query = Anggota::where('status', 'aktif');
        
        if ($exactId) {
            // Jika ada exact_id, cari berdasarkan ID
            $query->where('id', $exactId);
        } else {
            // Jika tidak ada exact_id, cari berdasarkan search term
            $query->where(function($q) use ($search) {
                $q->where('nomor_anggota', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }
        
        $anggotas = $query->orderBy('nama_lengkap')
                          ->limit(10)
                          ->get(['id', 'nomor_anggota', 'nama_lengkap', 'kelas']);

        return response()->json($anggotas);
    }

    // Method untuk mencari buku
    public function searchBuku(Request $request)
    {
        $search = $request->get('search');
        
        if (empty($search)) {
            return response()->json([]);
        }
        
        $bukus = Buku::where('status', 'tersedia')
                     ->where('stok_tersedia', '>', 0)
                     ->where(function($query) use ($search) {
                         $query->where('judul', 'like', "%{$search}%")
                               ->orWhere('kode_buku', 'like', "%{$search}%")
                               ->orWhere('pengarang', 'like', "%{$search}%");
                     })
                     ->orderBy('judul')
                     ->limit(10)
                     ->get(['id', 'judul', 'kode_buku', 'pengarang', 'stok_tersedia']);

        return response()->json($bukus);
    }
}