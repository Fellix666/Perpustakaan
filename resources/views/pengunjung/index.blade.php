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
        @if(auth('admin')->user()->role === 'admin')
        <a href="{{ route('pengunjung.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-2"></i>Tambah Kunjungan
        </a>
        @endif
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
        @if($pengunjungs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
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
                        @foreach($pengunjungs as $index => $pengunjung)
                        <tr>
                            <td>{{ $pengunjungs->firstItem() + $index }}</td>
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
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pengunjung.show', $pengunjung) }}" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(auth('admin')->user()->role === 'admin')
                                    <form action="{{ route('pengunjung.destroy', $pengunjung) }}" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $pengunjungs->firstItem() ?? 0 }} - {{ $pengunjungs->lastItem() ?? 0 }} dari {{ $pengunjungs->total() }} data
                    </div>
                    <div>
                        {{ $pengunjungs->links('vendor.pagination.simple-bootstrap-5') }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data pengunjung</h5>
                <p class="text-muted">Belum ada data kunjungan yang dicatat.</p>
                <a href="{{ route('pengunjung.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Kunjungan Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 