<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kantin - {{ $workUnitName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Garamond', 'Georgia', serif;
            color: #000;
            background: #ffffff;
            padding: 55px 80px;
            line-height: 1.15;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 3px solid #000;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            filter: grayscale(100%);
        }

        .header h1 {
            color: #000;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.15;
        }

        .header h2 {
            color: #000;
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 6px;
            line-height: 1.15;
        }

        .header .subtitle {
            color: #000;
            font-size: 10px;
            line-height: 1.3;
        }

        .contact-info {
            font-size: 9px;
            color: #000;
            line-height: 1.4;
            margin-top: 6px;
        }

        .category-section {
            margin-bottom: 18px;
        }

        .category-header {
            color: #000;
            padding: 6px 0;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            border-bottom: 2.5px solid #000;
            line-height: 1.15;
        }

        .menu-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .menu-item.out-of-stock {
            opacity: 0.6;
        }

        .menu-name {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5px;
            color: #000;
            line-height: 1.15;
        }

        .menu-name.out-of-stock {
            text-decoration: line-through;
        }

        .price-list {
            width: 100%;
        }

        .price-item {
            margin-bottom: 3px;
            line-height: 1.15;
        }

        .price-item table {
            width: 100%;
            border-collapse: collapse;
        }

        .price-item td {
            padding: 3px 0;
            border-bottom: 1px dotted #666;
            line-height: 1.15;
        }

        .price-item .label {
            font-size: 14px;
            color: #000;
            text-align: left;
            padding-right: 8px;
        }

        .price-item .price {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            text-align: right;
            white-space: nowrap;
            padding-left: 8px;
        }

        .price-single .label {
            font-size: 15px;
            font-weight: bold;
            color: #000;
        }

        .price-single .price {
            font-size: 15px;
        }

        .out-of-stock .label {
            text-decoration: line-through;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2.5px solid #000;
            text-align: center;
            font-size: 9px;
            color: #333;
        }

        .footer p {
            margin: 2px 0;
            line-height: 1.4;
        }

        .generated-info {
            margin-top: 8px;
            padding: 6px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            font-size: 8px;
            color: #555;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('logo-BPPU-flat.png') }}" alt="Logo BPPU" class="logo">
            <h1>Menu Kantin</h1>
            <h2>{{ $workUnitName }}</h2>
            <div class="subtitle">Badan Pengelola dan Pengembangan Usaha - IKIP Siliwangi</div>
            <div class="contact-info">
                Email: bppu@ikipsiliwangi.ac.id | Website: https://bppu.ikipsiliwangi.ac.id/<br>
                WhatsApp: {{ $contactPhone }}
            </div>
        </div>

        <!-- Menu Items by Sub Category -->
        @foreach($groupedMenus as $subKategori => $menus)
        <div class="category-section">
            <div class="category-header">{{ $subKategori }}</div>

            @foreach($menus as $menu)
            <div class="menu-item {{ $menu['stok'] == 0 ? 'out-of-stock' : '' }}">
                @if($menu['varians']->isNotEmpty())
                    <div class="menu-name {{ $menu['stok'] == 0 ? 'out-of-stock' : '' }}">{{ $menu['nama_barang'] }}</div>
                    <div class="price-list">
                        @foreach($menu['varians'] as $varian)
                        <div class="price-item">
                            <table>
                                <tr>
                                    <td class="label">{{ $varian['nama'] }}</td>
                                    <td class="price">
                                        @if($varian['harga'] && $varian['harga'] > 0)
                                            Rp {{ number_format($varian['harga'], 0, ',', '.') }}
                                        @else
                                            Hubungi Kami
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="price-list">
                        <div class="price-item price-single {{ $menu['stok'] == 0 ? 'out-of-stock' : '' }}">
                            <table>
                                <tr>
                                    <td class="label">{{ $menu['nama_barang'] }}</td>
                                    <td class="price">Rp {{ number_format($menu['harga_jual'], 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <p><strong>Badan Pengelola dan Pengembangan Usaha (BPPU)</strong></p>
            <p>IKIP Siliwangi</p>
            <p>Jl. Terusan Jenderal Sudirman, Cimahi, Jawa Barat 40526</p>
            <div class="generated-info">
                Dicetak pada: {{ $generatedDate }} pukul {{ $generatedTime }} WIB
            </div>
        </div>
    </div>
</body>
</html>
