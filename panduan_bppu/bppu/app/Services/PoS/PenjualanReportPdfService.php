<?php

namespace App\Services\PoS;

use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Supplier;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkUnit;

class PenjualanReportPdfService
{
    public function generateReportData(array $params, User $user, $workUnits)
    {
        $selectedWorkUnitId = $params['work_unit_id'] ?? null;
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $workUnit = WorkUnit::find($selectedWorkUnitId);

        $baseFilter = function ($q) use ($startDate, $endDate, $selectedWorkUnitId, $workUnits, $user) {
            $q->whereBetween('tanggal_transaksi', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ])->where('status', '!=', 'dibatalkan');

            if ($selectedWorkUnitId) {
                $q->where('work_unit_id', $selectedWorkUnitId);
            } elseif (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
                $q->whereIn('work_unit_id', $workUnits->pluck('id'));
            }
        };

        // Items verified saja — untuk tabel pembagian mitra
        $verifiedItems = PenjualanItem::with(['barang.supplier.user', 'penjualan.user'])
            ->whereHas('penjualan', function ($q) use ($baseFilter) {
                $baseFilter($q);
                $q->where('is_verified', true);
            })->get();

        // Items semua (tidak filter verified) — untuk tabel per-user dan rekap akhir
        $allItems = PenjualanItem::with(['barang.supplier.user', 'penjualan.user'])
            ->whereHas('penjualan', $baseFilter)
            ->get();

        // Semua penjualan (tidak filter verified) — untuk rekap akhir
        $allPenjualanIds = $allItems->pluck('penjualan_id')->unique();
        $allPenjualans = Penjualan::whereIn('id', $allPenjualanIds)->get();

        // Tabel 1: Pembagian Mitra — hanya dari verified items
        $pembagianMitra = $this->buildPembagianMitra($verifiedItems, $startDate, $endDate, $selectedWorkUnitId);

