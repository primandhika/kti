<?php

namespace App\Services\PoS;

use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkUnit;

class PenjualanReportService
{
    /**
     * Get rekap penjualan data
     */
    public function getRekapPenjualan(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $verifiedOnly = $params['verified_only'] ?? false;
        $search = $params['search'] ?? null;

        // Build query for pagination
        $query = Penjualan::with(['workUnit', 'user', 'items', 'verifiedBy', 'approvedBy', 'recordedBy', 'buyer.membershipTier', 'voucher.potongan'])
            ->whereBetween('tanggal_transaksi', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'dibatalkan');

        if ($selectedWorkUnitId) {
            $query->where('work_unit_id', $selectedWorkUnitId);
        } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            $query->whereIn('work_unit_id', $workUnits->pluck('id'));
        }

        // Filter verified_only hanya jika diminta (untuk Kantin yang ingin lihat verified saja)
        if ($verifiedOnly) {
            $query->where('is_verified', true);
        }

        // Officer hanya boleh lihat yang sudah verified (untuk proses approval)
        if ($user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            $query->where('is_verified', true);
        }

        // Search by nomor transaksi or member name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('buyer', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Sysadmin melihat semua transaksi
        // Kantin sudah terfilter di atas berdasarkan work_unit yang mereka akses

        // Build summary query
        $summary = $this->calculateSummary($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user, $verifiedOnly);

        // Get item summary
        $itemSummary = $this->getItemSummary($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user);

