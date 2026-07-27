<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\PesananSelfOrder;
use App\Models\User;
use App\Models\Voucher;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
use App\Services\PoS\VarianKomponenStockService;

class PenjualanTransactionService
{
    protected $pointService;
    protected $proofService;
    protected $komponenStockService;

    public function __construct(TransactionProofService $proofService = null)
    {
        $this->pointService = new PointService();
        $this->proofService = $proofService;
        $this->komponenStockService = new VarianKomponenStockService();
    }

    /**
     * Store a new transaction
     */
    public function store(array $validated, User $user)
    {
        DB::beginTransaction();
        try {
            // Handle redeem points if buyer_id provided
            $buyer = null;
            $pointsRedeemed = 0;

            if (!empty($validated['buyer_id'])) {
                $buyer = User::find($validated['buyer_id']);

                if ($buyer && $buyer->hasRole('buyer') && !empty($validated['redeem_points'])) {
                    $pointsRedeemed = $validated['redeem_points'];

                    // Validate points
                    if ($pointsRedeemed > $buyer->total_points) {
                        throw new \Exception('Poin tidak mencukupi');
                    }
                }
            }

            // Gunakan work_unit_id dari item (sudah divalidasi controller), bukan header
            $effectiveWorkUnitId = $validated['items'][0]['work_unit_id'] ?? $validated['work_unit_id'];

            // Create penjualan
            $penjualan = Penjualan::create([
                'work_unit_id' => $effectiveWorkUnitId,
                'user_id' => $user->id,
                'buyer_id' => $validated['buyer_id'] ?? null,
                'tanggal_transaksi' => $validated['tanggal_transaksi'] . ' ' . now()->format('H:i:s'),
                'subtotal' => $validated['subtotal'],
                'diskon' => $validated['diskon'] ?? 0,
                'total' => $validated['total'],
                'bayar' => $validated['bayar'],
                'kembalian' => $validated['bayar'] - $validated['total'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'catatan' => $validated['catatan'],
                'status' => 'selesai',
            ]);

            // Mark voucher as used if provided
            if (!empty($validated['voucher_id'])) {
                Voucher::where('id', $validated['voucher_id'])
                    ->where('status', 'printed')
                    ->update([
                        'status' => 'used',
                        'penjualan_id' => $penjualan->id,
                        'used_by' => $validated['buyer_id'] ?? null,
                        'used_at' => now(),
                    ]);
            }

            // Redeem points if applicable (before transaction approved)
            if ($buyer && $pointsRedeemed > 0) {
                $this->pointService->redeemPointsForDiscount($buyer, $pointsRedeemed, $penjualan);
            }

            // Validate stock availability first
            foreach ($validated['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);

                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok tidak cukup untuk barang {$barang->nama_barang}. Stok tersedia: {$barang->stok}, diminta: {$item['qty']}");
                }
            }

            // Create penjualan items and update stock
            foreach ($validated['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                $varianId = $item['varian_id'] ?? null;
                $namaBarang = $barang->nama_barang;

                // If varian exists, append varian name
                if ($varianId) {
                    $varian = \App\Models\BarangVarian::find($varianId);
                    if ($varian) {
                        $namaBarang = $barang->nama_barang . ' - ' . $varian->nama_varian;
                    }
                }

                // Calculate subtotal for item
                $diskonPerItem = $item['diskon_per_item'] ?? 0;
                $subtotal = ($item['qty'] * $item['harga_satuan']) - $diskonPerItem;

                // Create penjualan item
                PenjualanItem::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barang->id,
                    'varian_id' => $varianId,
                    'nama_barang' => $namaBarang,
                    'qty' => $item['qty'],
                    'satuan' => $barang->satuan,
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon_per_item' => $diskonPerItem,
                    'subtotal' => $subtotal,
                ]);

                // Update stock in main table
                $barang->decrement('stok', $item['qty']);

                // Kurangi stok komponen jika varian punya komponen terhubung
                if ($varianId) {
                    $this->komponenStockService->decrementKomponens($varianId, $item['qty']);
                }
            }

            // Calculate points earned if buyer exists
            $pointsEarned = 0;
            if ($buyer) {
                // Refresh buyer to get latest points after redeem
                $buyer->refresh();

                // Hitung poin dari subtotal sebelum potongan poin (bukan total akhir)
                // agar tier rule tidak terpengaruh jumlah poin yang diredeem
                $amountForPoints = $validated['subtotal'] - ($validated['diskon'] ?? 0);

                $memberPoint = $this->pointService->addPointsFromTransaction($buyer, $penjualan, max(0, $amountForPoints));
                $pointsEarned = $memberPoint ? $memberPoint->points : 0;
            }

            DB::commit();

            // Prepare transaction data for receipt
            $transactionData = $this->prepareReceiptData($penjualan, $validated, $user, $buyer, $pointsRedeemed, $pointsEarned);

            return ['success' => true, 'data' => $transactionData];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('PoS Transaction Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'work_unit_id' => $validated['work_unit_id'] ?? null,
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Prepare receipt data
     */
    protected function prepareReceiptData($penjualan, $validated, $user, $buyer, $pointsRedeemed, $pointsEarned)
    {
        return [
            'id' => $penjualan->id,
            'nomor_transaksi' => $penjualan->nomor_transaksi,
            'tanggal' => $penjualan->tanggal_transaksi,
            'work_unit_name' => $penjualan->workUnit?->nama ?? 'Kantin',
            'kasir' => $user->name,
            'pelanggan' => $validated['nama_pelanggan'] ?? ($buyer?->name ?? '-'),
            'member_name' => $buyer?->name,
            'member_id' => $buyer?->member_code,
            'items' => collect($validated['items'])->map(function ($item) use ($validated) {
                $barang = Barang::find($item['barang_id']);
                $namaBarang = $barang->nama_barang;

                if (!empty($item['varian_id'])) {
                    $varian = \App\Models\BarangVarian::find($item['varian_id']);
                    if ($varian) {
                        $namaBarang .= ' - ' . $varian->nama_varian;
                    }
                }

                $diskonPerItem = $item['diskon_per_item'] ?? 0;
                return [
                    'nama' => $namaBarang,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon_per_item' => $diskonPerItem,
                    'subtotal' => $item['qty'] * ($item['harga_satuan'] - $diskonPerItem),
                ];
            })->toArray(),
            'subtotal' => $validated['subtotal'],
            'diskon' => $validated['diskon'] ?? 0,
            'potongan_poin' => $pointsRedeemed > 0 ? PointService::pointsToRupiah($pointsRedeemed) : 0,
            'total' => $validated['total'],
            'bayar' => $validated['bayar'],
            'kembalian' => $validated['bayar'] - $validated['total'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'poin_didapat' => $pointsEarned,
        ];
    }

    /**
     * Store multiple transactions (satu per toko) dalam satu DB transaction
     */
    public function storeMulti(array $validated, array $items, array $workUnitIds, User $user, ?int $buyerId = null)
    {
        DB::beginTransaction();
        try {
            $results = [];
            $buyer = $buyerId ? User::find($buyerId) : null;

            foreach ($workUnitIds as $workUnitId) {
                $unitItems = array_values(array_filter($items, fn($i) => $i['work_unit_id'] == $workUnitId));

                $unitSubtotal = array_sum(array_map(
                    fn($i) => $i['qty'] * ($i['harga_satuan'] - ($i['diskon_per_item'] ?? 0)),
                    $unitItems
                ));

                // Validate stock per item
                foreach ($unitItems as $item) {
                    $barang = Barang::findOrFail($item['barang_id']);
                    if ($barang->stok < $item['qty']) {
                        throw new \Exception("Stok tidak cukup untuk barang {$barang->nama_barang}. Stok tersedia: {$barang->stok}, diminta: {$item['qty']}");
                    }
                }

                $penjualan = Penjualan::create([
                    'work_unit_id' => $workUnitId,
                    'user_id' => $user->id,
                    'buyer_id' => $buyer?->id,
                    'tanggal_transaksi' => $validated['tanggal_transaksi'] . ' ' . now()->format('H:i:s'),
                    'subtotal' => $unitSubtotal,
                    'diskon' => 0,
                    'total' => $unitSubtotal,
                    'bayar' => $validated['bayar'],
                    'kembalian' => $validated['bayar'] - $unitSubtotal,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'nama_pelanggan' => $validated['nama_pelanggan'] ?? ($buyer?->name ?? null),
                    'catatan' => $validated['catatan'] ?? null,
                    'status' => 'selesai',
                ]);

                foreach ($unitItems as $item) {
                    $barang = Barang::findOrFail($item['barang_id']);
                    $varianId = $item['varian_id'] ?? null;
                    $namaBarang = $barang->nama_barang;

                    if ($varianId) {
                        $varian = \App\Models\BarangVarian::find($varianId);
                        if ($varian) $namaBarang .= ' - ' . $varian->nama_varian;
                    }

                    $diskonPerItem = $item['diskon_per_item'] ?? 0;

                    PenjualanItem::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $barang->id,
                        'varian_id' => $varianId,
                        'nama_barang' => $namaBarang,
                        'qty' => $item['qty'],
                        'satuan' => $barang->satuan,
                        'harga_satuan' => $item['harga_satuan'],
                        'diskon_per_item' => $diskonPerItem,
                        'subtotal' => $item['qty'] * ($item['harga_satuan'] - $diskonPerItem),
                    ]);

                    $barang->decrement('stok', $item['qty']);

                    // Kurangi stok komponen jika varian punya komponen terhubung
                    if ($varianId) {
                        $this->komponenStockService->decrementKomponens($varianId, $item['qty']);
                    }
                }

                $results[] = $this->prepareReceiptData($penjualan, [
                    'items' => $unitItems,
                    'subtotal' => $unitSubtotal,
                    'diskon' => 0,
                    'total' => $unitSubtotal,
                    'bayar' => $validated['bayar'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'nama_pelanggan' => $validated['nama_pelanggan'] ?? ($buyer?->name ?? null),
                ], $user, $buyer, 0, 0);
            }

            DB::commit();
            return ['success' => true, 'data' => $results];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('PoS Multi-Toko Transaction Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'work_unit_ids' => $workUnitIds,
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel transaction
     */
    public function cancel(Penjualan $penjualan)
    {
        DB::beginTransaction();
        try {
            // Validate transaction can be cancelled
            if ($penjualan->status === 'dibatalkan') {
                throw new \Exception('Transaksi sudah dibatalkan sebelumnya');
            }

            // Prevent cancellation if already recorded to buku kas
            if ($penjualan->is_recorded) {
                throw new \Exception('Transaksi yang sudah tercatat di Buku Kas tidak dapat dibatalkan. Silakan hubungi Officer/Sysadmin.');
            }

            // Restore stock
            foreach ($penjualan->items as $item) {
                $barang = Barang::find($item->barang_id);
                if ($barang && $barang->work_unit_id == $penjualan->work_unit_id) {
                    $barang->increment('stok', $item->qty);
                }

                // Restore stok komponen jika item punya varian dengan komponen
                if ($item->varian_id) {
                    $this->komponenStockService->restoreKomponens($item->varian_id, $item->qty);
                }
            }

            // Refund redeemed points if any
            $redeemedPoints = \App\Models\MemberPoint::where('penjualan_id', $penjualan->id)
                ->where('type', 'redeem')
                ->first();

            if ($redeemedPoints && $penjualan->buyer) {
                // Return points (redeem was negative, so we add back)
                $pointsToReturn = abs($redeemedPoints->points);

                // Create refund record
                \App\Models\MemberPoint::create([
                    'user_id' => $penjualan->buyer->id,
                    'points' => $pointsToReturn,
                    'type' => 'refund',
                    'transaction_amount' => $penjualan->total,
                    'penjualan_id' => $penjualan->id,
                    'description' => "Pengembalian {$pointsToReturn} poin dari pembatalan transaksi {$penjualan->nomor_transaksi}",
                ]);

                // Update user total points
                $penjualan->buyer->total_points += $pointsToReturn;
                $penjualan->buyer->save();
            }

            // Cancel earned points if transaction was approved
            if ($penjualan->is_approved) {
                $earnedPoints = \App\Models\MemberPoint::where('penjualan_id', $penjualan->id)
                    ->where('type', 'earn')
                    ->first();

                if ($earnedPoints && $penjualan->buyer) {
                    // Deduct earned points
                    $pointsToDeduct = $earnedPoints->points;

                    // Create cancellation record
                    \App\Models\MemberPoint::create([
                        'user_id' => $penjualan->buyer->id,
                        'points' => -$pointsToDeduct,
                        'type' => 'cancel',
                        'transaction_amount' => $penjualan->total,
                        'penjualan_id' => $penjualan->id,
                        'description' => "Pembatalan {$pointsToDeduct} poin dari transaksi dibatalkan {$penjualan->nomor_transaksi}",
                    ]);

                    // Update user total points
                    $penjualan->buyer->total_points -= $pointsToDeduct;
                    $penjualan->buyer->save();
                }
            }

            // Delete foto bukti if exists (to save storage space)
            if ($penjualan->foto_bukti && $this->proofService) {
                $this->proofService->deleteFotoBukti($penjualan);
            }

            // Update status pesanan self-order jika ada
            $pesananSelfOrder = PesananSelfOrder::where('penjualan_id', $penjualan->id)->first();
            if ($pesananSelfOrder) {
                $pesananSelfOrder->update([
                    'status' => 'dibatalkan',
                    'alasan_batal' => 'Dibatalkan dari Rekap Penjualan',
                    'kode_alasan_batal' => 'CANCEL_FROM_REKAP',
                ]);
            }

            // Update status
            $penjualan->update(['status' => 'dibatalkan']);

            DB::commit();

            return ['success' => true, 'message' => 'Transaksi berhasil dibatalkan dan poin dikembalikan'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal membatalkan transaksi: ' . $e->getMessage()];
        }
    }
}
