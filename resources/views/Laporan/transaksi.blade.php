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
                <div class="col-md-8">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                        @if($availableYears->count() > 0)
                            @foreach($availableYears as $academicYear)
                                <option value="{{ $academicYear }}" {{ $tahunAjaran == $academicYear ? 'selected' : '' }}>
                                    {{ $academicYear }}/{{ $academicYear + 1 }}
                                </option>
                            @endforeach
                        @else
                            <option value="{{ date('Y') }}">{{ date('Y') }}/{{ date('Y') + 1 }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hasil Laporan: Peminjaman (Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }})</h5>
        <a href="{{ route('laporan.print.transaksi', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-2"></i>Cetak
        </a>
    </div>
    <div class="card-body p-0">
        @if($data->count() > 0 || !empty($summaryData))
            <div class="table-responsive">
                {{-- Tampilkan laporan peminjaman seperti di gambar --}}
                <div class="p-3">
                    <h4 class="text-center mb-3">DATA PEMINJAM BUKU PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO</h4>
                    <h5 class="text-center mb-4">TAHUN PELAJARAN {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</h5>
                    
                    @if(!empty($summaryData))
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">No</th>
                                        <th rowspan="2" class="text-center align-middle">Bulan</th>
                                        <th colspan="5" class="text-center">Kelas VII</th>
                                        <th colspan="4" class="text-center">Kelas VIII</th>
                                        <th colspan="5" class="text-center">Kelas IX</th>
                                        <th rowspan="2" class="text-center align-middle">Jumlah</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">VIIA</th>
                                        <th class="text-center">VIIB</th>
                                        <th class="text-center">VIIC</th>
                                        <th class="text-center">VIID</th>
                                        <th class="text-center">VIIE</th>
                                        <th class="text-center">Jlh</th>
                                        <th class="text-center">VIIIA</th>
                                        <th class="text-center">VIIIB</th>
                                        <th class="text-center">VIIIC</th>
                                        <th class="text-center">VIIID</th>
                                        <th class="text-center">Jlh</th>
                                        <th class="text-center">IXA</th>
                                        <th class="text-center">IXB</th>
                                        <th class="text-center">IXC</th>
                                        <th class="text-center">IXD</th>
                                        <th class="text-center">IXE</th>
                                        <th class="text-center">Jlh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($summaryData as $bulanKey => $bulanData)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $bulanData['bulan'] }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VII A'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VII B'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VII C'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VII D'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VII E'] ?? 0 }}</td>
                                        <td class="text-center fw-bold">{{ array_sum(array_intersect_key($bulanData['kelas'], array_flip(['VII A', 'VII B', 'VII C', 'VII D', 'VII E']))) }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VIII A'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VIII B'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VIII C'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['VIII D'] ?? 0 }}</td>
                                        <td class="text-center fw-bold">{{ array_sum(array_intersect_key($bulanData['kelas'], array_flip(['VIII A', 'VIII B', 'VIII C', 'VIII D']))) }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['IX A'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['IX B'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['IX C'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['IX D'] ?? 0 }}</td>
                                        <td class="text-center">{{ $bulanData['kelas']['IX E'] ?? 0 }}</td>
                                        <td class="text-center fw-bold">{{ array_sum(array_intersect_key($bulanData['kelas'], array_flip(['IX A', 'IX B', 'IX C', 'IX D', 'IX E']))) }}</td>
                                        <td class="text-center fw-bold">{{ array_sum($bulanData['kelas']) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-warning">
                                        <td colspan="2" class="text-center fw-bold">Jumlah</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VII A'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VII B'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VII C'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VII D'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VII E'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { 
                                            $kelasVII = ['VII A', 'VII B', 'VII C', 'VII D', 'VII E'];
                                            return array_sum(array_intersect_key($bulan['kelas'], array_flip($kelasVII)));
                                        }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII A'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII B'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII C'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII D'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { 
                                            $kelasVIII = ['VIII A', 'VIII B', 'VIII C', 'VIII D'];
                                            return array_sum(array_intersect_key($bulan['kelas'], array_flip($kelasVIII)));
                                        }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX A'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX B'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX C'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX D'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX E'] ?? 0; }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { 
                                            $kelasIX = ['IX A', 'IX B', 'IX C', 'IX D', 'IX E'];
                                            return array_sum(array_intersect_key($bulan['kelas'], array_flip($kelasIX)));
                                        }) }}</td>
                                        <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return array_sum($bulan['kelas']); }) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Laporan Detail Peminjaman</h6>
                            <p class="mb-0">Menampilkan data peminjaman detail untuk tahun ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}.</p>
                        </div>
                        @include('laporan.partials.peminjaman', ['data' => $data])
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-5"><h5 class="text-muted">Tidak ada data untuk periode yang dipilih.</h5></div>
        @endif
    </div>
</div>

<!-- Pengumuman Denda & Keterlambatan -->
@if(isset($dendaData) && ($dendaData['dendaBelumBayar']->count() > 0 || $dendaData['peminjamanTerlambat']->count() > 0))
<div class="card shadow-sm mt-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>Pengumuman Denda & Keterlambatan
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Total Denda Belum Dibayar</h6>
                    <h4 class="fw-bold">Rp {{ number_format($dendaData['totalDendaBelumBayar']) }}</h4>
                    <small>{{ $dendaData['dendaBelumBayar']->count() }} transaksi</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Total Denda Terlambat</h6>
                    <h4 class="fw-bold">Rp {{ number_format($dendaData['totalDendaTerlambat']) }}</h4>
                    <small>{{ $dendaData['peminjamanTerlambat']->count() }} peminjaman terlambat</small>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="fas fa-bullhorn me-2"></i>PERHATIAN!
            </h6>
            <p class="mb-2">Kepada seluruh anggota perpustakaan yang terlambat mengembalikan buku:</p>
            <ul class="mb-2">
                <li>Denda keterlambatan sebesar <strong>Rp 1.000 per hari</strong></li>
                <li>Segera kembalikan buku yang dipinjam untuk menghindari penumpukan denda</li>
                <li>Denda yang belum dibayar akan mempengaruhi status keanggotaan</li>
            </ul>
            <hr>
            <p class="mb-0">
                <strong>Total denda yang belum dibayar: Rp {{ number_format($dendaData['totalDendaBelumBayar'] + $dendaData['totalDendaTerlambat']) }}</strong>
            </p>
        </div>
    </div>
</div>
@endif
@endsection