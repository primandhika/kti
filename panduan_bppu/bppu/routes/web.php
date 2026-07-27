<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\TokoController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\PengajuanBarangController;
use App\Http\Controllers\Admin\PengajuanTambahBarangController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\CanteenMenuController;
use App\Http\Controllers\Admin\BelanjaMenuController;
use App\Http\Controllers\Admin\ShopItemController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WorkUnitController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Sysadmin\KategoriTransaksiController;
use App\Http\Controllers\PoSController;
use App\Http\Controllers\PenjualanManagementController;
use App\Http\Controllers\PenjualanReportController;
use App\Http\Controllers\MitraReportController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SelfOrderController;
use App\Http\Controllers\BelanjaSelfOrderController;
use App\Http\Controllers\BuyerAuthController;
use App\Http\Controllers\MitraAuthController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\Member\MemberRegistrationController;
use App\Http\Controllers\Member\MemberAreaController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SurveyResultController;
use App\Http\Controllers\Admin\PotonganController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\RedeemPoinController;

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', function (\Illuminate\Http\Request $request) {
    // If there's a search query, redirect to berita page
    if ($request->has('search') && $request->search) {
        return redirect()->route('posts.index', ['search' => $request->search]);
    }

    $posts = \App\Models\Post::with(['category', 'user', 'tags'])
        ->where('status', 'published')
        ->latest('published_at')
        ->limit(3)
        ->get();

    // Get menus from Barang with is_menu_item = true (only available ones or those without display record)
    $menus = \App\Models\Barang::with(['menuDisplay', 'workUnit', 'kategoriBarang', 'varians' => function($query) {
            $query->where('is_active', true)->orderBy('display_order')->limit(3);
        }])
        ->where('is_menu_item', true)
        ->get()
        ->filter(function($barang) {
            // Show if no display record OR display is available
            return !$barang->menuDisplay || $barang->menuDisplay->is_available;
        })
        ->map(function($barang) {
            $display = $barang->menuDisplay;
            return [
                'id' => $barang->id,
                'name' => $barang->nama_barang,
                'description' => $display?->deskripsi_display ?? $barang->deskripsi ?? 'Menu lezat dari ' . ($barang->workUnit?->name ?? 'kantin'),
                'price' => $barang->harga_jual,
                'image' => $display?->gambar ?? null,
                'is_active' => true,
                'display_order' => $display?->display_order ?? 0,
                'category' => [
                    'name' => $barang->kategoriBarang?->nama ?? $barang->kategori ?? 'Makanan & Minuman',
                ],
                'varians' => $barang->varians->map(function($varian) {
                    return [
                        'id' => $varian->id,
                        'nama_varian' => $varian->nama_varian,
                        'deskripsi' => $varian->deskripsi,
                        'harga_jual' => $varian->harga_jual,
                    ];
                }),
            ];
        })
        ->values();

    // Get shop items from Barang with show_in_shop = true (only available ones with images)
    $shopItems = \App\Models\Barang::with(['menuDisplay', 'workUnit', 'kategoriBarang'])
        ->where('show_in_shop', true)
        ->whereHas('menuDisplay', function($query) {
            $query->whereNotNull('gambar')
                  ->where('gambar', '!=', '');
        })
        ->get()
        ->filter(function($barang) {
            // Show only if display is available and has image
            return $barang->menuDisplay &&
                   $barang->menuDisplay->is_available &&
                   $barang->menuDisplay->gambar;
        })
        ->map(function($barang) {
            $display = $barang->menuDisplay;
            return [
                'id' => $barang->id,
                'name' => $barang->nama_barang,
                'description' => $display->deskripsi_display ?? $barang->deskripsi,
                'price' => $barang->harga_jual,
                'image' => $display->gambar,
                'display_order' => $display->display_order ?? 0,
                'category' => $barang->kategoriBarang?->nama ?? $barang->kategori ?? 'Belanja',
            ];
        })
        ->sortBy('display_order')
        ->values();

    // Get stats for the homepage
    $unitKerjaCount = \App\Models\WorkUnit::where('is_active', true)->count();
    $yearsOperating = now()->year - 2017;
    $memberCount = \App\Models\User::role('buyer')->count();

    // Get unit kerja for display
    $unitUsaha = \App\Models\WorkUnit::where('is_active', true)
        ->orderBy('display_order')
        ->get();

    // Get mitra usaha yang punya logo untuk carousel
    $mitraUsaha = \App\Models\Supplier::where('is_active', true)
        ->whereNotNull('logo')
        ->where('logo', '!=', '')
        ->orderBy('nama')
        ->get();

    return Inertia::render('Welcome', [
        'laravelVersion' => app()->version(),
        'posts' => $posts,
        'menus' => $menus,
        'shopItems' => $shopItems,
        'unitUsaha' => $unitUsaha,
        'mitraUsaha' => $mitraUsaha,
        'stats' => [
            'unitKerja' => $unitKerjaCount,
            'yearsOperating' => $yearsOperating,
            'memberCount' => $memberCount,
        ],
    ]);
});

// Public Routes - Posts with date structure
Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{year}/{month}/{day}/{slug}', [PostController::class, 'show'])
    ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{2}', 'day' => '[0-9]{2}'])
    ->name('posts.show');

// Post Reactions
Route::post('/berita/{post}/reactions', [PostReactionController::class, 'toggle'])->name('posts.reactions.toggle');
Route::get('/berita/{post}/reactions', [PostReactionController::class, 'getReactions'])->name('posts.reactions.get');

// Public Routes - Order
Route::get('/pesan', [OrderController::class, 'index'])->name('order.index');
Route::get('/kantin/self-order', [SelfOrderController::class, 'index'])->name('self-order.index');
Route::get('/api/self-order/ping', [SelfOrderController::class, 'ping'])->name('self-order.ping');

