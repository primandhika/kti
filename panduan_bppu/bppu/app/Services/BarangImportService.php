<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\MenuDisplay;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BarangImportService
{
    /**
     * Import multiple barangs from source work unit to target work unit
     *
     * @param int $targetWorkUnitId
     * @param int $sourceWorkUnitId
     * @param array $barangIds
     * @return array ['success' => bool, 'imported' => int, 'skipped' => int, 'errors' => array]
     */
    public function importBarangs(int $targetWorkUnitId, int $sourceWorkUnitId, array $barangIds): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        // Validate work units exist
        $targetWorkUnit = WorkUnit::find($targetWorkUnitId);
        $sourceWorkUnit = WorkUnit::find($sourceWorkUnitId);

        if (!$targetWorkUnit || !$sourceWorkUnit) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['Unit kerja tidak ditemukan']
            ];
        }

        // Get source barangs with varians and menuDisplay
        $sourceBarangs = Barang::with([
                'varians' => function($q) {
                    $q->where('is_active', true);
                },
                'menuDisplay'
            ])
            ->whereIn('id', $barangIds)
            ->where('work_unit_id', $sourceWorkUnitId)
            ->get();

        if ($sourceBarangs->isEmpty()) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['Barang yang dipilih tidak ditemukan']
            ];
        }

        // Process each barang in a transaction
        DB::beginTransaction();
        try {
            foreach ($sourceBarangs as $sourceBarang) {
                try {
                    $result = $this->importSingleBarang($targetWorkUnitId, $sourceBarang);

                    if ($result['success']) {
                        $imported++;
                    } else {
                        $skipped++;
                        $errors[] = "{$sourceBarang->nama_barang}: {$result['reason']}";
                    }
                } catch (\Exception $e) {
                    $skipped++;
                    $errorMsg = "{$sourceBarang->nama_barang}: " . $e->getMessage();
                    $errors[] = $errorMsg;

                    Log::error('Import barang error', [
                        'barang_id' => $sourceBarang->id,
                        'nama_barang' => $sourceBarang->nama_barang,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => $imported > 0,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Import barangs transaction failed', [
                'target_work_unit_id' => $targetWorkUnitId,
                'source_work_unit_id' => $sourceWorkUnitId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'imported' => 0,
                'skipped' => count($barangIds),
                'errors' => ['Gagal mengimpor barang: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Import single barang
     *
     * @param int $targetWorkUnitId
     * @param Barang $sourceBarang
     * @return array ['success' => bool, 'reason' => string|null]
     */
    private function importSingleBarang(int $targetWorkUnitId, Barang $sourceBarang): array
    {
        // Use the same kode_barang from source (for scannable barcodes)
        $kodeBarang = $sourceBarang->kode_barang;

        // Check if this barang already exists in target work unit
        $existingBarang = Barang::where('work_unit_id', $targetWorkUnitId)
            ->where('kode_barang', $kodeBarang)
            ->first();

        if ($existingBarang) {
            return [
                'success' => false,
                'reason' => 'Kode barang sudah ada'
            ];
        }

        // Create new barang with same kode_barang
        $newBarang = Barang::create([
            'work_unit_id' => $targetWorkUnitId,
            'kode_barang' => $kodeBarang,
            'nama_barang' => $sourceBarang->nama_barang,
            'kategori' => $sourceBarang->kategori,
            'kategori_id' => $sourceBarang->kategori_id,
            'satuan' => $sourceBarang->satuan,
            'stok' => 0, // Start with 0 stock
            'harga_beli' => $sourceBarang->harga_beli ?? 0,
            'harga_jual' => $sourceBarang->harga_jual ?? 0,
            'harga_grosir' => $sourceBarang->harga_grosir ?? 0,
            'minimum_stok' => $sourceBarang->minimum_stok ?? 0,
            'deskripsi' => $sourceBarang->deskripsi,
            'supplier_id' => $sourceBarang->supplier_id,
            'jenis_barang' => $sourceBarang->jenis_barang ?? 'jual_langsung',
            'created_by' => $sourceBarang->created_by,
            'is_menu_item' => $sourceBarang->is_menu_item ?? false,
            'is_active' => $sourceBarang->is_active ?? true,
            'tanggal_kadaluarsa' => $sourceBarang->tanggal_kadaluarsa,
        ]);

        // Copy varians if any
        if ($sourceBarang->varians && $sourceBarang->varians->count() > 0) {
            foreach ($sourceBarang->varians as $index => $varian) {
                $newBarang->varians()->create([
                    'nama_varian' => $varian->nama_varian,
                    'deskripsi' => $varian->deskripsi,
                    'harga_jual' => $varian->harga_jual ?? 0,
                    'harga_grosir' => $varian->harga_grosir ?? 0,
                    'hpp_tambahan' => $varian->hpp_tambahan ?? 0,
                    'display_order' => $varian->display_order ?? $index,
                    'is_active' => $varian->is_active ?? true,
                ]);
            }
        }

        // Copy menuDisplay with image if exists
        if ($sourceBarang->menuDisplay) {
            $this->copyMenuDisplay($sourceBarang->menuDisplay, $newBarang);
        }

        return [
            'success' => true,
            'reason' => null
        ];
    }

    /**
     * Copy menu display with image from source barang to new barang
     *
     * @param MenuDisplay $sourceMenuDisplay
     * @param Barang $newBarang
     * @return void
     */
    private function copyMenuDisplay(MenuDisplay $sourceMenuDisplay, Barang $newBarang): void
    {
        try {
            $newGambarPath = null;

            // Copy image file if exists
            if ($sourceMenuDisplay->gambar) {
                $newGambarPath = $this->copyImageFile($sourceMenuDisplay->gambar);
            }

            // Create new menu display record
            MenuDisplay::create([
                'barang_id' => $newBarang->id,
                'gambar' => $newGambarPath,
                'deskripsi_display' => $sourceMenuDisplay->deskripsi_display,
                'is_available' => $sourceMenuDisplay->is_available ?? true,
                'display_order' => $sourceMenuDisplay->display_order ?? 0,
            ]);

            Log::info('Menu display copied successfully', [
                'source_barang_id' => $sourceMenuDisplay->barang_id,
                'new_barang_id' => $newBarang->id,
                'image_copied' => !empty($newGambarPath)
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to copy menu display', [
                'source_barang_id' => $sourceMenuDisplay->barang_id,
                'new_barang_id' => $newBarang->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Copy image file to new location
     *
     * @param string $sourcePath Path like "/storage/menu-images/menu_xxx.webp"
     * @return string|null New path or null if failed
     */
    private function copyImageFile(string $sourcePath): ?string
    {
        try {
            // Remove /storage prefix to get actual storage path
            $sourceStoragePath = str_replace('/storage/', '', $sourcePath);

            // Full path in storage/app/public
            $sourceFullPath = storage_path('app/public/' . $sourceStoragePath);

            // Check if source file exists
            if (!File::exists($sourceFullPath)) {
                Log::warning('Source image file not found', ['path' => $sourceFullPath]);
                return null;
            }

            // Get file extension
            $extension = File::extension($sourceFullPath);

            // Generate new unique filename
            $newFileName = 'menu_' . uniqid() . '_' . time() . '.' . $extension;
            $newStoragePath = 'menu-images/' . $newFileName;
            $newFullPath = storage_path('app/public/' . $newStoragePath);

            // Ensure directory exists
            $directory = dirname($newFullPath);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Copy file
            if (File::copy($sourceFullPath, $newFullPath)) {
                return '/storage/' . $newStoragePath;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to copy image file', [
                'source_path' => $sourcePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
