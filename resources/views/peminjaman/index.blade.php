@extends('layouts.app')
@section('title', 'Data Peminjaman')
@section('page-title', 'Data Peminjaman')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Peminjaman</li>
@endsection

@section('page-actions')
    @if(auth('admin')->user()->role === 'admin')
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Peminjaman
        </a>
    @endif
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('peminjaman.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label mb-0">Status</label>
                    <select class="form-select" id="filterStatus" name="status">
                        <option value="">Semua</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterAnggota" class="form-label mb-0">Anggota</label>
                    <input type="text" class="form-control" id="filterAnggota" name="anggota" value="{{ request('anggota') }}" placeholder="Nama anggota">
                </div>
                <div class="col-md-3">
                    <label for="filterBuku" class="form-label mb-0">Buku</label>
                    <input type="text" class="form-control" id="filterBuku" name="buku" value="{{ request('buku') }}" placeholder="Judul buku">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50"><i class="fas fa-filter me-2"></i>Filter</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Daftar Peminjaman</h5>
                </div>
                <div class="col-auto">
                    <form method="GET" action="{{ route('peminjaman.index') }}" class="input-group">
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari kode/nama/buku...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($peminjamans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamans as $index => $peminjaman)
                                <tr>
                                    <td>{{ $peminjamans->firstItem() + $index }}</td>
                                    <td><code>{{ $peminjaman->kode_peminjaman }}</code></td>
                                    <td>{{ $peminjaman->anggota->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $peminjaman->buku->judul ?? '-' }}</td>
                                    <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td>{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                                    <td>
                                        @php $status = $peminjaman->status_realtime; @endphp
                                        @if($status == 'dipinjam')
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        @elseif($status == 'dikembalikan')
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @elseif($status == 'terlambat')
                                            <span class="badge bg-danger">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('peminjaman.show', $peminjaman) }}" class="btn btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                                            @if(auth('admin')->user()->role === 'admin')
                                                @if($peminjaman->status != 'dikembalikan')
                                                    <a href="{{ route('peminjaman.edit', $peminjaman) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                    <form action="{{ route('peminjaman.destroy', $peminjaman) }}" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    <a href="{{ route('pengembalian.create', $peminjaman->id) }}" class="btn btn-success" title="Proses Pengembalian"><i class="fas fa-undo"></i></a>
                                                @endif
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
                            Menampilkan {{ $peminjamans->firstItem() ?? 0 }} - {{ $peminjamans->lastItem() ?? 0 }} dari {{ $peminjamans->total() }} data
                        </div>
                        <div>
                            {{ $peminjamans->links('vendor.pagination.simple-bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-muted">Data tidak ditemukan.</h5>
                </div>
            @endif
        </div>
    </div>
@endsection