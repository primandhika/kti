<?php

namespace App\Services\PoS;

use App\Models\Supplier;
use App\Models\SkemaBisnis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraUsahaPdfService
{
    /**
     * Generate PDF tabel semua mitra usaha
     */
    public function generateAllMitraPdf()
    {
        $suppliers = Supplier::with('skemaBisnisAktif')
            ->orderBy('tipe_mitra')
            ->orderBy('nama')
            ->get();

        $data = $this->prepareAllMitraData($suppliers);

        $pdf = Pdf::loadView('pdf.all-mitra-usaha', ['data' => $data]);

        $filename = 'Daftar_Mitra_Usaha_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Prepare data untuk PDF semua mitra
     */
    private function prepareAllMitraData($suppliers): array
    {
        $tipeMitraOptions = Supplier::getTipeMitraOptions();
        $jenisSkemaOptions = SkemaBisnis::getJenisSkemaOptions();

        // Group suppliers by tipe_mitra
        $groupedSuppliers = [];
        foreach ($suppliers as $supplier) {
            $tipeLabel = $tipeMitraOptions[$supplier->tipe_mitra] ?? $supplier->tipe_mitra;

            if (!isset($groupedSuppliers[$tipeLabel])) {
                $groupedSuppliers[$tipeLabel] = [];
            }

            $groupedSuppliers[$tipeLabel][] = [
                'kode_supplier' => $supplier->kode_supplier,
                'nama' => $supplier->nama,
                'perusahaan' => $supplier->perusahaan,
                'telepon' => $supplier->telepon,
                'email' => $supplier->email,
                'kota' => $supplier->kota,
                'is_active' => $supplier->is_active,
                'skema_bisnis' => $supplier->skemaBisnisAktif ? [
                    'jenis_skema_label' => $jenisSkemaOptions[$supplier->skemaBisnisAktif->jenis_skema] ?? $supplier->skemaBisnisAktif->jenis_skema,
                ] : null,
            ];
        }

        // Sort groups alphabetically
        ksort($groupedSuppliers);

        return [
            'grouped_suppliers' => $groupedSuppliers,
            'total' => $suppliers->count(),
        ];
    }

    /**
     * Generate PDF dokumen mitra usaha (kept for backward compatibility)
     */
    public function generatePdf(int $supplierId)
    {
        $supplier = Supplier::with('skemaBisnisAktif')->findOrFail($supplierId);

        $data = $this->prepareData($supplier);

        $pdf = Pdf::loadView('pdf.mitra-usaha', ['data' => $data]);

        $filename = 'Dokumen_Mitra_' . str_replace(' ', '_', $supplier->nama) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Prepare data untuk PDF
     */
    private function prepareData(Supplier $supplier): array
    {
        $tipeMitraOptions = Supplier::getTipeMitraOptions();
        $jenisSkemaOptions = SkemaBisnis::getJenisSkemaOptions();

        return [
            'supplier' => [
                'kode_supplier' => $supplier->kode_supplier,
                'tipe_mitra' => $supplier->tipe_mitra,
                'tipe_mitra_label' => $tipeMitraOptions[$supplier->tipe_mitra] ?? $supplier->tipe_mitra,
                'nama' => $supplier->nama,
                'perusahaan' => $supplier->perusahaan,
                'alamat' => $supplier->alamat,
                'kota' => $supplier->kota,
                'provinsi' => $supplier->provinsi,
                'kode_pos' => $supplier->kode_pos,
                'telepon' => $supplier->telepon,
                'email' => $supplier->email,
                'kontak_person' => $supplier->kontak_person,
                'telepon_kontak' => $supplier->telepon_kontak,
                'catatan' => $supplier->catatan,
                'is_active' => $supplier->is_active,
                'logo' => $supplier->logo,
                'created_at' => $supplier->created_at->format('d/m/Y'),
            ],
            'skema_bisnis' => $supplier->skemaBisnisAktif ? [
                'jenis_skema' => $supplier->skemaBisnisAktif->jenis_skema,
                'jenis_skema_label' => $jenisSkemaOptions[$supplier->skemaBisnisAktif->jenis_skema] ?? $supplier->skemaBisnisAktif->jenis_skema,
                'persentase_vendor' => $supplier->skemaBisnisAktif->persentase_vendor,
                'persentase_badan_usaha' => $supplier->skemaBisnisAktif->persentase_badan_usaha,
                'nominal_flat' => $supplier->skemaBisnisAktif->nominal_flat,
                'minimum_harga_item' => $supplier->skemaBisnisAktif->minimum_harga_item,
                'persentase_dari_keuntungan' => $supplier->skemaBisnisAktif->persentase_dari_keuntungan,
                'is_active' => $supplier->skemaBisnisAktif->is_active,
                'catatan' => $supplier->skemaBisnisAktif->catatan,
            ] : null,
        ];
    }
}