        return [
            'query' => $query,
            'summary' => $summary,
            'itemSummary' => $itemSummary,
        ];
    }

    /**
     * Calculate summary statistics
     */
    protected function calculateSummary($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user, $verifiedOnly)
    {
        $summaryQuery = Penjualan::whereBetween('tanggal_transaksi', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'dibatalkan');

        if ($selectedWorkUnitId) {
            $summaryQuery->where('work_unit_id', $selectedWorkUnitId);
        } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            $summaryQuery->whereIn('work_unit_id', $workUnits->pluck('id'));
        }

        // Filter verified_only hanya jika diminta
        if ($verifiedOnly) {
            $summaryQuery->where('is_verified', true);
        }

        // Officer hanya boleh lihat yang sudah verified
        if ($user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            $summaryQuery->where('is_verified', true);
        }

        // Sysadmin melihat semua transaksi untuk summary

        // Calculate HPP and Margin
        $allItems = PenjualanItem::with(['barang', 'varian'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user, $verifiedOnly) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
                    $q->whereIn('work_unit_id', $workUnits->pluck('id'));
                }

                if ($verifiedOnly) {
                    $q->where('is_verified', true);
                }

                // Officer hanya boleh lihat yang sudah verified
                if ($user->hasRole('officer') && !$user->hasRole('sysadmin')) {
                    $q->where('is_verified', true);
                }
            })
            ->get();

        $totalHpp = $allItems->sum('total_hpp');
        $totalPenjualan = $summaryQuery->sum('total');

        // Breakdown per metode pembayaran
        $summaryQueryClone = clone $summaryQuery;
        $paymentMethods = $summaryQueryClone->select('metode_pembayaran')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('metode_pembayaran')
            ->get()
            ->keyBy('metode_pembayaran');

        return [
            'total_transaksi' => $summaryQuery->count(),
            'total_penjualan' => $totalPenjualan,
            'total_diskon' => $summaryQuery->sum('diskon'),
            'total_subtotal' => $summaryQuery->sum('subtotal'),
            'total_hpp' => $totalHpp,
            'total_margin' => $totalPenjualan - $totalHpp,
            'margin_persen' => $totalPenjualan > 0 ? (($totalPenjualan - $totalHpp) / $totalPenjualan) * 100 : 0,
            'total_verified' => $summaryQuery->where('is_verified', true)->count(),
            'tunai' => $paymentMethods->get('tunai')?->total ?? 0,
            'tunai_count' => $paymentMethods->get('tunai')?->count ?? 0,
            'transfer' => $paymentMethods->get('transfer')?->total ?? 0,
            'transfer_count' => $paymentMethods->get('transfer')?->count ?? 0,
            'qris' => $paymentMethods->get('qris')?->total ?? 0,
            'qris_count' => $paymentMethods->get('qris')?->count ?? 0,
            'debit' => $paymentMethods->get('debit')?->total ?? 0,
            'debit_count' => $paymentMethods->get('debit')?->count ?? 0,
            'kredit' => $paymentMethods->get('kredit')?->total ?? 0,
            'kredit_count' => $paymentMethods->get('kredit')?->count ?? 0,
        ];
    }

    /**
     * Get item summary with margin details
     */
    protected function getItemSummary($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user)
    {
        return PenjualanItem::with(['barang.supplier.user', 'varian', 'penjualan'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
                    $q->whereIn('work_unit_id', $workUnits->pluck('id'));
                }

                // Item summary selalu hanya yang sudah verified (sama dengan pembagian-mitra)
                $q->where('is_verified', true);
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->nama_barang;
            })
            ->map(function ($items, $nama) {
                $totalQty = $items->sum('qty');
                $totalHarga = $items->sum('subtotal');
                $totalHpp = $items->sum('total_hpp');
                $totalMargin = $items->sum('total_margin');

                $stockReductions = $items->map(function ($item) {
                    return [
                        'tanggal_transaksi' => $item->penjualan->tanggal_transaksi,
                        'nomor_transaksi' => $item->penjualan->nomor_transaksi,
                        'qty' => $item->qty,
                        'satuan' => $item->satuan,
                        'harga_satuan' => $item->harga_satuan,
                        'subtotal' => $item->subtotal,
                    ];
                })->sortByDesc('tanggal_transaksi')->values();

                $firstItem = $items->first();

                $supplierName = null;
                if ($firstItem->barang && $firstItem->barang->supplier_id) {
                    $supplier = Supplier::with('user')->find($firstItem->barang->supplier_id);
                    if ($supplier) {
                        $supplierName = $supplier->user?->name ?? $supplier->nama_supplier ?? null;
                    }
                }

                return [
                    'nama_barang' => $nama,
                    'satuan' => $firstItem->satuan,
                    'supplier_name' => $supplierName,
                    'total_qty' => $totalQty,
                    'total_harga' => $totalHarga,
                    'total_hpp' => $totalHpp,
                    'total_margin' => $totalMargin,
                    'margin_persen' => $totalHarga > 0 ? ($totalMargin / $totalHarga) * 100 : 0,
                    'stock_reductions' => $stockReductions,
                ];
            })
            ->sortByDesc('total_harga')
            ->values();
    }

    /**
     * Export item summary to CSV
     */
    public function exportItemSummary(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $itemsQuery = PenjualanItem::with(['barang.supplier.user', 'penjualan'])
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
            ->groupBy('nama_barang')
            ->map(function ($items, $nama) {
                $firstItem = $items->first();

                $supplierName = '-';
                if ($firstItem->barang && $firstItem->barang->supplier_id) {
                    $supplier = Supplier::with('user')->find($firstItem->barang->supplier_id);
                    if ($supplier) {
                        $supplierName = $supplier->user?->name ?? $supplier->nama_supplier ?? '-';
                    }
                }

                return [
                    'nama_barang' => $nama,
                    'satuan' => $firstItem->satuan,
                    'supplier_name' => $supplierName,
                    'total_qty' => $items->sum('qty'),
                    'total_harga' => $items->sum('subtotal'),
                ];
            })
            ->sortBy('nama_barang')
            ->values();

        // Get summary stats
        $totalTransaksi = Penjualan::whereBetween('tanggal_transaksi', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->where('status', '!=', 'dibatalkan')
            ->when($selectedWorkUnitId, function ($q) use ($selectedWorkUnitId) {
                $q->where('work_unit_id', $selectedWorkUnitId);
            })
            ->when(!$user->hasRole('sysadmin'), function ($q) use ($workUnits) {
                $q->whereIn('work_unit_id', $workUnits);
            })
            ->count();

        $totalVerified = Penjualan::whereBetween('tanggal_transaksi', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->where('status', '!=', 'dibatalkan')
            ->where('is_verified', true)
            ->when($selectedWorkUnitId, function ($q) use ($selectedWorkUnitId) {
                $q->where('work_unit_id', $selectedWorkUnitId);
            })
            ->when(!$user->hasRole('sysadmin'), function ($q) use ($workUnits) {
                $q->whereIn('work_unit_id', $workUnits);
            })
            ->count();

        return $this->generateItemSummaryCSV($itemsQuery, $startDate, $endDate, $totalTransaksi, $totalVerified);
    }

    /**
     * Generate CSV content for item summary
     */
    protected function generateItemSummaryCSV($items, $startDate, $endDate, $totalTransaksi, $totalVerified)
    {
        $csvData = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvData .= "RINGKASAN ITEM PENJUALAN\n";
        $csvData .= "Periode: {$startDate} s/d {$endDate}\n";
        $csvData .= "Total Transaksi: {$totalTransaksi}\n";
        $csvData .= "Transaksi Terverifikasi: {$totalVerified}\n";
        $csvData .= "\n";
        $csvData .= "No,Nama Barang,Satuan,Mitra,Total Kuantitas,Total Harga\n";

        $no = 1;
        foreach ($items as $item) {
            $csvData .= $no . ',' .
                        '"' . str_replace('"', '""', $item['nama_barang']) . '",' .
                        '"' . $item['satuan'] . '",' .
                        '"' . str_replace('"', '""', $item['supplier_name']) . '",' .
                        $item['total_qty'] . ',' .
                        $item['total_harga'] . "\n";
            $no++;
        }

        $csvData .= "\n";
        $csvData .= "TOTAL," . ($no - 1) . " item,,,," . $items->sum('total_harga') . "\n";

        return $csvData;
    }

    /**
     * Get approved summary by work unit (for Buku Kas)
     */
    public function getApprovedSummaryByUnit()
    {
        return Penjualan::select('work_unit_id')
            ->selectRaw('COUNT(*) as total_transaksi')
            ->selectRaw('SUM(total) as total_penghasilan')
            ->where('status', 'selesai')
            ->where('is_approved', true)
            ->where('is_recorded', false)
            ->whereNotNull('work_unit_id')
            ->groupBy('work_unit_id')
            ->with('workUnit')
            ->get()
            ->map(function ($item) {
                return [
                    'work_unit_id' => $item->work_unit_id,
                    'work_unit_name' => $item->workUnit ? $item->workUnit->name : 'Unknown',
                    'total_transaksi' => $item->total_transaksi,
                    'total_penghasilan' => $item->total_penghasilan,
                ];
            });
    }

    /**
     * Get approved penjualan by work unit (for Buku Kas)
     */
    public function getApprovedByUnit($workUnitId)
    {
        return Penjualan::where('work_unit_id', $workUnitId)
            ->where('status', 'selesai')
            ->where('is_approved', true)
            ->where('is_recorded', false)
            ->with(['workUnit', 'user', 'buyer'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_transaksi' => $item->nomor_transaksi,
                    'tanggal_transaksi' => $item->tanggal_transaksi->format('d M Y H:i'),
                    'total' => $item->total,
                    'metode_pembayaran' => $item->metode_pembayaran,
                    'nama_pelanggan' => $item->nama_pelanggan,
                    'catatan' => $item->catatan,
                ];
            });
    }
}
