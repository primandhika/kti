<?php

namespace App\Services\PoS;

use App\Models\Penjualan;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;

class TransactionProofService
{
    protected $compressionService;

    public function __construct(ImageCompressionService $compressionService)
    {
        $this->compressionService = $compressionService;
    }

    /**
     * Upload foto bukti transaksi dengan kompresi
     *
     * @param Penjualan $penjualan
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function uploadFotoBukti(Penjualan $penjualan, $file)
    {
        try {
            // Delete old photo if exists
            if ($penjualan->foto_bukti) {
                $this->deleteFotoBukti($penjualan);
            }

            // Compress and store (max 600px width, target 150-300KB)
            $path = $this->compressionService->compressAndStore(
                $file,
                'transaction-proofs',
                600,
                85,
                300
            );

            $imageUrl = Storage::url($path);

            // Update penjualan record
            $penjualan->foto_bukti = $imageUrl;
            $penjualan->save();

            return [
                'success' => true,
                'message' => 'Foto bukti berhasil diupload',
                'foto_bukti' => $imageUrl,
            ];
        } catch (\Exception $e) {
            \Log::error('Upload foto bukti error', [
                'penjualan_id' => $penjualan->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal upload foto bukti: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete foto bukti transaksi
     *
     * @param Penjualan $penjualan
     * @return array
     */
    public function deleteFotoBukti(Penjualan $penjualan)
    {
        try {
            if ($penjualan->foto_bukti) {
                // Delete file from storage
                $this->compressionService->deleteImage($penjualan->foto_bukti);

                // Update record
                $penjualan->foto_bukti = null;
                $penjualan->save();
            }

            return [
                'success' => true,
                'message' => 'Foto bukti berhasil dihapus',
            ];
        } catch (\Exception $e) {
            \Log::error('Delete foto bukti error', [
                'penjualan_id' => $penjualan->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal hapus foto bukti: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get foto bukti URL
     *
     * @param Penjualan $penjualan
     * @return string|null
     */
    public function getFotoBukti(Penjualan $penjualan)
    {
        return $penjualan->foto_bukti;
    }
}
