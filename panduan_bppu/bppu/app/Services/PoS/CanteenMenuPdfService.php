<?php

namespace App\Services\PoS;

use App\Models\Barang;
use App\Models\WorkUnit;
use Barryvdh\DomPDF\Facade\Pdf;

class CanteenMenuPdfService
{
    /**
     * Generate menu PDF with or without images
     */
    public function generateMenuPdf(string $theme = 'with-image', ?int $workUnitId = null, bool $availableOnly = false)
    {
        // Get work units for canteen
        $workUnits = WorkUnit::where('type', 'Kantin')->get();

        // Build query for menu items
        $query = Barang::with(['menuDisplay', 'workUnit', 'kategoriBarang', 'varians' => function($q) {
            $q->where('is_active', true)->orderBy('display_order');
        }])
            ->where('is_menu_item', true)
            ->whereIn('work_unit_id', $workUnits->pluck('id'))
            ->where(function($q) {
                $q->whereDoesntHave('menuDisplay')
                  ->orWhereHas('menuDisplay', function($subQ) {
                      $subQ->where('is_available', true);
                  });
            });

        // Filter by work unit if specified
        if ($workUnitId) {
            $query->where('work_unit_id', $workUnitId);
            $workUnit = WorkUnit::find($workUnitId);
            $workUnitName = $workUnit?->name ?? 'Semua Kantin';
            $contactPhone = $workUnit?->contact_phone ?? '-';
        } else {
            $workUnitName = 'Semua Kantin';
            $contactPhone = '-';
        }

        // Get menus and group by sub_kategori
        $menus = $query->get()
            ->when($availableOnly, function($collection) {
                return $collection->filter(function($barang) {
                    return $barang->stok > 0;
                });
            })
            ->map(function($barang) {
            $display = $barang->menuDisplay;
            $varians = $barang->varians->map(function($varian) {
                return [
                    'nama' => $varian->nama_varian,
                    'harga' => $varian->harga_jual, // FIELD YANG BENAR!
                ];
            });

            return [
                'nama_barang' => $barang->nama_barang,
                'sub_kategori' => $barang->sub_kategori ?? 'Lainnya',
                'harga_jual' => $barang->harga_jual,
                'satuan' => $barang->satuan,
                'stok' => $barang->stok ?? 0,
                'gambar' => $display->gambar ?? null,
                'deskripsi_display' => $display->deskripsi_display ?? $barang->deskripsi,
                'is_available' => $display->is_available ?? true,
                'work_unit' => $barang->workUnit->name ?? '-',
                'varians' => $varians,
            ];
        });

        // Get urutan mapping from sub_kategori_menu
        $subKategoriUrutan = \App\Models\SubKategoriMenu::orderBy('urutan')
            ->orderBy('nama')
            ->pluck('urutan', 'nama')
            ->toArray();

        // Group by sub_kategori and sort by urutan
        $groupedMenus = $menus->groupBy('sub_kategori')
            ->map(function($items) {
                // Sort items alfabetis by nama_barang
                return $items->sortBy('nama_barang')->values();
            })
            ->sortBy(function($items, $key) use ($subKategoriUrutan) {
                return $subKategoriUrutan[$key] ?? 9999;
            });

        // Prepare data
        $data = [
            'groupedMenus' => $groupedMenus,
            'workUnitName' => $workUnitName,
            'contactPhone' => $contactPhone,
            'generatedDate' => now()->format('d F Y'),
            'generatedTime' => now()->format('H:i'),
        ];

        // Select view based on theme
        $view = $theme === 'with-image'
            ? 'pdf.menu-kantin-dengan-gambar'
            : 'pdf.menu-kantin-tanpa-gambar';

        // Generate PDF
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'serif',
                'chroot' => public_path(),
            ]);

        $filename = 'Menu_Kantin_' . str_replace(' ', '_', $workUnitName) . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
