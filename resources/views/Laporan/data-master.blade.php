@extends('layouts.app')

@section('title', 'Laporan Data Master')
@section('page-title', 'Laporan Data Master')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Data Master</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Pilih Laporan Data Master</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.data-master') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label for="type" class="form-label">Jenis Laporan</label>
                    <select name="type" id="type" class="form-select">
                        <option value="anggota" {{ $type == 'anggota' ? 'selected' : '' }}>Data Anggota</option>
                        <option value="buku" {{ $type == 'buku' ? 'selected' : '' }}>Data Buku</option>
                        <option value="kategori" {{ $type == 'kategori' ? 'selected' : '' }}>Data Kategori</option>
                        <option value="rak" {{ $type == 'rak' ? 'selected' : '' }}>Data Rak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hasil Laporan: {{ ucwords($type) }}</h5>
        {{-- Tombol Cetak bisa ditambahkan di sini nanti --}}
    </div>
    <div class="card-body p-0">
        {{-- Tampilkan tabel berdasarkan jenis laporan --}}
    </div>
</div>
@endsection