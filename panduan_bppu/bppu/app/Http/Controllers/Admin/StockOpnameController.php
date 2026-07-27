<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Services\PoS\StockOpnameReportPdfService;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOpnameController extends Controller
{
    /**
     * Display stock opname report for a work unit
     */
    public function index(Request $request, $workUnitId)
    {
        $workUnit = \App\Models\WorkUnit::withCount('barangs')->findOrFail($workUnitId);

        $perPage = $request->get('per_page', 50);
        $search = $request->get('search');
        $period = $request->get('period', 'this_month'); // Default bulan ini
        $sortBy = $request->get('sort_by', 'waktu');
        $sortOrder = $request->get('sort_order', 'desc');

        // Build query
        $query = StockOpname::where('work_unit_id', $workUnitId)
            ->with(['barang', 'user']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('barang', function($subQ) use ($search) {
                    $subQ->where('kode_barang', 'like', "%{$search}%")
                         ->orWhere('nama_barang', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Apply period filter
        if ($period) {
            $now = now();
            switch ($period) {
                case 'today':
                    $query->whereDate('opname_date', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('opname_date', [
                        $now->startOfWeek()->toDateString(),
                        $now->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('opname_date', $now->month)
                          ->whereYear('opname_date', $now->year);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereMonth('opname_date', $lastMonth->month)
                          ->whereYear('opname_date', $lastMonth->year);
                    break;
            }
        }

        // Apply sorting
        if ($sortBy === 'alfabetis') {
            $query->join('barangs', 'stock_opnames.barang_id', '=', 'barangs.id')
                  ->orderBy('barangs.nama_barang', $sortOrder)
                  ->select('stock_opnames.*');
        } else {
            $query->orderBy('opname_date', $sortOrder);
        }

        // Paginate
        $stockOpnames = $query->paginate($perPage)
            ->appends($request->query())
            ->through(function($opname) {
                return [
                    'id' => $opname->id,
                    'tanggal_so' => $opname->opname_date,
                    'plu' => $opname->barang->kode_barang ?? '-',
                    'deskripsi_barang' => $opname->barang->nama_barang ?? '-',
                    'kategori' => $opname->barang->kategori ?? '-',
                    'cogs' => $opname->harga_pokok,
                    'stock_awal' => $opname->stock_awal,
                    'penyesuaian' => $opname->stock_fisik,
                    'selisih_quantity' => $opname->selisih,
                    'harga_pokok' => $opname->harga_pokok,
                    'selisih_rupiah' => $opname->selisih_rupiah,
                    'keterangan' => $opname->keterangan,
                    'expired_date' => $opname->expired_date,
                    'status' => $opname->status,
                    'user_name' => $opname->user->name ?? '-',
                ];
            });

        // Get all barangs for this work unit for the dropdown
        $barangs = Barang::where('work_unit_id', $workUnitId)
            ->orderBy('nama_barang')
            ->get()
            ->map(function($barang) {
                return [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'kategori' => $barang->kategori,
                    'stok' => $barang->stok,
                    'harga_beli' => $barang->harga_beli,
                ];
            });

        return \Inertia\Inertia::render('Admin/OpnameStock/Barang', [
            'toko' => $workUnit,
            'stockOpnames' => $stockOpnames,
            'barangs' => $barangs,
            'filters' => [
                'search' => $search,
                'period' => $period,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]
        ]);
    }

    /**
     * Get stock opname history for a specific barang
     */
    public function getHistory(Request $request, $barangId)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = StockOpname::where('barang_id', $barangId)
            ->with(['user', 'workUnit'])
            ->byPeriod($period, $startDate, $endDate)
            ->orderBy('opname_date', 'desc');

        $opnames = $query->get();

        return response()->json($opnames);
    }

    /**
     * Store new stock opname record
     */
    public function store(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'opname_date' => 'required|date',
            'stock_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'expired_date' => 'nullable|date|after:today',
        ]);

        // Get barang untuk mendapatkan stock awal dan harga pokok
        $barang = Barang::findOrFail($validated['barang_id']);

        $stockAwal = $barang->stok;
        $stockFisik = $validated['stock_fisik'];
        $selisih = $stockFisik - $stockAwal;
        $selisihRupiah = $selisih * $barang->harga_beli;

        $stockOpname = StockOpname::create([
            'barang_id' => $validated['barang_id'],
            'work_unit_id' => $workUnitId,
            'user_id' => auth()->id(),
            'opname_date' => $validated['opname_date'],
            'stock_awal' => $stockAwal,
            'stock_fisik' => $stockFisik,
            'selisih' => $selisih,
            'harga_pokok' => $barang->harga_beli,
            'selisih_rupiah' => $selisihRupiah,
            'keterangan' => $validated['keterangan'] ?? null,
            'expired_date' => $validated['expired_date'] ?? null,
            'status' => 'approved', // Auto approve for now
        ]);

        // Update stock barang sesuai hasil opname
        $barang->update(['stok' => $stockFisik]);

        return redirect()->back()->with('success', 'Stock opname berhasil disimpan dan stock diupdate');
    }

    /**
     * Update stock opname record
     */
    public function update(Request $request, $workUnitId, StockOpname $stockOpname)
    {
        $validated = $request->validate([
            'stock_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'expired_date' => 'nullable|date|after:today',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $barang = $stockOpname->barang;
        $stockFisik = $validated['stock_fisik'];
        $selisih = $stockFisik - $stockOpname->stock_awal;
        $selisihRupiah = $selisih * $stockOpname->harga_pokok;

        $stockOpname->update([
            'stock_fisik' => $stockFisik,
            'selisih' => $selisih,
            'selisih_rupiah' => $selisihRupiah,
            'keterangan' => $validated['keterangan'] ?? $stockOpname->keterangan,
            'expired_date' => $validated['expired_date'] ?? $stockOpname->expired_date,
            'status' => $validated['status'] ?? $stockOpname->status,
        ]);

        // Update stock barang jika approved
        if ($stockOpname->status === 'approved') {
            $barang->update(['stok' => $stockFisik]);
        }

        return redirect()->back()->with('success', 'Stock opname berhasil diupdate');
    }

    /**
     * Delete stock opname record
     */
    public function destroy($workUnitId, StockOpname $stockOpname)
    {
        $stockOpname->delete();

        return redirect()->back()->with('success', 'Stock opname berhasil dihapus');
    }

    /**
     * Get summary statistics for stock opname
     */
    public function getSummary(Request $request, $workUnitId)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = StockOpname::where('work_unit_id', $workUnitId)
            ->byPeriod($period, $startDate, $endDate);

        $summary = [
            'total_opnames' => $query->count(),
            'total_selisih_plus' => $query->where('selisih', '>', 0)->sum('selisih'),
            'total_selisih_minus' => $query->where('selisih', '<', 0)->sum('selisih'),
            'total_selisih_rupiah_plus' => $query->where('selisih_rupiah', '>', 0)->sum('selisih_rupiah'),
            'total_selisih_rupiah_minus' => $query->where('selisih_rupiah', '<', 0)->sum('selisih_rupiah'),
        ];

        return response()->json($summary);
    }

    /**
     * Import multiple stock opname records from CSV
     */
    public function import(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'data' => 'required|array|min:1',
            'data.*.barang_id' => 'required|exists:barangs,id',
            'data.*.stock_fisik' => 'required|integer|min:0',
            'data.*.keterangan' => 'nullable|string|max:255',
        ]);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        \DB::beginTransaction();

        try {
            foreach ($validated['data'] as $index => $item) {
                try {
                    $barang = Barang::findOrFail($item['barang_id']);

                    if ($barang->work_unit_id != $workUnitId) {
                        $errors[] = "Baris " . ($index + 1) . ": Barang tidak ditemukan di unit kerja ini";
                        $skipped++;
                        continue;
                    }

                    $stockAwal = $barang->stok;
                    $stockFisik = $item['stock_fisik'];
                    $selisih = $stockFisik - $stockAwal;
                    $selisihRupiah = $selisih * $barang->harga_beli;

                    StockOpname::create([
                        'barang_id' => $item['barang_id'],
                        'work_unit_id' => $workUnitId,
                        'user_id' => auth()->id(),
                        'opname_date' => now(),
                        'stock_awal' => $stockAwal,
                        'stock_fisik' => $stockFisik,
                        'selisih' => $selisih,
                        'harga_pokok' => $barang->harga_beli,
                        'selisih_rupiah' => $selisihRupiah,
                        'keterangan' => $item['keterangan'] ?? 'Import CSV',
                        'status' => 'approved',
                    ]);

                    $barang->update(['stok' => $stockFisik]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            \DB::commit();

            $message = "Berhasil mengimpor {$imported} stock opname";
            if ($skipped > 0) {
                $message .= " ({$skipped} dilewati)";
            }

            return redirect()->back()->with('success', $message)->with('import_warnings', $errors);

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Import stock opname error', [
                'work_unit_id' => $workUnitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Auto import stock opname with custom opname date
     */
    public function autoImport(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'data' => 'required|array|min:1',
            'data.*.barang_id' => 'required|exists:barangs,id',
            'data.*.stock_fisik' => 'required|integer|min:0',
            'data.*.keterangan' => 'nullable|string|max:255',
            'opname_date' => 'required|date',
        ]);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        \DB::beginTransaction();

        try {
            foreach ($validated['data'] as $index => $item) {
                try {
                    $barang = Barang::findOrFail($item['barang_id']);

                    if ($barang->work_unit_id != $workUnitId) {
                        $errors[] = "Item " . ($index + 1) . ": Barang tidak ditemukan di unit kerja ini";
                        $skipped++;
                        continue;
                    }

                    $stockAwal = $barang->stok;
                    $stockFisik = $item['stock_fisik'];
                    $selisih = $stockFisik - $stockAwal;
                    $selisihRupiah = $selisih * $barang->harga_beli;

                    StockOpname::create([
                        'barang_id' => $item['barang_id'],
                        'work_unit_id' => $workUnitId,
                        'user_id' => auth()->id(),
                        'opname_date' => $validated['opname_date'],
                        'stock_awal' => $stockAwal,
                        'stock_fisik' => $stockFisik,
                        'selisih' => $selisih,
                        'harga_pokok' => $barang->harga_beli,
                        'selisih_rupiah' => $selisihRupiah,
                        'keterangan' => $item['keterangan'] ?? 'Auto Opname',
                        'status' => 'approved',
                    ]);

                    $barang->update(['stok' => $stockFisik]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Item " . ($index + 1) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            \DB::commit();

            $message = "Berhasil memproses {$imported} auto opname";
            if ($skipped > 0) {
                $message .= " ({$skipped} dilewati)";
            }

            return redirect()->back()->with('success', $message)->with('import_warnings', $errors);

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Auto opname error', [
                'work_unit_id' => $workUnitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses auto opname: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF report for stock opname
     */
    /**
     * Export stock opname to CSV (ALL data with filters)
     */
    public function exportCsv(Request $request, $workUnitId)
    {
        $workUnit = \App\Models\WorkUnit::findOrFail($workUnitId);

        $search = $request->get('search');
        $period = $request->get('period');
        $sortBy = $request->get('sort_by', 'waktu');
        $sortOrder = $request->get('sort_order', 'desc');

        // Build query (same as index but without pagination)
        $query = StockOpname::where('work_unit_id', $workUnitId)
            ->with(['barang', 'user']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('barang', function($subQ) use ($search) {
                    $subQ->where('kode_barang', 'like', "%{$search}%")
                         ->orWhere('nama_barang', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Apply period filter
        if ($period) {
            $now = now();
            switch ($period) {
                case 'today':
                    $query->whereDate('opname_date', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('opname_date', [
                        $now->startOfWeek()->toDateString(),
                        $now->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('opname_date', $now->month)
                          ->whereYear('opname_date', $now->year);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereMonth('opname_date', $lastMonth->month)
                          ->whereYear('opname_date', $lastMonth->year);
                    break;
            }
        }

        // Apply sorting
        if ($sortBy === 'alfabetis') {
            $query->join('barangs', 'stock_opnames.barang_id', '=', 'barangs.id')
                  ->orderBy('barangs.nama_barang', $sortOrder)
                  ->select('stock_opnames.*');
        } else {
            $query->orderBy('opname_date', $sortOrder);
        }

        // Get ALL data (no pagination, but limit to 10000 for safety)
        $stockOpnames = $query->limit(10000)->get();

        // Generate CSV
        $filename = 'Stock_Opname_' . str_replace(' ', '_', $workUnit->name) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($stockOpnames) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'No',
                'Tanggal SO',
                'PLU',
                'Deskripsi Barang',
                'Kategori',
                'COGS',
                'Stock Awal',
                'Penyesuaian',
                'Selisih Qty',
                'Harga Pokok',
                'Selisih Rupiah',
                'Expired Date',
                'Keterangan',
                'Status',
                'Petugas'
            ]);

            // Data rows
            foreach ($stockOpnames as $index => $opname) {
                fputcsv($file, [
                    $index + 1,
                    $opname->opname_date,
                    $opname->barang->kode_barang ?? '-',
                    $opname->barang->nama_barang ?? '-',
                    $opname->barang->kategori ?? '-',
                    $opname->harga_pokok,
                    $opname->stock_awal,
                    $opname->stock_fisik,
                    $opname->selisih,
                    $opname->harga_pokok,
                    $opname->selisih_rupiah,
                    $opname->expired_date ?? '-',
                    $opname->keterangan ?? '-',
                    $opname->status,
                    $opname->user->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadPdf(Request $request, $workUnitId)
    {
        // Increase memory limit for PDF generation
        ini_set('memory_limit', '1024M');
        set_time_limit(300); // 5 minutes

        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'sort_by' => 'nullable|in:waktu,alfabetis',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        $params = [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'sort_by' => $validated['sort_by'] ?? 'waktu',
            'sort_order' => $validated['sort_order'] ?? 'desc',
        ];

        try {
            // Generate report data
            $pdfService = new StockOpnameReportPdfService();
            $data = $pdfService->generateReportData($workUnitId, $params);

            // Generate PDF
            $pdf = Pdf::loadView('pdf.stock-opname', ['data' => $data]);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'Laporan_Stock_Opname_' . str_replace(' ', '_', $data['work_unit']) . '_' . date('Ymd_His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF generation error', [
                'work_unit_id' => $workUnitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal generate PDF. Data terlalu banyak, silakan gunakan filter periode untuk mengurangi jumlah data.');
        }
    }
}
