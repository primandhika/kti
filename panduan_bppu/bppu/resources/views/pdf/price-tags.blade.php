<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Price Tags - {{ $workUnit->name }}</title>
    <style>
        @page {
            margin: 10mm 15mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        /* Grid container: 4 columns x multiple rows */
        .price-tag-container {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 2mm;
        }

        .price-tag-row {
            display: table-row;
            page-break-inside: avoid;
        }

        .price-tag {
            display: table-cell;
            width: 45mm;
            height: 25mm;
            border: 0.5pt solid #000;
            padding: 1.5mm;
            vertical-align: top;
            position: relative;
            page-break-inside: avoid;
            background: #fff;
        }

        /* Product name - lebih besar */
        .product-name {
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 1mm;
            height: 7mm;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        /* Price section - minimalist */
        .price-section {
            border-top: 0.5pt solid #000;
            border-bottom: 0.5pt solid #000;
            padding: 0.5mm 0;
            margin-bottom: 1mm;
        }

        .price-value {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
        }

        /* Barcode section - lebih kecil */
        .barcode-section {
            text-align: center;
        }

        .barcode-img {
            width: 28mm;
            height: 6mm;
            margin: 0 auto;
            display: block;
        }

        .barcode-text {
            font-size: 4.5pt;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-top: 0.3mm;
            text-align: center;
        }

        /* Store name - subtle */
        .store-name {
            font-size: 4pt;
            text-align: right;
            color: #666;
            position: absolute;
            top: 1mm;
            right: 1.5mm;
        }
    </style>
</head>
<body>
    <div class="price-tag-container">
        @foreach ($barangs as $index => $barang)
            @if ($index % 4 == 0)
                <div class="price-tag-row">
            @endif

            <div class="price-tag">
                <div class="store-name">{{ $workUnit->name }}</div>

                <div class="product-name">{{ $barang->nama_barang }}</div>

                <div class="price-section">
                    <div class="price-value">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</div>
                </div>

                <div class="barcode-section">
                    <img class="barcode-img" src="data:image/png;base64,{{ $barang->barcode_base64 }}" alt="Barcode">
                    <div class="barcode-text">{{ $barang->kode_barang }}</div>
                </div>
            </div>

            @if (($index + 1) % 4 == 0 || $loop->last)
                @for ($i = 0; $i < (4 - (($index % 4) + 1)); $i++)
                    @if ($loop->last)
                        <div class="price-tag" style="border: none;"></div>
                    @endif
                @endfor
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>
