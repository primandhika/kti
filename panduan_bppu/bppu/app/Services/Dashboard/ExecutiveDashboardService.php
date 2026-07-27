<?php

namespace App\Services\Dashboard;

use App\Models\WorkUnit;
use App\Models\User;
use App\Models\BukuKas;
use App\Models\TransaksiKas;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Barang;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardService
{
    private $period;
    private $startDate;
    private $endDate;
    private $currentMonth;
    private $lastMonth;

    /**
     * Get comprehensive executive statistics
     */
    public function getExecutiveStatistics($period = 'current_month', $startDate = null, $endDate = null): array
    {
        $this->period = $period;
        $this->setDateRange($period, $startDate, $endDate);

        return [
            'overview' => $this->getOverviewStats(),
            'financial' => $this->getFinancialStats(),
            'workUnits' => $this->getWorkUnitsStats(),
            'sales' => $this->getSalesStats(),
            'inventory' => $this->getInventoryStats(),
            'trends' => $this->getTrendsData(),
        ];
    }

    /**
     * Set date range based on period
     */
    private function setDateRange($period, $startDate, $endDate)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'current_month':
                $this->currentMonth = $now;
                $this->lastMonth = $now->copy()->subMonth();
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $this->currentMonth = $now->copy()->subMonth();
                $this->lastMonth = $now->copy()->subMonths(2);
                $this->startDate = $now->copy()->subMonth()->startOfMonth();
                $this->endDate = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'current_year':
                $this->currentMonth = $now;
                $this->lastMonth = $now->copy()->subYear();
                $this->startDate = $now->copy()->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                $this->startDate = Carbon::parse($startDate);
                $this->endDate = Carbon::parse($endDate);
                $this->currentMonth = $this->endDate;
                $this->lastMonth = $this->startDate->copy()->subMonth();
                break;
            default:
                $this->currentMonth = $now;
                $this->lastMonth = $now->copy()->subMonth();
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['officer', 'canteen', 'shop', 'head']);
        })->count();

        $totalWorkUnits = WorkUnit::count();
        $activeWorkUnits = WorkUnit::where('is_active', true)->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_work_units' => $totalWorkUnits,
            'active_work_units' => $activeWorkUnits,
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
        ];
    }

    /**
     * Get financial statistics
     */
    private function getFinancialStats(): array
    {
        // Total keseluruhan
        $totalIncome = TransaksiKas::sum('pemasukan');
        $totalExpenses = TransaksiKas::sum('pengeluaran');
        $netBalance = $totalIncome - $totalExpenses;

        // Periode saat ini
        $currentMonthIncome = TransaksiKas::whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->sum('pemasukan');

        $currentMonthExpenses = TransaksiKas::whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->sum('pengeluaran');

        // Periode sebelumnya (untuk comparison)
        $daysDiff = $this->startDate->diffInDays($this->endDate);
        $lastPeriodStart = $this->startDate->copy()->subDays($daysDiff + 1);
        $lastPeriodEnd = $this->endDate->copy()->subDays($daysDiff + 1);

        $lastMonthIncome = TransaksiKas::whereBetween('tanggal', [$lastPeriodStart, $lastPeriodEnd])
            ->sum('pemasukan');

        $lastMonthExpenses = TransaksiKas::whereBetween('tanggal', [$lastPeriodStart, $lastPeriodEnd])
            ->sum('pengeluaran');

        // Growth
        $incomeGrowth = $lastMonthIncome > 0
            ? (($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100
            : 0;

        $expensesGrowth = $lastMonthExpenses > 0
            ? (($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100
            : 0;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_balance' => $netBalance,
            'current_month_income' => $currentMonthIncome,
            'current_month_expenses' => $currentMonthExpenses,
            'current_month_net' => $currentMonthIncome - $currentMonthExpenses,
            'last_month_income' => $lastMonthIncome,
            'last_month_expenses' => $lastMonthExpenses,
            'income_growth_percentage' => round($incomeGrowth, 2),
            'expenses_growth_percentage' => round($expensesGrowth, 2),
        ];
    }

    /**
     * Get work units performance statistics
     */
    private function getWorkUnitsStats(): array
    {
        $workUnits = WorkUnit::where('is_active', true)
            ->get()
            ->map(function($unit) {
                // Revenue dari penjualan
                $revenue = Penjualan::where('work_unit_id', $unit->id)
                    ->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
                    ->where('status', '!=', 'dibatalkan')
                    ->sum('total');

                // Total transaksi
                $transactionCount = Penjualan::where('work_unit_id', $unit->id)
                    ->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
                    ->where('status', '!=', 'dibatalkan')
                    ->count();

                // Untuk kas, kita ambil dari BukuKas yang user-nya berada di unit ini
                $userIds = $unit->users()->pluck('users.id');

                $cashIncome = 0;
                $cashExpenses = 0;

                if ($userIds->isNotEmpty()) {
                    // Kas masuk dari buku kas milik users di unit ini
                    $cashIncome = TransaksiKas::whereHas('bukuKas', function($q) use ($userIds) {
                            $q->whereIn('user_id', $userIds);
                        })
                        ->whereBetween('tanggal', [$this->startDate, $this->endDate])
                        ->sum('pemasukan');

                    // Kas keluar dari buku kas milik users di unit ini
                    $cashExpenses = TransaksiKas::whereHas('bukuKas', function($q) use ($userIds) {
                            $q->whereIn('user_id', $userIds);
                        })
                        ->whereBetween('tanggal', [$this->startDate, $this->endDate])
                        ->sum('pengeluaran');
                }

                return [
                    'id' => $unit->id,
                    'unit_id' => $unit->unit_id,
                    'name' => $unit->name,
                    'logo' => $unit->logo ? asset('storage/' . $unit->logo) : null,
                    'revenue' => $revenue,
                    'transaction_count' => $transactionCount,
                    'cash_income' => $cashIncome,
                    'cash_expenses' => $cashExpenses,
                    'net_cash' => $cashIncome - $cashExpenses,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return [
            'units' => $workUnits,
            'total_revenue' => $workUnits->sum('revenue'),
            'total_transactions' => $workUnits->sum('transaction_count'),
        ];
    }

    /**
     * Get sales statistics
     */
    private function getSalesStats(): array
    {
        // Current period sales
        $currentMonthSales = Penjualan::whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'dibatalkan')
            ->sum('total');

        $currentMonthCount = Penjualan::whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'dibatalkan')
            ->count();

        // Last period sales (untuk comparison)
        $daysDiff = $this->startDate->diffInDays($this->endDate);
        $lastPeriodStart = $this->startDate->copy()->subDays($daysDiff + 1);
        $lastPeriodEnd = $this->endDate->copy()->subDays($daysDiff + 1);

        $lastMonthSales = Penjualan::whereBetween('tanggal_transaksi', [$lastPeriodStart, $lastPeriodEnd])
            ->where('status', '!=', 'dibatalkan')
            ->sum('total');

        $lastMonthCount = Penjualan::whereBetween('tanggal_transaksi', [$lastPeriodStart, $lastPeriodEnd])
            ->where('status', '!=', 'dibatalkan')
            ->count();

        // Growth
        $salesGrowth = $lastMonthSales > 0
            ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100
            : 0;

        $countGrowth = $lastMonthCount > 0
            ? (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100
            : 0;

        // Average transaction value
        $avgTransactionValue = $currentMonthCount > 0
            ? $currentMonthSales / $currentMonthCount
            : 0;

        // Top selling items
        $topItems = PenjualanItem::with('barang')
            ->whereHas('penjualan', function($q) {
                $q->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
                  ->where('status', '!=', 'dibatalkan');
            })
            ->select('barang_id', 'nama_barang', 'satuan')
            ->selectRaw('SUM(qty) as total_qty')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->selectRaw('COUNT(DISTINCT penjualan_id) as transaction_count')
            ->groupBy('barang_id', 'nama_barang', 'satuan')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return [
            'current_month_sales' => $currentMonthSales,
            'current_month_count' => $currentMonthCount,
            'last_month_sales' => $lastMonthSales,
            'last_month_count' => $lastMonthCount,
            'sales_growth_percentage' => round($salesGrowth, 2),
            'count_growth_percentage' => round($countGrowth, 2),
            'avg_transaction_value' => $avgTransactionValue,
            'top_items' => $topItems,
        ];
    }

    /**
     * Get inventory statistics
     */
    private function getInventoryStats(): array
    {
        $totalItems = Barang::count();
        $activeItems = Barang::where('is_active', true)->count();

        // Items dengan stock rendah (kurang dari 10)
        $lowStockItems = Barang::where('stok', '<', 10)
            ->where('is_active', true)
            ->count();

        // Items dengan stock habis
        $outOfStockItems = Barang::where('stok', '<=', 0)
            ->where('is_active', true)
            ->count();

        // Total nilai inventory (berdasarkan harga_beli)
        $totalInventoryValue = Barang::where('is_active', true)
            ->selectRaw('SUM(stok * harga_beli) as total_value')
            ->value('total_value') ?? 0;

        // Items yang hampir expired (30 hari ke depan)
        $expiringItems = Barang::where('tanggal_kadaluarsa', '<=', Carbon::now()->addDays(30))
            ->where('tanggal_kadaluarsa', '>', Carbon::now())
            ->where('is_active', true)
            ->count();

        return [
            'total_items' => $totalItems,
            'active_items' => $activeItems,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'expiring_items' => $expiringItems,
            'total_inventory_value' => $totalInventoryValue,
        ];
    }

    /**
     * Get trends data (6 months)
     */
    private function getTrendsData(): array
    {
        $months = [];
        $salesData = [];
        $incomeData = [];
        $expensesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('id')->translatedFormat('M Y');

            // Sales
            $sales = Penjualan::whereYear('tanggal_transaksi', $date->year)
                ->whereMonth('tanggal_transaksi', $date->month)
                ->where('status', '!=', 'dibatalkan')
                ->sum('total');

            // Income from buku kas
            $income = TransaksiKas::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('pemasukan');

            // Expenses from buku kas
            $expenses = TransaksiKas::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('pengeluaran');

            $months[] = $monthName;
            $salesData[] = $sales;
            $incomeData[] = $income;
            $expensesData[] = $expenses;
        }

        return [
            'months' => $months,
            'sales' => $salesData,
            'income' => $incomeData,
            'expenses' => $expensesData,
        ];
    }

    /**
     * Get performance by category
     */
    public function getCategoryPerformance($period = 'current_month', $startDate = null, $endDate = null): array
    {
        if (!$this->startDate) {
            $this->setDateRange($period, $startDate, $endDate);
        }

        $results = DB::table('penjualan_items as pi')
            ->join('barangs as b', 'pi.barang_id', '=', 'b.id')
            ->leftJoin('kategori_barangs as kb', 'b.kategori_id', '=', 'kb.id')
            ->join('penjualans as p', 'pi.penjualan_id', '=', 'p.id')
            ->whereBetween('p.tanggal_transaksi', [$this->startDate, $this->endDate])
            ->where('p.status', '!=', 'dibatalkan')
            ->select(DB::raw('COALESCE(kb.id, 0) as id'), DB::raw('COALESCE(kb.nama, "Tanpa Kategori") as category_name'))
            ->selectRaw('SUM(pi.qty) as total_qty')
            ->selectRaw('SUM(pi.subtotal) as total_revenue')
            ->selectRaw('COUNT(DISTINCT pi.penjualan_id) as transaction_count')
            ->groupBy('kb.id', 'kb.nama')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return $results->toArray();
    }
}
