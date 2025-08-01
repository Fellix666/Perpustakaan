<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ ucwords($type) }}</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1, h2 { text-align: center; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        thead { background-color: #f2f2f2; }
        @media print {
            .no-print { display: none; }
        }
        .btn { padding: 8px 12px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn">Cetak Laporan</button>
    </div>

    <h2>Laporan {{ ucwords($type) }}</h2>
    <p style="text-align: center; margin-top:0;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>

    @if($data->count() > 0)
        @if($type == 'peminjaman')
            @include('laporan.partials.peminjaman', ['data' => $data])
        @elseif($type == 'pengembalian')
            @include('laporan.partials.pengembalian', ['data' => $data])
        @elseif($type == 'denda')
            {{-- Anda perlu membuat file laporan.partials.denda.blade.php --}}
            <p>Tabel laporan denda akan ditampilkan di sini.</p>
        @endif
    @else
        <p style="text-align: center;">Tidak ada data untuk periode yang dipilih.</p>
    @endif

</body>
</html>
