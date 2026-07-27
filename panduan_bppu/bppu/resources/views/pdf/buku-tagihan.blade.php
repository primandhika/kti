<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tagihan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #996600;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #6b4700;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            color: #996600;
            margin-bottom: 5px;
        }

        .header .info {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        .summary-box {
            background-color: #f4efe5;
            border: 1px solid #996600;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 8pt;
            color: #6b4700;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 11pt;
            font-weight: bold;
            color: #996600;
        }

        .filters {
            background-color: #eae0cc;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .filters strong {
            color: #6b4700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background-color: #996600;
            color: white;
        }

        thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 6px 5px;
            font-size: 8pt;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .member-name {
            color: #996600;
            font-weight: bold;
        }

        .tier-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .verified {
            color: #10b981;
        }

        .pending {
            color: #f59e0b;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 7pt;
            color: #666;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .transaksi-detail {
            margin-left: 15px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .transaksi-item {
            background-color: #f9f9f9;
            padding: 5px;
            margin-bottom: 3px;
            border-left: 3px solid #996600;
            font-size: 8pt;
        }

        .transaksi-item strong {
            color: #6b4700;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>IKIP Siliwangi Bandung</h1>
        <h2>BUKU TAGIHAN MEMBER</h2>
        <div class="info">
            <div><strong>{{ $workUnit ? $workUnit->name : 'Semua Unit Kerja' }}</strong></div>
            <div>Periode: {{ $periode }}</div>
            @if(count($filters) > 0)
                <div style="margin-top: 3px;">
                    @foreach($filters as $filter)
                        <span style="background: #eae0cc; padding: 2px 6px; border-radius: 3px; margin-right: 5px;">{{ $filter }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Member</div>
                <div class="summary-value">{{ $summary['total_member'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ $summary['total_transaksi'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Tagihan</div>
                <div class="summary-value">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Member</th>
                <th style="width: 15%;">Kontak</th>
                <th style="width: 10%;">Trx</th>
                <th style="width: 15%;">Total Tagihan</th>
                <th style="width: 15%;">Tertua</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="member-name">{{ $item['buyer_name'] }}</span>
                        @if($item['tier'])
                            <br>
                            <span class="tier-badge" style="background: {{ $item['tier']['color'] }}20; color: {{ $item['tier']['color'] }};">
                                {{ $item['tier']['name'] }}
                            </span>
                        @endif
                        <br>
                        <small style="color: #666;">{{ $item['member_code'] ?? '-' }}</small>
                    </td>
                    <td>
                        {{ $item['phone'] ?? '-' }}
                        @if($item['email'])
                            <br><small>{{ $item['email'] }}</small>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $item['total_transaksi'] }}</td>
                    <td class="text-right font-bold" style="color: #dc2626;">
                        Rp {{ number_format($item['total_tagihan'], 0, ',', '.') }}
                    </td>
                    <td>{{ date('d/m/Y', strtotime($item['transaksi_tertua'])) }}</td>
                    <td>
                        @php
                            $verifiedCount = collect($item['transaksi'])->where('is_verified', true)->count();
                            $pendingCount = collect($item['transaksi'])->where('is_verified', false)->count();
                        @endphp
                        @if($verifiedCount > 0)
                            <span class="verified">✓ {{ $verifiedCount }} Verified</span>
                        @endif
                        @if($pendingCount > 0)
                            <br><span class="pending">⧗ {{ $pendingCount }} Pending</span>
                        @endif
                    </td>
                </tr>
                @if(count($item['transaksi']) > 0)
                    <tr>
                        <td colspan="7">
                            <div class="transaksi-detail">
                                <strong style="color: #6b4700; font-size: 8pt;">Detail Transaksi:</strong>
                                @foreach($item['transaksi'] as $trx)
                                    <div class="transaksi-item">
                                        <strong>{{ $trx['nomor_transaksi'] }}</strong>
                                        | {{ date('d/m/Y H:i', strtotime($trx['tanggal_transaksi'])) }}
                                        | Rp {{ number_format($trx['total'], 0, ',', '.') }}
                                        @if($trx['work_unit_name'])
                                            | {{ $trx['work_unit_name'] }}
                                        @endif
                                        | <span class="{{ $trx['is_verified'] ? 'verified' : 'pending' }}">
                                            {{ $trx['is_verified'] ? 'Verified' : 'Pending' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    @if($tagihan->isEmpty())
        <div style="text-align: center; padding: 30px; color: #999;">
            <p>Tidak ada data tagihan sesuai filter yang dipilih</p>
        </div>
    @endif

    <div class="footer">
        <div class="footer-grid">
            <div>
                <strong>Dibuat oleh:</strong> {{ $generatedBy }}<br>
                <strong>Tanggal cetak:</strong> {{ $generatedAt }}
            </div>
            <div style="text-align: right;">
                <strong>Buku Tagihan Member</strong><br>
                IKIP Siliwangi Bandung
            </div>
        </div>
    </div>
</body>
</html>
