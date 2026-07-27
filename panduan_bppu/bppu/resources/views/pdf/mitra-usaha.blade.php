<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumen Mitra Usaha - {{ $data['supplier']['nama'] }}</title>
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
            font-size: 12pt;
            margin-bottom: 8px;
            margin-top: 15px;
            color: #000;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 8px;
            border: 1px solid #000;
            vertical-align: top;
        }
        .info-table .label {
            width: 35%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .info-table .value {
            width: 65%;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-img {
            max-width: 200px;
            max-height: 200px;
            border: 2px solid #996600;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            margin-right: 5px;
        }
        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .badge-primary {
            background-color: #d6c199;
            color: #6b4700;
        }
        .skema-detail {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .skema-detail-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .skema-detail-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            padding: 4px 8px;
            background-color: #fff;
        }
        .skema-detail-value {
            display: table-cell;
            width: 60%;
            padding: 4px 8px;
            background-color: #fff;
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
        <p><strong>Dokumen Mitra Usaha</strong></p>
        <p>Waktu Pencetakan: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
    </div>

    <!-- Logo Mitra -->
    @if($data['supplier']['logo'])
    <div class="logo-section">
        <img src="{{ public_path($data['supplier']['logo']) }}" alt="Logo Mitra" class="logo-img">
    </div>
    @endif

    <!-- Informasi Umum -->
    <div class="section-title">Informasi Umum</div>
    <table class="info-table">
        <tr>
            <td class="label">Kode Mitra</td>
            <td class="value">{{ $data['supplier']['kode_supplier'] }}</td>
        </tr>
        <tr>
            <td class="label">Tipe Mitra</td>
            <td class="value">
                <span class="badge badge-info">{{ $data['supplier']['tipe_mitra_label'] }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">Nama Mitra</td>
            <td class="value">{{ $data['supplier']['nama'] }}</td>
        </tr>
        @if($data['supplier']['perusahaan'])
        <tr>
            <td class="label">Perusahaan</td>
            <td class="value">{{ $data['supplier']['perusahaan'] }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Status</td>
            <td class="value">
                <span class="badge {{ $data['supplier']['is_active'] ? 'badge-active' : 'badge-inactive' }}">
                    {{ $data['supplier']['is_active'] ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Tanggal Terdaftar</td>
            <td class="value">{{ $data['supplier']['created_at'] }}</td>
        </tr>
    </table>

    <!-- Informasi Kontak -->
    <div class="section-title">Informasi Kontak</div>
    <table class="info-table">
        @if($data['supplier']['telepon'])
        <tr>
            <td class="label">Telepon</td>
            <td class="value">{{ $data['supplier']['telepon'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['email'])
        <tr>
            <td class="label">Email</td>
            <td class="value">{{ $data['supplier']['email'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['kontak_person'])
        <tr>
            <td class="label">Kontak Person</td>
            <td class="value">{{ $data['supplier']['kontak_person'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['telepon_kontak'])
        <tr>
            <td class="label">Telepon Kontak</td>
            <td class="value">{{ $data['supplier']['telepon_kontak'] }}</td>
        </tr>
        @endif
    </table>

    <!-- Alamat -->
    @if($data['supplier']['alamat'] || $data['supplier']['kota'])
    <div class="section-title">Alamat</div>
    <table class="info-table">
        @if($data['supplier']['alamat'])
        <tr>
            <td class="label">Alamat Lengkap</td>
            <td class="value">{{ $data['supplier']['alamat'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['kota'])
        <tr>
            <td class="label">Kota</td>
            <td class="value">{{ $data['supplier']['kota'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['provinsi'])
        <tr>
            <td class="label">Provinsi</td>
            <td class="value">{{ $data['supplier']['provinsi'] }}</td>
        </tr>
        @endif
        @if($data['supplier']['kode_pos'])
        <tr>
            <td class="label">Kode Pos</td>
            <td class="value">{{ $data['supplier']['kode_pos'] }}</td>
        </tr>
        @endif
    </table>
    @endif

    <!-- Skema Bisnis -->
    @if($data['skema_bisnis'])
    <div class="section-title">Skema Bisnis</div>
    <div class="skema-detail">
        <div class="skema-detail-row">
            <div class="skema-detail-label">Jenis Skema</div>
            <div class="skema-detail-value">
                <span class="badge badge-primary">{{ $data['skema_bisnis']['jenis_skema_label'] }}</span>
            </div>
        </div>

        <!-- Bagi Hasil Detail -->
        @if($data['skema_bisnis']['jenis_skema'] === 'bagi_hasil')
        <div class="skema-detail-row">
            <div class="skema-detail-label">Persentase Vendor</div>
            <div class="skema-detail-value">{{ $data['skema_bisnis']['persentase_vendor'] }}%</div>
        </div>
        <div class="skema-detail-row">
            <div class="skema-detail-label">Persentase Badan Usaha</div>
            <div class="skema-detail-value">{{ $data['skema_bisnis']['persentase_badan_usaha'] }}%</div>
        </div>
        @endif

        <!-- Konsinyasi Detail -->
        @if($data['skema_bisnis']['jenis_skema'] === 'konsinyasi')
        <div class="skema-detail-row">
            <div class="skema-detail-label">Nominal Flat per Item</div>
            <div class="skema-detail-value">Rp {{ number_format($data['skema_bisnis']['nominal_flat'], 0, ',', '.') }}</div>
        </div>
        @if($data['skema_bisnis']['minimum_harga_item'])
        <div class="skema-detail-row">
            <div class="skema-detail-label">Minimum Harga Item</div>
            <div class="skema-detail-value">Rp {{ number_format($data['skema_bisnis']['minimum_harga_item'], 0, ',', '.') }}</div>
        </div>
        @endif
        @endif

        <!-- Supplier Detail -->
        @if($data['skema_bisnis']['jenis_skema'] === 'supplier')
        <div class="skema-detail-row">
            <div class="skema-detail-label">Persentase dari Keuntungan</div>
            <div class="skema-detail-value">{{ $data['skema_bisnis']['persentase_dari_keuntungan'] }}%</div>
        </div>
        @endif

        @if($data['skema_bisnis']['catatan'])
        <div class="skema-detail-row">
            <div class="skema-detail-label">Catatan Skema</div>
            <div class="skema-detail-value">{{ $data['skema_bisnis']['catatan'] }}</div>
        </div>
        @endif

        <div class="skema-detail-row">
            <div class="skema-detail-label">Status Skema</div>
            <div class="skema-detail-value">
                <span class="badge {{ $data['skema_bisnis']['is_active'] ? 'badge-active' : 'badge-inactive' }}">
                    {{ $data['skema_bisnis']['is_active'] ? 'Berlakukan' : 'Tidak Berlakukan' }}
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Catatan Tambahan -->
    @if($data['supplier']['catatan'])
    <div class="section-title">Catatan Tambahan</div>
    <table class="info-table">
        <tr>
            <td class="value">{{ $data['supplier']['catatan'] }}</td>
        </tr>
    </table>
    @endif

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
                    <div>Mitra Usaha,</div>
                    <div><strong>{{ $data['supplier']['nama'] }}</strong></div>
                    <div class="signature-line">
                        <strong>(.............................)</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
