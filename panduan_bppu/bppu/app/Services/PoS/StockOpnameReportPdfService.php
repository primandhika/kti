<?php

namespace App\Services\PoS;

use App\Models\StockOpname;
use App\Models\WorkUnit;
use App\Models\Setting;

class StockOpnameReportPdfService
{
    public function generateReportData(int $workUnitId, array $params)
    {
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;
        $sortBy = $params['sort_by'] ?? 'waktu'; // 'waktu' atau 'alfabetis'
        $sortOrder = $params['sort_order'] ?? 'desc'; // 'asc' atau 'desc'

        // Get work unit info
        $workUnit = WorkUnit::findOrFail($workUnitId);

        // Query stock opname
        $query = StockOpname::where('work_unit_id', $workUnitId)
            ->with(['barang', 'user']);

        // Filter by date range if provided
        if ($startDate && $endDate) {
            $query->whereBetween('opname_date', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // Apply sorting
        if ($sortBy === 'alfabetis') {
            $query->join('barangs', 'stock_opnames.barang_id', '=', 'barangs.id')
                ->orderBy('barangs.nama_barang', $sortOrder)
                ->select('stock_opnames.*');
        } else {
            $query->orderBy('opname_date', $sortOrder);
        }

        // Check total count before limiting
        $totalCount = $query->count();
        $isLimited = $totalCount > 500;

        // Limit to prevent memory exhaustion (max 500 records for PDF)
        // DomPDF is very memory-intensive for large tables
        $stockOpnames = $query->limit(500)->get();

        // Transform data
        $data = $stockOpnames->map(function ($opname, $index) {
            return [
                'nomor' => $index + 1,
                'tanggal_so' => $opname->opname_date,
                'kode_barang' => $opname->barang->kode_barang ?? '-',
                'nama_barang' => $opname->barang->nama_barang ?? '-',
                'kategori' => $opname->barang->kategori ?? '-',
                'stock_awal' => $opname->stock_awal,
                'stock_fisik' => $opname->stock_fisik,
                'selisih' => $opname->selisih,
                'harga_pokok' => $opname->harga_pokok,
                'selisih_rupiah' => $opname->selisih_rupiah,
                'expired_date' => $opname->expired_date,
                'keterangan' => $opname->keterangan,
                'petugas' => $opname->user->name ?? '-',
            ];
        });

        // Calculate summary
        $totalTransaksi = $stockOpnames->count();
        $totalSelisihPlus = $stockOpnames->where('selisih', '>', 0)->sum('selisih');
        $totalSelisihMinus = $stockOpnames->where('selisih', '<', 0)->sum('selisih');
        $totalSelisihRupiahPlus = $stockOpnames->where('selisih_rupiah', '>', 0)->sum('selisih_rupiah');
        $totalSelisihRupiahMinus = $stockOpnames->where('selisih_rupiah', '<', 0)->sum('selisih_rupiah');
        $totalSelisihRupiah = $stockOpnames->sum('selisih_rupiah');

        $periodStart = $startDate ?? $stockOpnames->min('opname_date');
        $periodEnd = $endDate ?? $stockOpnames->max('opname_date');
        $kepalaBppu = Setting::getKepalaBppuForPeriod($periodStart, $periodEnd);

        return [
            'work_unit' => $workUnit->name,
            'work_unit_location' => $workUnit->location ?? '',
            'periode' => [
                'start' => $periodStart,
                'end' => $periodEnd,
            ],
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'data' => $data,
            'is_limited' => $isLimited,
            'total_count' => $totalCount,
            'summary' => [
                'total_transaksi' => $totalTransaksi,
                'total_selisih_plus' => $totalSelisihPlus,
                'total_selisih_minus' => abs($totalSelisihMinus),
                'total_selisih_rupiah_plus' => $totalSelisihRupiahPlus,
                'total_selisih_rupiah_minus' => abs($totalSelisihRupiahMinus),
                'total_selisih_rupiah' => $totalSelisihRupiah,
            ],
            'kepala_bppu_nama' => $kepalaBppu['nama'],
            'kepala_bppu_nip' => $kepalaBppu['nip'],
        ];
    }
}
