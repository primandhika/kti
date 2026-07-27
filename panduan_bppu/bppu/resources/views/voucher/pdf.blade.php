<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voucher {{ $potongan->kode_potongan }}</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .page-header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #996600;
            padding-bottom: 10px;
        }
        .page-header h1 {
            font-size: 16pt;
            color: #996600;
            margin: 0 0 4px;
        }
        .page-header p {
            font-size: 9pt;
            color: #666;
            margin: 0;
        }
        .voucher-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            table-layout: fixed;
            display: table;
        }
        .voucher-row {
            display: table-row;
        }
        .voucher-card {
            display: table-cell;
            width: 33.333%;
            border: 2px solid #996600;
            border-radius: 8px;
            padding: 10px;
            box-sizing: border-box;
            vertical-align: top;
            page-break-inside: avoid;
        }
        .voucher-cell-empty {
            display: table-cell;
            width: 33.333%;
        }
        .voucher-card .program {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .voucher-card .nama {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .voucher-card .nilai {
            font-size: 18pt;
            font-weight: bold;
            color: #996600;
            text-align: center;
            padding: 6px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
            margin: 6px 0;
        }
        .voucher-card .barcode-wrap {
            text-align: center;
            margin: 6px 0 2px;
        }
        .voucher-card .barcode-wrap img {
            max-width: 100%;
            height: 36px;
        }
        .voucher-card .kode {
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
            color: #555;
            margin: 0 0 4px;
        }
        .voucher-card .meta {
            font-size: 7pt;
            color: #888;
            text-align: center;
        }
        .voucher-card .assigned {
            font-size: 7.5pt;
            color: #444;
            text-align: center;
            margin-top: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Voucher Diskon</h1>
        <p>Program: {{ $potongan->nama }} &mdash; Kode: {{ $potongan->kode_potongan }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="voucher-grid">
        @foreach($vouchers->chunk(3) as $row)
        <div class="voucher-row">
            @foreach($row as $voucher)
            <div class="voucher-card">
                <div class="program">BPPU IKIP Siliwangi</div>
                <div class="nama">{{ $potongan->nama }}</div>
                <div class="nilai">
                    @if($potongan->tipe === 'persen')
                        {{ number_format($potongan->nilai, 0) }}%
                    @else
                        Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                    @endif
                </div>
                <div class="barcode-wrap">
                    @if($voucher->barcode_base64)
                        <img src="data:image/png;base64,{{ $voucher->barcode_base64 }}" alt="{{ $voucher->kode_voucher }}">
                    @endif
                </div>
                <div class="kode">{{ $voucher->kode_voucher }}</div>
                <div class="meta">
                    @if($potongan->minimum_transaksi > 0)
                        Min. Rp {{ number_format($potongan->minimum_transaksi, 0, ',', '.') }}<br>
                    @endif
                    @if($potongan->berlaku_sampai)
                        s/d {{ $potongan->berlaku_sampai->format('d/m/Y') }}
                    @else
                        Berlaku tidak terbatas
                    @endif
                </div>
                @if($voucher->assignedTo)
                <div class="assigned">Untuk: {{ $voucher->assignedTo->name }}</div>
                @endif
            </div>
            @endforeach
            @for($i = $row->count(); $i < 3; $i++)
            <div class="voucher-cell-empty"></div>
            @endfor
        </div>
        @endforeach
    </div>
</body>
</html>
