<?php

namespace App\Services\PoS;

use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Supplier;
use App\Models\SkemaBisnis;
use App\Models\User;

class SupplierReportService
{
    /**
     * Get pembagian per mitra data
     */
    public function getPembagianMitra(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $verifiedOnly = $params['verified_only'] ?? true;

        $query = PenjualanItem::with(['barang.supplier.user', 'penjualan'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user, $verifiedOnly) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                // Filter berdasarkan verified status
                if ($verifiedOnly) {
                    $q->where('is_verified', true);
                }

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
                    $q->whereIn('work_unit_id', $workUnits->pluck('id'));
                }
            });

        $allItems = $query->get();

        // Group by supplier
        $pembagianMitra = $allItems->groupBy(function ($item) {
                return $item->barang?->supplier_id ?? 'no_supplier';
            })
            ->map(function ($items, $supplierId) {
                if ($supplierId === 'no_supplier') {
                    $groupedItems = $items->groupBy('nama_barang')->map(function ($itemGroup, $namaBarang) {
                        return [
                            'nama_barang' => $namaBarang,
                            'qty' => $itemGroup->sum('qty'),
                            'satuan' => $itemGroup->first()->satuan,
                            'subtotal' => $itemGroup->sum('subtotal'),
                            'work_unit_id' => $itemGroup->first()->penjualan?->work_unit_id,
                            'bagian_vendor' => 0,
                            'bagian_badan_usaha' => $itemGroup->sum('subtotal'),
                        ];
                    })->values();

                    return [
                        'supplier_id' => null,
                        'supplier_name' => 'Tanpa Mitra',
                        'supplier_type' => null,
                        'total_penjualan' => $items->sum('subtotal'),
                        'total_qty' => $items->sum('qty'),
                        'total_items' => $groupedItems->count(), // jumlah unique nama barang
                        'items' => $groupedItems,
                    ];
                }

                $supplier = Supplier::with(['user', 'skemaBisnisAktif'])->find($supplierId);

                // Get supplier name with priority: user name > nama from supplier table > 'Unknown'
                if (!$supplier) {
                    $supplierName = 'Unknown';
                } else {
                    $supplierName = $supplier->nama ?? $supplier->user?->name ?? 'Unknown';
                }

                $totalPenjualan = $items->sum('subtotal');
                $totalQty = $items->sum('qty');

                $skemaBisnis = $supplier?->skemaBisnisAktif;
                $pembagian = null;
                if ($skemaBisnis && $skemaBisnis->is_active) {
                    if ($skemaBisnis->jenis_skema === 'konsinyasi') {
                        $totalBadanUsaha = 0;
                        $totalVendor = 0;

                        // Cek apakah ada konsinyasi bersyarat
                        $isKonsinyasiBersyarat = $skemaBisnis->minimum_harga_item !== null
                            && $skemaBisnis->nominal_flat !== null
                            && $skemaBisnis->minimum_harga_item > 0
                            && $skemaBisnis->nominal_flat > 0;

                        if ($isKonsinyasiBersyarat) {
                            // Hitung per grup item (subtotal)
                            foreach ($items as $item) {
                                $subtotal = $item->subtotal;

                                if ($subtotal > $skemaBisnis->minimum_harga_item) {
                                    // Subtotal item > minimum: badan usaha dapat flat fee
                                    $totalBadanUsaha += $skemaBisnis->nominal_flat;
                                    $totalVendor += $subtotal - $skemaBisnis->nominal_flat;
                                } else {
                                    // Subtotal item <= minimum: vendor dapat semua
                                    $totalVendor += $subtotal;
                                }
                            }
                        } else {
                            // Konsinyasi normal
                            foreach ($items as $item) {
                                $qty = $item->qty;
                                $subtotal = $item->subtotal;
                                $hargaSatuan = $item->harga_satuan;
                                $hargaKonsinyasi = $item->barang?->harga_konsinyasi;

                                $hasil = $skemaBisnis->hitungPembagian($subtotal, null, $qty, $hargaSatuan, $hargaKonsinyasi);
                                $totalBadanUsaha += $hasil['bagian_badan_usaha'];
                                $totalVendor += $hasil['bagian_vendor'];
                            }
                        }

                        $pembagian = [
                            'bagian_badan_usaha' => round($totalBadanUsaha, 2),
                            'bagian_vendor' => round($totalVendor, 2),
                        ];
                    } else {
                        $pembagian = $skemaBisnis->hitungPembagian($totalPenjualan, null, $totalQty);
                    }
                }

                $groupedDetailItems = $items->groupBy('nama_barang')->map(function ($itemGroup, $namaBarang) use ($skemaBisnis) {
                        $totalQtyPerBarang = $itemGroup->sum('qty');
                        $subtotalPerBarang = $itemGroup->sum('subtotal');

                        // Hitung pembagian per item
                        $bagianVendorItem = 0;
                        $bagianBadanUsahaItem = 0;

                        if ($skemaBisnis && $skemaBisnis->is_active) {
                            if ($skemaBisnis->jenis_skema === 'konsinyasi') {
                                // Cek konsinyasi bersyarat
                                $isKonsinyasiBersyarat = $skemaBisnis->minimum_harga_item !== null
                                    && $skemaBisnis->nominal_flat !== null
                                    && $skemaBisnis->minimum_harga_item > 0
                                    && $skemaBisnis->nominal_flat > 0;

                                if ($isKonsinyasiBersyarat) {
                                    foreach ($itemGroup as $item) {
                                        $subtotal = $item->subtotal;
                                        if ($subtotal > $skemaBisnis->minimum_harga_item) {
                                            $bagianBadanUsahaItem += $skemaBisnis->nominal_flat;
                                            $bagianVendorItem += $subtotal - $skemaBisnis->nominal_flat;
                                        } else {
                                            $bagianVendorItem += $subtotal;
                                        }
                                    }
                                } else {
                                    foreach ($itemGroup as $item) {
                                        $hasil = $skemaBisnis->hitungPembagian(
                                            $item->subtotal,
                                            null,
                                            $item->qty,
                                            $item->harga_satuan,
                                            $item->barang?->harga_konsinyasi
                                        );
                                        $bagianBadanUsahaItem += $hasil['bagian_badan_usaha'];
                                        $bagianVendorItem += $hasil['bagian_vendor'];
                                    }
                                }
                            } else {
                                $hasil = $skemaBisnis->hitungPembagian($subtotalPerBarang, null, $totalQtyPerBarang);
                                $bagianBadanUsahaItem = $hasil['bagian_badan_usaha'];
                                $bagianVendorItem = $hasil['bagian_vendor'];
                            }
                        }

                        return [
                            'nama_barang' => $namaBarang,
                            'qty' => $totalQtyPerBarang,
                            'satuan' => $itemGroup->first()->satuan,
                            'subtotal' => $subtotalPerBarang,
                            'work_unit_id' => $itemGroup->first()->penjualan?->work_unit_id,
                            'bagian_vendor' => round($bagianVendorItem, 2),
                            'bagian_badan_usaha' => round($bagianBadanUsahaItem, 2),
                        ];
                    })->values();

                return [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplierName,
                    'supplier_type' => $supplier?->tipe_mitra ?? null,
                    'total_penjualan' => $totalPenjualan,
                    'total_qty' => $totalQty,
                    'total_items' => $groupedDetailItems->count(), // jumlah unique nama barang
                    'skema_bisnis' => $skemaBisnis ? [
                        'jenis_skema' => $skemaBisnis->jenis_skema,
                        'jenis_skema_label' => SkemaBisnis::getJenisSkemaOptions()[$skemaBisnis->jenis_skema] ?? $skemaBisnis->jenis_skema,
                        'is_active' => $skemaBisnis->is_active,
                    ] : null,
                    'pembagian' => $pembagian,
                    'items' => $groupedDetailItems,
                ];
            })
            ->sortByDesc('total_penjualan')
            ->values();

        // Hitung breakdown per metode pembayaran
        $penjualanIds = $allItems->pluck('penjualan_id')->unique();
        $penjualans = Penjualan::whereIn('id', $penjualanIds)->get();

        $tunai = $penjualans->where('metode_pembayaran', 'tunai')->sum('total');
        $tunaiCount = $penjualans->where('metode_pembayaran', 'tunai')->count();
        $qris = $penjualans->where('metode_pembayaran', 'qris')->sum('total');
        $qrisCount = $penjualans->where('metode_pembayaran', 'qris')->count();
        $transfer = $penjualans->where('metode_pembayaran', 'transfer')->sum('total');
        $transferCount = $penjualans->where('metode_pembayaran', 'transfer')->count();
        $debit = $penjualans->where('metode_pembayaran', 'debit')->sum('total');
        $debitCount = $penjualans->where('metode_pembayaran', 'debit')->count();
        $kredit = $penjualans->where('metode_pembayaran', 'kredit')->sum('total');
        $kreditCount = $penjualans->where('metode_pembayaran', 'kredit')->count();

        // Summary
        $summary = [
            'total_mitra' => $pembagianMitra->count(),
            'total_penjualan' => $pembagianMitra->sum('total_penjualan'),
            'total_items' => $allItems->count(),
            'total_vendor' => $pembagianMitra->sum(function ($mitra) {
                return $mitra['pembagian']['bagian_vendor'] ?? 0;
            }),
            'total_badan_usaha' => $pembagianMitra->sum(function ($mitra) {
                return $mitra['pembagian']['bagian_badan_usaha'] ?? 0;
            }),
            'tunai' => $tunai,
            'tunai_count' => $tunaiCount,
            'qris' => $qris,
            'qris_count' => $qrisCount,
            'transfer' => $transfer,
            'transfer_count' => $transferCount,
            'debit' => $debit,
            'debit_count' => $debitCount,
            'kredit' => $kredit,
            'kredit_count' => $kreditCount,
        ];

        return [
            'pembagian' => $pembagianMitra,
            'summary' => $summary,
        ];
    }

    /**
     * Export pembagian mitra to CSV
     */
    public function exportPembagianMitra(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $verifiedOnly = $params['verified_only'] ?? true;

        $query = PenjualanItem::with(['barang.supplier.user', 'penjualan'])
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user, $verifiedOnly) {
                $q->whereBetween('tanggal_transaksi', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->where('status', '!=', 'dibatalkan');

                // Filter berdasarkan verified status
                if ($verifiedOnly) {
                    $q->where('is_verified', true);
                }

                if ($selectedWorkUnitId) {
                    $q->where('work_unit_id', $selectedWorkUnitId);
                } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
                    $q->whereIn('work_unit_id', $workUnits->pluck('id'));
                }
            });

        $allItems = $query->get();

        $pembagianMitra = $allItems->groupBy(function ($item) {
                return $item->barang?->supplier_id ?? 'no_supplier';
            })
            ->map(function ($items, $supplierId) {
                if ($supplierId === 'no_supplier') {
                    return [
                        'supplier_name' => 'Tanpa Mitra',
                        'supplier_type' => '-',
                        'total_penjualan' => $items->sum('subtotal'),
                        'total_qty' => $items->sum('qty'),
                        'total_items' => $items->count(),
                    ];
                }

                $supplier = Supplier::with('user')->find($supplierId);
                $supplierName = $supplier?->nama ?? $supplier?->user?->name ?? 'Unknown';

                return [
                    'supplier_name' => $supplierName,
                    'supplier_type' => $supplier?->tipe_mitra ?? '-',
                    'total_penjualan' => $items->sum('subtotal'),
                    'total_qty' => $items->sum('qty'),
                    'total_items' => $items->count(),
                ];
            })
            ->sortByDesc('total_penjualan')
            ->values();

        return $this->generatePembagianMitraCSV($pembagianMitra, $startDate, $endDate);
    }

    /**
     * Generate CSV for pembagian mitra
     */
    protected function generatePembagianMitraCSV($pembagianMitra, $startDate, $endDate)
    {
        $csvData = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvData .= "Periode,{$startDate} s/d {$endDate}\n";
        $csvData .= "\n";
        $csvData .= "Nama Mitra,Tipe Mitra,Total Penjualan,Total Qty,Jumlah Item\n";

        foreach ($pembagianMitra as $mitra) {
            $csvData .= '"' . str_replace('"', '""', $mitra['supplier_name']) . '",' .
                        '"' . $mitra['supplier_type'] . '",' .
                        $mitra['total_penjualan'] . ',' .
                        $mitra['total_qty'] . ',' .
                        $mitra['total_items'] . "\n";
        }

        return $csvData;
    }

    /**
     * Get buku tagihan data
     */
    public function getBukuTagihan(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $search = $params['search'] ?? null;
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $verifiedFilter = $params['verified_filter'] ?? null;
        $sortField = $params['sort_field'] ?? 'total_tagihan';
        $sortDirection = $params['sort_direction'] ?? 'desc';

        $query = Penjualan::with(['buyer.membershipTier', 'workUnit'])
            ->whereNotNull('buyer_id')
            ->where('is_approved', false)
            ->where('status', '!=', 'dibatalkan');

        if ($selectedWorkUnitId) {
            $query->where('work_unit_id', $selectedWorkUnitId);
        } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            $query->whereIn('work_unit_id', $workUnits->pluck('id'));
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('tanggal_transaksi', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59'
            ]);
        }

        if ($verifiedFilter !== null) {
            $query->where('is_verified', $verifiedFilter === 'verified');
        }

        if ($search) {
            $query->whereHas('buyer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allTransaksi = $query->orderBy('tanggal_transaksi', 'asc')->get();

        $transaksiByBuyer = $allTransaksi->groupBy('buyer_id')
            ->map(function ($transaksis, $buyerId) {
                $buyer = $transaksis->first()->buyer;
                $totalTagihan = $transaksis->sum('total');
                $totalTransaksi = $transaksis->count();

                $detailTransaksi = $transaksis->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'nomor_transaksi' => $t->nomor_transaksi,
                        'tanggal_transaksi' => $t->tanggal_transaksi,
                        'total' => $t->total,
                        'is_verified' => $t->is_verified,
                        'work_unit_name' => $t->workUnit?->name,
                    ];
                });

                return [
                    'buyer_id' => $buyer->id,
                    'buyer_name' => $buyer->name,
                    'member_code' => $buyer->member_code,
                    'phone' => $buyer->phone,
                    'email' => $buyer->email,
                    'tier' => $buyer->membershipTier ? [
                        'name' => $buyer->membershipTier->name,
                        'color' => $buyer->membershipTier->color,
                    ] : null,
                    'total_tagihan' => $totalTagihan,
                    'total_transaksi' => $totalTransaksi,
                    'transaksi' => $detailTransaksi,
                    'transaksi_tertua' => $transaksis->first()->tanggal_transaksi,
                ];
            });

        $transaksiByBuyer = $this->sortBukuTagihan($transaksiByBuyer, $sortField, $sortDirection);

        $summary = [
            'total_member' => $transaksiByBuyer->count(),
            'total_tagihan' => $transaksiByBuyer->sum('total_tagihan'),
            'total_transaksi' => $transaksiByBuyer->sum('total_transaksi'),
        ];

        return [
            'transaksi' => $transaksiByBuyer,
            'summary' => $summary,
        ];
    }

    /**
     * Sort buku tagihan berdasarkan field
     */
    protected function sortBukuTagihan($transaksiByBuyer, $sortField, $sortDirection)
    {
        $sorted = $transaksiByBuyer;

        switch ($sortField) {
            case 'buyer_name':
                $sorted = $sortDirection === 'asc'
                    ? $transaksiByBuyer->sortBy('buyer_name')
                    : $transaksiByBuyer->sortByDesc('buyer_name');
                break;
            case 'total_tagihan':
                $sorted = $sortDirection === 'asc'
                    ? $transaksiByBuyer->sortBy('total_tagihan')
                    : $transaksiByBuyer->sortByDesc('total_tagihan');
                break;
            case 'total_transaksi':
                $sorted = $sortDirection === 'asc'
                    ? $transaksiByBuyer->sortBy('total_transaksi')
                    : $transaksiByBuyer->sortByDesc('total_transaksi');
                break;
            case 'transaksi_tertua':
                $sorted = $sortDirection === 'asc'
                    ? $transaksiByBuyer->sortBy('transaksi_tertua')
                    : $transaksiByBuyer->sortByDesc('transaksi_tertua');
                break;
            default:
                $sorted = $transaksiByBuyer->sortByDesc('total_tagihan');
        }

        return $sorted->values();
    }

    /**
     * Export buku tagihan to CSV
     */
    public function exportBukuTagihan(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $search = $params['search'] ?? null;
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $verifiedFilter = $params['verified_filter'] ?? null;

        $query = Penjualan::with(['buyer', 'workUnit'])
            ->whereNotNull('buyer_id')
            ->where('is_approved', false)
            ->where('status', '!=', 'dibatalkan');

        if ($selectedWorkUnitId) {
            $query->where('work_unit_id', $selectedWorkUnitId);
        } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            $query->whereIn('work_unit_id', $workUnits->pluck('id'));
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('tanggal_transaksi', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59'
            ]);
        }

        if ($verifiedFilter !== null) {
            $query->where('is_verified', $verifiedFilter === 'verified');
        }

        if ($search) {
            $query->whereHas('buyer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $transaksi = $query->orderBy('tanggal_transaksi', 'asc')->get();

        return $transaksi;
    }
}
