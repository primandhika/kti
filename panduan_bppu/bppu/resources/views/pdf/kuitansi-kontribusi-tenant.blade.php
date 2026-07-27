<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Biaya Kontribusi - {{ $tenant['nama'] }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; size: B5; }
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 10pt; color: #000; margin: 0; }

        .header { display: flex; align-items: center; gap: 14px; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header img { width: 56px; height: 56px; object-fit: contain; flex-shrink: 0; }
        .header-text { flex: 1; }
        .header-text .instansi { font-size: 8pt; color: #555; margin: 0; }
        .header-text .bppu { font-size: 13pt; font-weight: bold; margin: 0; line-height: 1.2; }
        .header-text .sub { font-size: 9.5pt; margin: 0; }
        .header-text .alamat { font-size: 7.5pt; color: #555; margin: 0; }

        .judul { text-align: center; font-size: 12pt; font-weight: bold; letter-spacing: 1.5px;
                 text-transform: uppercase; border: 1.5px solid #000; padding: 4px 8px;
                 margin: 0 0 10px; }

        .meta { display: flex; justify-content: space-between; font-size: 8.5pt; margin-bottom: 10px; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9.5pt; }
        table.info td { padding: 3px 4px; vertical-align: top; }
        table.info td.label { width: 130px; }
        table.info td.sep { width: 10px; text-align: center; }

        .nominal-box { border: 2px solid #000; padding: 8px 12px; margin-bottom: 10px; }
        .nominal-box .label { font-size: 8pt; color: #555; margin-bottom: 2px; }
        .nominal-box .nilai { font-size: 17pt; font-weight: bold; line-height: 1; }
        .nominal-box .terbilang { font-size: 8.5pt; font-style: italic; margin-top: 4px;
                                   border-top: 1px dashed #aaa; padding-top: 4px; }

        .ttd { display: flex; justify-content: space-between; margin-top: 14px; }
        .ttd-block { text-align: center; width: 160px; font-size: 8.5pt; }
        .ttd-block .garis { border-top: 1px solid #000; padding-top: 3px; margin-top: 44px;
                             font-weight: bold; font-size: 9pt; }

        .footer { margin-top: 10px; border-top: 1px solid #ccc; padding-top: 5px;
                  text-align: center; font-size: 7.5pt; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU">
        <div class="header-text">
            <p class="instansi">IKIP Siliwangi</p>
            <p class="bppu">Badan Pengelola Pengembangan Usaha</p>
            <p class="sub">(BPPU)</p>
            <p class="alamat">Jl. Terusan Jend. Sudirman, Cimahi &nbsp;&bull;&nbsp; bppu.ikipsiliwangi.ac.id</p>
        </div>
    </div>

    <div class="judul">Kuitansi Biaya Kontribusi Tenant</div>

    <div class="meta">
        <span>No: <strong>{{ $nomorKuitansi }}</strong></span>
        <span>Cimahi, {{ $tanggal }}</span>
    </div>

    <table class="info">
        <tr>
            <td class="label">Telah Diterima Dari</td>
            <td class="sep">:</td>
            <td><strong>{{ $tenant['nama'] }}{{ $tenant['perusahaan'] ? ' / ' . $tenant['perusahaan'] : '' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kode Mitra</td>
            <td class="sep">:</td>
            <td>{{ $tenant['kode_supplier'] }}</td>
        </tr>
        <tr>
            <td class="label">Penempatan</td>
            <td class="sep">:</td>
            <td>{{ $tenant['penempatan_work_unit_nama'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Untuk Pembayaran</td>
            <td class="sep">:</td>
            <td>Biaya Kontribusi Tenant Bulan {{ $bulan }}</td>
        </tr>
    </table>

    <div class="nominal-box">
        <div class="label">Jumlah Pembayaran</div>
        <div class="nilai">Rp {{ number_format($tenant['biaya_kontribusi'], 0, ',', '.') }},-</div>
        <div class="terbilang">Terbilang: <em>{{ $terbilang }}</em></div>
    </div>

    <div class="ttd">
        <div class="ttd-block">
            Yang Membayar,
            <div class="garis">{{ $tenant['nama'] }}</div>
        </div>
        <div class="ttd-block">
            Yang Menerima,
            <div class="garis">Bendahara BPPU</div>
        </div>
    </div>

    <div class="footer">Dokumen diterbitkan secara sistem &mdash; sah tanpa tanda tangan basah</div>
</body>
</html>
