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
            width: 480px;
            height: 250px;
            position: relative;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            position: relative;
            overflow: hidden;
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
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .library-title {
            font-size: 11px;
            margin: 2px 0 0 0;
            opacity: 0.9;
        }

        .school-address {
            font-size: 9px;
            margin: 1px 0 0 0;
            opacity: 0.8;
        }

        .card-body {
            padding: 15px 20px 0 20px;
            background: white;
            position: relative;
            height: calc(100% - 70px);
        }

        .member-info {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            height: 100%;
        }

        .info-section {
            flex: 2;
            min-width: 0;
            margin-bottom: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            font-size: 9.5px;
            line-height: 1.4;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 35%;
            font-weight: 500;
            color: #555;
        }

        .info-table td:nth-child(2) {
            width: 5%;
            text-align: center;
            color: #888;
        }

        .info-table td:last-child {
            width: 60%;
            font-weight: 600;
            color: #333;
            word-break: break-word;
            white-space: pre-line;
            max-width: 160px;
        }

        .member-id {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .photo-section {
            flex: 1;
            width: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            align-self: flex-start;
            justify-content: flex-start;
        }

        .photo-placeholder {
            width: 60px;
            height: 70px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: linear-gradient(145deg, #f8f9fa, #e9ecef);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .photo-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .photo-text {
            font-size: 7px;
            text-align: center;
            font-weight: 500;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8f9fa;
            padding: 10px 20px 8px 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            margin-top: 0;
        }

        .validity-info {
            color: #666;
        }

        .validity-date {
            font-weight: bold;
            color: #333;
        }

        .signature-area {
            text-align: center;
            color: #666;
        }

        .signature-line {
            border-bottom: 1px solid #999;
            width: 80px;
            margin: 5px auto 2px;
        }

        .logo-corner {
            position: absolute;
            top: 10px;
            right: 15px;
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                display: block;
                min-height: auto;
            }
            
            .card-container {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
                width: 110mm;
                min-height: 54mm;
                height: auto !important;
                border-radius: 8px;
            }
            
            .card-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .member-id {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .card-footer {
                position: static !important;
            }

            @page {
                size: A4;
                margin: 20mm;
            }
        }

        /* No Print Button for Print View */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .print-button:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        🖨️ Cetak Kartu
    </button>

    <div class="card-container">
        <div class="card-header">
            <div class="logo-corner">
                🎓
            </div>
            <div class="school-info">
                <h1 class="school-name">SMP NEGERI 1 SANGGAU LEDO</h1>
                <p class="library-title">KARTU ANGGOTA PERPUSTAKAAN</p>
                <p class="school-address">Jl. Pendidikan No. 1, Sanggau Ledo, Bengkayang</p>
            </div>
        </div>

        <div class="card-body">
            <div class="member-info">
                <div class="info-section">
                    <table class="info-table">
                        <tr>
                            <td>No. Anggota</td>
                            <td>:</td>
                            <td><span class="member-id">{{ $anggota->nomor_anggota }}</span></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td>{{ $anggota->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td>Tempat, Tgl Lahir</td>
                            <td>:</td>
                            <td>{{ $anggota->tempat_lahir }}, {{ $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td>{{ $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>{{ $anggota->kelas }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td style="word-break: break-word; white-space: pre-line;">{{ $anggota->alamat }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>
                                @if($anggota->status_realtime == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="photo-section">
                    @if($anggota->foto)
                        <img src="{{ asset('storage/anggota/'.$anggota->foto) }}" alt="Foto Anggota" style="width:60px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #ddd;">
                    @else
                        <div class="photo-placeholder">
                            <div class="photo-icon">👤</div>
                            <div class="photo-text">Foto<br>3x4</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="validity-info">
                <div>Berlaku sampai:</div>
                <div class="validity-date">{{ $anggota->tanggal_daftar->addYears(3)->format('d/m/Y') }}</div>
            </div>
            <div class="signature-area">
                <div>Kepala Perpustakaan</div>
                <div class="signature-line"></div>
                <div>{{ config('app.librarian_name', '________________') }}</div>
            </div>
        </div>
    </div>
</body>
</html>