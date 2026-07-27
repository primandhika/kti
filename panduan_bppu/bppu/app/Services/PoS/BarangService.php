<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\MenuDisplay;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\Storage;

class BarangService
{
    /**
     * Get barangs data for one or multiple work units
     *
     * @param WorkUnit|array|null $workUnits Single WorkUnit, array of WorkUnit models, or null
     */
    public function getBarangs($workUnits)
    {
        if (!$workUnits) {
            return [];
        }

        $isMultiple = is_array($workUnits);
        $workUnitCollection = $isMultiple ? collect($workUnits) : collect([$workUnits]);

        if ($workUnitCollection->isEmpty()) {
            return [];
        }

        $workUnitIds = $workUnitCollection->pluck('id');
        $workUnitMap = $workUnitCollection->keyBy('id');

        $barangs = Barang::whereIn('work_unit_id', $workUnitIds)
            ->where('is_active', true)
            ->with(['kategoriBarang', 'menuDisplay', 'varians' => function($query) {
                $query->where('is_active', true)->orderBy('display_order');
            }])
            ->orderBy('nama_barang')
            ->get()
            ->map(function ($barang) use ($workUnitMap, $isMultiple) {
                $imageUrl = $barang->menuDisplay?->gambar;
                if ($imageUrl) {
                    if (!str_starts_with($imageUrl, '/')) {
                        $imageUrl = '/' . $imageUrl;
                    }
                    if ($barang->menuDisplay?->updated_at) {
                        $timestamp = strtotime($barang->menuDisplay->updated_at);
                        $imageUrl .= '?v=' . $timestamp;
                    }
                }

                $nominalDiskon = $barang->nominal_diskon;
                $workUnit = $workUnitMap->get($barang->work_unit_id);

                return [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'kategori' => $barang->kategoriBarang?->nama ?? $barang->kategori,
                    'satuan' => $barang->satuan,
                    'stok' => $barang->stok,
                    'harga_jual' => $barang->harga_jual,
                    'is_menu_item' => $barang->is_menu_item,
                    'image' => $imageUrl,
                    'tanggal_kadaluarsa' => $barang->tanggal_kadaluarsa,
                    'diskon_tipe' => $barang->diskon_tipe,
                    'diskon_nilai' => $barang->diskon_nilai,
                    'diskon_mulai' => $barang->diskon_mulai?->toDateString(),
                    'diskon_selesai' => $barang->diskon_selesai?->toDateString(),
                    'diskon_aktif' => $barang->isDiskonAktif(),
                    'nominal_diskon' => $nominalDiskon,
                    'work_unit_id' => $barang->work_unit_id,
                    'work_unit_name' => $isMultiple ? ($workUnit?->name ?? '') : null,
                    'varians' => $barang->varians->map(function ($varian) {
                        return [
                            'id' => $varian->id,
                            'nama_varian' => $varian->nama_varian,
                            'deskripsi' => $varian->deskripsi,
                            'harga_jual' => $varian->harga_jual,
                            'harga_grosir' => $varian->harga_grosir,
                            'display_order' => $varian->display_order,
                        ];
                    }),
                ];
            });

        return $barangs;
    }

    /**
     * Upload image for barang with WhatsApp-style compression
     */
    public function uploadImage(Barang $barang, $imageFile)
    {
        // Compress and upload image (WhatsApp style - target 50-100KB)
        $compressionService = new \App\Services\ImageCompressionService();

        // Check if MenuDisplay exists, create or update
        $menuDisplay = MenuDisplay::firstOrNew(['barang_id' => $barang->id]);

        // Delete old image if exists
        if ($menuDisplay->gambar) {
            $deleted = $compressionService->deleteImage($menuDisplay->gambar);
            \Log::info('Old image deletion', [
                'barang_id' => $barang->id,
                'old_image' => $menuDisplay->gambar,
                'deleted' => $deleted
            ]);
        }

        // Compress and store (max 800px width, target 100KB)
        $path = $compressionService->compressAndStore($imageFile, 'menu-images', 800, 85, 100);
        $imageUrl = Storage::url($path);

        $menuDisplay->barang_id = $barang->id;
        $menuDisplay->gambar = $imageUrl;
        $menuDisplay->is_available = $menuDisplay->is_available ?? true;
        $menuDisplay->display_order = $menuDisplay->display_order ?? 0;
        $menuDisplay->save();

        return $menuDisplay;
    }

    /**
     * Delete image for barang
     */
    public function deleteImage(Barang $barang)
    {
        $compressionService = new \App\Services\ImageCompressionService();
        $menuDisplay = MenuDisplay::where('barang_id', $barang->id)->first();

        if ($menuDisplay && $menuDisplay->gambar) {
            // Delete file from storage using compression service
            $compressionService->deleteImage($menuDisplay->gambar);

            // Update record
            $menuDisplay->gambar = null;
            $menuDisplay->save();
        }

        return true;
    }
}
