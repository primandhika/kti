<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kantin - {{ $workUnitName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            color: #1e1400;
            background: #fff;
            padding: 32px 48px;
            font-size: 14px;
            line-height: 1.4;
        }

        /* HEADER */
        .header {
            background-color: #6b4700;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 28px;
            color: #fff;
            overflow: hidden;
        }
        .header-logo {
            float: left;
            width: 64px;
            height: 64px;
            margin-right: 16px;
            object-fit: contain;
        }
        .header-text h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 2px;
        }
        .header-text h2 {
            font-size: 14px;
            font-weight: normal;
            color: #f4efe5;
            margin-bottom: 3px;
        }
        .header-text .subtitle { font-size: 11px; color: #e0d1b2; }
        .header-text .contact { font-size: 10px; color: #d6c199; margin-top: 3px; }
        .header-clearfix { clear: both; }

        /* KATEGORI */
        .category-section { margin-bottom: 20px; }
        .category-header {
            font-size: 13px;
            font-weight: bold;
            color: #6b4700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #996600;
            padding-bottom: 4px;
            margin-bottom: 10px;
            page-break-after: avoid;
            break-after: avoid;
        }

        /* MENU ITEM — pakai table agar break-inside bekerja di semua browser print */
        .menu-item {
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .menu-item table {
            width: 100%;
            border-collapse: collapse;
        }
        .menu-item td {
            vertical-align: middle;
            padding: 0;
        }
        .menu-item td.img-cell {
            width: 86px;
            padding-right: 10px;
        }
        .menu-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            display: block;
        }
        .menu-item td.body-cell {
            border-bottom: 1px dotted #b7934c;
            padding-bottom: 6px;
        }
        .menu-item.out-of-stock td.body-cell { opacity: 0.55; }
        .menu-item-name {
            font-size: 13px;
            font-weight: bold;
            color: #2d1e00;
            margin-bottom: 3px;
        }
        .menu-item-name.crossed { text-decoration: line-through; }
        .price-row {
            width: 100%;
            border-collapse: collapse;
        }
        .price-row td { padding: 1px 0; font-size: 12px; color: #3d2800; }
        .price-row td.lbl { text-align: left; color: #5b3d00; }
        .price-row td.val { text-align: right; font-weight: bold; color: #996600; white-space: nowrap; padding-left: 8px; }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1.5px solid #e0d1b2;
            text-align: center;
            font-size: 9px;
            color: #7a5100;
        }
        .footer p { margin: 2px 0; }
        .generated-info { margin-top: 5px; font-size: 8px; color: #b7934c; }

        /* PRINT BUTTON (layar saja) */
        .print-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #996600;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 999;
        }
        .print-btn:hover { background: #7a5100; }

        @media print {
            body { padding: 10mm 14mm; }
            .print-btn { display: none; }
            .menu-item { page-break-inside: avoid; break-inside: avoid; }
            .category-header { page-break-after: avoid; break-after: avoid; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">Cetak / Simpan PDF</button>
    <script>window.addEventListener('load', function() { window.print(); });</script>

    <!-- Header -->
    <div class="header">
        <img src="{{ asset('storage/logo-round_ijokuning.png') }}" alt="Logo BPPU" class="header-logo">
        <div class="header-text">
            <h1>Menu Kantin</h1>
            <h2>{{ $workUnitName }}</h2>
            <div class="subtitle">Badan Pengelola dan Pengembangan Usaha &mdash; IKIP Siliwangi</div>
            <div class="contact">
                bppu@ikipsiliwangi.ac.id &nbsp;|&nbsp; bppu.ikipsiliwangi.ac.id
                @if($contactPhone && $contactPhone !== '-')
                &nbsp;|&nbsp; WA: {{ $contactPhone }}
                @endif
            </div>
        </div>
        <div class="header-clearfix"></div>
    </div>

    <!-- Menu per Sub Kategori -->
    @foreach($groupedMenus as $subKategori => $menus)
    <div class="category-section">
        <div class="category-header">{{ $subKategori }}</div>

        @foreach($menus as $menu)
        <div class="menu-item {{ $menu['stok'] == 0 ? 'out-of-stock' : '' }}">
            <table>
                <tr>
                    @if($menu['gambar'])
                    <td class="img-cell">
                        <img src="{{ asset(ltrim($menu['gambar'], '/')) }}" alt="{{ $menu['nama_barang'] }}" class="menu-item-img">
                    </td>
                    @endif
                    <td class="body-cell">
                        @if($menu['varians']->isNotEmpty())
                            <div class="menu-item-name {{ $menu['stok'] == 0 ? 'crossed' : '' }}">{{ $menu['nama_barang'] }}</div>
                            <table class="price-row">
                                @foreach($menu['varians'] as $varian)
                                <tr>
                                    <td class="lbl">{{ $varian['nama'] }}</td>
                                    <td class="val">
                                        @if($varian['harga'] && $varian['harga'] > 0)
                                            Rp {{ number_format($varian['harga'], 0, ',', '.') }}
                                        @else
                                            Hubungi Kami
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        @else
                            <table class="price-row">
                                <tr>
                                    <td class="lbl"><span class="menu-item-name {{ $menu['stok'] == 0 ? 'crossed' : '' }}">{{ $menu['nama_barang'] }}</span></td>
                                    <td class="val">Rp {{ number_format($menu['harga_jual'], 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p><strong>Badan Pengelola dan Pengembangan Usaha &mdash; IKIP Siliwangi</strong></p>
        <p>Jl. Terusan Jenderal Sudirman, Cimahi, Jawa Barat 40526</p>
        <div class="generated-info">Dicetak pada {{ $generatedDate }} pukul {{ $generatedTime }} WIB</div>
    </div>

</body>
</html>
