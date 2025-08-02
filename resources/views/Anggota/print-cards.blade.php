@extends('layouts.app')

@section('title', 'Cetak Kartu')
@section('page-title', 'Cetak Kartu')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('anggota.index') }}">Data Anggota</a></li>
<li class="breadcrumb-item active">Cetak Kartu</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Cetak Kartu Anggota</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Cetak Kartu</h6>
                    <p class="mb-2">Fitur ini memungkinkan Anda untuk mencetak kartu anggota secara massal berdasarkan:</p>
                    <ul class="mb-0">
                        <li><strong>Kelas:</strong> Pilih kelas tertentu atau biarkan kosong untuk semua kelas</li>
                        <li><strong>Tahun Ajaran:</strong> Berdasarkan tahun daftar anggota</li>
                    </ul>
                </div>

                <form method="GET" action="{{ route('anggota.print-cards') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" id="kelas" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelasOption)
                                    <option value="{{ $kelasOption }}" {{ $kelas == $kelasOption ? 'selected' : '' }}>
                                        {{ $kelasOption }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih kelas tertentu atau biarkan kosong untuk semua kelas</small>
                        </div>
                        <div class="col-md-6">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran (Tahun Daftar)</label>
                            <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun }}" {{ $tahunAjaran == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}/{{ $tahun + 1 }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Berdasarkan tahun daftar anggota</small>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Tampilkan Anggota
                        </button>
                        <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>

                @if(request()->has('kelas') || request()->has('tahun_ajaran'))
                    <hr>
                    <div class="mt-4">
                        <h6 class="mb-3">
                            <i class="fas fa-users me-2"></i>
                            Hasil Pencarian: {{ $anggotas->count() }} Anggota
                            @if($kelas)
                                <span class="badge bg-primary ms-2">Kelas: {{ $kelas }}</span>
                            @endif
                            @if($tahunAjaran)
                                <span class="badge bg-success ms-2">Tahun: {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</span>
                            @endif
                        </h6>

                        @if($anggotas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Anggota</th>
                                            <th>Nama Lengkap</th>
                                            <th>Kelas</th>
                                            <th>Tahun Daftar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($anggotas as $index => $anggota)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>{{ $anggota->nomor_anggota }}</code></td>
                                            <td>{{ $anggota->nama_lengkap }}</td>
                                            <td><span class="badge bg-info">{{ $anggota->kelas }}</span></td>
                                            <td>{{ $anggota->tanggal_daftar->format('Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('anggota.print-cards-view', request()->all()) }}" target="_blank" class="btn btn-success">
                                    <i class="fas fa-print me-2"></i>Cetak {{ $anggotas->count() }} Kartu
                                </a>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Kartu akan dicetak dengan desain yang sama
                                </small>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Tidak ada anggota yang ditemukan dengan filter yang dipilih.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 