<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\BarangVarian;
use App\Models\BarangVarianKomponen;

class VarianKomponenStockService
{
    /**
     * Kurangi stok semua komponen dari sebuah varian yang terjual.
     * Dipanggil di dalam DB transaction.
     *
     * @throws \Exception jika stok komponen tidak cukup
     */
    public function decrementKomponens(int $varianId, int $qty): void
    {
        $komponens = BarangVarianKomponen::with('barangKomponen')
            ->where('varian_id', $varianId)
            ->get();

        foreach ($komponens as $komponen) {
            $barang = $komponen->barangKomponen;
            if (!$barang) {
                continue;
            }

            $needed = $komponen->qty * $qty;

            if ($barang->stok < $needed) {
                throw new \Exception(
                    "Stok komponen \"{$barang->nama_barang}\" tidak cukup. " .
                    "Dibutuhkan: {$needed} {$barang->satuan}, tersedia: {$barang->stok} {$barang->satuan}."
                );
            }

            $barang->decrement('stok', $needed);
        }
    }

    /**
     * Kembalikan stok semua komponen dari sebuah varian (saat transaksi dibatalkan).
     * Dipanggil di dalam DB transaction.
     */
    public function restoreKomponens(int $varianId, int $qty): void
    {
        $komponens = BarangVarianKomponen::with('barangKomponen')
            ->where('varian_id', $varianId)
            ->get();

        foreach ($komponens as $komponen) {
            $barang = $komponen->barangKomponen;
            if (!$barang) {
                continue;
            }

            $barang->increment('stok', $komponen->qty * $qty);
        }
    }
}
