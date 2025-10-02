@extends('layouts.app')

@section('title', 'Data Anggota - Nama Aplikasi')
@section('page-title', 'Data Anggota')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Anggota</li>
@endsection

@section('page-actions')
<div class="d-flex flex-wrap gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('anggota.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Anggota
    </a>
    <a href="/template/template_import_anggota.xlsx" class="btn btn-outline-success" target="_blank">
        <i class="fas fa-download me-2"></i>Template Excel
    </a>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="fas fa-file-import me-2"></i>Import Excel
    </button>
    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadFotoModal">
        <i class="fas fa-file-archive me-2"></i>Upload Foto ZIP
    </button>
    @endif
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('anggota.print-cards') }}" class="btn btn-warning">
        <i class="fas fa-id-card me-2"></i>Cetak Kartu
    </a>
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
          <div class="alert alert-info">
            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Petunjuk Import Excel</h6>
            <ol class="mb-0">
              <li>Download template Excel terlebih dahulu</li>
              <li>Isi data sesuai format yang ada di template</li>
              <li>Pastikan nomor anggota unik dan tidak duplikat</li>
              <li>Upload file Excel yang sudah diisi</li>
            </ol>
          </div>
          
          <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" class="form-control" id="file" name="file" accept=".xlsx" required>
            <div class="form-text">Hanya file Excel (.xlsx) yang diperbolehkan</div>
          </div>
          
          <div class="alert alert-warning">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</h6>
            <ul class="mb-0">
              <li>Data yang sudah ada dengan nomor anggota yang sama akan diupdate</li>
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

<div class="modal fade" id="uploadFotoModal" tabindex="-1" aria-labelledby="uploadFotoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('anggota.proses-upload-foto') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="uploadFotoModalLabel">Upload Foto Anggota Massal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info">
                <strong>Petunjuk:</strong>
                <ol class="mb-0">
                    <li>Siapkan semua file foto (JPG, PNG).</li>
                                            <li>Ubah nama setiap foto agar sama persis dengan <strong>Nomor Anggota</strong>. Contoh: <strong>01-PPUS-2025.jpg</strong></li>
                    <li>Masukkan semua foto tersebut ke dalam satu file <strong>.zip</strong>.</li>
                    <li>Upload file .zip di bawah ini.</li>
                </ol>
            </div>
            <div class="mb-3">
                <label for="zip_file" class="form-label">Pilih File ZIP</label>
                <input type="file" class="form-control" name="zip_file" id="zip_file" accept=".zip" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Upload & Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('anggota.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="filterTahun" class="form-label mb-0">Tahun Ajaran Masuk</label>
                <select class="form-select" id="filterTahun" name="tahun_daftar">
                    <option value="">Semua</option>
                    @foreach($tahunDaftarList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun_daftar')==$tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterKelas" class="form-label mb-0">Kelas</label>
                <select class="form-select" id="filterKelas" name="kelas">
                    <option value="">Semua</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas }}" {{ request('kelas')==$kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterStatus" class="form-label mb-0">Status</label>
                <select class="form-select" id="filterStatus" name="status">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ request('status')=='non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50"><i class="fas fa-filter me-2"></i>Filter</button>
                <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
            </div>
        </form>
    </div>
</div>
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
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Cetak Kartu">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('anggota.card', $anggota) }}?color=blue" target="_blank">
                                            <i class="fas fa-circle text-primary me-2"></i>Biru
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('anggota.card', $anggota) }}?color=red" target="_blank">
                                            <i class="fas fa-circle text-danger me-2"></i>Merah
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('anggota.card', $anggota) }}?color=green" target="_blank">
                                            <i class="fas fa-circle text-success me-2"></i>Hijau
                                        </a></li>
                                    </ul>
                                </div>
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
                    Menampilkan {{ $anggotas->firstItem() ?? 0 }} - {{ $anggotas->lastItem() ?? 0 }} dari {{ $anggotas->total() }} data
                </div>
                <div>
                    {{ $anggotas->links('vendor.pagination.simple-bootstrap-5') }}
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