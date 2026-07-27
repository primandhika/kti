<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voucher Thermal {{ $potongan->kode_potongan }}</title>
    <style>
        @page {
            margin: 3mm 3mm;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            color: #000;
            margin: 0;
            padding: 0;
            width: 74mm;
        }
        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .ticket {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000;
        }
        .ticket:last-child {
            border-bottom: none;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 16pt; font-weight: bold; }
        .barcode-wrap {
            text-align: center;
            margin: 4px 0 2px;
        }
        .barcode-wrap img {
            max-width: 100%;
            height: 40px;
        }
        .kode {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-align: center;
            color: #333;
        }
        .small { font-size: 7.5pt; color: #555; }
        .title { font-size: 10pt; font-weight: bold; }
        .header { text-align: center; margin-bottom: 4px; }
    </style>
</head>
<body>
    @foreach($vouchers as $voucher)
    <div class="ticket">
        <div class="header">
            <div class="title">BPPU IKIP SILIWANGI</div>
            <div class="small">VOUCHER DISKON</div>
        </div>
        <hr class="divider">
        <div class="center bold">{{ $potongan->nama }}</div>
        <hr class="divider">
        <div class="center large">
            @if($potongan->tipe === 'persen')
                DISKON {{ number_format($potongan->nilai, 0) }}%
            @else
                Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
            @endif
        </div>
        <hr class="divider">
        <div class="barcode-wrap">
            @if($voucher->barcode_base64)
                <img src="data:image/png;base64,{{ $voucher->barcode_base64 }}" alt="{{ $voucher->kode_voucher }}">
            @endif
        </div>
        <div class="kode">{{ $voucher->kode_voucher }}</div>
        <hr class="divider">
        <div class="small center">
            @if($potongan->minimum_transaksi > 0)
                Min. transaksi Rp {{ number_format($potongan->minimum_transaksi, 0, ',', '.') }}<br>
            @endif
            @if($potongan->berlaku_sampai)
                Berlaku s/d: {{ $potongan->berlaku_sampai->format('d/m/Y') }}<br>
            @else
                Berlaku tidak terbatas<br>
            @endif
            @if($voucher->assignedTo)
                Untuk: {{ $voucher->assignedTo->name }}
            @endif
        </div>
    </div>
    @endforeach
</body>
</html>
