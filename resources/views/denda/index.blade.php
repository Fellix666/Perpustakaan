@extends('layouts.app')

@section('title', 'Data Denda')
@section('page-title', 'Data Denda')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Data Denda</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Denda</h5>
    </div>
    <div class="card-body p-0">
        @if($dendas->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode Peminjaman</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Hari Terlambat</th>
                        <th>Total Denda</th>
                        <th>Status Bayar</th>
                        <th>Tanggal Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dendas as $index => $denda)
                    <tr>
                        <td>{{ $dendas->firstItem() + $index }}</td>
                        <td><code>{{ $denda->peminjaman->kode_peminjaman ?? '-' }}</code></td>
                        <td>{{ $denda->peminjaman->anggota->nama_lengkap ?? '-' }}</td>
                        <td>{{ $denda->peminjaman->buku->judul ?? '-' }}</td>
                        <td>{{ $denda->hari_terlambat }}</td>
                        <td>Rp {{ number_format($denda->total_denda, 0, ',', '.') }}</td>
                        <td>
                            @if($denda->status_bayar == 'belum-dibayar')
                                <span class="badge bg-danger">Belum Lunas</span>
                            @else
                                <span class="badge bg-success">Lunas</span>
                            @endif
                        </td>
                        <td>{{ $denda->tanggal_bayar ? date('d/m/Y', strtotime($denda->tanggal_bayar)) : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('denda.bayar', $denda->id) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Bayar/Lunas"><i class="fas fa-money-bill-wave"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>Menampilkan {{ $dendas->firstItem() }} - {{ $dendas->lastItem() }} dari {{ $dendas->total() }} data</div>
                <div>{{ $dendas->links() }}</div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data denda</h5>
        </div>
        @endif
    </div>
</div>
@endsection 