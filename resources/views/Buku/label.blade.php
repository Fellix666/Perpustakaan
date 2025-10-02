<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Buku - {{ $buku->kode_buku }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .label-container {
            width: 200px;
            height: 80px;
            background-color: white;
            border: 2px solid #000;
            margin: 20px auto;
            position: relative;
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
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
        
        @media print {
            .label-container {
                border: 2px solid #000;
                margin: 5mm;
                width: 60mm;
                height: 20mm;
                page-break-inside: avoid;
            }
            
            .label-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
                background: #000 !important;
                color: white !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Label
    </button>
    
    <div class="label-container">
        <div class="label-header">
            PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO
        </div>
        <div class="label-body">
            <div class="book-code">{{ $buku->kode_buku }}</div>
            <div class="book-title">{{ $buku->judul }}</div>
            <div class="book-author">{{ $buku->pengarang }}</div>
            <div class="book-info">
                {{ $buku->kategori->nama_kategori ?? '-' }} | {{ $buku->rak->nama_rak ?? '-' }}
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
        }
    </script>
</body>
</html>
