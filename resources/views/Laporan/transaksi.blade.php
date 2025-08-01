@extends('layouts.app')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Transaksi</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan Transaksi</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.transaksi') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Jenis Laporan</label>
                    <select name="type" id="type" class="form-select">
                        <option value="peminjaman" {{ $type == 'peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                        <option value="pengembalian" {{ $type == 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                        <option value="denda" {{ $type == 'denda' ? 'selected' : '' }}>Denda</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hasil Laporan: {{ ucwords($type) }} ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</h5>
        <a href="{{ route('laporan.print.transaksi', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-2"></i>Cetak
        </a>
    </div>
    <div class="card-body p-0">
        @if($data->count() > 0)
            <div class="table-responsive">
                {{-- Logika untuk menampilkan tabel yang sesuai --}}
                @if($type == 'peminjaman')
                    @include('laporan.partials.peminjaman', ['data' => $data])
                @elseif($type == 'pengembalian')
                    @include('laporan.partials.pengembalian', ['data' => $data])
                @elseif($type == 'denda')
                    @include('laporan.partials.denda', ['data' => $data])
                @endif
            </div>
        @else
            <div class="text-center py-5"><h5 class="text-muted">Tidak ada data untuk periode yang dipilih.</h5></div>
        @endif
    </div>
</div>
@endsection