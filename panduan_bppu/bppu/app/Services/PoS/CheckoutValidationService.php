<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\BarangVarian;

class CheckoutValidationService
{
    /**
     * Validasi items checkout: harga, stok, dan varian.
     *
     * @param  array  $items  Items dari request client
     * @param  string $filterField  'is_menu_item' atau 'show_in_shop'
     * @return array ['valid' => bool, 'errors' => string[], 'validatedItems' => array]
     */
    public function validate(array $items, string $filterField = 'is_menu_item'): array
    {
        $errors = [];
        $validatedItems = [];

        foreach ($items as $index => $item) {
            $barangId = $item['id'] ?? null;

            $barang = Barang::with(['activeVarians', 'workUnit'])->find($barangId);

            if (!$barang) {
                $errors[] = "Item \"{$item['name']}\" tidak ditemukan di katalog.";
                continue;
            }

            if (!$barang->{$filterField}) {
                $errors[] = "Item \"{$barang->nama_barang}\" tidak tersedia untuk pemesanan ini.";
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 1);

            // Validasi stok
            if ($barang->stok < $quantity) {
                if ($barang->stok <= 0) {
                    $errors[] = "Item \"{$barang->nama_barang}\" sudah habis.";
                } else {
                    $errors[] = "Stok \"{$barang->nama_barang}\" tidak cukup. Tersedia: {$barang->stok}, diminta: {$quantity}.";
                }
                continue;
            }

            // Hitung harga yang benar dari DB
            $hargaBenar = $this->resolveHarga($barang, $item['varian'] ?? null, $errors);

            if ($hargaBenar === null) {
                continue;
            }

            // Validasi varian jika ada
            $varianValidated = null;
            if (!empty($item['varian']) && isset($item['varian']['id'])) {
                $varian = $barang->activeVarians->firstWhere('id', $item['varian']['id']);
                if (!$varian) {
                    $errors[] = "Varian untuk \"{$barang->nama_barang}\" tidak valid atau tidak aktif.";
                    continue;
                }
                $varianValidated = [
                    'id'          => $varian->id,
                    'nama_varian' => $varian->nama_varian,
                    'harga_jual'  => (float) $varian->harga_jual,
                ];
            }

            $validatedItem = $item;
            $validatedItem['price'] = $hargaBenar;
            $validatedItem['varian'] = $varianValidated;
            $validatedItem['work_unit_name'] = $barang->workUnit?->name ?? null;

            $validatedItems[] = $validatedItem;
        }

        return [
            'valid'          => empty($errors),
            'errors'         => $errors,
            'validatedItems' => $validatedItems,
        ];
    }

    /**
     * Hitung harga yang benar dari DB, termasuk diskon & varian.
     * Return null jika ada error (dan push ke $errors).
     */
    private function resolveHarga(Barang $barang, ?array $varianRaw, array &$errors): ?float
    {
        if (!empty($varianRaw) && isset($varianRaw['id'])) {
            $varian = $barang->activeVarians->firstWhere('id', $varianRaw['id']);
            if (!$varian) {
                $errors[] = "Varian untuk \"{$barang->nama_barang}\" tidak ditemukan.";
                return null;
            }
            // Harga varian langsung dari DB (varian punya harga_jual sendiri)
            return (float) $varian->harga_jual;
        }

        // Harga barang biasa dengan diskon
        $harga = (float) $barang->harga_jual;
        if ($barang->isDiskonAktif()) {
            $harga = max(0, $harga - $barang->nominal_diskon);
        }

        return $harga;
    }
}
