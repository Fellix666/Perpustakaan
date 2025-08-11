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
            width: 300px;
            height: 120px;
            background-color: white;
            border: 2px solid #000;
            margin: 20px auto;
            position: relative;
        }
        
        .label-header {
            background: #000;
            color: white;
            padding: 8px 12px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #000;
        }
        
        .label-body {
            padding: 8px 12px;
            font-size: 10px;
            line-height: 1.2;
            height: calc(100% - 35px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .book-code {
            font-weight: bold;
            font-size: 16px;
            color: #000;
            margin-bottom: 4px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        
        .book-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
            line-height: 1.1;
            font-size: 11px;
            text-align: center;
        }
        
        .book-author {
            color: #000;
            margin-bottom: 2px;
            font-size: 10px;
            text-align: center;
        }
        
        .book-info {
            color: #000;
            font-size: 9px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 2px;
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
                margin: 10mm;
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
        // Auto print when page loads
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        }
    </script>
</body>
</html>
