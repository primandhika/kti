<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Penjualan</title>
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
            background-color: #ddd;
            color: #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #000;
        }
        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .payment-breakdown {
            font-size: 9pt;
            color: #555;
            margin-top: 2px;
        }
        .payment-detail {
            font-size: 9pt;
            color: #333;
            margin-left: 15px;
            margin-top: 3px;
            line-height: 1.5;
        }
        .payment-detail:before {
            content: "\2022";
            margin-right: 8px;
        }
        .bagian-info {
            font-size: 9pt;
            color: #666;
            font-style: italic;
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
        .rekap-final {
            background-color: #e0e0e0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU" class="header-logo">
        <h2>Badan Pengelola Pengembangan Usaha</h2>
        <h3>IKIP Siliwangi</h3>
        <p><strong>Unit Usaha: {{ $data['work_unit'] }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['periode']['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data['periode']['end'])->format('d/m/Y') }}</p>
        <p>Waktu Pencetakan: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
    </div>

    <!-- Pembagian Hasil dengan Mitra -->
    <div class="table-title">Pembagian Hasil dengan Mitra</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama Mitra</th>
                <th style="width: 40%">Penjualan</th>
                <th style="width: 25%">Penghasilan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['pembagian_mitra'] as $index => $mitra)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $mitra['supplier_name'] }}</td>
                <td>
                    <strong>Rp {{ number_format($mitra['total_penjualan'], 0, ',', '.') }}</strong>
                    <span style="font-size: 9pt; color: #090; font-weight: bold;"> (Verified)</span>
                    @if(!empty($mitra['total_belum_verified']) && $mitra['total_belum_verified'] > 0)
                    <div style="font-size: 9pt; color: #c00; margin-top: 2px;">
                        Belum verified: Rp {{ number_format($mitra['total_belum_verified'], 0, ',', '.') }}
                    </div>
                    @endif
                    <div class="payment-breakdown">
                        @php
                            $methods = [];
                            if ($mitra['tunai'] > 0) $methods[] = 'Tunai: Rp ' . number_format($mitra['tunai'], 0, ',', '.');
                            if ($mitra['qris'] > 0) $methods[] = 'QRIS: Rp ' . number_format($mitra['qris'], 0, ',', '.');
                            if ($mitra['transfer'] > 0) $methods[] = 'Transfer: Rp ' . number_format($mitra['transfer'], 0, ',', '.');
                            if ($mitra['debit'] > 0) $methods[] = 'Debit: Rp ' . number_format($mitra['debit'], 0, ',', '.');
                            if ($mitra['kredit'] > 0) $methods[] = 'Kredit: Rp ' . number_format($mitra['kredit'], 0, ',', '.');
                        @endphp
                        ({{ implode(' - ', $methods) }})
                    </div>
                    <div class="bagian-info">
                        [Total Disimpan: Rp {{ number_format($mitra['bagian_vendor'], 0, ',', '.') }}]
                    </div>
                </td>
                <td class="text-right">
                    <strong>Rp {{ number_format($mitra['bagian_bppu'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pembagian mitra</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pendapatan Per User -->
    <div class="table-title">Transaksi Per User Kantin</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%">No</th>
                <th style="width: 60%">Nama User</th>
                <th style="width: 30%">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['pendapatan_per_user'] as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $user['user_name'] }}</td>
                <td class="text-right">
                    <strong>Rp {{ number_format($user['total'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data pendapatan user</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rekap Akhir -->
    <div class="table-title">Rekap Akhir</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 70%"><strong>Total Transaksi Keseluruhan</strong></td>
                <td style="width: 30%" class="text-right">
                    <strong>Rp {{ number_format($data['rekap']['total_keseluruhan'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @php
                $methodsKeseluruhan = [];
                if ($data['rekap']['tunai_keseluruhan'] > 0) $methodsKeseluruhan[] = 'Tunai: Rp ' . number_format($data['rekap']['tunai_keseluruhan'], 0, ',', '.');
                if ($data['rekap']['qris_keseluruhan'] > 0) $methodsKeseluruhan[] = 'QRIS: Rp ' . number_format($data['rekap']['qris_keseluruhan'], 0, ',', '.');
                if ($data['rekap']['transfer_keseluruhan'] > 0) $methodsKeseluruhan[] = 'Transfer: Rp ' . number_format($data['rekap']['transfer_keseluruhan'], 0, ',', '.');
                if ($data['rekap']['debit_keseluruhan'] > 0) $methodsKeseluruhan[] = 'Debit: Rp ' . number_format($data['rekap']['debit_keseluruhan'], 0, ',', '.');
                if ($data['rekap']['kredit_keseluruhan'] > 0) $methodsKeseluruhan[] = 'Kredit: Rp ' . number_format($data['rekap']['kredit_keseluruhan'], 0, ',', '.');
            @endphp
            @if(count($methodsKeseluruhan) > 0)
                <tr>
                    <td colspan="2" style="padding-left: 0; padding-right: 0; border: none;">
                        @foreach($methodsKeseluruhan as $method)
                            <div class="payment-detail">{{ $method }}</div>
                        @endforeach
                    </td>
                </tr>
            @endif
            <tr class="rekap-final">
                <td><strong>Penghasilan Total + Konsinyasi</strong></td>
                <td class="text-right">
                    <strong>Rp {{ number_format($data['rekap']['total_bagian_bppu'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 15px; border: none; font-size: 9pt; color: #666;">
                    [Total Disimpan: Rp {{ number_format($data['rekap']['total_bagian_vendor'], 0, ',', '.') }}]
                </td>
            </tr>
            <tr>
                <td><strong>Total Transaksi Terverifikasi</strong></td>
                <td class="text-right">
                    <strong>Rp {{ number_format($data['rekap']['total_verified'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @php
                $methodsVerified = [];
                if ($data['rekap']['tunai_verified'] > 0) $methodsVerified[] = 'Tunai: Rp ' . number_format($data['rekap']['tunai_verified'], 0, ',', '.');
                if ($data['rekap']['qris_verified'] > 0) $methodsVerified[] = 'QRIS: Rp ' . number_format($data['rekap']['qris_verified'], 0, ',', '.');
                if ($data['rekap']['transfer_verified'] > 0) $methodsVerified[] = 'Transfer: Rp ' . number_format($data['rekap']['transfer_verified'], 0, ',', '.');
                if ($data['rekap']['debit_verified'] > 0) $methodsVerified[] = 'Debit: Rp ' . number_format($data['rekap']['debit_verified'], 0, ',', '.');
                if ($data['rekap']['kredit_verified'] > 0) $methodsVerified[] = 'Kredit: Rp ' . number_format($data['rekap']['kredit_verified'], 0, ',', '.');
            @endphp
            @if(count($methodsVerified) > 0)
                <tr>
                    <td colspan="2" style="padding-left: 0; padding-right: 0; border: none;">
                        @foreach($methodsVerified as $method)
                            <div class="payment-detail">{{ $method }}</div>
                        @endforeach
                    </td>
                </tr>
            @endif
            <tr>
                <td><strong>Total Transaksi Sisa</strong></td>
                <td class="text-right">
                    <strong>Rp {{ number_format($data['rekap']['total_sisa'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @php
                $methodsSisa = [];
                if ($data['rekap']['tunai_sisa'] > 0) $methodsSisa[] = 'Tunai: Rp ' . number_format($data['rekap']['tunai_sisa'], 0, ',', '.');
                if ($data['rekap']['qris_sisa'] > 0) $methodsSisa[] = 'QRIS: Rp ' . number_format($data['rekap']['qris_sisa'], 0, ',', '.');
                if ($data['rekap']['transfer_sisa'] > 0) $methodsSisa[] = 'Transfer: Rp ' . number_format($data['rekap']['transfer_sisa'], 0, ',', '.');
                if ($data['rekap']['debit_sisa'] > 0) $methodsSisa[] = 'Debit: Rp ' . number_format($data['rekap']['debit_sisa'], 0, ',', '.');
                if ($data['rekap']['kredit_sisa'] > 0) $methodsSisa[] = 'Kredit: Rp ' . number_format($data['rekap']['kredit_sisa'], 0, ',', '.');
            @endphp
            @if(count($methodsSisa) > 0)
                <tr>
                    <td colspan="2" style="padding-left: 0; padding-right: 0; border: none;">
                        @foreach($methodsSisa as $method)
                            <div class="payment-detail">{{ $method }}</div>
                        @endforeach
                    </td>
                </tr>
            @endif
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
                        <div>Penyetor</div>
                        <div><strong>Petugas</strong></div>
                        <div class="signature-line">
                            <strong>{{ $data['penyetor_nama'] }}</strong>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
