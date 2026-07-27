<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stock Opname</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 4cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
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
            margin-bottom: 20px;
        }
        .table-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            margin-top: 15px;
            color: #000;
        }
        th {
            background-color: #e0e0e0;
            color: #000;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            border: 0.5pt solid #999;
            font-size: 8pt;
        }
        td {
            padding: 3px 5px;
            border: 0.5pt solid #ccc;
            font-size: 8pt;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-green {
            color: #059669;
            font-weight: bold;
        }
        .text-red {
            color: #dc2626;
            font-weight: bold;
        }
        .summary-box {
            border: 0.5pt solid #999;
            padding: 6px;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        .summary-row {
            padding: 2px 0;
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
        .keterangan {
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU" class="header-logo">
        <h2>Badan Pengelola Pengembangan Usaha</h2>
        <h3>IKIP Siliwangi</h3>
        <p><strong>Laporan Stock Opname</strong></p>
        <p><strong>Unit Usaha: {{ $data['work_unit'] }}{{ $data['work_unit_location'] ? ' - ' . $data['work_unit_location'] : '' }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['periode']['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data['periode']['end'])->format('d/m/Y') }}</p>
        <p>Waktu Pencetakan: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
        <p style="font-size: 9pt; color: #666;">Urutan: {{ $data['sort_by'] === 'alfabetis' ? 'Alfabetis' : 'Waktu' }} ({{ $data['sort_order'] === 'asc' ? 'A-Z / Lama-Baru' : 'Z-A / Baru-Lama' }})</p>
    </div>

    @if($data['is_limited'])
    <!-- Warning Box -->
    <div style="background-color: #fff3cd; border: 2pt solid #856404; padding: 8px; margin-bottom: 10px;">
        <p style="margin: 0; font-size: 9pt; color: #856404;">
            <strong>⚠ PERHATIAN:</strong> PDF dibatasi 500 record pertama dari total {{ number_format($data['total_count']) }} record.
            <br>Untuk data lengkap, gunakan <strong>Export CSV</strong> atau filter periode lebih spesifik.
        </p>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-box">
        <div class="summary-row">
            <span>Total Transaksi Stock Opname:</span>
            <span><strong>{{ $data['summary']['total_transaksi'] }}</strong></span>
        </div>
        <div class="summary-row">
            <span>Total Selisih Positif (Lebih):</span>
            <span class="text-green">+{{ $data['summary']['total_selisih_plus'] }} unit</span>
        </div>
        <div class="summary-row">
            <span>Total Selisih Negatif (Kurang):</span>
            <span class="text-red">-{{ $data['summary']['total_selisih_minus'] }} unit</span>
        </div>
        <div class="summary-row">
            <span>Total Selisih Rupiah Positif:</span>
            <span class="text-green">Rp {{ number_format($data['summary']['total_selisih_rupiah_plus'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Selisih Rupiah Negatif:</span>
            <span class="text-red">Rp {{ number_format($data['summary']['total_selisih_rupiah_minus'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Selisih Rupiah (Net):</span>
            <span style="font-weight: bold; {{ $data['summary']['total_selisih_rupiah'] >= 0 ? 'color: #059669;' : 'color: #dc2626;' }}">
                Rp {{ number_format($data['summary']['total_selisih_rupiah'], 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Stock Opname Table -->
    <div class="table-title">Detail Stock Opname</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 9%">Tanggal</th>
                <th style="width: 10%">Kode</th>
                <th style="width: 20%">Nama Barang</th>
                <th style="width: 10%">Kategori</th>
                <th style="width: 7%">Stok Awal</th>
                <th style="width: 7%">Stok Fisik</th>
                <th style="width: 7%">Selisih</th>
                <th style="width: 10%">COGS</th>
                <th style="width: 10%">Selisih Rp</th>
                <th style="width: 6%">Expired</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['data'] as $item)
            <tr>
                <td class="text-center">{{ $item['nomor'] }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item['tanggal_so'])->format('d/m/Y') }}</td>
                <td>{{ $item['kode_barang'] }}</td>
                <td>
                    <strong>{{ $item['nama_barang'] }}</strong>
                    @if($item['keterangan'])
                        <div class="keterangan">{{ $item['keterangan'] }}</div>
                    @endif
                </td>
                <td>{{ $item['kategori'] }}</td>
                <td class="text-center">{{ $item['stock_awal'] }}</td>
                <td class="text-center" style="background-color: #dbeafe;">{{ $item['stock_fisik'] }}</td>
                <td class="text-center {{ $item['selisih'] > 0 ? 'text-green' : ($item['selisih'] < 0 ? 'text-red' : '') }}">
                    {{ $item['selisih'] > 0 ? '+' : '' }}{{ $item['selisih'] }}
                </td>
                <td class="text-right">Rp {{ number_format($item['harga_pokok'], 0, ',', '.') }}</td>
                <td class="text-right {{ $item['selisih_rupiah'] > 0 ? 'text-green' : ($item['selisih_rupiah'] < 0 ? 'text-red' : '') }}">
                    Rp {{ number_format($item['selisih_rupiah'], 0, ',', '.') }}
                </td>
                <td class="text-center" style="font-size: 8pt;">
                    @if($item['expired_date'] && stripos($item['kategori'], 'makanan') !== false)
                        {{ \Carbon\Carbon::parse($item['expired_date'])->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">Tidak ada data stock opname</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="footer-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%">
                    <div class="signature-box">
                        <div>Mengetahui,</div>
                        <div><strong>Kepala BPPU</strong></div>
                        <div class="signature-line">
                            <strong>{{ $data['kepala_bppu_nama'] }}</strong>
                        </div>
                    </div>
                </td>
                <td style="width: 50%">
                    <div class="signature-box">
                        <div>Petugas Stock Opname</div>
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