// Member Registration Routes
Route::prefix('member')->group(function () {
    Route::get('/register', [MemberRegistrationController::class, 'showRegistrationForm'])->name('member.register.form');
    Route::post('/register', [MemberRegistrationController::class, 'register'])->name('member.register');
    Route::post('/survey', [MemberRegistrationController::class, 'submitSurvey'])->name('member.survey');
    Route::get('/verify-email', [MemberRegistrationController::class, 'showVerifyEmailPage'])->name('member.verify-email.show');
    Route::post('/verify-email', [MemberRegistrationController::class, 'verifyEmail'])->name('member.verify-email');
    Route::get('/terms-conditions', [MemberRegistrationController::class, 'showTermsConditions'])->name('member.terms-conditions');
});

// Member Area (auth check dilakukan manual di controller, redirect ke /kantin/self-order jika belum login)
Route::get('/member-area', [MemberAreaController::class, 'index'])->name('member.area');
Route::put('/member/default-meja', [MemberAreaController::class, 'updateDefaultMeja'])->name('member.default-meja.update');
Route::put('/member/profil', [MemberAreaController::class, 'updateProfil'])->name('member.profil.update');
Route::put('/member/password', [MemberAreaController::class, 'updatePassword'])->name('member.password.update');

// Self-Order - Checkout (guest via meja token diizinkan, auth via login biasa)
Route::post('/kantin/self-order/checkout', [SelfOrderController::class, 'checkout'])->name('self-order.checkout');
Route::middleware('auth')->group(function () {
    Route::post('/api/self-order/cancel/{id}', [SelfOrderController::class, 'cancelPesanan'])->name('self-order.cancel');
});
Route::get('/kantin/self-order/invoice/{id}', [SelfOrderController::class, 'invoice'])->name('self-order.invoice');
Route::post('/api/self-order/upload-bukti/{id}', [SelfOrderController::class, 'uploadBuktiBayar'])->name('self-order.upload-bukti');
Route::get('/api/self-order/status/{id}', [SelfOrderController::class, 'statusApi'])->name('self-order.status');
Route::post('/api/self-order/update-status/{id}', [SelfOrderController::class, 'updateStatus'])->name('self-order.update-status')->middleware('admin');
Route::post('/api/self-order/edit-items/{id}', [SelfOrderController::class, 'editItems'])->name('self-order.edit-items')->middleware('admin');
Route::get('/api/self-order/katalog-kantin', [SelfOrderController::class, 'katalogKasir'])->name('self-order.katalog-kasir')->middleware('admin');
Route::post('/api/self-order/tandai-bayar/{id}', [SelfOrderController::class, 'tandaiBayar'])->name('self-order.tandai-bayar')->middleware('admin');
Route::post('/api/self-order/cancel-with-penjualan/{id}', [SelfOrderController::class, 'cancelPesananWithPenjualan'])->name('self-order.cancel-with-penjualan')->middleware('admin');
Route::get('/api/self-order/pesanan-kantin', [SelfOrderController::class, 'pesananKantinApi'])->name('self-order.pesanan-kantin-api')->middleware('admin');
Route::get('/live-order', [SelfOrderController::class, 'liveOrder'])->name('live-order');
Route::get('/live-order/{workUnitId}', [SelfOrderController::class, 'liveOrderByUnit'])->name('live-order.unit');
Route::post('/api/live-order/verify-passcode', [SelfOrderController::class, 'verifyLiveOrderPasscode'])->name('live-order.verify');
Route::get('/api/live-order', [SelfOrderController::class, 'liveOrderApi'])->name('live-order.api');
Route::get('/api/live-order/{workUnitId}', [SelfOrderController::class, 'liveOrderApi'])->name('live-order.api.unit');
Route::middleware(['admin', 'role:officer,sysadmin'])->group(function () {
    Route::get('/live-transaction', [SelfOrderController::class, 'liveTransaction'])->name('live-transaction');
    Route::get('/live-transaction/{workUnitId}', [SelfOrderController::class, 'liveTransactionByUnit'])->name('live-transaction.unit');
    Route::get('/api/live-transaction', [SelfOrderController::class, 'liveTransactionApi'])->name('live-transaction.api');
    Route::get('/api/live-transaction/{workUnitId}', [SelfOrderController::class, 'liveTransactionApi'])->name('live-transaction.api.unit');
});

// Belanja Self-Order Routes
Route::get('/belanja/self-order', [BelanjaSelfOrderController::class, 'index'])->name('belanja.index');
Route::get('/api/belanja/ping', [BelanjaSelfOrderController::class, 'ping'])->name('belanja.ping');

// Belanja Self-Order - Checkout & Invoice
Route::middleware('auth')->group(function () {
    Route::post('/belanja/self-order/checkout', [BelanjaSelfOrderController::class, 'checkout'])->name('belanja.checkout');
    Route::post('/api/belanja/cancel/{id}', [BelanjaSelfOrderController::class, 'cancelPesanan'])->name('belanja.cancel');
});
Route::get('/belanja/self-order/invoice/{id}', [BelanjaSelfOrderController::class, 'invoice'])->name('belanja.invoice');
Route::post('/api/belanja/upload-bukti/{id}', [BelanjaSelfOrderController::class, 'uploadBuktiBayar'])->name('belanja.upload-bukti');
Route::get('/api/belanja/status/{id}', [BelanjaSelfOrderController::class, 'statusApi'])->name('belanja.status');
Route::post('/api/belanja/update-status/{id}', [BelanjaSelfOrderController::class, 'updateStatus'])->name('belanja.update-status')->middleware('admin');
Route::post('/api/belanja/edit-items/{id}', [BelanjaSelfOrderController::class, 'editItems'])->name('belanja.edit-items')->middleware('admin');
Route::get('/api/belanja/katalog-toko', [BelanjaSelfOrderController::class, 'katalogKasir'])->name('belanja.katalog-kasir')->middleware('admin');
Route::post('/api/belanja/cancel-with-penjualan/{id}', [BelanjaSelfOrderController::class, 'cancelPesananWithPenjualan'])->name('belanja.cancel-with-penjualan')->middleware('admin');
Route::get('/api/belanja/pesanan-toko', [BelanjaSelfOrderController::class, 'pesananKantinApi'])->name('belanja.pesanan-toko-api')->middleware('admin');

