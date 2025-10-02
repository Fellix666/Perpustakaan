@extends('layouts.app')

@section('title', 'Cetak Label Massal')
@section('page-title', 'Cetak Label Massal')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Data Buku</a></li>
<li class="breadcrumb-item active">Cetak Label Massal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Cetak Label Buku Massal</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('buku.print-labels-view') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="filter_kategori" name="filter_kategori">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ request('filter_kategori') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filter_rak" class="form-label">Rak</label>
                            <select class="form-select" id="filter_rak" name="filter_rak">
                                <option value="">Semua Rak</option>
                                @foreach($raks as $rak)
                                    <option value="{{ $rak->id }}" {{ request('filter_rak') == $rak->id ? 'selected' : '' }}>
                                        {{ $rak->nama_rak }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filter_tahun" class="form-label">Tahun Terbit</label>
                            <select class="form-select" id="filter_tahun" name="filter_tahun">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunTerbitList as $tahun)
                                    <option value="{{ $tahun }}" {{ request('filter_tahun') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filter_stok" class="form-label">Status Stok</label>
                            <select class="form-select" id="filter_stok" name="filter_stok">
                                <option value="">Semua Status</option>
                                <option value="tersedia" {{ request('filter_stok') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="habis" {{ request('filter_stok') == 'habis' ? 'selected' : '' }}>Habis</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter & Preview
                            </button>
                            <a href="{{ route('buku.print-labels-view') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh me-2"></i>Reset
                            </a>
                        </div>
                        @if($books->count() > 0)
                        <div class="col-md-6 text-end">
                            <a href="{{ route('buku.print-labels', [
                                'filter_kategori' => request('filter_kategori'),
                                'filter_rak' => request('filter_rak'),
                                'filter_tahun' => request('filter_tahun'),
                                'filter_stok' => request('filter_stok')
                            ]) }}" class="btn btn-success btn-lg" target="_blank">
                                <i class="fas fa-print me-2"></i>Cetak {{ $books->count() }} Label
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
                
                @if($books->count() > 0)
                <div class="mt-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ $books->count() }} buku</strong> ditemukan berdasarkan filter yang dipilih.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Buku</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Kategori</th>
                                    <th>Rak</th>
                                    <th>Stok</th>
                                    <th>Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($books as $index => $buku)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $buku->kode_buku }}</code></td>
                                    <td>{{ $buku->judul }}</td>
                                    <td>{{ $buku->pengarang }}</td>
                                    <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ $buku->rak->nama_rak ?? '-' }}</td>
                                    <td>
                                        @if($buku->stok_tersedia > 0)
                                            <span class="badge bg-success">{{ $buku->stok_tersedia }}</span>
                                        @else
                                            <span class="badge bg-danger">Habis</span>
                                        @endif
                                    </td>
                                    <td>{{ $buku->tahun_terbit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(request('filter_kategori') || request('filter_rak') || request('filter_tahun') || request('filter_stok'))
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tidak ada buku yang ditemukan dengan filter yang dipilih.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#filter_kategori, #filter_rak, #filter_tahun, #filter_stok');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
@endsection
