<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Peminjaman</title>
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
        .bg-primary { background-color: #007bff; color: white; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-info { background-color: #17a2b8; color: white; }
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
        .stat-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px;
            text-align: center;
        }
        .progress {
            background-color: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar {
            background-color: #007bff;
            height: 100%;
        }
        .section-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn">Cetak Laporan</button>
    </div>

    <h4>LAPORAN ANALISIS PEMINJAMAN BUKU</h4>
    <h5>PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO</h5>
    <p style="text-align: center; margin-top:0;">Tahun Ajaran: {{ $tahunAjaran }}/{{ (int)$tahunAjaran + 1 }}</p>
    <p style="text-align: center; margin-top:0;">Periode: Juli {{ $tahunAjaran }} - Juni {{ (int)$tahunAjaran + 1 }}</p>
    
    <!-- Statistik Utama -->
    <div style="display: flex; gap: 10px; margin: 20px 0;">
        <div class="stat-card bg-primary" style="flex: 1;">
            <h3 style="margin: 0;">{{ number_format($statistikData['totalPeminjaman']) }}</h3>
            <small>Total Peminjaman</small>
        </div>
        <div class="stat-card bg-success" style="flex: 1;">
            <h3 style="margin: 0;">{{ number_format($statistikData['rataRataPerBulan']) }}</h3>
            <small>Rata-rata per Bulan</small>
        </div>
        <div class="stat-card bg-warning" style="flex: 1;">
            <h3 style="margin: 0;">{{ $statistikData['kelasTertinggi']->kelas ?? 'N/A' }}</h3>
            <small>Kelas Tertinggi</small>
        </div>
        <div class="stat-card bg-info" style="flex: 1;">
            <h3 style="margin: 0;">{{ $statistikData['bulanTertinggi'] ? \Carbon\Carbon::createFromDate($statistikData['bulanTertinggi']->tahun, $statistikData['bulanTertinggi']->bulan, 1)->format('M Y') : 'N/A' }}</h3>
            <small>Bulan Tertinggi</small>
        </div>
    </div>

    <!-- Detail Statistik -->
    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <!-- Persentase per Tingkat -->
        <div style="flex: 1;">
            <h5 style="margin-bottom: 10px;">Persentase per Tingkat</h5>
            @foreach($statistikData['persentaseTingkat'] as $tingkat => $data)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <span style="font-weight: bold;">{{ $tingkat }}</span>
                <div style="display: flex; align-items: center;">
                    <div class="progress" style="width: 100px; margin-right: 10px;">
                        <div class="progress-bar" style="width: {{ $data['persentase'] }}%"></div>
                    </div>
                    <small>{{ $data['persentase'] }}% ({{ $data['total'] }})</small>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Kategori Buku Favorit -->
        <div style="flex: 1;">
            <h5 style="margin-bottom: 10px;">Kategori Buku Favorit</h5>
            @forelse($statistikData['kategoriFavorit'] as $index => $kategori)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <span>{{ $index + 1 }}. {{ $kategori->nama_kategori }}</span>
                <span style="background-color: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">{{ $kategori->total }}</span>
            </div>
            @empty
            <p style="color: #666; margin: 0;">Tidak ada data kategori</p>
            @endforelse
        </div>
    </div>

    <!-- Top 10 Peminjam -->
    <div class="section-break"></div>
    <h5 style="margin: 20px 0 10px 0;">Top 10 Peminjam Terbanyak</h5>
    <table>
        <thead>
            <tr>
                <th class="text-center">Rank</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th class="text-center">Total Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topPeminjamData['topPeminjam'] as $index => $anggota)
            <tr>
                <td class="text-center fw-bold">
                    @if($index == 0)
                        🥇 1st
                    @elseif($index == 1)
                        🥈 2nd
                    @elseif($index == 2)
                        🥉 3rd
                    @else
                        {{ $index + 1 }}
                    @endif
                </td>
                <td>{{ $anggota->nama_lengkap }}</td>
                <td>{{ $anggota->kelas }}</td>
                <td class="text-center fw-bold">{{ $anggota->total_peminjaman }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data peminjaman untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Top 3 per Kelas -->
    <div class="section-break"></div>
    <h5 style="margin: 20px 0 10px 0;">Top 3 Peminjam per Kelas</h5>
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        @forelse($topPeminjamData['topPerKelas'] as $kelas => $data)
        <div style="flex: 1; min-width: 200px;">
            <h6 style="margin-bottom: 10px; color: #007bff;">{{ $kelas }}</h6>
            @foreach($data as $index => $anggota)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <span>{{ $index + 1 }}. {{ $anggota->nama_lengkap }}</span>
                <span style="background-color: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">{{ $anggota->total_peminjaman }}</span>
            </div>
            @endforeach
        </div>
        @empty
        <p style="color: #666; margin: 0;">Tidak ada data per kelas</p>
        @endforelse
    </div>

    <!-- Top 5 per Bulan -->
    <div class="section-break"></div>
    <h5 style="margin: 20px 0 10px 0;">Top 5 Peminjam per Bulan</h5>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        @forelse($topPeminjamData['topPerBulan'] as $bulanKey => $bulanData)
        <div style="flex: 1; min-width: 180px; border: 1px solid #ddd; padding: 10px;">
            <h6 style="margin-bottom: 10px; background-color: #007bff; color: white; padding: 5px; text-align: center;">{{ $bulanData['bulan'] }}</h6>
            @foreach($bulanData['data'] as $index => $anggota)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                <small>{{ $index + 1 }}. {{ $anggota->nama_lengkap }}</small>
                <small style="background-color: #007bff; color: white; padding: 1px 4px; border-radius: 2px;">{{ $anggota->total_peminjaman }}</small>
            </div>
            @endforeach
        </div>
        @empty
        <p style="color: #666; margin: 0;">Tidak ada data per bulan</p>
        @endforelse
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Laporan ini dibuat secara otomatis oleh sistem perpustakaan SMP Negeri 1 Sanggau Ledo</p>
        <p>Tanggal cetak: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html> 