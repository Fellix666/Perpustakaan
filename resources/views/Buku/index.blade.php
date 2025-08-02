@extends('layouts.app')

@section('title', 'Data Buku')
@section('page-title', 'Data Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Buku</li>
@endsection

@section('page-actions')
<div class="d-flex flex-wrap gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('buku.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Buku</a>
    <a href="/template/template_import_buku.xlsx" class="btn btn-outline-success" target="_blank"><i class="fas fa-download me-2"></i>Template Excel</a>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-file-import me-2"></i>Import Excel</button>
    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadCoverModal"><i class="fas fa-file-archive me-2"></i>Upload Cover ZIP</button>
    @endif
</div>
@endsection

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('buku.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="filterKategori" class="form-label mb-0">Kategori</label>
                <select class="form-select" id="filterKategori" name="kategori_id">
                    <option value="">Semua</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id')==$kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterRak" class="form-label mb-0">Rak</label>
                <select class="form-select" id="filterRak" name="rak_id">
                    <option value="">Semua</option>
                    @foreach($raks as $rak)
                        <option value="{{ $rak->id }}" {{ request('rak_id')==$rak->id ? 'selected' : '' }}>{{ $rak->nama_rak }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="filterTahun" class="form-label mb-0">Tahun Terbit</label>
                <select class="form-select" id="filterTahun" name="tahun_terbit">
                    <option value="">Semua</option>
                    @foreach($tahunTerbitList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun_terbit')==$tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="filterStatus" class="form-label mb-0">Status</label>
                <select class="form-select" id="filterStatus" name="status">
                    <option value="">Semua</option>
                    <option value="tersedia" {{ request('status')=='tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="tidak-tersedia" {{ request('status')=='tidak-tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50"><i class="fas fa-filter me-2"></i>Filter</button>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
            </div>
        </form>
    </div>
</div>

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
                <div class="text-muted small">
                    Menampilkan {{ $bukus->firstItem() ?? 0 }} - {{ $bukus->lastItem() ?? 0 }} dari {{ $bukus->total() }} data
                </div>
                <div>
                    {{ $bukus->links('vendor.pagination.simple-bootstrap-5') }}
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
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Petunjuk Import Excel</h6>
                        <ol class="mb-0">
                            <li>Download template Excel terlebih dahulu</li>
                            <li>Isi data sesuai format yang ada di template</li>
                            <li>Pastikan kode buku unik dan tidak duplikat</li>
                            <li>Upload file Excel yang sudah diisi</li>
                        </ol>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="file" accept=".xlsx" required>
                        <div class="form-text">Hanya file Excel (.xlsx) yang diperbolehkan</div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</h6>
                        <ul class="mb-0">
                            <li>Data yang sudah ada dengan kode buku yang sama akan diupdate</li>
                            <li>Pastikan format data sesuai dengan template</li>
                            <li>Proses import mungkin memakan waktu beberapa saat</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import me-2"></i>Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BARU: Upload Cover ZIP -->
<div class="modal fade" id="uploadCoverModal" tabindex="-1" aria-labelledby="uploadCoverModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('buku.proses-upload-cover') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header"><h5 class="modal-title" id="uploadCoverModalLabel">Upload Cover Buku Massal</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body">
            <div class="alert alert-info"><p class="fw-bold">Petunjuk:</p><ol class="mb-0"><li>Ubah nama setiap cover agar sama persis dengan <strong>Kode Buku</strong> (Contoh: <strong>BKU001.jpg</strong>).</li><li>Masukkan semua cover ke dalam satu file <strong>.zip</strong>.</li></ol></div>
            <div class="mb-3"><label for="zip_file" class="form-label">Pilih File ZIP</label><input type="file" class="form-control" name="zip_file" id="zip_file" accept=".zip" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Upload & Proses</button></div>
      </form>
    </div>
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
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // File input validation for Excel
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var fileName = file.name.toLowerCase();
                if (!fileName.endsWith('.xlsx')) {
                    alert('Hanya file Excel (.xlsx) yang diperbolehkan!');
                    this.value = '';
                }
            }
        });
    }
    
    // File input validation for ZIP
    const zipInput = document.getElementById('zip_file');
    if (zipInput) {
        zipInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var fileName = file.name.toLowerCase();
                if (!fileName.endsWith('.zip')) {
                    alert('Hanya file ZIP yang diperbolehkan!');
                    this.value = '';
                }
            }
        });
    }
});
</script>
@endsection 