// Buyer Authentication Routes
Route::prefix('buyer')->group(function () {
    Route::post('/login', [BuyerAuthController::class, 'login'])->name('buyer.login');
    Route::post('/register', [BuyerAuthController::class, 'register'])->name('buyer.register');
    Route::post('/logout', [BuyerAuthController::class, 'logout'])->name('buyer.logout');
});

// API Routes for Cart
Route::prefix('api')->group(function () {
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::put('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem']);
    Route::post('/cart/clear', [CartController::class, 'clearCart']);

    // API for Penjualan (need authentication)
    Route::middleware('admin')->group(function () {
        Route::get('/penjualan/unit-kerja/summary', [PenjualanReportController::class, 'getApprovedSummaryByUnit']);
        Route::get('/penjualan/unit-kerja/{workUnitId}/approved', [PenjualanReportController::class, 'getApprovedByUnit']);
    });
});

// Admin Routes
Route::prefix('pengelola')->group(function () {
    // Redirect /pengelola to login
    Route::get('/', function () {
        return redirect('/pengelola/login');
    });

    // Login routes (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/dasbor', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dasbor/export', [AdminController::class, 'exportExecutiveDashboard'])->name('admin.dashboard.export');

        // Pengelolaan Konten - Officer & Sysadmin
        Route::middleware('role:officer,sysadmin')->group(function () {
            // Posts Management
            Route::resource('pos', AdminPostController::class)->names([
                'index' => 'admin.posts.index',
                'create' => 'admin.posts.create',
                'store' => 'admin.posts.store',
                'show' => 'admin.posts.show',
                'edit' => 'admin.posts.edit',
                'update' => 'admin.posts.update',
                'destroy' => 'admin.posts.destroy',
            ]);
            Route::post('/pos/upload-content-image', [AdminPostController::class, 'uploadContentImage'])->name('admin.posts.uploadContentImage');

            // Laporan Penjualan - Officer & Sysadmin
            Route::get('/laporan-penjualan', [AdminController::class, 'laporanPenjualan'])->name('admin.laporan-penjualan');
            Route::get('/laporan-penjualan/detail-transaksi', [AdminController::class, 'getDetailTransaksiBarang'])->name('admin.laporan-penjualan.detail');
            Route::get('/laporan-penjualan/export', [AdminController::class, 'exportLaporanPenjualan'])->name('admin.laporan-penjualan.export');
            Route::get('/laporan-penjualan/mitra-produk-pdf', [AdminController::class, 'downloadLaporanPenjualanMitraPdf'])->name('admin.laporan-penjualan.mitra-produk-pdf');

            // Buku Kas Write Operations - Officer & Sysadmin only
            Route::post('/buku-kas', [BukuKasController::class, 'store'])->name('admin.bukukas.store');
            Route::put('/buku-kas/{id}', [BukuKasController::class, 'update'])->name('admin.bukukas.update');
            Route::put('/buku-kas/{id}/assign-jenis', [BukuKasController::class, 'assignJenis'])->name('admin.bukukas.assignJenis');
            Route::delete('/buku-kas/{id}', [BukuKasController::class, 'destroy'])->name('admin.bukukas.destroy');
            Route::post('/buku-kas/{bukuKasId}/transaksi', [TransaksiKasController::class, 'store'])->name('admin.transaksi.store');
            Route::post('/buku-kas/{bukuKasId}/record-penjualan-by-date', [TransaksiKasController::class, 'recordPenjualanByDate'])->name('admin.transaksi.recordPenjualanByDate');
            Route::put('/buku-kas/{bukuKasId}/transaksi/{id}', [TransaksiKasController::class, 'update'])->name('admin.transaksi.update');
            Route::delete('/buku-kas/{bukuKasId}/transaksi/{id}', [TransaksiKasController::class, 'destroy'])->name('admin.transaksi.destroy');
        });

        // Menu Kantin Management - Officer, Sysadmin & Data Clerk
        Route::middleware('role:officer,sysadmin,data_clerk')->group(function () {
            Route::get('/menu-kantin', [CanteenMenuController::class, 'index'])->name('admin.canteen-menu.index');
            Route::get('/menu-kantin/download', [CanteenMenuController::class, 'downloadMenu'])->name('admin.canteen-menu.download');
            Route::post('/menu-kantin/{barangId}/display', [CanteenMenuController::class, 'updateDisplay'])->name('admin.canteen-menu.updateDisplay');
            Route::put('/menu-kantin/{barangId}/barang', [CanteenMenuController::class, 'updateBarang'])->name('admin.canteen-menu.updateBarang');
            Route::post('/menu-kantin/{barangId}/toggle-availability', [CanteenMenuController::class, 'toggleAvailability'])->name('admin.canteen-menu.toggleAvailability');
            Route::get('/menu-kantin/buat', [CanteenMenuController::class, 'create'])->name('admin.canteen-menu.create');
            Route::post('/menu-kantin', [CanteenMenuController::class, 'store'])->name('admin.canteen-menu.store');
            Route::get('/menu-kantin/{canteenMenu}/edit', [CanteenMenuController::class, 'edit'])->name('admin.canteen-menu.edit');
            Route::put('/menu-kantin/{canteenMenu}', [CanteenMenuController::class, 'update'])->name('admin.canteen-menu.update');
            Route::delete('/menu-kantin/{canteenMenu}', [CanteenMenuController::class, 'destroy'])->name('admin.canteen-menu.destroy');
            Route::post('/menu-kantin/{canteenMenu}/toggle', [CanteenMenuController::class, 'toggleActive'])->name('admin.canteen-menu.toggle');
            Route::post('/menu-kantin/update-order', [CanteenMenuController::class, 'updateOrder'])->name('admin.canteen-menu.updateOrder');
        });

        // Arsip & Barang Management - Officer, Sysadmin & Data Clerk
        Route::middleware('role:officer,sysadmin,data_clerk')->group(function () {
            // Arsip
            Route::get('/arsip', [\App\Http\Controllers\Admin\ArsipController::class, 'index'])->name('admin.arsip');
            Route::post('/arsip', [\App\Http\Controllers\Admin\ArsipController::class, 'store'])->name('admin.arsip.store');
            Route::put('/arsip/{arsip}', [\App\Http\Controllers\Admin\ArsipController::class, 'update'])->name('admin.arsip.update');
            Route::delete('/arsip/{arsip}', [\App\Http\Controllers\Admin\ArsipController::class, 'destroy'])->name('admin.arsip.destroy');
            Route::get('/arsip/{arsip}/usage', [\App\Http\Controllers\Admin\ArsipController::class, 'usage'])->name('admin.arsip.usage');

            // Opname Stock & Barang
            Route::get('/opname-stock', [TokoController::class, 'index'])->name('admin.opname.index');
            Route::get('/opname-stock/tenant/{supplier}/kuitansi', [TokoController::class, 'printKuitansi'])->name('admin.opname.tenant-kuitansi');
            Route::get('/opname-stock/{workUnit}', [StockOpnameController::class, 'index'])->name('admin.stock-opname.index');
            Route::get('/opname-stock/{workUnit}/stock-opname/barang/{barang}/history', [StockOpnameController::class, 'getHistory'])->name('admin.stock-opname.history');
            Route::post('/opname-stock/{workUnit}/stock-opname', [StockOpnameController::class, 'store'])->name('admin.stock-opname.store');
            Route::post('/opname-stock/{workUnit}/stock-opname/import', [StockOpnameController::class, 'import'])->name('admin.stock-opname.import');
            Route::post('/opname-stock/{workUnit}/stock-opname/auto-import', [StockOpnameController::class, 'autoImport'])->name('admin.stock-opname.auto-import');
            Route::put('/opname-stock/{workUnit}/stock-opname/{stockOpname}', [StockOpnameController::class, 'update'])->name('admin.stock-opname.update');
            Route::delete('/opname-stock/{workUnit}/stock-opname/{stockOpname}', [StockOpnameController::class, 'destroy'])->name('admin.stock-opname.destroy');
            Route::get('/opname-stock/{workUnit}/stock-opname/summary', [StockOpnameController::class, 'getSummary'])->name('admin.stock-opname.summary');
            Route::get('/opname-stock/{workUnit}/stock-opname/download-pdf', [StockOpnameController::class, 'downloadPdf'])->name('admin.stock-opname.download-pdf');
            Route::get('/opname-stock/{workUnit}/stock-opname/export-csv', [StockOpnameController::class, 'exportCsv'])->name('admin.stock-opname.export-csv');
            Route::get('/opname-stock/{workUnit}/barang', [BarangController::class, 'index'])->name('admin.barang.index');
            Route::post('/opname-stock/{workUnit}/barang', [BarangController::class, 'store'])->name('admin.barang.store');
            Route::put('/opname-stock/{workUnit}/barang/{barang}', [BarangController::class, 'update'])->name('admin.barang.update');
            Route::delete('/opname-stock/{workUnit}/barang/{barang}', [BarangController::class, 'destroy'])->name('admin.barang.destroy');
            Route::get('/opname-stock/{workUnit}/generate-plu', [BarangController::class, 'generatePLU'])->name('admin.barang.generate-plu');
            Route::get('/opname-stock/{workUnit}/barang/list', [BarangController::class, 'listBarangs'])->name('admin.barang.list');
            Route::post('/opname-stock/{workUnit}/barang/import', [BarangController::class, 'importBarangs'])->name('admin.barang.import');
            Route::post('/opname-stock/{workUnit}/barang/import-csv', [BarangController::class, 'importBarangsFromCsv'])->name('admin.barang.import-csv');
            Route::post('/opname-stock/{workUnit}/barang/print-price-tags', [BarangController::class, 'printPriceTags'])->name('admin.barang.print-price-tags');
            Route::post('/opname-stock/{workUnit}/barang/upload-image', [BarangController::class, 'uploadImage'])->name('admin.barang.upload-image');
            Route::post('/opname-stock/{workUnit}/barang/delete-image', [BarangController::class, 'deleteImage'])->name('admin.barang.delete-image');
            Route::get('/opname-stock/{workUnit}/barang/search-komponen', [BarangController::class, 'searchKomponen'])->name('admin.barang.search-komponen');
            Route::post('/opname-stock/{workUnit}/varian/{varian}/komponens', [BarangController::class, 'saveVarianKomponens'])->name('admin.varian.komponens.save');

            // Pengajuan barang - officer/sysadmin bisa proses & lihat
            Route::get('/opname-stock/{workUnit}/barang/{barang}/pengajuan', [PengajuanBarangController::class, 'forBarang'])->name('admin.pengajuan-barang.for-barang');
            Route::post('/opname-stock/{workUnit}/barang/{barang}/pengajuan/{pengajuan}/proses', [PengajuanBarangController::class, 'proses'])->name('admin.pengajuan-barang.proses');
            Route::post('/opname-stock/{workUnit}/barang/pengajuan/bulk-approve', [PengajuanBarangController::class, 'bulkApprove'])->name('admin.pengajuan-barang.bulk-approve');
            // Pengajuan tambah barang
            Route::get('/opname-stock/{workUnit}/pengajuan-tambah', [PengajuanTambahBarangController::class, 'index'])->name('admin.pengajuan-tambah.index');
            Route::post('/opname-stock/{workUnit}/pengajuan-tambah/{pengajuan}/proses', [PengajuanTambahBarangController::class, 'proses'])->name('admin.pengajuan-tambah.proses');
        });

        // Diskon & Voucher - Sysadmin & Officer only
        Route::middleware('role:sysadmin,officer')->group(function () {
            Route::get('/diskon', [PotonganController::class, 'index'])->name('admin.diskon.index');
            Route::post('/diskon', [PotonganController::class, 'store'])->name('admin.diskon.store');
            // Static routes harus sebelum wildcard {potongan}
            Route::get('/diskon/export-voucher', [PotonganController::class, 'exportVoucher'])->name('admin.diskon.export-voucher');
            Route::post('/diskon/import-csv', [PotonganController::class, 'importCsv'])->name('admin.diskon.import-csv');
            Route::get('/diskon/redeem-poin', [RedeemPoinController::class, 'index'])->name('admin.redeem-poin.index');
            Route::get('/diskon/redeem-poin/export', [RedeemPoinController::class, 'export'])->name('admin.redeem-poin.export');
            Route::post('/diskon/redeem-poin/catat-buku-kas', [RedeemPoinController::class, 'catatKeBukuKas'])->name('admin.redeem-poin.catat');
            Route::post('/diskon/redeem-poin/catat-campaign', [RedeemPoinController::class, 'catatCampaignKeBukuKas'])->name('admin.redeem-poin.catat-campaign');
            Route::post('/diskon/redeem-poin/hapus-catatan', [RedeemPoinController::class, 'hapusCatatan'])->name('admin.redeem-poin.hapus-catatan');

            Route::put('/diskon/{potongan}', [PotonganController::class, 'update'])->name('admin.diskon.update');
            Route::delete('/diskon/{potongan}', [PotonganController::class, 'destroy'])->name('admin.diskon.destroy');

            Route::get('/diskon/{potongan}/vouchers', [VoucherController::class, 'index'])->name('admin.voucher.index');
            Route::post('/diskon/{potongan}/vouchers/generate', [VoucherController::class, 'generate'])->name('admin.voucher.generate');
            Route::post('/diskon/{potongan}/vouchers/print', [VoucherController::class, 'print'])->name('admin.voucher.print');
            Route::post('/diskon/{potongan}/vouchers/print-all', [VoucherController::class, 'printAll'])->name('admin.voucher.print-all');
            Route::post('/diskon/{potongan}/vouchers/print-pdf', [VoucherController::class, 'printPdf'])->name('admin.voucher.print-pdf');
            Route::post('/diskon/{potongan}/vouchers/{voucher}/assign', [VoucherController::class, 'assign'])->name('admin.voucher.assign');
            Route::post('/diskon/{potongan}/vouchers/assign-bulk', [VoucherController::class, 'assignBulk'])->name('admin.voucher.assign-bulk');
            Route::delete('/diskon/{potongan}/vouchers/{voucher}/unassign', [VoucherController::class, 'unassign'])->name('admin.voucher.unassign');
            Route::delete('/diskon/{potongan}/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('admin.voucher.destroy');
        });

        // Check voucher (bisa diakses pos user juga)
        Route::middleware('role:sysadmin,officer,canteen,shop')->group(function () {
            Route::post('/diskon/check-voucher', [VoucherController::class, 'checkVoucher'])->name('admin.voucher.check');
            Route::get('/diskon/member-vouchers', [VoucherController::class, 'memberVouchers'])->name('admin.voucher.member-vouchers');
        });

        // Buku Kas Recycle Bin - Sysadmin only
        Route::middleware('role:sysadmin')->group(function () {
            Route::get('/buku-kas/recycle-bin/list', [BukuKasController::class, 'recycleBin'])->name('admin.bukukas.recycle-bin');
            Route::put('/buku-kas/{id}/restore', [BukuKasController::class, 'restore'])->name('admin.bukukas.restore');
            Route::delete('/buku-kas/{id}/permanent-delete', [BukuKasController::class, 'permanentDelete'])->name('admin.bukukas.permanent-delete');

            Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('admin.activity-log.index');
        });

        // Buku Kas Read Access - Officer, Sysadmin & Head
        Route::middleware('role:officer,sysadmin,head')->group(function () {
            Route::get('/buku-kas', [BukuKasController::class, 'index'])->name('admin.bukukas.index');
            Route::get('/buku-kas/export/csv', [BukuKasController::class, 'exportBukuKasCsv'])->name('admin.bukukas.export.csv');
            Route::get('/buku-kas/export/xlsx', [BukuKasController::class, 'exportBukuKasXlsx'])->name('admin.bukukas.export.xlsx');
            Route::get('/buku-kas/{id}', [BukuKasController::class, 'show'])->name('admin.bukukas.show');
            Route::get('/buku-kas/{id}/export/csv', [BukuKasController::class, 'exportTransaksiCsv'])->name('admin.bukukas.transaksi.export.csv');
            Route::get('/buku-kas/{id}/export/xlsx', [BukuKasController::class, 'exportTransaksiXlsx'])->name('admin.bukukas.transaksi.export.xlsx');

            // Laporan Keuangan - Officer, Sysadmin & Head
            Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('admin.laporan');
            Route::get('/laporan/detail', [LaporanKeuanganController::class, 'detail'])->name('admin.laporan.detail');
            Route::get('/laporan/export-csv', [LaporanKeuanganController::class, 'exportCsv'])->name('admin.laporan.export-csv');
        });

        // Belanja - Officer, Shop, Sysadmin & Data Clerk
        Route::middleware('role:officer,shop,sysadmin,data_clerk')->group(function () {
            Route::get('/belanja', [BelanjaMenuController::class, 'index'])->name('admin.belanja.index');
            Route::get('/belanja/download', [BelanjaMenuController::class, 'downloadMenu'])->name('admin.belanja.download');
            Route::post('/belanja/{barang}/display', [BelanjaMenuController::class, 'updateDisplay'])->name('admin.belanja.updateDisplay');
            Route::put('/belanja/{barang}/barang', [BelanjaMenuController::class, 'updateBarang'])->name('admin.belanja.updateBarang');
            Route::post('/belanja/{barang}/toggle-availability', [BelanjaMenuController::class, 'toggleAvailability'])->name('admin.belanja.toggleAvailability');
        });

        // Pengajuan barang - Kantin bisa submit
        Route::middleware('role:canteen,officer,sysadmin')->group(function () {
            Route::post('/opname-stock/{workUnit}/barang/{barang}/pengajuan', [PengajuanBarangController::class, 'store'])->name('admin.pengajuan-barang.store');
            Route::post('/opname-stock/{workUnit}/pengajuan-tambah', [PengajuanTambahBarangController::class, 'store'])->name('admin.pengajuan-tambah.store');
        });

        // Upload/delete gambar barang - bisa diakses data_clerk juga (dari opname-stock)
        Route::middleware('role:officer,canteen,shop,sysadmin,data_clerk')->group(function () {
            Route::post('/penjualan/upload-image', [PoSController::class, 'uploadImage'])->name('pos.uploadImage');
            Route::post('/penjualan/delete-image', [PoSController::class, 'deleteImage'])->name('pos.deleteImage');
        });

        // Point of Sale - Officer, Canteen, Shop & Sysadmin
        Route::middleware('role:officer,canteen,shop,sysadmin')->group(function () {
            Route::get('/pesanan-kantin', [SelfOrderController::class, 'pesananKantin'])->name('pos.pesanan-kantin');
            Route::get('/pesanan-toko', [BelanjaSelfOrderController::class, 'pesananToko'])->name('pos.pesanan-toko');
            Route::get('/penjualan', [PoSController::class, 'index'])->name('pos.index');
            Route::post('/penjualan', [PoSController::class, 'store'])->name('pos.store');
            Route::get('/penjualan/refresh-barangs', [PoSController::class, 'refreshBarangs'])->name('pos.refreshBarangs');
            Route::post('/penjualan/{id}/upload-foto-bukti', [PenjualanManagementController::class, 'uploadFotoBukti'])->name('pos.uploadFotoBukti');
            Route::delete('/penjualan/{id}/delete-foto-bukti', [PenjualanManagementController::class, 'deleteFotoBukti'])->name('pos.deleteFotoBukti');
            Route::get('/penjualan/history', [PenjualanManagementController::class, 'history'])->name('pos.history');
            Route::get('/penjualan/{id}', [PenjualanManagementController::class, 'show'])->name('pos.show');
            Route::post('/penjualan/{id}/cancel', [PenjualanManagementController::class, 'cancel'])->name('pos.cancel');

            // Buyer points endpoints
            Route::get('/penjualan/buyer/{buyerId}/points', [PoSController::class, 'getBuyerPoints'])->name('pos.buyer.points');
            Route::post('/penjualan/buyer/redeem-preview', [PoSController::class, 'redeemPreview'])->name('pos.buyer.redeemPreview');

            // Verification routes
            Route::post('/penjualan/{id}/verify', [PenjualanManagementController::class, 'verify'])->name('pos.verify');
            Route::post('/penjualan/{id}/unverify', [PenjualanManagementController::class, 'unverify'])->name('pos.unverify');
            Route::post('/penjualan/verify-bulk', [PenjualanManagementController::class, 'verifyBulk'])->name('pos.verifyBulk');
            Route::post('/penjualan/verify-all', [PenjualanManagementController::class, 'verifyAll'])->name('pos.verifyAll');
            Route::post('/penjualan/{id}/approve', [PenjualanManagementController::class, 'approve'])->name('pos.approve');
            Route::post('/penjualan/{id}/unapprove', [PenjualanManagementController::class, 'unapprove'])->name('pos.unapprove');
            Route::post('/penjualan/approve-bulk', [PenjualanManagementController::class, 'approveBulk'])->name('pos.approveBulk');
            Route::post('/penjualan/{id}/record', [PenjualanManagementController::class, 'recordToBukuKas'])->name('pos.record');
            Route::post('/penjualan/{id}/assign-member', [PenjualanManagementController::class, 'assignMember'])->name('pos.assignMember');
        });

        // Rekap Penjualan - Kantin, Shop, Officer, Sysadmin & Head
        Route::middleware('role:canteen,shop,officer,sysadmin,head')->group(function () {
            Route::get('/rekap-penjualan', [PenjualanReportController::class, 'index'])->name('admin.rekap-penjualan');
            Route::get('/rekap-penjualan/export-item-summary', [PenjualanReportController::class, 'exportItemSummary'])->name('pos.exportItemSummary');
            Route::get('/rekap-penjualan/download-pdf', [PenjualanReportController::class, 'downloadPdf'])->name('pos.downloadRekapPdf')->middleware('role:officer,sysadmin');
            Route::get('/buku-tagihan', [MitraReportController::class, 'bukuTagihan'])->name('pos.bukuTagihan');
            Route::get('/buku-tagihan/export', [MitraReportController::class, 'exportBukuTagihan'])->name('pos.exportBukuTagihan');
            Route::get('/buku-tagihan/download-pdf', [MitraReportController::class, 'downloadBukuTagihanPdf'])->name('pos.downloadBukuTagihanPdf');
            Route::get('/pembagian-mitra', [MitraReportController::class, 'pembagianMitra'])->name('pos.pembagianMitra');
            Route::get('/pembagian-mitra/export', [MitraReportController::class, 'exportPembagianMitra'])->name('pos.exportPembagianMitra');
            Route::post('/pembagian-mitra/simpan-riwayat', [MitraReportController::class, 'simpanRiwayatPembagian'])->name('pos.simpanRiwayatPembagian');
            Route::get('/pembagian-mitra/riwayat', [MitraReportController::class, 'riwayatPembagian'])->name('pos.riwayatPembagian');
            Route::delete('/pembagian-mitra/riwayat/{id}', [MitraReportController::class, 'hapusRiwayatPembagian'])->name('pos.hapusRiwayatPembagian');
            Route::post('/pembagian-mitra/riwayat/{id}/tandai-cair', [MitraReportController::class, 'tandaiCair'])->name('pos.tandaiCair');
            Route::post('/pembagian-mitra/riwayat/{id}/batalkan-cair', [MitraReportController::class, 'batalkanCair'])->name('pos.batalkanCair');
            Route::post('/pengajuan-pencairan/{id}/proses', [MitraReportController::class, 'prosesPengajuanPencairan'])->name('pos.pengajuanPencairan.proses');
            Route::post('/pengajuan-pencairan/{id}/selesai', [MitraReportController::class, 'selesaiPengajuanPencairan'])->name('pos.pengajuanPencairan.selesai');
            Route::post('/pengajuan-pencairan/{id}/tolak', [MitraReportController::class, 'tolakPengajuanPencairan'])->name('pos.pengajuanPencairan.tolak');
        });

        // Mitra Usaha - Sysadmin, Officer & Head
        Route::middleware('role:sysadmin,officer,head')->group(function () {
            Route::get('/mitra-usaha', [SupplierController::class, 'index'])->name('admin.mitra-usaha.index');
            Route::get('/mitra-usaha/download-pdf', [SupplierController::class, 'downloadPdf'])->name('admin.mitra-usaha.downloadPdf');
        });

        // Mitra Usaha Write Operations - Sysadmin & Officer only
        Route::middleware('role:sysadmin,officer')->group(function () {
            Route::post('/mitra-usaha', [SupplierController::class, 'store'])->name('admin.mitra-usaha.store');
            Route::put('/mitra-usaha/{supplier}', [SupplierController::class, 'update'])->name('admin.mitra-usaha.update');
            Route::delete('/mitra-usaha/{supplier}', [SupplierController::class, 'destroy'])->name('admin.mitra-usaha.destroy');
        });

        // Pages Management - Sysadmin & Officer
        Route::middleware('role:sysadmin,officer')->group(function () {
            Route::resource('halaman', AdminPageController::class)->names([
                'index' => 'admin.pages.index',
                'create' => 'admin.pages.create',
                'store' => 'admin.pages.store',
                'show' => 'admin.pages.show',
                'edit' => 'admin.pages.edit',
                'update' => 'admin.pages.update',
                'destroy' => 'admin.pages.destroy',
            ]);
            Route::post('/halaman/upload-content-image', [AdminPageController::class, 'uploadContentImage'])->name('admin.pages.uploadContentImage');

            // Menu Navbar Management - Sysadmin & Officer
            Route::get('/menu', [MenuItemController::class, 'index'])->name('admin.menu.index');
            Route::post('/menu', [MenuItemController::class, 'store'])->name('admin.menu.store');
            Route::put('/menu/{id}', [MenuItemController::class, 'update'])->name('admin.menu.update');
            Route::delete('/menu/{id}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
            Route::post('/menu/update-order', [MenuItemController::class, 'updateOrder'])->name('admin.menu.updateOrder');
        });

        // Meja & Lokasi Meja Management - Sysadmin & Officer
        Route::middleware('role:sysadmin,officer')->group(function () {
            Route::get('/meja', [\App\Http\Controllers\Admin\MejaController::class, 'index'])->name('admin.meja.index');
            Route::get('/meja/cetak-pdf', [\App\Http\Controllers\Admin\MejaController::class, 'cetakPdf'])->name('admin.meja.cetak-pdf');
            Route::get('/meja/data', [\App\Http\Controllers\Admin\MejaController::class, 'apiIndex'])->name('admin.meja.api-index');
            Route::post('/meja', [\App\Http\Controllers\Admin\MejaController::class, 'store'])->name('admin.meja.store');
            Route::put('/meja/{meja}', [\App\Http\Controllers\Admin\MejaController::class, 'update'])->name('admin.meja.update');
            Route::delete('/meja/{meja}', [\App\Http\Controllers\Admin\MejaController::class, 'destroy'])->name('admin.meja.destroy');
            Route::post('/meja/{meja}/toggle', [\App\Http\Controllers\Admin\MejaController::class, 'toggleStatus'])->name('admin.meja.toggle');
            Route::get('/meja/lokasi', [\App\Http\Controllers\Admin\LokasiMejaController::class, 'index'])->name('admin.lokasi-meja.index');
            Route::post('/meja/lokasi', [\App\Http\Controllers\Admin\LokasiMejaController::class, 'store'])->name('admin.lokasi-meja.store');
            Route::put('/meja/lokasi/{lokasiMeja}', [\App\Http\Controllers\Admin\LokasiMejaController::class, 'update'])->name('admin.lokasi-meja.update');
            Route::delete('/meja/lokasi/{lokasiMeja}', [\App\Http\Controllers\Admin\LokasiMejaController::class, 'destroy'])->name('admin.lokasi-meja.destroy');
            Route::post('/meja/lokasi/{lokasiMeja}/toggle', [\App\Http\Controllers\Admin\LokasiMejaController::class, 'toggleStatus'])->name('admin.lokasi-meja.toggle');
        });

        // Survey Results - Sysadmin only
        Route::middleware('role:sysadmin')->group(function () {
            Route::get('/survey', [SurveyResultController::class, 'index'])->name('admin.survey.index');
        });

        // User Management (Pengguna) - Sysadmin, Officer & Data Clerk (data_clerk hanya buyer)
        Route::middleware('role:sysadmin,officer,data_clerk')->group(function () {
            Route::get('/manajemen-pengguna', [UserManagementController::class, 'index'])->name('admin.users.index');
            Route::post('/manajemen-pengguna', [UserManagementController::class, 'store'])->name('admin.users.store');
            Route::put('/manajemen-pengguna/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
            Route::delete('/manajemen-pengguna/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
            Route::get('/member/search', [UserManagementController::class, 'searchMember'])->name('admin.users.search-member');

            // Daftar Unit - Sysadmin only
            Route::get('/unit-kerja', [WorkUnitController::class, 'index'])->name('admin.work-units.index');
            Route::post('/unit-kerja', [WorkUnitController::class, 'store'])->name('admin.work-units.store');
            Route::put('/unit-kerja/{workUnit}', [WorkUnitController::class, 'update'])->name('admin.work-units.update');
            Route::delete('/unit-kerja/{workUnit}', [WorkUnitController::class, 'destroy'])->name('admin.work-units.destroy');
            Route::post('/unit-kerja/{workUnit}/toggle', [WorkUnitController::class, 'toggleActive'])->name('admin.work-units.toggle');
            Route::post('/unit-kerja/update-order', [WorkUnitController::class, 'updateOrder'])->name('admin.work-units.updateOrder');

            // Unit Kerja Page (view) - Sysadmin only
            Route::get('/unit-kerja/{unitId}', [AdminController::class, 'unitKerja'])->name('admin.unit-kerja');

            // Kategori Transaksi Management
            Route::get('/kategori-transaksi', [KategoriTransaksiController::class, 'index'])->name('admin.kategori-transaksi.index');
            Route::post('/kategori-transaksi', [KategoriTransaksiController::class, 'store'])->name('admin.kategori-transaksi.store');
            Route::put('/kategori-transaksi/{kategori}', [KategoriTransaksiController::class, 'update'])->name('admin.kategori-transaksi.update');
            Route::delete('/kategori-transaksi/{kategori}', [KategoriTransaksiController::class, 'destroy'])->name('admin.kategori-transaksi.destroy');
            Route::post('/kategori-transaksi/{kategori}/toggle', [KategoriTransaksiController::class, 'toggleStatus'])->name('admin.kategori-transaksi.toggle');

            // Kategori Barang Management
            Route::get('/kategori-barang', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'index'])->name('admin.kategori-barang.index');
            Route::post('/kategori-barang', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'store'])->name('admin.kategori-barang.store');
            Route::put('/kategori-barang/{kategoriBarang}', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'update'])->name('admin.kategori-barang.update');
            Route::delete('/kategori-barang/{kategoriBarang}', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'destroy'])->name('admin.kategori-barang.destroy');
            Route::post('/kategori-barang/{kategoriBarang}/toggle', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'toggleStatus'])->name('admin.kategori-barang.toggle');
            Route::post('/kategori-barang/reorder', [\App\Http\Controllers\Admin\KategoriBarangController::class, 'reorder'])->name('admin.kategori-barang.reorder');


            // Sub Kategori Menu Management
            Route::get('/setting/sub-kategori-menu', [\App\Http\Controllers\Admin\SubKategoriMenuController::class, 'index'])->name('admin.sub-kategori-menu.index');
            Route::post('/setting/sub-kategori-menu', [\App\Http\Controllers\Admin\SubKategoriMenuController::class, 'store'])->name('admin.sub-kategori-menu.store');
            Route::post('/setting/sub-kategori-menu/reorder', [\App\Http\Controllers\Admin\SubKategoriMenuController::class, 'reorder'])->name('admin.sub-kategori-menu.reorder');
            Route::put('/setting/sub-kategori-menu/{id}', [\App\Http\Controllers\Admin\SubKategoriMenuController::class, 'update'])->name('admin.sub-kategori-menu.update');
            Route::delete('/setting/sub-kategori-menu/{id}', [\App\Http\Controllers\Admin\SubKategoriMenuController::class, 'destroy'])->name('admin.sub-kategori-menu.destroy');

            Route::get('/setting/sub-kategori-toko', [\App\Http\Controllers\Admin\SubKategoriTokoController::class, 'index'])->name('admin.sub-kategori-toko.index');
            Route::post('/setting/sub-kategori-toko', [\App\Http\Controllers\Admin\SubKategoriTokoController::class, 'store'])->name('admin.sub-kategori-toko.store');
            Route::post('/setting/sub-kategori-toko/reorder', [\App\Http\Controllers\Admin\SubKategoriTokoController::class, 'reorder'])->name('admin.sub-kategori-toko.reorder');
            Route::put('/setting/sub-kategori-toko/{id}', [\App\Http\Controllers\Admin\SubKategoriTokoController::class, 'update'])->name('admin.sub-kategori-toko.update');
            Route::delete('/setting/sub-kategori-toko/{id}', [\App\Http\Controllers\Admin\SubKategoriTokoController::class, 'destroy'])->name('admin.sub-kategori-toko.destroy');
        });

        // Settings - accessible only for non-canteen users
        Route::middleware('role:sysadmin,officer,head,shop')->group(function () {
            Route::get('/setting', [AdminController::class, 'setting'])->name('admin.setting');
            Route::post('/settings/report', [AdminController::class, 'saveReportSettings'])->name('admin.settings.report');
            Route::post('/settings/points', [AdminController::class, 'savePointsSettings'])->name('admin.settings.points');
            Route::post('/settings/selforder', [AdminController::class, 'saveSelfOrderSettings'])->name('admin.settings.selforder');
        });

        Route::middleware('role:sysadmin')->group(function () {
            Route::get('/settings/point-rules', [\App\Http\Controllers\Admin\PointRuleController::class, 'index'])->name('admin.point-rules.index');
            Route::post('/settings/point-rules', [\App\Http\Controllers\Admin\PointRuleController::class, 'store'])->name('admin.point-rules.store');
            Route::put('/settings/point-rules/{pointRule}', [\App\Http\Controllers\Admin\PointRuleController::class, 'update'])->name('admin.point-rules.update');
            Route::delete('/settings/point-rules/{pointRule}', [\App\Http\Controllers\Admin\PointRuleController::class, 'destroy'])->name('admin.point-rules.destroy');
            Route::post('/settings/point-rules/{pointRule}/toggle', [\App\Http\Controllers\Admin\PointRuleController::class, 'toggleStatus'])->name('admin.point-rules.toggle');
        });

        Route::post('/update-password', [AdminAuthController::class, 'updatePassword'])->name('admin.updatePassword');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

// ===== MITRA PORTAL =====
Route::prefix('mitra')->name('mitra.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [MitraAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [MitraAuthController::class, 'login'])->name('login.post');
    });

    // Authenticated mitra routes
    Route::middleware('mitra')->group(function () {
        Route::get('/dasbor', [MitraDashboardController::class, 'index'])->name('dasbor');
        Route::get('/riwayat-pencairan', [MitraDashboardController::class, 'riwayatPencairan'])->name('riwayatPencairan');
        Route::post('/riwayat-pencairan/{id}/ajukan', [MitraDashboardController::class, 'ajukanPencairan'])->name('ajukanPencairan');
        Route::post('/riwayat-pencairan/{id}/batal-ajuan', [MitraDashboardController::class, 'batalkanAjuan'])->name('batalkanAjuan');
        Route::post('/ajukan-pencairan', [MitraDashboardController::class, 'ajukanPencairanBaru'])->name('ajukanPencairanBaru');
        Route::delete('/ajukan-pencairan/{id}', [MitraDashboardController::class, 'batalkanPengajuanBaru'])->name('batalkanPengajuanBaru');
        Route::post('/profil/update', [MitraDashboardController::class, 'updateProfil'])->name('profil.update');
        Route::post('/profil/ganti-password', [MitraDashboardController::class, 'gantiPassword'])->name('profil.gantiPassword');
        Route::post('/logout', [MitraAuthController::class, 'logout'])->name('logout');
    });
});

// Public Routes - Pages (must be at the end to avoid conflicts)
Route::get('/{category}/{slug}', [PageController::class, 'showByCategory'])->name('pages.show.category');
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
