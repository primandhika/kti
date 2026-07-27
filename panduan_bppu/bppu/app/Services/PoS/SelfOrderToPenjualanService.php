<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\PesananSelfOrder;
use App\Services\ActivityLogger;
use App\Services\PointService;
use App\Services\PoS\VarianKomponenStockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelfOrderToPenjualanService
{
    protected $pointService;
    protected $komponenStockService;

    public function __construct()
    {
        $this->pointService = new PointService();
        $this->komponenStockService = new VarianKomponenStockService();
    }

    /**
     * Convert pesanan self-order yang selesai ke penjualan
     */
    public function convertToPenjualan(PesananSelfOrder $pesanan)
    {
        // Validasi bahwa pesanan belum pernah dikonversi
        if ($pesanan->penjualan_id) {
            Log::warning("Pesanan {$pesanan->id} sudah pernah dikonversi ke penjualan {$pesanan->penjualan_id}");
            return [
                'success' => false,
                'error' => 'Pesanan sudah tercatat sebagai penjualan',
                'penjualan_id' => $pesanan->penjualan_id,
            ];
        }

        DB::beginTransaction();
        try {
            // Validasi user yang sedang login (pengelola/kasir)
            $currentUser = auth()->user();
            if (!$currentUser) {
                throw new \Exception('User tidak terautentikasi. Silakan login kembali.');
            }

            // Get buyer
            $buyer = $pesanan->buyer;

            // Ambil barang pertama untuk mendapatkan work_unit_id
            // Asumsi: semua item dari kantin yang sama
            $firstItem = $pesanan->items[0] ?? null;
            if (!$firstItem) {
                throw new \Exception('Pesanan tidak memiliki item');
            }

            $firstBarang = Barang::find($firstItem['id']);
            if (!$firstBarang) {
                throw new \Exception("Barang dengan ID {$firstItem['id']} tidak ditemukan");
            }

            $workUnitId = $firstBarang->work_unit_id;

            // Validasi bahwa semua items dari work unit yang sama
            foreach ($pesanan->items as $item) {
                $barangCheck = Barang::find($item['id']);
                if ($barangCheck && $barangCheck->work_unit_id != $workUnitId) {
                    throw new \Exception("Pesanan mengandung item dari unit kerja berbeda. Tidak dapat diproses sebagai satu transaksi. Harap hubungi admin.");
                }
            }

            // Build catatan dengan handling null
            $catatan = $pesanan->catatan
                ? "{$pesanan->catatan} (Self Order #{$pesanan->nomor_antrian})"
                : "Self Order #{$pesanan->nomor_antrian}";

            // Buat penjualan
            $penjualan = Penjualan::create([
                'work_unit_id' => $workUnitId,
                'user_id' => $currentUser->id,
                'buyer_id' => $buyer?->id,
                'tanggal_transaksi' => $pesanan->tanggal_antrian->format('Y-m-d') . ' ' . $pesanan->created_at->format('H:i:s'),
                'subtotal' => $pesanan->subtotal,
                'biaya_layanan' => $pesanan->biaya_layanan ?? 0,
                'diskon' => 0,
                'total' => $pesanan->total,
                'bayar' => $pesanan->total,
                'kembalian' => 0,
                'metode_pembayaran' => 'tunai',
                'nama_pelanggan' => $pesanan->nama_pemesan,
                'catatan' => $catatan,
                'status' => 'selesai',
            ]);

            // Buat penjualan items dan update stok
            foreach ($pesanan->items as $item) {
                $barang = Barang::find($item['id']);

                if (!$barang) {
                    throw new \Exception("Barang {$item['name']} tidak ditemukan");
                }

                // Validasi stok
                if ($barang->stok < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk {$barang->nama_barang}. Stok tersedia: {$barang->stok}, diminta: {$item['quantity']}");
                }

                // Get varian_id dan build nama barang dengan varian (divalidasi dari DB)
                $varianId = null;
                $namaBarang = $barang->nama_barang;

                if (isset($item['varian']) && is_array($item['varian']) && isset($item['varian']['id'])) {
                    $varian = \App\Models\BarangVarian::where('id', $item['varian']['id'])
                        ->where('barang_id', $barang->id)
                        ->where('is_active', true)
                        ->first();
                    if (!$varian) {
                        throw new \Exception("Varian untuk {$barang->nama_barang} tidak valid atau tidak aktif.");
                    }
                    $varianId = $varian->id;
                    $namaBarang = "{$barang->nama_barang} - {$varian->nama_varian}";
                } elseif (isset($item['varian_id'])) {
                    $varian = \App\Models\BarangVarian::where('id', $item['varian_id'])
                        ->where('barang_id', $barang->id)
                        ->where('is_active', true)
                        ->first();
                    if ($varian) {
                        $varianId = $varian->id;
                        $namaBarang = "{$barang->nama_barang} - {$varian->nama_varian}";
                    }
                }

                // Calculate subtotal
                $subtotal = $item['quantity'] * $item['price'];

                // Buat penjualan item
                PenjualanItem::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barang->id,
                    'varian_id' => $varianId,
                    'nama_barang' => $namaBarang,
                    'qty' => $item['quantity'],
                    'satuan' => $barang->satuan,
                    'harga_satuan' => $item['price'],
                    'diskon_per_item' => 0,
                    'subtotal' => $subtotal,
                ]);

                // Update stok
                $barang->decrement('stok', $item['quantity']);

                // Kurangi stok komponen jika varian punya komponen terhubung
                if ($varianId) {
                    $this->komponenStockService->decrementKomponens($varianId, $item['quantity']);
                }
            }

            // Add points jika buyer ada
            if ($buyer) {
                $this->pointService->addPointsFromTransaction($buyer, $penjualan);
            }

            // Update pesanan dengan penjualan_id
            $pesanan->update(['penjualan_id' => $penjualan->id]);

            DB::commit();

            Log::info("Pesanan self-order #{$pesanan->nomor_antrian} berhasil dikonversi ke penjualan {$penjualan->nomor_transaksi}");

            ActivityLogger::log(
                'selesai',
                'pesanan_self_order',
                "Pesanan self-order #{$pesanan->nomor_antrian} diselesaikan oleh {$currentUser->name} → {$penjualan->nomor_transaksi}",
                $pesanan,
                ['nomor_transaksi' => $penjualan->nomor_transaksi, 'work_unit_id' => $workUnitId, 'pemesan' => $pesanan->nama_pemesan]
            );

            return [
                'success' => true,
                'penjualan_id' => $penjualan->id,
                'nomor_transaksi' => $penjualan->nomor_transaksi,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal convert pesanan {$pesanan->id} ke penjualan: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'pesanan_id' => $pesanan->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
