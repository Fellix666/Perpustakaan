@extends('layouts.app')

@section('title', 'Detail Kunjungan')
@section('page-title', 'Detail Kunjungan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('pengunjung.index') }}">Data Pengunjung</a></li>
<li class="breadcrumb-item active">Detail Kunjungan</li>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Detail Kunjungan</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Informasi Anggota -->
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Anggota</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama Lengkap:</strong></td>
                                <td>{{ $pengunjung->anggota->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nomor Anggota:</strong></td>
                                <td>{{ $pengunjung->anggota->nomor_anggota }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kelas:</strong></td>
                                <td>{{ $pengunjung->anggota->kelas }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin:</strong></td>
                                <td>{{ $pengunjung->anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($pengunjung->anggota->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Informasi Kunjungan -->
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Informasi Kunjungan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Tanggal:</strong></td>
                                <td>{{ $pengunjung->tanggal->format('d/m/Y') }}</td>
                            </tr>
                            
                            <tr>
                                <td><strong>Tujuan:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $pengunjung->tujuan_kunjungan_text }}</span>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        @if($pengunjung->keterangan)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Keterangan</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $pengunjung->keterangan }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2">
                                         <a href="{{ route('pengunjung.edit', $pengunjung) }}" class="btn btn-warning">
                         <i class="fas fa-edit me-2"></i>Edit Kunjungan
                     </a>
                    <a href="{{ route('pengunjung.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <form action="{{ route('pengunjung.destroy', $pengunjung) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data kunjungan ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 