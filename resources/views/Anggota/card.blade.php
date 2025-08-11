<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - {{ $anggota->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 500px;
            height: 280px;
            position: relative;
            page-break-inside: avoid;
        }

        /* Sistem Warna Kartu */
        .card-header {
            color: white;
            padding: 15px 20px;
            position: relative;
            overflow: hidden;
        }

        /* Warna Biru (Default) */
        .card-header.blue {
            background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        }

        /* Warna Merah */
        .card-header.red {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        /* Warna Hijau */
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

        .card-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .card-body {
            padding: 15px 20px;
            background: white;
            position: relative;
            height: calc(100% - 70px);
            display: flex;
            flex-direction: column;
        }

        .member-info {
            flex: 1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 0;
            font-size: 12px;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: bold;
            color: #666;
            width: 100px;
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

        .signature-name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }

        .nip {
            font-size: 9px;
            color: #999;
        }

        .photo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            margin-right: 20px;
        }

        .member-photo {
            width: 70px;
            height: 90px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e0e0e0;
            background: #f8f9fa;
        }

        .photo-placeholder {
            width: 70px;
            height: 90px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 28px;
        }

        .card-footer {
            margin-top: auto;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 2px solid #dee2e6;
            padding: 12px 20px;
            border-radius: 0 0 15px 15px;
        }

        .validity-info {
            font-size: 11px;
            color: #495057;
            text-align: center;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .card-note {
            font-size: 9px;
            color: #6c757d;
            text-align: center;
            font-style: italic;
            margin: 0;
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
                display: block;
                min-height: auto;
            }
            
            .print-button {
                display: none;
            }
            
            .card-container {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
                width: 140mm;
                min-height: 60mm;
                height: auto !important;
                border-radius: 8px;
                page-break-inside: avoid;
                position: relative;
            }
            
            .card-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .card-header.blue {
                background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .card-header.red {
                background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .card-header.green {
                background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .card-body {
                padding: 15px 20px 10px 20px;
                height: auto !important;
                min-height: 120px;
            }
            
            .member-info {
                display: block;
            }
            
            .signature-section {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                background: white;
            }
            
            .photo-section {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end;
            }
            
            .member-photo {
                width: 70px;
                height: 90px;
                border-radius: 8px;
                object-fit: cover;
                border: 2px solid #e0e0e0;
                background: #f8f9fa;
            }
            
            .photo-placeholder {
                width: 70px;
                height: 90px;
                border-radius: 8px;
                background: #f8f9fa;
                border: 2px solid #e0e0e0;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #999;
                font-size: 32px;
            }
            
            .signature-section {
                position: static !important;
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 10px 20px;
                margin-top: 10px;
            }
            
            .card-footer {
                margin-top: auto !important;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
                border-top: 2px solid #dee2e6 !important;
                padding: 12px 20px !important;
                border-radius: 0 0 15px 15px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .validity-info {
                font-size: 11px !important;
                color: #495057 !important;
                text-align: center !important;
                font-weight: 500 !important;
                margin-bottom: 6px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .card-note {
                font-size: 9px !important;
                color: #6c757d !important;
                text-align: center !important;
                font-style: italic !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4;
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">
        <i class="fas fa-print"></i> Cetak Kartu
    </button>

    <div class="card-container">
        <div class="card-header {{ $color ?? 'blue' }}">
            <div class="school-info">
                <div class="card-title">KARTU PERPUSTAKAAN</div>
                <div class="school-name">SMP NEGERI 1 SANGGAU LEDO</div>
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
                            
                            // Ekstrak tahun dari format YYYY/YYYY+1 atau gunakan tahun daftar
                            if (strpos($tahunMasuk, '/') !== false) {
                                $tahunMasuk = (int) explode('/', $tahunMasuk)[0];
                            } else {
                                $tahunMasuk = (int) $tahunMasuk;
                            }
                            
                            // Logika yang benar: Semua siswa lulus dalam 3 tahun dari tahun ajaran masuk
                            // Terlepas dari kelas saat ini, masa berlaku tetap 3 tahun dari tahun masuk
                            $tahunLulus = $tahunMasuk + 3;
                            
                            $tanggalLulus = $tahunLulus . '-06-30'; // Akhir tahun ajaran
                        @endphp
                        {{ \Carbon\Carbon::parse($tanggalLulus)->format('F Y') }}
                    </div>
                    <div class="card-note">
                        Kartu anggota dibawa setiap siswa mengunjungi perpustakaan
                    </div>
                </div>
            </div>
    </div>
</body>
</html>