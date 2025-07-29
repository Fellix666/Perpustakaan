@extends('layouts.app')

@section('title', 'Detail Anggota - Nama Aplikasi')
@section('page-title', 'Detail Anggota')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('anggota.index') }}">Data Anggota</a></li>
<li class="breadcrumb-item active">Detail Anggota</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('anggota.card', $anggota) }}" class="btn btn-info" target="_blank">
        <i class="fas fa-print me-2"></i>Cetak Kartu
    </a>
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Edit
    </a>
    <form action="{{ route('anggota.destroy', $anggota) }}" method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus data ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Anggota</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($anggota->foto)
                        <img src="{{ asset('storage/anggota/'.$anggota->foto) }}" alt="Foto Anggota" class="img-thumbnail rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                    @else
                        <div class="avatar-placeholder bg-light border rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>
                
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="40%"><strong>No. Anggota</strong></td>
                        <td>: <code class="fs-6">{{ $anggota->nomor_anggota }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap</strong></td>
                        <td>: {{ $anggota->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat, Tgl Lahir</strong></td>
                        <td>: {{ $anggota->tempat_lahir }}, {{ $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Kelamin</strong></td>
                        <td>: 
                            @if($anggota->jenis_kelamin == 'L')
                                <span class="badge bg-primary">Laki-laki</span>
                            @else
                                <span class="badge bg-info">Perempuan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>: {{ $anggota->kelas }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: {{ $anggota->alamat }}</td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td>: {{ $anggota->telepon ?? 'Tidak ada' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Daftar</strong></td>
                        <td>: {{ $anggota->tanggal_daftar->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>: 
                            @if($anggota->status == 'aktif')
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
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Peminjaman</h5>
            </div>
            <div class="card-body">
                @if($anggota->peminjamans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggota->peminjamans as $index => $peminjaman)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $peminjaman->buku->judul ?? 'Buku Dihapus' }}</div>
                                    <small class="text-muted">{{ $peminjaman->buku->pengarang ?? '-' }}</small>
                                </td>
                                <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td>
                                    {{-- ====================================================== --}}
                                    {{-- <<<--- PERBAIKAN 1: Gunakan tanggal_kembali_aktual ---<<< --}}
                                    {{-- ====================================================== --}}
                                    @if($peminjaman->tanggal_kembali_aktual)
                                        {{ $peminjaman->tanggal_kembali_aktual->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php $status = $peminjaman->status_realtime; @endphp
                                    @if($status == 'dipinjam')
                                        <span class="badge bg-warning text-dark">Dipinjam</span>
                                    @elseif($status == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($status == 'terlambat')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- ====================================================== --}}
                                    {{-- <<<--- PERBAIKAN 2: Gunakan relasi dendaRecord ---<<< --}}
                                    {{-- ====================================================== --}}
                                    @if($peminjaman->dendaRecord)
                                        <span class="text-danger fw-bold">Rp {{ number_format($peminjaman->dendaRecord->total_denda, 0, ',', '.') }}</span>
                                        @if($peminjaman->dendaRecord->status_bayar == 'dibayar')
                                            <br><small class="badge bg-success">Lunas</small>
                                        @else
                                            <br><small class="badge bg-danger">Belum Lunas</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Kartu rekapitulasi Anda --}}
                
                @else
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada riwayat peminjaman</h5>
                    <p class="text-muted">Anggota ini belum pernah melakukan peminjaman buku</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection