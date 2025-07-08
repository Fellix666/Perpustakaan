@extends('layouts.app')

@section('title', 'Data Rak - Nama Aplikasi')
@section('page-title', 'Data Rak')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Rak</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('rak.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Rak
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Daftar Rak Buku</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari rak..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($raks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Rak</th>
                        <th>Lokasi</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($raks as $index => $rak)
                    <tr>
                        <td>{{ $raks->firstItem() + $index }}</td>
                        <td>{{ $rak->nama_rak }}</td>
                        <td>{{ $rak->lokasi ?? '-' }}</td>
                        <td>{{ $rak->deskripsi ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @if(auth('admin')->user()->role === 'admin')
                                <a href="{{ route('rak.edit', $rak) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('rak.destroy', $rak) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" title="Hapus" onclick="return confirmDelete(event)">
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
                <div>
                    Menampilkan {{ $raks->firstItem() }} - {{ $raks->lastItem() }} dari {{ $raks->total() }} data
                </div>
                <div>
                    {{ $raks->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-archive fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data rak</h5>
            <p class="text-muted">Silakan tambah rak baru untuk memulai</p>
            <a href="{{ route('rak.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Rak Pertama
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
    if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
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