        // Tabel 2: Pendapatan Per User — dari semua items
        $pendapatanPerUser = $allItems->groupBy(fn ($item) => $item->penjualan?->user_id)
            ->map(function ($items, $userId) {
                return [
                    'user_name' => User::find($userId)?->name ?? 'Unknown',
                    'total' => $items->sum('subtotal'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Tabel 3: Rekap Akhir — dari semua penjualan
        $rekap = $this->buildRekap($allPenjualans, $pembagianMitra);

        $kepalaBppu = Setting::getKepalaBppuForPeriod($startDate, $endDate);

        return [
            'work_unit' => $workUnit ? $workUnit->name : 'Semua Unit',
            'periode' => ['start' => $startDate, 'end' => $endDate],
            'pembagian_mitra' => $pembagianMitra,
            'pendapatan_per_user' => $pendapatanPerUser,
            'rekap' => $rekap,
            'kepala_bppu_nama' => $kepalaBppu['nama'],
            'kepala_bppu_nip' => $kepalaBppu['nip'],
            'penyetor_nama' => $user->name,
        ];
    }

    protected function buildPembagianMitra($verifiedItems, $startDate, $endDate, $selectedWorkUnitId)
    {
        return $verifiedItems->groupBy(fn ($item) => $item->barang?->supplier_id ?? 'no_supplier')
            ->map(function ($items, $supplierId) use ($startDate, $endDate, $selectedWorkUnitId) {
                if ($supplierId === 'no_supplier') return null;

                $supplier = Supplier::with(['user', 'skemaBisnisAktif'])->find($supplierId);
                if (!$supplier) return null;

                $supplierName = $supplier->nama ?? $supplier->user?->name ?? $supplier->nama_supplier ?? 'Unknown';
                $totalVerified = $items->sum('subtotal');


                // Hitung breakdown metode pembayaran dari verified items
                $penjualanIds = $items->pluck('penjualan_id')->unique();
                $penjualans = Penjualan::whereIn('id', $penjualanIds)->get();

                $tunai = $qris = $transfer = $debit = $kredit = 0;
                foreach ($items->groupBy('penjualan_id') as $penjualanId => $penjualanItems) {
                    $penjualan = $penjualans->firstWhere('id', $penjualanId);
                    if (!$penjualan) continue;
                    $itemsTotal = $penjualanItems->sum('subtotal');
                    match ($penjualan->metode_pembayaran) {
                        'tunai'    => $tunai    += $itemsTotal,
                        'qris'     => $qris     += $itemsTotal,
                        'transfer' => $transfer += $itemsTotal,
                        'debit'    => $debit    += $itemsTotal,
                        'kredit'   => $kredit   += $itemsTotal,
                        default    => null,
                    };
                }

                // Hitung pembagian dari verified items
                $skemaBisnis = $supplier->skemaBisnisAktif;
                $pembagian = ['bagian_vendor' => 0, 'bagian_badan_usaha' => 0];

                if ($skemaBisnis && $skemaBisnis->is_active) {
                    if ($skemaBisnis->jenis_skema === 'konsinyasi') {
                        $totalBadanUsaha = 0;
                        $totalVendor = 0;

                        $isBersyarat = $skemaBisnis->minimum_harga_item > 0 && $skemaBisnis->nominal_flat > 0;

                        foreach ($items as $item) {
                            if ($isBersyarat) {
                                if ($item->subtotal > $skemaBisnis->minimum_harga_item) {
                                    $totalBadanUsaha += $skemaBisnis->nominal_flat;
                                    $totalVendor += $item->subtotal - $skemaBisnis->nominal_flat;
                                } else {
                                    $totalVendor += $item->subtotal;
                                }
                            } else {
                                $hasil = $skemaBisnis->hitungPembagian(
                                    $item->subtotal, null, $item->qty,
                                    $item->harga_satuan, $item->barang?->harga_konsinyasi
                                );
                                $totalBadanUsaha += $hasil['bagian_badan_usaha'];
                                $totalVendor += $hasil['bagian_vendor'];
                            }
                        }

                        $pembagian = [
                            'bagian_badan_usaha' => round($totalBadanUsaha, 2),
                            'bagian_vendor' => round($totalVendor, 2),
                        ];
                    } else {
                        $pembagian = $skemaBisnis->hitungPembagian($totalVerified, null, $items->sum('qty'));
                    }
                }

                // Hitung total belum verified untuk supplier ini dalam periode yang sama
                $totalBelumVerified = PenjualanItem::whereHas('barang', fn ($q) => $q->where('supplier_id', $supplierId))
                    ->whereHas('penjualan', function ($q) use ($startDate, $endDate, $selectedWorkUnitId) {
                        $q->whereBetween('tanggal_transaksi', [
                                $startDate . ' 00:00:00',
                                $endDate . ' 23:59:59',
                            ])
                            ->where('status', '!=', 'dibatalkan')
                            ->where('is_verified', false);
                        if ($selectedWorkUnitId) {
                            $q->where('work_unit_id', $selectedWorkUnitId);
                        }
                    })->sum('subtotal');

                return [
                    'supplier_name'      => $supplierName,
                    'total_penjualan'    => $totalVerified,
                    'total_belum_verified' => $totalBelumVerified,
                    'tunai'              => $tunai,
                    'qris'               => $qris,
                    'transfer'           => $transfer,
                    'debit'              => $debit,
                    'kredit'             => $kredit,
                    'bagian_vendor'      => $pembagian['bagian_vendor'] ?? 0,
                    'bagian_bppu'        => $pembagian['bagian_badan_usaha'] ?? 0,
                ];
            })
            ->filter()
            ->sortByDesc('total_penjualan')
            ->values();
    }

    protected function buildRekap($allPenjualans, $pembagianMitra)
    {
        $totalKeseluruhan = $allPenjualans->sum('total');
        $totalVerified    = $allPenjualans->where('is_verified', true)->sum('total');
        $totalSisa        = $totalKeseluruhan - $totalVerified;

        $methods = ['tunai', 'qris', 'transfer', 'debit', 'kredit'];
        $rekap = [
            'total_keseluruhan' => $totalKeseluruhan,
            'total_verified'    => $totalVerified,
            'total_sisa'        => $totalSisa,
            'total_bagian_vendor' => $pembagianMitra->sum('bagian_vendor'),
            'total_bagian_bppu'   => $pembagianMitra->sum('bagian_bppu'),
        ];

        foreach ($methods as $method) {
            $rekap["{$method}_keseluruhan"] = $allPenjualans->where('metode_pembayaran', $method)->sum('total');
            $rekap["{$method}_verified"]    = $allPenjualans->where('is_verified', true)->where('metode_pembayaran', $method)->sum('total');
            $rekap["{$method}_sisa"]        = $allPenjualans->where('is_verified', false)->where('metode_pembayaran', $method)->sum('total');
        }

        return $rekap;
    }
}
