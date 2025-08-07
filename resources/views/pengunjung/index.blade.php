@extends('layouts.app')

@section('title', 'Data Pengunjung')
@section('page-title', 'Data Pengunjung Perpustakaan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Pengunjung</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Data Pengunjung Perpustakaan</h5>
        <a href="{{ route('pengunjung.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-2"></i>Tambah Kunjungan
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('pengunjung.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
            </div>

            <div class="col-md-3">
                <label for="tujuan" class="form-label">Tujuan</label>
                <select class="form-select" id="tujuan" name="tujuan">
                    <option value="">Semua Tujuan</option>
                    <option value="pinjam" {{ request('tujuan') == 'pinjam' ? 'selected' : '' }}>Pinjam Buku</option>
                    <option value="baca" {{ request('tujuan') == 'baca' ? 'selected' : '' }}>Baca di Tempat</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('pengunjung.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel Data -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Anggota</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengunjungs as $index => $pengunjung)
                    <tr>
                        <td>{{ $index + 1 + ($pengunjungs->currentPage() - 1) * $pengunjungs->perPage() }}</td>
                        <td>{{ $pengunjung->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $pengunjung->anggota->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $pengunjung->anggota->nomor_anggota }} - {{ $pengunjung->anggota->kelas }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $pengunjung->tujuan_kunjungan_text }}</span>
                        </td>
                        <td>{{ $pengunjung->keterangan ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pengunjung.show', $pengunjung) }}" class="btn btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <form action="{{ route('pengunjung.destroy', $pengunjung) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>Tidak ada data pengunjung</h5>
                                <p>Belum ada data kunjungan yang dicatat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $pengunjungs->links() }}
        </div>
    </div>
</div>

<!-- Statistik Cepat -->
<div class="row">
         <div class="col-md-4">
         <div class="card bg-primary text-white">
             <div class="card-body">
                 <h6 class="card-title">Total Kunjungan Hari Ini</h6>
                 <h3 class="fw-bold">{{ $pengunjungs->where('tanggal', now()->toDateString())->count() }}</h3>
             </div>
         </div>
     </div>
     
     <div class="col-md-4">
         <div class="card bg-info text-white">
             <div class="card-body">
                 <h6 class="card-title">Total Semua</h6>
                 <h3 class="fw-bold">{{ $pengunjungs->total() }}</h3>
             </div>
         </div>
     </div>
     
     <div class="col-md-4">
         <div class="card bg-success text-white">
             <div class="card-body">
                 <h6 class="card-title">Pinjam Buku</h6>
                 <h3 class="fw-bold">{{ $pengunjungs->where('tujuan_kunjungan', 'pinjam')->count() }}</h3>
             </div>
         </div>
     </div>
</div>
@endsection 