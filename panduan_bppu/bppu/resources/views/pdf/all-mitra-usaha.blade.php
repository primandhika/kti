<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Mitra Usaha</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 4cm;
        }
        body {
            font-family: 'Cambria', 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.4;
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
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            margin-top: 15px;
            color: #000;
            background-color: #d6c199;
            padding: 8px 12px;
            border-left: 4px solid #996600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #ddd;
            color: #000;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 10pt;
        }
        td {
            padding: 6px;
            border: 1px solid #000;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .summary-box {
            background-color: #eae0cc;
            border: 2px solid #996600;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .summary-box h3 {
            margin: 0;
            font-size: 12pt;
            color: #6b4700;
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
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU" class="header-logo">
        <h2>Badan Pengelola Pengembangan Usaha</h2>
        <h3>IKIP Siliwangi</h3>
        <p><strong>Daftar Mitra Usaha</strong></p>
        <p>Waktu Pencetakan: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h3>Total Mitra Usaha: {{ $data['total'] }}</h3>
    </div>

    <!-- Grouped Suppliers -->
    @foreach($data['grouped_suppliers'] as $tipeLabel => $suppliers)
    <div class="section-title">{{ $tipeLabel }} ({{ count($suppliers) }} Mitra)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 12%">Kode</th>
                <th style="width: 20%">Nama Mitra</th>
                <th style="width: 18%">Perusahaan</th>
                <th style="width: 13%">Telepon</th>
                <th style="width: 13%">Kota</th>
                <th style="width: 12%">Skema</th>
                <th style="width: 8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $index => $supplier)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $supplier['kode_supplier'] }}</td>
                <td><strong>{{ $supplier['nama'] }}</strong></td>
                <td>{{ $supplier['perusahaan'] ?? '-' }}</td>
                <td>{{ $supplier['telepon'] ?? '-' }}</td>
                <td>{{ $supplier['kota'] ?? '-' }}</td>
                <td>{{ $supplier['skema_bisnis']['jenis_skema_label'] ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $supplier['is_active'] ? 'badge-active' : 'badge-inactive' }}">
                        {{ $supplier['is_active'] ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <!-- Signature Section -->
    <div class="footer-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%">
                    <div>Mengetahui,</div>
                    <div><strong>Kepala BPPU</strong></div>
                    <div class="signature-line">
                        <strong>(.............................)</strong>
                    </div>
                </td>
                <td style="width: 50%">
                    <div>Dicetak oleh,</div>
                    <div><strong>Petugas</strong></div>
                    <div class="signature-line">
                        <strong>(.............................)</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
