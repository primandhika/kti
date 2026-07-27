<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WorkUnit;
use App\Models\User;
use App\Models\BukuKas;
use App\Models\TransaksiKas;
use App\Models\Post;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\KategoriTransaksi;
use App\Models\SubKategoriMenu;
use App\Models\SubKategoriToko;
use App\Models\Setting;
use App\Models\Penjualan;
use App\Models\Arsip;
use App\Services\Dashboard\ExecutiveDashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Dashboard beranda - renders different dashboard based on user role
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Route to appropriate dashboard based on role
        // head adalah role pimpinan, menggunakan dashboard eksekutif
        if ($user->hasRole('head')) {
            return $this->executiveDashboard();
        }

        if ($user->hasRole('sysadmin')) {
            $stats = $this->getStatistics();
            return Inertia::render('Admin/Dashboard/SysadminDashboard', $stats);
        }

        if ($user->hasRole('canteen')) {
            $stats = $this->getStatistics();
            return Inertia::render('Admin/Dashboard/CanteenDashboard', $stats);
        }

        if ($user->hasRole('shop')) {
            $stats = $this->getStatistics();
            return Inertia::render('Admin/Dashboard/ShopDashboard', $stats);
        }

        // Default to officer dashboard (Beranda)
        $stats = $this->getStatistics();
        return Inertia::render('Admin/Dashboard/Beranda', $stats);
    }

    /**
     * Executive Dashboard for head role (pimpinan)
     */
    private function executiveDashboard(?Request $request = null)
    {
        $request = $request ?? request();

        // Get filter parameters
        $period = $request->get('period', 'current_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $service = new ExecutiveDashboardService();
        $stats = $service->getExecutiveStatistics($period, $startDate, $endDate);
        $categoryPerformance = $service->getCategoryPerformance($period, $startDate, $endDate);

        return Inertia::render('Admin/Dashboard/HeadDashboard', [
            'overview' => $stats['overview'],
            'financial' => $stats['financial'],
            'workUnits' => $stats['workUnits'],
            'sales' => $stats['sales'],
            'inventory' => $stats['inventory'],
            'trends' => $stats['trends'],
            'categoryPerformance' => $categoryPerformance,
            'filters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Export Executive Dashboard to CSV
     */
    public function exportExecutiveDashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('head')) {
            abort(403, 'Unauthorized');
        }

        $period = $request->get('period', 'current_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $service = new ExecutiveDashboardService();
        $stats = $service->getExecutiveStatistics($period, $startDate, $endDate);

        $filename = 'dashboard-eksekutif-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Financial Summary
            fputcsv($file, ['RINGKASAN KEUANGAN']);
            fputcsv($file, ['Total Kas Masuk', number_format($stats['financial']['total_income'], 0, ',', '.')]);
            fputcsv($file, ['Total Kas Keluar', number_format($stats['financial']['total_expenses'], 0, ',', '.')]);
            fputcsv($file, ['Saldo Bersih', number_format($stats['financial']['net_balance'], 0, ',', '.')]);
            fputcsv($file, []);

            // Work Units Performance
            fputcsv($file, ['PERFORMA UNIT KERJA']);
            fputcsv($file, ['Unit ID', 'Nama Unit', 'Pendapatan', 'Transaksi', 'Kas Masuk', 'Kas Keluar', 'Kas Bersih']);
            foreach ($stats['workUnits']['units'] as $unit) {
                fputcsv($file, [
                    $unit['unit_id'],
                    $unit['name'],
                    number_format($unit['revenue'], 0, ',', '.'),
                    $unit['transaction_count'],
                    number_format($unit['cash_income'], 0, ',', '.'),
                    number_format($unit['cash_expenses'], 0, ',', '.'),
                    number_format($unit['net_cash'], 0, ',', '.'),
                ]);
            }
            fputcsv($file, []);

            // Top Items
            fputcsv($file, ['TOP 10 ITEM TERLARIS']);
            fputcsv($file, ['Nama Barang', 'Qty Terjual', 'Total Revenue', 'Jumlah Transaksi']);
            foreach ($stats['sales']['top_items'] as $item) {
                fputcsv($file, [
                    $item->nama_barang,
                    $item->total_qty,
                    number_format($item->total_revenue, 0, ',', '.'),
                    $item->transaction_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics()
    {
        // Total counts
        $totalUsers = User::count();
        $totalWorkUnits = WorkUnit::count();
        $activeWorkUnits = WorkUnit::where('is_active', true)->count();
        $totalBukuKas = BukuKas::count();
        $totalPosts = Post::where('status', 'published')->count();

        // Financial statistics
        $totalIncome = TransaksiKas::sum('pemasukan');
        $totalExpenses = TransaksiKas::sum('pengeluaran');

        // Monthly cash flow (last 3 months)
        $currentMonth = Carbon::now();
        $monthlyIncome = [];

        for ($i = 0; $i < 3; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthName = $month->locale('id')->translatedFormat('F');
            $income = TransaksiKas::whereYear('tanggal', $month->year)
                ->whereMonth('tanggal', $month->month)
                ->sum('pemasukan');
            $transactionCount = TransaksiKas::whereYear('tanggal', $month->year)
                ->whereMonth('tanggal', $month->month)
                ->where('pemasukan', '>', 0)
                ->count();

            $monthlyIncome[] = [
                'month' => $monthName,
                'income' => $income,
                'transactionCount' => $transactionCount
            ];
        }

        // Role distribution
        $roleDistribution = [
            'officer' => User::role('officer')->count(),
            'shop' => User::role('shop')->count(),
            'canteen' => User::role('canteen')->count(),
            'head' => User::role('head')->count(),
            'sysadmin' => User::role('sysadmin')->count(),
        ];

        // Active sessions dari tabel sessions
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(30)->timestamp)
            ->count();

        // Postingan terbaru (real data)
        $recentPosts = Post::where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id', 'title', 'created_at']);

        // Transaksi terbaru hari ini
        $transaksHariIni = Penjualan::whereDate('tanggal_transaksi', today())
            ->where('status', '!=', 'dibatalkan')
            ->count();

        $omzetHariIni = Penjualan::whereDate('tanggal_transaksi', today())
            ->where('status', '!=', 'dibatalkan')
            ->sum('total');

        // Total arsip
        $totalArsip = DB::table('arsip')->count();

        return [
            'totalUsers' => $totalUsers,
            'totalWorkUnits' => $totalWorkUnits,
            'activeWorkUnits' => $activeWorkUnits,
            'totalBukuKas' => $totalBukuKas,
            'totalPosts' => $totalPosts,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'monthlyIncome' => $monthlyIncome,
            'roleDistribution' => $roleDistribution,
            'activeSessions' => $activeSessions,
            'recentPosts' => $recentPosts,
            'transaksHariIni' => $transaksHariIni,
            'omzetHariIni' => $omzetHariIni,
            'totalArsip' => $totalArsip,
        ];
    }

    /**
     * Halaman laporan
     */
    public function laporan()
    {
        return Inertia::render('Admin/Dashboard/Laporan');
    }

    /**
     * Halaman arsip
     */
    public function arsip()
    {
        return Inertia::render('Admin/Dashboard/Arsip');
    }

    /**
     * Halaman detail unit kerja
     */
    public function unitKerja($unitId)
    {
        $workUnit = WorkUnit::where('unit_id', $unitId)->firstOrFail();

        return Inertia::render('Admin/Dashboard/UnitKerja', [
            'workUnit' => $workUnit
        ]);
    }

    /**
     * Halaman setting
     */
    public function setting(Request $request)
    {
        $user = $request->user();
        $data = [];

        // Load work units for authorized roles
        if ($user->hasAnyRole(['sysadmin', 'head', 'officer'])) {
            $data['workUnits'] = WorkUnit::orderBy('unit_id')->get();
        }

        // Load categories for sysadmin only
        if ($user->hasRole('sysadmin')) {
            $data['kategoriBarangs'] = KategoriBarang::withCount('barangs')
                ->orderBy('nama')
                ->get();

            $data['kategoriKas'] = KategoriTransaksi::orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function($kat) {
                    return [
                        'id' => $kat->id,
                        'nama' => $kat->nama,
                        'tipe' => $kat->jenis, // map jenis to tipe for frontend consistency
                        'kode' => $kat->kode_akun,
                        'deskripsi' => $kat->deskripsi,
                        'is_active' => $kat->is_active,
                    ];
                });

            // Load sub kategori menu
            $data['subKategoris'] = SubKategoriMenu::orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_menu' => $item->jumlah_menu,
                    ];
                });

            // Load sub kategori toko
            $data['subKategorisToko'] = SubKategoriToko::orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_barang' => $item->jumlah_barang,
                    ];
                });
        }

        // Load report settings for officer and sysadmin
        if ($user->hasAnyRole(['sysadmin', 'officer'])) {
            $kepalaBppuPeriods = Setting::getKepalaBppuPeriods();
            $legacyKepalaBppuNama = Setting::get('kepala_bppu_nama', '');

            if (empty($kepalaBppuPeriods) && $legacyKepalaBppuNama !== '') {
                $kepalaBppuPeriods[] = [
                    'nama' => $legacyKepalaBppuNama,
                    'nip' => Setting::get('kepala_bppu_nip', ''),
                    'periode_mulai' => '',
                    'periode_selesai' => '',
                ];
            }

            $data['reportSettings'] = [
                'kepala_bppu_nama' => $legacyKepalaBppuNama,
                'kepala_bppu_nip' => Setting::get('kepala_bppu_nip', ''),
                'kepala_bppu_periods' => $kepalaBppuPeriods,
            ];
        }

        // Load points settings for sysadmin only
        if ($user->hasRole('sysadmin')) {
            $data['pointsSettings'] = [
                'minimal_poin_redeem' => (int) Setting::get('minimal_poin_redeem', 5000),
                'kurs_poin_ke_rupiah' => (int) Setting::get('kurs_poin_ke_rupiah', 2),
            ];
            $data['pointRules'] = \App\Models\PointRule::orderBy('order')->get();

            $data['selfOrderSettings'] = [
                'kantin_minimal_order'        => (int) Setting::get('kantin_minimal_order', 0),
                'belanja_minimal_order'       => (int) Setting::get('belanja_minimal_order', 20000),
                'kantin_biaya_layanan_aktif'  => (bool) (int) Setting::get('kantin_biaya_layanan_aktif', 0),
                'kantin_biaya_layanan'        => (int) Setting::get('kantin_biaya_layanan', 0),
                'belanja_biaya_layanan_aktif' => (bool) (int) Setting::get('belanja_biaya_layanan_aktif', 0),
                'belanja_biaya_layanan'       => (int) Setting::get('belanja_biaya_layanan', 0),
                'kantin_verif_lokasi_aktif'   => (bool) (int) Setting::get('kantin_verif_lokasi_aktif', 0),
            ];
        }

        return Inertia::render('Admin/Dashboard/Setting', $data);
    }

    /**
     * Save report settings
     */
    public function saveReportSettings(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAnyRole(['sysadmin', 'officer'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'kepala_bppu_periods' => 'required|array|min:1',
            'kepala_bppu_periods.*.nama' => 'required|string|max:255',
            'kepala_bppu_periods.*.nip' => 'nullable|string|max:50',
            'kepala_bppu_periods.*.periode_mulai' => 'required|date',
            'kepala_bppu_periods.*.periode_selesai' => 'required|date',
        ]);

        $periods = collect($validated['kepala_bppu_periods'])
            ->map(fn ($period) => [
                'nama' => trim($period['nama']),
                'nip' => trim($period['nip'] ?? ''),
                'periode_mulai' => Carbon::parse($period['periode_mulai'])->toDateString(),
                'periode_selesai' => Carbon::parse($period['periode_selesai'])->toDateString(),
            ])
            ->sortBy('periode_mulai')
            ->values()
            ->all();

        $previousEnd = null;
        foreach ($periods as $index => $period) {
            $periodStart = Carbon::parse($period['periode_mulai']);
            $periodEnd = Carbon::parse($period['periode_selesai']);

            if ($periodEnd->lt($periodStart)) {
                throw ValidationException::withMessages([
                    "kepala_bppu_periods.$index.periode_selesai" => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                ]);
            }

            if ($previousEnd && $periodStart->lessThanOrEqualTo($previousEnd)) {
                throw ValidationException::withMessages([
                    "kepala_bppu_periods.$index.periode_mulai" => 'Periode kepala BPPU tidak boleh saling tumpang tindih.',
                ]);
            }

            $previousEnd = $periodEnd;
        }

        Setting::set('kepala_bppu_periods', json_encode($periods));

        $latestPeriod = collect($periods)->sortByDesc('periode_mulai')->first();
        Setting::set('kepala_bppu_nama', $latestPeriod['nama'] ?? '');
        Setting::set('kepala_bppu_nip', $latestPeriod['nip'] ?? '');

        return response()->json([
            'message' => 'Pengaturan laporan berhasil disimpan'
        ]);
    }

    /**
     * Save points settings
     */
    public function savePointsSettings(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('sysadmin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'minimal_poin_redeem' => 'required|integer|min:0',
            'kurs_poin_ke_rupiah' => 'required|integer|min:1',
        ]);

        Setting::set('minimal_poin_redeem', $validated['minimal_poin_redeem']);
        Setting::set('kurs_poin_ke_rupiah', $validated['kurs_poin_ke_rupiah']);

        return response()->json([
            'message' => 'Pengaturan poin berhasil disimpan'
        ]);
    }

    /**
     * Save self-order settings (minimal order & biaya layanan)
     */
    public function saveSelfOrderSettings(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('sysadmin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'kantin_minimal_order'        => 'required|integer|min:0',
            'belanja_minimal_order'       => 'required|integer|min:0',
            'kantin_biaya_layanan_aktif'  => 'required|boolean',
            'kantin_biaya_layanan'        => 'required|integer|min:0',
            'belanja_biaya_layanan_aktif' => 'required|boolean',
            'belanja_biaya_layanan'       => 'required|integer|min:0',
            'kantin_verif_lokasi_aktif'   => 'required|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, (int) $value);
        }

        return response()->json([
            'message' => 'Pengaturan self-order berhasil disimpan'
        ]);
    }

    /**
     * Laporan Penjualan - untuk Officer & Sysadmin
     * Menampilkan laporan penjualan dari semua unit kerja
     */
    public function laporanPenjualan(Request $request)
    {
        $user = auth()->user();

        // Only sysadmin and officer can access
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            abort(403, 'Unauthorized access');
        }

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get selected work unit dan date range
        $selectedWorkUnitId = $request->get('work_unit_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Build query for item summary TANPA detail stock reduction (untuk performa)
        $itemSummaryQuery = \App\Models\PenjualanItem::with(['barang'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin')) {
                    $q->whereIn('work_unit_id', $workUnits->pluck('id'));
                }
            })
            ->get()
            ->groupBy(function ($item) {
                // Group by barang_id untuk menghindari duplikasi nama
                return $item->barang_id;
            })
            ->map(function ($items, $barangId) {
                $firstItem = $items->first();
                $totalQty = $items->sum('qty');
                $totalHarga = $items->sum('subtotal'); // BRUTO (Total Penjualan)
                $totalHpp = $items->sum('total_hpp'); // Total Modal/HPP
                $totalMargin = $items->sum('total_margin'); // UNTUNG KOTOR

                return [
                    'barang_id' => $barangId,
                    'kode_barang' => $firstItem->barang->kode_barang ?? '-',
                    'nama_barang' => $firstItem->nama_barang,
                    'satuan' => $firstItem->satuan,
                    'total_qty' => $totalQty,
                    'penjualan_bruto' => $totalHarga, // Renamed untuk clarity
                    'total_hpp' => $totalHpp,
                    'untung_kotor' => $totalMargin, // Renamed untuk clarity
                    'margin_persen' => $totalHarga > 0 ? ($totalMargin / $totalHarga) * 100 : 0,
                    'jumlah_transaksi' => $items->count(), // Tambah jumlah transaksi
                ];
            })
            ->sortByDesc('penjualan_bruto')
            ->values();

        // Calculate summary
        $summary = [
            'total_items' => $itemSummaryQuery->count(),
            'total_qty' => $itemSummaryQuery->sum('total_qty'),
            'penjualan_bruto' => $itemSummaryQuery->sum('penjualan_bruto'),
            'total_hpp' => $itemSummaryQuery->sum('total_hpp'),
            'untung_kotor' => $itemSummaryQuery->sum('untung_kotor'),
        ];

        return Inertia::render('Admin/Dashboard/LaporanPenjualan', [
            'workUnits' => $workUnits,
            'selectedWorkUnitId' => $selectedWorkUnitId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemSummary' => $itemSummaryQuery,
            'summary' => $summary,
        ]);
    }

    /**
     * Get detail transaksi untuk barang tertentu (AJAX endpoint untuk modal)
     */
    public function getDetailTransaksiBarang(Request $request)
    {
        $user = auth()->user();

        // Only sysadmin and officer can access
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'barang_id' => 'required|exists:barangs,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'work_unit_id' => 'nullable|exists:work_units,id',
                'page' => 'nullable|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ', $e->errors());
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        }

        $barangId = $validated['barang_id'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $workUnitId = $validated['work_unit_id'] ?? null;
        $page = $validated['page'] ?? 1;
        $perPage = 10;

        \Log::info('Detail Transaksi Request:', [
            'barang_id' => $barangId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'work_unit_id' => $workUnitId,
            'page' => $page
        ]);

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin')) {
            $workUnits = WorkUnit::where('is_active', true)->pluck('id');
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->pluck('work_units.id');
        }

        // Query detail transaksi
        $query = \App\Models\PenjualanItem::with(['penjualan.workUnit', 'varian'])
            ->where('barang_id', $barangId)
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $workUnitId, $workUnits, $user) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($workUnitId) {
                    $q->where('work_unit_id', $workUnitId);
                } elseif (!$user->hasRole('sysadmin')) {
                    $q->whereIn('work_unit_id', $workUnits);
                }
            });

        // Get total count (before pagination)
        $total = $query->count();

        \Log::info('Query count: ' . $total);

        // Get paginated results
        $items = $query->orderByDesc('id')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($item) {
                return [
                    'nomor_transaksi' => $item->penjualan->nomor_transaksi,
                    'tanggal_transaksi' => $item->penjualan->tanggal_transaksi,
                    'work_unit' => $item->penjualan->workUnit->name ?? '-',
                    'varian' => $item->varian ? $item->varian->nama_varian : '-',
                    'qty' => $item->qty,
                    'satuan' => $item->satuan,
                    'harga_satuan' => $item->harga_satuan,
                    'subtotal' => $item->subtotal,
                    'hpp_satuan' => $item->hpp_satuan,
                    'total_hpp' => $item->total_hpp,
                    'margin' => $item->total_margin,
                ];
            });

        \Log::info('Items found: ' . $items->count());

        $response = [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'debug' => [
                'barang_id' => $barangId,
                'query_count' => $total,
                'items_count' => $items->count(),
            ]
        ];

        \Log::info('Returning response:', $response);

        return response()->json($response);
    }

    /**
     * Export Laporan Penjualan to CSV
     */
    public function exportLaporanPenjualan(Request $request)
    {
        $user = auth()->user();

        // Only sysadmin and officer can access
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'work_unit_id' => 'nullable|exists:work_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin')) {
            $workUnits = WorkUnit::where('is_active', true)->pluck('id');
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->pluck('work_units.id');
        }

        $selectedWorkUnitId = $validated['work_unit_id'] ?? null;
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Build query for item summary
        $itemSummary = \App\Models\PenjualanItem::with(['barang', 'varian', 'penjualan.workUnit'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin')) {
                    $q->whereIn('work_unit_id', $workUnits);
                }
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->barang_id;
            })
            ->map(function ($items, $barangId) {
                $firstItem = $items->first();
                $totalQty = $items->sum('qty');
                $totalHarga = $items->sum('subtotal');
                $totalHpp = $items->sum('total_hpp');
                $totalMargin = $items->sum('total_margin');

                return [
                    'kode_barang' => $firstItem->barang->kode_barang ?? '-',
                    'nama_barang' => $firstItem->nama_barang,
                    'satuan' => $firstItem->satuan,
                    'total_qty' => $totalQty,
                    'penjualan_bruto' => $totalHarga,
                    'total_hpp' => $totalHpp,
                    'untung_kotor' => $totalMargin,
                    'margin_persen' => $totalHarga > 0 ? ($totalMargin / $totalHarga) * 100 : 0,
                ];
            })
            ->sortByDesc('penjualan_bruto')
            ->values();

        // Generate CSV
        $filename = 'laporan-penjualan-' . $startDate . '-sd-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($itemSummary) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'Kode Barang',
                'Nama Barang',
                'Satuan',
                'Total Qty',
                'Penjualan Bruto (Rp)',
                'Total HPP (Rp)',
                'Untung Kotor (Rp)',
                'Margin (%)'
            ]);

            // Data
            foreach ($itemSummary as $item) {
                fputcsv($file, [
                    $item['kode_barang'],
                    $item['nama_barang'],
                    $item['satuan'],
                    $item['total_qty'],
                    number_format($item['penjualan_bruto'], 0, ',', '.'),
                    number_format($item['total_hpp'], 0, ',', '.'),
                    number_format($item['untung_kotor'], 0, ',', '.'),
                    number_format($item['margin_persen'], 2, ',', '.'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download PDF laporan penjualan per item per mitra sesuai filter laporan penjualan.
     */
    public function downloadLaporanPenjualanMitraPdf(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'work_unit_id' => 'nullable|exists:work_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $accessibleWorkUnitIds = $user->hasRole('sysadmin')
            ? WorkUnit::where('is_active', true)->pluck('id')
            : $user->workUnits()->where('is_active', true)->pluck('work_units.id');

        $selectedWorkUnitId = $validated['work_unit_id'] ?? null;
        if ($selectedWorkUnitId && !$accessibleWorkUnitIds->contains((int) $selectedWorkUnitId)) {
            abort(403, 'Unauthorized work unit');
        }

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $items = \App\Models\PenjualanItem::with(['barang.supplier', 'penjualan.workUnit'])
            ->whereHas('barang', function ($q) {
                $q->whereNotNull('supplier_id');
            })
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $accessibleWorkUnitIds, $user) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59',
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin')) {
                    $q->whereIn('work_unit_id', $accessibleWorkUnitIds);
                }
            })
            ->get();

        $mitraProduk = $items
            ->groupBy(fn ($item) => $item->barang?->supplier_id)
            ->map(function ($supplierItems) {
                $first = $supplierItems->first();
                $supplier = $first->barang?->supplier;

                $produk = $supplierItems
                    ->groupBy('barang_id')
                    ->map(function ($productItems) {
                        $firstProduct = $productItems->first();
                        $totalPenjualan = $productItems->sum('subtotal');
                        $totalMargin = $productItems->sum('total_margin');

                        return [
                            'kode_barang' => $firstProduct->barang?->kode_barang ?? '-',
                            'nama_barang' => $firstProduct->nama_barang,
                            'satuan' => $firstProduct->satuan,
                            'total_qty' => $productItems->sum('qty'),
                            'jumlah_transaksi' => $productItems->pluck('penjualan_id')->unique()->count(),
                            'penjualan_bruto' => $totalPenjualan,
                            'total_hpp' => $productItems->sum('total_hpp'),
                            'untung_kotor' => $totalMargin,
                            'margin_persen' => $totalPenjualan > 0 ? ($totalMargin / $totalPenjualan) * 100 : 0,
                        ];
                    })
                    ->sort(function ($a, $b) {
                        if ($a['total_qty'] !== $b['total_qty']) {
                            return $b['total_qty'] <=> $a['total_qty'];
                        }

                        if ($a['penjualan_bruto'] !== $b['penjualan_bruto']) {
                            return $b['penjualan_bruto'] <=> $a['penjualan_bruto'];
                        }

                        return strcmp($a['nama_barang'], $b['nama_barang']);
                    })
                    ->values();

                return [
                    'supplier_id' => $supplier?->id,
                    'kode_supplier' => $supplier?->kode_supplier ?? '-',
                    'nama' => $supplier?->nama ?? $supplier?->user?->name ?? 'Mitra tidak diketahui',
                    'tipe_mitra' => $supplier?->tipe_mitra ?? '-',
                    'total_produk' => $produk->count(),
                    'total_qty' => $produk->sum('total_qty'),
                    'jumlah_transaksi' => $supplierItems->pluck('penjualan_id')->unique()->count(),
                    'penjualan_bruto' => $produk->sum('penjualan_bruto'),
                    'total_hpp' => $produk->sum('total_hpp'),
                    'untung_kotor' => $produk->sum('untung_kotor'),
                    'produk' => $produk,
                ];
            })
            ->sort(function ($a, $b) {
                if ($a['total_qty'] !== $b['total_qty']) {
                    return $b['total_qty'] <=> $a['total_qty'];
                }

                if ($a['penjualan_bruto'] !== $b['penjualan_bruto']) {
                    return $b['penjualan_bruto'] <=> $a['penjualan_bruto'];
                }

                return strcmp($a['nama'], $b['nama']);
            })
            ->values();

        $allProduk = $mitraProduk->flatMap(function ($mitra) {
            return collect($mitra['produk'])->map(function ($produk) use ($mitra) {
                return array_merge($produk, [
                    'mitra_nama' => $mitra['nama'],
                    'kode_supplier' => $mitra['kode_supplier'],
                ]);
            });
        });

        $workUnit = $selectedWorkUnitId ? WorkUnit::find($selectedWorkUnitId) : null;
        $summary = [
            'total_mitra' => $mitraProduk->count(),
            'total_produk' => $allProduk->count(),
            'total_qty' => $mitraProduk->sum('total_qty'),
            'jumlah_transaksi' => $items->pluck('penjualan_id')->unique()->count(),
            'penjualan_bruto' => $mitraProduk->sum('penjualan_bruto'),
            'total_hpp' => $mitraProduk->sum('total_hpp'),
            'untung_kotor' => $mitraProduk->sum('untung_kotor'),
        ];

        $kepalaBppu = Setting::getKepalaBppuForPeriod($startDate, $endDate);

        $data = [
            'work_unit' => $workUnit ? $workUnit->name : 'Semua Unit Kerja',
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => $summary,
            'mitra_produk' => $mitraProduk,
            'kepala_bppu_nama' => $kepalaBppu['nama'],
            'kepala_bppu_nip' => $kepalaBppu['nip'],
        ];

        $pdf = Pdf::loadView('pdf.laporan-penjualan-mitra-produk', ['data' => $data])
            ->setPaper('a4', 'portrait');

        $filename = 'laporan-produk-penjualan-mitra-' . $startDate . '-sd-' . $endDate . '.pdf';

        return $pdf->stream($filename);
    }

}
