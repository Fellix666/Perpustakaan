@extends('layouts.app')

@section('title', 'Data Pengembalian')
@section('page-title', 'Data Pengembalian')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Pengembalian</li>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pengembalian.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="filterStatus" class="form-label mb-0">Status</label>
                <select class="form-select" id="filterStatus" name="status">
                    <option value="">Semua</option>
                    <option value="dikembalikan" {{ request('status')=='dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="terlambat" {{ request('status')=='terlambat' ? 'selected' : '' }}>Terlambat</option>
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
                <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Daftar Pengembalian</h5>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('pengembalian.index') }}" class="input-group">
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari kode/nama anggota/buku...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
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
                        <th>Tgl Kembali (Rencana)</th>
                        <th>Tgl Kembali (Aktual)</th>
                        <th>Status</th>
                        <th>Denda</th>
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
                        <td>{{ $peminjaman->tanggal_kembali_aktual ? $peminjaman->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($peminjaman->status == 'dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>
                            @elseif($peminjaman->status == 'terlambat')
                                <span class="badge bg-danger">Terlambat</span>
                            @else
                                <span class="badge bg-warning">Dipinjam</span>
                            @endif
                        </td>
                        <td>
                            @if($peminjaman->denda)
                                <span class="text-danger fw-bold">Rp {{ number_format($peminjaman->denda->total_denda, 0, ',', '.') }}</span>
                                <br><small class="badge bg-{{ $peminjaman->denda->status_bayar == 'belum-dibayar' ? 'danger' : 'success' }}">{{ $peminjaman->denda->status_bayar == 'belum-dibayar' ? 'Belum Lunas' : 'Lunas' }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('pengembalian.show', $peminjaman->id) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i></a>
                                @if($peminjaman->status == 'dikembalikan')
                                <a href="{{ route('pengembalian.edit', $peminjaman->id) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pengembalian.destroy', $peminjaman->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" title="Hapus" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></button>
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
                <div>Menampilkan {{ $peminjamans->firstItem() }} - {{ $peminjamans->lastItem() }} dari {{ $peminjamans->total() }} data</div>
                <div>{{ $peminjamans->links() }}</div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-undo fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data pengembalian</h5>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        event.target.closest('form').submit();
    }
    return false;
}
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection 