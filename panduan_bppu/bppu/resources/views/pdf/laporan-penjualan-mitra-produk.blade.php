<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Per Item Per Mitra</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 4cm;
        }
        body {
            font-family: 'Cambria', 'Times New Roman', serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 2px solid #000;
            padding-bottom: 14px;
            line-height: 1;
        }
        .header-logo {
            width: 120px;
            height: auto;
            margin: 0 auto 5px;
        }
        .header h2 {
            margin: 3px 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 12pt;
            font-weight: normal;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th {
            background-color: #ddd;
            color: #000;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 8.5pt;
        }
        td {
            padding: 5px 6px;
            border: 1px solid #000;
            font-size: 8.5pt;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        .table-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            margin-top: 14px;
            color: #000;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-table td {
            font-size: 9pt;
        }
        .rekap-final {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .mitra-block {
            page-break-inside: avoid;
            margin-top: 12px;
        }
        .mitra-heading {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            margin-bottom: 0;
        }
        .mitra-meta {
            font-size: 8.5pt;
            font-weight: normal;
            margin-top: 2px;
        }
        .small {
            font-size: 8pt;
            color: #333;
        }
        .footer-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border: none;
            margin-top: 20px;
        }
        .signature-table td {
            border: none;
            vertical-align: top;
            padding: 10px 20px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU" class="header-logo">
        <h2>Badan Pengelola Pengembangan Usaha</h2>
        <h3>IKIP Siliwangi</h3>
        <p><strong>Laporan Penjualan Per Item Per Mitra</strong></p>
        <p><strong>Unit Usaha: {{ $data['work_unit'] }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['periode']['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data['periode']['end'])->format('d/m/Y') }}</p>
        <p>Waktu Pencetakan: {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
    </div>

    <div class="table-title">Rekap Akhir</div>
    <table class="summary-table">
        <tbody>
            <tr>
                <td style="width: 70%"><strong>Total Mitra</strong></td>
                <td class="text-right"><strong>{{ number_format($data['summary']['total_mitra'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Item Mitra</strong></td>
                <td class="text-right"><strong>{{ number_format($data['summary']['total_produk'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Qty Terjual</strong></td>
                <td class="text-right"><strong>{{ number_format($data['summary']['total_qty'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Jumlah Transaksi</strong></td>
                <td class="text-right"><strong>{{ number_format($data['summary']['jumlah_transaksi'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="rekap-final">
                <td><strong>Total Penjualan Bruto</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($data['summary']['penjualan_bruto'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total HPP</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($data['summary']['total_hpp'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Untung Kotor</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($data['summary']['untung_kotor'], 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="table-title">Detail Penjualan Per Item Per Mitra</div>

    @forelse($data['mitra_produk'] as $mitra)
        <div class="mitra-block">
            <div class="mitra-heading">
                {{ $loop->iteration }}. {{ $mitra['nama'] }}
                <div class="mitra-meta">
                    Kode: {{ $mitra['kode_supplier'] }} | Tipe: {{ $mitra['tipe_mitra'] }} | Item: {{ number_format($mitra['total_produk'], 0, ',', '.') }} | Qty: {{ number_format($mitra['total_qty'], 0, ',', '.') }} | Transaksi: {{ number_format($mitra['jumlah_transaksi'], 0, ',', '.') }} | Penjualan: Rp {{ number_format($mitra['penjualan_bruto'], 0, ',', '.') }}
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 14%">Kode Barang</th>
                        <th style="width: 27%">Nama Barang</th>
                        <th style="width: 8%">Satuan</th>
                        <th style="width: 8%" class="text-right">Qty</th>
                        <th style="width: 9%" class="text-right">Trx</th>
                        <th style="width: 14%" class="text-right">Penjualan</th>
                        <th style="width: 12%" class="text-right">HPP</th>
                        <th style="width: 13%" class="text-right">Untung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mitra['produk'] as $produk)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $produk['kode_barang'] }}</td>
                            <td>
                                <strong>{{ $produk['nama_barang'] }}</strong>
                                <div class="small">Margin: {{ number_format($produk['margin_persen'], 1, ',', '.') }}%</div>
                            </td>
                            <td>{{ $produk['satuan'] }}</td>
                            <td class="text-right"><strong>{{ number_format($produk['total_qty'], 0, ',', '.') }}</strong></td>
                            <td class="text-right">{{ number_format($produk['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($produk['penjualan_bruto'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($produk['total_hpp'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($produk['untung_kotor'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <table>
            <tbody>
                <tr>
                    <td class="text-center">Tidak ada data penjualan item mitra pada periode ini.</td>
                </tr>
            </tbody>
        </table>
    @endforelse

    <div class="footer-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%">
                    <div class="signature-box">
                        <div>Mengetahui,</div>
                        <div><strong>Kepala BPPU</strong></div>
                        <div class="signature-line">
                            <strong>{{ $data['kepala_bppu_nama'] ?: '( ........................... )' }}</strong>
                            @if(!empty($data['kepala_bppu_nip']))
                                <div class="small">NIP. {{ $data['kepala_bppu_nip'] }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="width: 50%">
                    <div class="signature-box">
                        <div>Petugas Laporan</div>
                        <div><strong>Pelaksana</strong></div>
                        <div class="signature-line">
                            <strong>( ........................... )</strong>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
