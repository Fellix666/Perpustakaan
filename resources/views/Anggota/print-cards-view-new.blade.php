<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Massal - {{ $anggotas->count() }} Anggota</title>
    <link rel="stylesheet" href="{{ asset('css/card-styles.css') }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 10px;
            background: #f5f5f5;
        }

        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0 0 10px 0;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666;
            margin: 0;
        }

        .filter-info {
            font-size: 14px;
            color: #888;
            margin-top: 10px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            justify-items: center;
        }

        /* Sistem Warna Kartu */
        .card-header.blue {
            background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        }

        .card-header.red {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        .card-header.green {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-50px) translateY(-50px); }
        }

        .school-info {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .member-info {
            flex: 1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td:last-child {
            color: #333;
        }

        .signature-section {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 10px;
            background: white;
        }

        .signature-info {
            flex: 2;
            text-align: right;
        }

        .photo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            margin-right: 20px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .page-header {
                box-shadow: none;
                border: 1px solid #ddd;
                margin-bottom: 10px;
            }
            
            .cards-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">
        <i class="fas fa-print"></i> Cetak Semua Kartu
    </button>

    <div class="page-header">
        <div class="page-title">Cetak Kartu Anggota Massal</div>
        <div class="page-subtitle">SMP NEGERI 1 SANGGAU LEDO</div>
        <div class="filter-info">
            @if($selectedKelas)
                <strong>Kelas:</strong> {{ $selectedKelas }} | 
            @endif
            @if($selectedTahun)
                <strong>Tahun Daftar:</strong> {{ $selectedTahun }} | 
            @endif
            <strong>Total:</strong> {{ $anggotas->count() }} Anggota | 
            <strong>Warna:</strong> {{ ucfirst($cardColor) }}
        </div>
    </div>

    <div class="cards-container">
        @foreach($anggotas as $anggota)
        <div class="card-container">
            <div class="card-header {{ $cardColor }}">
                <div class="school-info">
                    <div class="card-title">KARTU PERPUSTAKAAN</div>
                    <div class="school-name">SMPN 1 SANGGAU LEDO</div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="member-info">
                    <table class="info-table">
                        <tr>
                            <td>No Anggota</td>
                            <td>: {{ $anggota->nomor_anggota }}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: {{ $anggota->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td>TTL</td>
                            <td>: {{ $anggota->tempat_lahir ?? 'Sanggau Ledo' }}, {{ $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('d-m-Y') : '01-01-2010' }}</td>
                        </tr>
                        <tr>
                            <td>J Kelamin</td>
                            <td>: {{ $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: {{ $anggota->alamat }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="signature-section">
                    <div class="photo-section">
                        @if($anggota->foto)
                            <img src="{{ asset('storage/anggota/' . $anggota->foto) }}" alt="Foto {{ $anggota->nama_lengkap }}" class="member-photo">
                        @else
                            <div class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="signature-info">
                        <div>Sanggau Ledo, {{ now()->format('d F Y') }}</div>
                        <div class="signature-name">Kepala Perpustakaan</div>
                        <div style="height: 40px; margin: 10px 0;"></div>
                        <div class="signature-name">Julita, S.Ag</div>
                        <div class="nip">NIP: 196707072003122007</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="validity-info">
                        <strong>Berlaku sampai:</strong> 
                        @php
                            $kelas = $anggota->kelas;
                            $tahunMasuk = $anggota->tahun_ajaran_masuk ?? $anggota->tanggal_daftar->year;

                            if (strpos($tahunMasuk, '/') !== false) {
                                $tahunMasuk = (int) explode('/', $tahunMasuk)[0];
                            } else {
                                $tahunMasuk = (int) $tahunMasuk;
                            }
                            
                            $tahunLulus = $tahunMasuk + 3;
                            
                            $tanggalLulus = $tahunLulus . '-06-30';
                        @endphp
                        {{ \Carbon\Carbon::parse($tanggalLulus)->format('F Y') }}
                    </div>
                    <div class="card-note">
                        Kartu anggota dibawa setiap siswa mengunjungi perpustakaan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>

