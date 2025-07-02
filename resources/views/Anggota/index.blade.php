@extends('layouts.app')

@section('title', 'Data Anggota - Nama Aplikasi')
@section('page-title', 'Data Anggota')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Anggota</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('anggota.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Anggota
    </a>
    <a href="{{ route('anggota.export') }}" class="btn btn-success">
        <i class="fas fa-file-excel me-2"></i>Export CSV
    </a>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Daftar Anggota Perpustakaan</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari anggota..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($anggotas->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>No. Anggota</th>
                        <th>Nama Lengkap</th>
                        <th>Jenis Kelamin</th>
                        <th>Kelas</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anggotas as $index => $anggota)
                    <tr>
                        <td>{{ $anggotas->firstItem() + $index }}</td>
                        <td><code>{{ $anggota->nomor_anggota }}</code></td>
                        <td>
                            <div class="fw-bold">{{ $anggota->nama_lengkap }}</div>
                            <small class="text-muted">{{ $anggota->telepon ?? 'Tidak ada telepon' }}</small>
                        </td>
                        <td>
                            @if($anggota->jenis_kelamin == 'L')
                                <span class="badge bg-primary">Laki-laki</span>
                            @else
                                <span class="badge bg-info">Perempuan</span>
                            @endif
                        </td>
                        <td>{{ $anggota->kelas }}</td>
                        <td>{{ $anggota->tanggal_daftar->format('d/m/Y') }}</td>
                        <td>
                            @if($anggota->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('anggota.show', $anggota) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('anggota.card', $anggota) }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Cetak Kartu" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <form action="{{ route('anggota.destroy', $anggota) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" title="Hapus" onclick="return confirmDelete(event)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Menampilkan {{ $anggotas->firstItem() }} - {{ $anggotas->lastItem() }} dari {{ $anggotas->total() }} data
                </div>
                <div>
                    {{ $anggotas->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data anggota</h5>
            <p class="text-muted">Silakan tambah anggota baru untuk memulai</p>
            <a href="{{ route('anggota.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Anggota Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

function confirmDelete(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
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