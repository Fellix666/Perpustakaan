<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Buku - {{ $books->count() }} Buku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .labels-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            justify-items: center;
        }

        .label-container {
            background: white;
            border: 2px solid #000;
            width: 200px;
            height: 80px;
            position: relative;
            page-break-inside: avoid;
            margin: 5px;
        }

        .label-header {
            background: #000;
            color: white;
            padding: 4px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border-bottom: 1px solid #000;
        }

        .label-body {
            padding: 4px 8px;
            font-size: 8px;
            line-height: 1.1;
            height: calc(100% - 25px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .book-code {
            font-weight: bold;
            font-size: 12px;
            color: #000;
            margin-bottom: 2px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1px;
        }

        .book-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 1px;
            line-height: 1.0;
            font-size: 8px;
            text-align: center;
        }

        .book-author {
            color: #000;
            margin-bottom: 1px;
            font-size: 7px;
            text-align: center;
        }

        .book-info {
            color: #000;
            font-size: 6px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 1px;
            margin-top: auto;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
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
            
            .labels-container {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }
            
            .label-container {
                border: 2px solid #000;
                margin: 2mm;
                width: 60mm;
                height: 20mm;
                page-break-inside: avoid;
                position: relative;
            }
            
            .label-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
                background: #000 !important;
                color: white !important;
                font-size: 7px;
                padding: 2px 4px;
            }
            
            .label-body {
                padding: 2px 4px;
                height: auto !important;
                min-height: 12mm;
                font-size: 6px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            
            .book-code {
                font-size: 10px;
                margin-bottom: 1px;
                font-weight: bold;
                text-align: center;
                border-bottom: 1px solid #000;
                padding-bottom: 1px;
            }
            
            .book-title {
                font-size: 7px;
                margin-bottom: 1px;
                font-weight: bold;
                text-align: center;
                line-height: 1.0;
            }
            
            .book-author {
                font-size: 6px;
                margin-bottom: 1px;
                text-align: center;
            }
            
            .book-info {
                font-size: 5px;
                text-align: center;
                border-top: 1px solid #000;
                padding-top: 1px;
                margin-top: auto;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">
        <i class="fas fa-print me-2"></i>Cetak {{ $books->count() }} Label
    </button>

    <div class="page-header">
        <div class="page-title">Cetak Label Buku Massal</div>
        <div class="page-subtitle">SMP NEGERI 1 SANGGAU LEDO</div>
        <div class="filter-info">
            <strong>Total:</strong> {{ $books->count() }} Buku
        </div>
    </div>

    <div class="labels-container">
        @foreach($books as $buku)
        <div class="label-container">
            <div class="label-header">
                PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO
            </div>
            <div class="label-body">
                <div class="book-code">{{ $buku->kode_buku }}</div>
                <div class="book-title">{{ Str::limit($buku->judul, 45) }}</div>
                <div class="book-author">{{ Str::limit($buku->pengarang, 35) }}</div>
                <div class="book-info">
                    {{ $buku->kategori->nama_kategori ?? '-' }} | {{ $buku->rak->nama_rak ?? '-' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
