@extends('layouts.app')

@section('title', 'Laporan Pengunjung')
@section('page-title', 'Laporan Pengunjung')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Data Pengunjung</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan Pengunjung</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('pengunjung.laporan') }}">
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
        <h5 class="mb-0">Hasil Laporan: Data Pengunjung (Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }})</h5>
        <a href="{{ route('pengunjung.print-laporan', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-2"></i>Cetak
        </a>
    </div>
    <div class="card-body p-0">
        @if(!empty($summaryData))
            <div class="table-responsive">
                <div class="p-3">
                    <h4 class="text-center mb-3">DATA PENGUNJUNG PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO</h4>
                    <h5 class="text-center mb-4">TAHUN PELAJARAN {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</h5>
                    
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
                                    <th class="text-center">VIIIA</th>
                                    <th class="text-center">VIIIB</th>
                                    <th class="text-center">VIIIC</th>
                                    <th class="text-center">VIIID</th>
                                    <th class="text-center">IXA</th>
                                    <th class="text-center">IXB</th>
                                    <th class="text-center">IXC</th>
                                    <th class="text-center">IXD</th>
                                    <th class="text-center">IXE</th>
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
                                    <td class="text-center">{{ $bulanData['kelas']['VIII A'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['VIII B'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['VIII C'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['VIII D'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['IX A'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['IX B'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['IX C'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['IX D'] ?? 0 }}</td>
                                    <td class="text-center">{{ $bulanData['kelas']['IX E'] ?? 0 }}</td>
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
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII A'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII B'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII C'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII D'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX A'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX B'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX C'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX D'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['IX E'] ?? 0; }) }}</td>
                                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return array_sum($bulan['kelas']); }) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="text-muted">Tidak ada data pengunjung untuk periode yang dipilih.</h5>
                <p class="text-muted">Silakan pilih tahun ajaran yang berbeda atau tambahkan data pengunjung terlebih dahulu.</p>
            </div>
        @endif
    </div>
</div>
@endsection
