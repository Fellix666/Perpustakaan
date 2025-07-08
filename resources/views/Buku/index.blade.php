@extends('layouts.app')

@section('title', 'Data Buku - Nama Aplikasi')
@section('page-title', 'Data Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Buku</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('buku.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Buku
    </a>
    <a href="/template/template_import_buku.xlsx" class="btn btn-outline-info" target="_blank">
        <i class="fas fa-download me-2"></i>Download Template Excel
    </a>
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="fas fa-file-import me-2"></i>Import Excel
    </button>
    @endif
</div>

<!-- Modal Import Excel Buku -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('buku.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Buku dari Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" class="form-control" id="file" name="file" accept=".xlsx" required>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Download template: <a href="/template/template_import_buku.xlsx" target="_blank">Excel</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Daftar Buku Perpustakaan</h5>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('buku.index') }}" class="input-group">
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari buku...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($bukus->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode Buku</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>Rak</th>
                        <th>Stok Tersedia</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bukus as $index => $buku)
                    <tr>
                        <td>{{ $bukus->firstItem() + $index }}</td>
                        <td><code>{{ $buku->kode_buku }}</code></td>
                        <td>{{ $buku->judul }}</td>
                        <td>{{ $buku->pengarang }}</td>
                        <td>{{ $buku->penerbit }}</td>
                        <td>{{ $buku->tahun_terbit }}</td>
                        <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $buku->rak->nama_rak ?? '-' }}</td>
                        <td>{{ $buku->stok_tersedia }}</td>
                        <td>
                            @if($buku->status == 'tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('buku.show', $buku) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth('admin')->user()->role === 'admin')
                                <a href="{{ route('buku.edit', $buku) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('buku.destroy', $buku) }}" method="POST" style="display: inline;">
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
                    Menampilkan {{ $bukus->firstItem() }} - {{ $bukus->lastItem() }} dari {{ $bukus->total() }} data
                </div>
                <div>
                    {{ $bukus->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-book fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data buku</h5>
            <p class="text-muted">Silakan tambah buku baru untuk memulai</p>
            <a href="{{ route('buku.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Buku Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
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