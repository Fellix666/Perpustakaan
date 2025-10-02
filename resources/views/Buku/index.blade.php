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
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('buku.print-labels-view') }}" class="btn btn-secondary"><i class="fas fa-tags me-2"></i>Cetak Label Masal</a>
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
                        <th width="5%">No</th>
                        <th width="10%">Kode Buku</th>
                        <th width="20%">Judul</th>
                        <th width="15%">Pengarang</th>
                        <th width="10%">Kategori</th>
                        <th width="10%">Rak</th>
                        <th width="10%">Stok</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bukus as $index => $buku)
                    <tr>
                        <td>{{ $bukus->firstItem() + $index }}</td>
                        <td>{{ $buku->kode_buku }}</td>
                        <td>{{ $buku->judul }}</td>
                        <td>{{ $buku->pengarang }}</td>
                        <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $buku->rak->nama_rak ?? '-' }}</td>
                        <td>{{ $buku->stok_tersedia }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth('admin')->user()->role === 'admin')
                                <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('buku.label', $buku->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-tag"></i>
                                </a>
                                <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
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

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('buku.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel</label>
                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls" required>
                        <div class="form-text">Format: .xlsx atau .xls</div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ asset('template/template_import_buku.xlsx') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> Download Template
                        </a>
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

<!-- Modal Upload Cover ZIP -->
<div class="modal fade" id="uploadCoverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Cover Buku (ZIP)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('buku.proses-upload-cover') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cover_zip" class="form-label">File ZIP Cover</label>
                        <input type="file" class="form-control" name="cover_zip" accept=".zip" required>
                        <div class="form-text">Upload file ZIP yang berisi foto cover buku. Nama file harus sesuai dengan kode buku.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection 