@extends('layouts.app')

@section('title', 'Cetak Kartu Massal')
@section('page-title', 'Cetak Kartu Massal')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('anggota.index') }}">Data Anggota</a></li>
<li class="breadcrumb-item active">Cetak Kartu Massal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Cetak Kartu Anggota Massal</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('anggota.print-cards-view') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select class="form-select" id="kelas" name="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasFilterList as $kelas)
                                    <option value="{{ $kelas }}" {{ $selectedKelas == $kelas ? 'selected' : '' }}>
                                        {{ $kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="tahun_daftar" class="form-label">Tahun Ajaran Masuk</label>
                            <select class="form-select" id="tahun_daftar" name="tahun_daftar">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunAjaranList as $tahunAjaran)
                                    <option value="{{ $tahunAjaran }}" {{ $selectedTahun == $tahunAjaran ? 'selected' : '' }}>
                                        {{ $tahunAjaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="color" class="form-label">Warna Kartu</label>
                            <select class="form-select" id="color" name="color">
                                <option value="blue" selected>Biru (Default)</option>
                                <option value="red">Merah</option>
                                <option value="green">Hijau</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter & Preview
                            </button>
                            <a href="{{ route('anggota.print-cards') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
                
                @if($anggotas->count() > 0)
                <div class="mt-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ $anggotas->count() }} anggota</strong> ditemukan berdasarkan filter yang dipilih.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>No. Anggota</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anggotas as $index => $anggota)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $anggota->nomor_anggota }}</code></td>
                                    <td>{{ $anggota->nama_lengkap }}</td>
                                    <td>{{ $anggota->kelas }}</td>
                                    <td>{{ $anggota->tahun_ajaran_masuk }}/{{ $anggota->tahun_ajaran_masuk + 1 }}</td>
                                    <td>
                                        @if($anggota->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('anggota.print-cards-view', [
                            'kelas' => $selectedKelas,
                            'tahun_daftar' => $selectedTahun,
                            'color' => request('color', 'blue')
                        ]) }}" class="btn btn-success btn-lg" target="_blank">
                            <i class="fas fa-print me-2"></i>Cetak {{ $anggotas->count() }} Kartu
                        </a>
                    </div>
                </div>
                @elseif($selectedKelas || $selectedTahun)
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tidak ada anggota yang ditemukan dengan filter yang dipilih.
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
    // Auto-submit form when filters change
    const filterInputs = document.querySelectorAll('#kelas, #tahun_daftar, #color');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
@endsection 