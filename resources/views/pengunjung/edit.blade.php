@extends('layouts.app')

@section('title', 'Selesai Kunjungan')
@section('page-title', 'Selesai Kunjungan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('pengunjung.index') }}">Data Pengunjung</a></li>
<li class="breadcrumb-item active">Selesai Kunjungan</li>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Selesai Kunjungan</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Kunjungan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Anggota:</strong> {{ $pengunjung->anggota->nama_lengkap }}</p>
                            <p><strong>Nomor Anggota:</strong> {{ $pengunjung->anggota->nomor_anggota }}</p>
                            <p><strong>Kelas:</strong> {{ $pengunjung->anggota->kelas }}</p>
                        </div>
                                                 <div class="col-md-6">
                             <p><strong>Tanggal:</strong> {{ $pengunjung->tanggal->format('d/m/Y') }}</p>
                             <p><strong>Tujuan:</strong> {{ $pengunjung->tujuan_kunjungan_text }}</p>
                             <p><strong>Keterangan:</strong> {{ $pengunjung->keterangan ?? '-' }}</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('pengunjung.update', $pengunjung) }}" method="POST">
            @csrf
            @method('PUT')
            
                         <div class="row g-3">
                 <div class="col-md-6">
                     <label for="tujuan_kunjungan" class="form-label">Tujuan Kunjungan <span class="text-danger">*</span></label>
                     <select class="form-select" id="tujuan_kunjungan" name="tujuan_kunjungan" required>
                         <option value="pinjam" {{ $pengunjung->tujuan_kunjungan == 'pinjam' ? 'selected' : '' }}>Pinjam Buku</option>
                         <option value="baca" {{ $pengunjung->tujuan_kunjungan == 'baca' ? 'selected' : '' }}>Baca di Tempat</option>
                     </select>
                 </div>
                 <div class="col-md-6">
                     <label for="keterangan" class="form-label">Keterangan</label>
                     <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                               placeholder="Tambahkan keterangan jika diperlukan...">{{ $pengunjung->keterangan }}</textarea>
                 </div>
             </div>

            

            <!-- Tombol Submit -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2">
                                                 <button type="submit" class="btn btn-success">
                             <i class="fas fa-save me-2"></i>Update Kunjungan
                         </button>
                        <a href="{{ route('pengunjung.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

 