<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ ucwords($type) }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            margin: 20px; 
            font-size: 12px;
        }
        h1, h2, h3, h4, h5 { 
            text-align: center; 
            margin-bottom: 5px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left; 
            font-size: 11px; 
        }
        thead { 
            background-color: #f2f2f2; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .table-primary { background-color: #e3f2fd; }
        .table-warning { background-color: #fff3cd; }
        @media print {
            .no-print { display: none; }
        }
        .btn { 
            padding: 8px 12px; 
            background-color: #0d6efd; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn">Cetak Laporan</button>
    </div>

    <h4>DATA PEMINJAMAN BUKU PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO</h4>
    <h5>TAHUN PELAJARAN {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</h5>
    
    @if(!empty($summaryData))
        <table>
            <thead class="table-primary">
                <tr>
                    <th rowspan="2" class="text-center">No</th>
                    <th rowspan="2" class="text-center">Bulan</th>
                    <th colspan="6" class="text-center">Kelas VII</th>
                    <th colspan="6" class="text-center">Kelas VIII</th>
                    <th colspan="6" class="text-center">Kelas IX</th>
                    <th rowspan="2" class="text-center">Jumlah</th>
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
                    <th class="text-center">VIIIE</th>
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
                    <td class="text-center">{{ $bulanData['kelas']['VIII E'] ?? 0 }}</td>
                    <td class="text-center fw-bold">{{ array_sum(array_intersect_key($bulanData['kelas'], array_flip(['VIII A', 'VIII B', 'VIII C', 'VIII D', 'VIII E']))) }}</td>
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
                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { return $bulan['kelas']['VIII E'] ?? 0; }) }}</td>
                    <td class="text-center fw-bold">{{ $summaryData->sum(function($bulan) { 
                        $kelasVIII = ['VIII A', 'VIII B', 'VIII C', 'VIII D', 'VIII E'];
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
    @else
        <h3>Laporan Detail Peminjaman</h3>
        <p style="text-align: center;">Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</p>
        @include('laporan.partials.peminjaman', ['data' => $data])
    @endif

</body>
</html>
