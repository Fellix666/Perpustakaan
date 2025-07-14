@extends('layouts.app')

@section('title', 'Data Anggota - Nama Aplikasi')
@section('page-title', 'Data Anggota')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Anggota</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('anggota.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Anggota
    </a>
    <a href="/template/template_import_anggota.xlsx" class="btn btn-outline-info" target="_blank">
        <i class="fas fa-download me-2"></i>Download Template Excel
    </a>
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="fas fa-file-import me-2"></i>Import Excel
    </button>
    @endif
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('anggota.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Anggota dari Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" class="form-control" id="file" name="file" accept=".xlsx" required>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Download template: <a href="/template/template_import_anggota.xlsx" target="_blank">Excel</a>
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
                <h5 class="mb-0">Daftar Anggota Perpustakaan</h5>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('anggota.index') }}" class="input-group">
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari anggota...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
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
                            @if($anggota->status_realtime == 'aktif')
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
                                @if(auth('admin')->user()->role === 'admin')
                                <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('anggota.destroy', $anggota) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" title="Hapus" onclick="return confirmDelete(event)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('anggota.card', $anggota) }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Cetak Kartu" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
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