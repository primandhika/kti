<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use App\Services\PoS\SupplierReportService;
use App\Services\PoS\BukuTagihanPdfService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MitraReportController extends Controller
{
    protected $supplierReportService;
    protected $bukuTagihanPdfService;

    public function __construct(
        SupplierReportService $supplierReportService,
        BukuTagihanPdfService $bukuTagihanPdfService
    ) {
        $this->supplierReportService = $supplierReportService;
        $this->bukuTagihanPdfService = $bukuTagihanPdfService;
    }

    /**
     * Pembagian per Mitra - Akumulasi harga jual item dari setiap mitra
     */
    public function pembagianMitra(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get selected work unit dan date range
        $selectedWorkUnitId = $request->get('work_unit_id', $workUnits->first()?->id);
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $verifiedOnly = $request->get('verified_only', true); // Default true: hanya tampilkan yang sudah verified

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verified_only' => $verifiedOnly,
        ];

        $data = $this->supplierReportService->getPembagianMitra($params, $user, $workUnits);

        // Paginate manually
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedPembagian = new \Illuminate\Pagination\LengthAwarePaginator(
            $data['pembagian']->slice($offset, $perPage)->values(),
            $data['pembagian']->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Status pencairan per supplier untuk periode yang dicairkan dalam range filter
        // Hanya tampilkan 'sudah dicairkan' kalau riwayatnya memang untuk periode yang sedang dilihat
        $riwayatCair = \App\Models\PembagianMitraHistory::whereIn('status_pencairan', ['mengajukan', 'dicairkan'])
            ->when($startDate, fn ($q) => $q->where('periode_mulai', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('periode_selesai', '<=', $endDate))
            ->when($selectedWorkUnitId, fn ($q) => $q->where('work_unit_id', $selectedWorkUnitId))
            ->with('dicairkanOleh:id,name')
            ->orderBy('dicairkan_at', 'desc')
            ->get(['id', 'supplier_id', 'bagian_vendor', 'metode_pencairan', 'detail_transfer', 'dicairkan_at', 'dicairkan_oleh', 'status_pencairan'])
            ->keyBy('supplier_id');

        // Pengajuan pencairan dari mitra (status menunggu/diproses) — keyed by supplier_id
        $pengajuanPencairan = \App\Models\PengajuanPencairan::whereIn('status', ['menunggu', 'diproses'])
            ->get(['id', 'supplier_id', 'tanggal_dari', 'tanggal_sampai', 'metode', 'detail_rekening', 'catatan', 'status', 'created_at'])
            ->groupBy('supplier_id')
            ->map(fn ($items) => $items->values());

        return Inertia::render('PoS/PembagianMitra', [
            'workUnits' => $workUnits,
            'selectedWorkUnitId' => $selectedWorkUnitId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'verifiedOnly' => $verifiedOnly,
            'pembagian' => $paginatedPembagian,
            'summary' => $data['summary'],
            'riwayatCair' => $riwayatCair,
            'pengajuanPencairan' => $pengajuanPencairan,
        ]);
    }

    /**
     * Export Pembagian Mitra to CSV
     */
    public function exportPembagianMitra(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        $selectedWorkUnitId = $request->get('work_unit_id');
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $verifiedOnly = $request->get('verified_only', true); // Ikut filter verified_only

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verified_only' => $verifiedOnly,
        ];

        $csvData = $this->supplierReportService->exportPembagianMitra($params, $user, $workUnits);
        $filename = 'pembagian-mitra-' . $startDate . '-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($csvData) {
            echo $csvData;
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan riwayat pembagian mitra
     * Re-query semua data dari DB (bukan dari frontend) agar tidak terpengaruh pagination
     */
    public function simpanRiwayatPembagian(Request $request)
    {
        $request->validate([
            'work_unit_id' => 'nullable|exists:work_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'catatan' => 'nullable|string',
        ]);

        $user = auth()->user();
        $workUnitId = $request->work_unit_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $catatan = $request->catatan;

        // Get work units untuk access control
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = \App\Models\WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        $params = [
            'work_unit_id' => $workUnitId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verified_only' => true,
        ];

        // Re-query semua data dari DB — tidak bergantung pada data frontend/pagination
        $data = $this->supplierReportService->getPembagianMitra($params, $user, $workUnits);
        $pembagianData = $data['pembagian'];

        try {
            foreach ($pembagianData as $item) {
                if ($item['supplier_id'] === null) {
                    continue; // Skip "Tanpa Mitra"
                }

                // total_items = jumlah unique nama barang (sesuai baris di detail_items)
                $detailItems = $item['items'] ?? [];
                $totalUniqueItems = count($detailItems);

                \App\Models\PembagianMitraHistory::create([
                    'supplier_id' => $item['supplier_id'],
                    'work_unit_id' => $workUnitId,
                    'periode_mulai' => $startDate,
                    'periode_selesai' => $endDate,
                    'jenis_skema' => $item['skema_bisnis']['jenis_skema'] ?? 'supplier',
                    'skema_config' => $item['skema_bisnis'] ?? [],
                    'total_penjualan' => $item['total_penjualan'],
                    'total_qty' => $item['total_qty'],
                    'total_items' => $totalUniqueItems,
                    'bagian_vendor' => $item['pembagian']['bagian_vendor'] ?? 0,
                    'bagian_badan_usaha' => $item['pembagian']['bagian_badan_usaha'] ?? 0,
                    'detail_items' => $detailItems,
                    'status' => 'finalized',
                    'finalized_by' => $user->id,
                    'finalized_at' => now(),
                    'catatan' => $catatan,
                ]);
            }

            return redirect()->back()->with('success', 'Riwayat pembagian berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan riwayat: ' . $e->getMessage());
        }
    }

    /**
     * Lihat riwayat pembagian mitra
     */
    public function riwayatPembagian(Request $request)
    {
        $user = auth()->user();

        $query = \App\Models\PembagianMitraHistory::with(['supplier.user', 'workUnit', 'finalizedBy', 'dicairkanOleh'])
            ->orderByRaw("FIELD(status_pencairan, 'mengajukan', 'belum_dicairkan', 'dicairkan')")
            ->orderBy('periode_selesai', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by work unit if not sysadmin
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            $workUnitIds = $user->workUnits()->pluck('work_units.id');
            $query->whereIn('work_unit_id', $workUnitIds);
        }

        // Hitung summary dari SELURUH data (sebelum paginate)
        $summaryQuery = clone $query;
        $allData = $summaryQuery->get(['id', 'status_pencairan', 'bagian_vendor']);
        $summary = [
            'total'          => $allData->count(),
            'mengajukan'     => $allData->where('status_pencairan', 'mengajukan')->count(),
            'sudah_cair'     => $allData->where('status_pencairan', 'dicairkan')->count(),
            'total_dicairkan' => $allData->where('status_pencairan', 'dicairkan')->sum('bagian_vendor'),
        ];

        $riwayat = $query->paginate(20);

        // Parse detail_items dan attach work_unit names
        // Handle data lama yang mungkin double-encoded (json_encode manual + Eloquent cast)
        $riwayat->getCollection()->transform(function ($item) {
            $detailItems = $item->detail_items;

            // Handle double-encoded: raw string JSON
            if (is_string($detailItems)) {
                $detailItems = json_decode($detailItems, true);
            }
            // Handle double-encoded lagi: hasil decode masih string
            if (is_string($detailItems)) {
                $detailItems = json_decode($detailItems, true);
            }

            if (is_array($detailItems)) {
                $item->detail_items = collect($detailItems)->map(function ($detailItem) {
                    if (isset($detailItem['work_unit_id'])) {
                        $workUnit = \App\Models\WorkUnit::find($detailItem['work_unit_id']);
                        $detailItem['work_unit'] = $workUnit ? ['name' => $workUnit->name] : null;
                    }
                    return $detailItem;
                })->values()->toArray();
            } else {
                $item->detail_items = [];
            }

            return $item;
        });

        return Inertia::render('PoS/RiwayatPembagian', [
            'riwayat' => $riwayat,
            'summary' => $summary,
        ]);
    }

    /**
     * Hapus riwayat pembagian
     */
    public function hapusRiwayatPembagian($id)
    {
        $riwayat = \App\Models\PembagianMitraHistory::findOrFail($id);
        $riwayat->delete();

        return redirect()->back()->with('success', 'Riwayat berhasil dihapus!');
    }

    /**
     * Tandai dana sudah dicairkan
     */
    public function tandaiCair(Request $request, $id)
    {
        $request->validate([
            'metode_pencairan'  => 'required|in:tunai,transfer_bank',
            'detail_transfer'   => 'nullable|string|max:255',
            'catatan_pencairan' => 'nullable|string|max:500',
        ]);

        $riwayat = \App\Models\PembagianMitraHistory::findOrFail($id);

        if ($riwayat->status_pencairan === 'dicairkan') {
            return redirect()->back()->with('error', 'Dana sudah ditandai cair sebelumnya.');
        }

        $riwayat->update([
            'dana_cair'         => true,
            'status_pencairan'  => 'dicairkan',
            'metode_pencairan'  => $request->metode_pencairan,
            'detail_transfer'   => $request->metode_pencairan === 'transfer_bank' ? $request->detail_transfer : null,
            'dicairkan_oleh'    => auth()->id(),
            'dicairkan_at'      => now(),
            'catatan_pencairan' => $request->catatan_pencairan,
        ]);

        return redirect()->back()->with('success', 'Dana berhasil ditandai cair.');
    }

    /**
     * Batalkan tanda cair
     */
    public function batalkanCair($id)
    {
        $riwayat = \App\Models\PembagianMitraHistory::findOrFail($id);

        if ($riwayat->status_pencairan === 'belum_dicairkan') {
            return redirect()->back()->with('error', 'Dana belum dicairkan.');
        }

        $riwayat->update([
            'dana_cair'         => false,
            'status_pencairan'  => 'belum_dicairkan',
            'metode_pencairan'  => null,
            'detail_transfer'   => null,
            'dicairkan_oleh'    => null,
            'dicairkan_at'      => null,
            'catatan_pencairan' => null,
        ]);

        return redirect()->back()->with('success', 'Tanda cair berhasil dibatalkan.');
    }

    public function prosesPengajuanPencairan($id)
    {
        $pengajuan = \App\Models\PengajuanPencairan::findOrFail($id);
        $pengajuan->update(['status' => 'diproses', 'diproses_oleh' => auth()->id(), 'diproses_at' => now()]);
        return redirect()->back()->with('success', 'Pengajuan sedang diproses.');
    }

    public function selesaiPengajuanPencairan($id)
    {
        $pengajuan = \App\Models\PengajuanPencairan::findOrFail($id);
        $pengajuan->update(['status' => 'selesai', 'diproses_oleh' => auth()->id(), 'diproses_at' => now()]);
        return redirect()->back()->with('success', 'Pengajuan selesai dicairkan.');
    }

    public function tolakPengajuanPencairan(Request $request, $id)
    {
        $request->validate(['catatan_pengelola' => 'nullable|string|max:500']);
        $pengajuan = \App\Models\PengajuanPencairan::findOrFail($id);
        $pengajuan->update([
            'status'            => 'ditolak',
            'catatan_pengelola' => $request->catatan_pengelola,
            'diproses_oleh'     => auth()->id(),
            'diproses_at'       => now(),
        ]);
        return redirect()->back()->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Buku Tagihan - Daftar member dengan transaksi yang belum diapprove
     */
    public function bukuTagihan(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get filters
        $selectedWorkUnitId = $request->get('work_unit_id', null);
        $search = $request->get('search', null);
        $dateFrom = $request->get('date_from', null);
        $dateTo = $request->get('date_to', null);
        $verifiedFilter = $request->get('verified_filter', 'pending');
        $sortField = $request->get('sort_field', 'total_tagihan');
        $sortDirection = $request->get('sort_direction', 'desc');

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'verified_filter' => $verifiedFilter,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ];

        $data = $this->supplierReportService->getBukuTagihan($params, $user, $workUnits);

        // Paginate manually
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedTagihan = new \Illuminate\Pagination\LengthAwarePaginator(
            $data['transaksi']->slice($offset, $perPage)->values(),
            $data['transaksi']->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('PoS/BukuTagihan', [
            'workUnits' => $workUnits,
            'filters' => [
                'work_unit_id' => $selectedWorkUnitId,
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'verified_filter' => $verifiedFilter,
                'sort_field' => $sortField,
                'sort_direction' => $sortDirection,
            ],
            'tagihan' => $paginatedTagihan,
            'summary' => $data['summary'],
        ]);
    }

    /**
     * Export Buku Tagihan to CSV
     */
    public function exportBukuTagihan(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get filters
        $selectedWorkUnitId = $request->get('work_unit_id', null);
        $search = $request->get('search', null);
        $dateFrom = $request->get('date_from', null);
        $dateTo = $request->get('date_to', null);
        $verifiedFilter = $request->get('verified_filter', 'pending');

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'verified_filter' => $verifiedFilter,
        ];

        $transaksi = $this->supplierReportService->exportBukuTagihan($params, $user, $workUnits);

        // Generate CSV
        $filename = 'buku-tagihan-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transaksi) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, ['Tanggal', 'ID Transaksi', 'Member', 'Kode Member', 'Unit Kerja', 'Nominal Tagihan', 'Status Verifikasi']);

            // Data
            foreach ($transaksi as $t) {
                fputcsv($file, [
                    $t->tanggal_transaksi->format('d/m/Y H:i'),
                    $t->nomor_transaksi,
                    $t->buyer?->name ?? '-',
                    $t->buyer?->member_code ?? '-',
                    $t->workUnit?->name ?? '-',
                    $t->total,
                    $t->is_verified ? 'Verified' : 'Pending',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Buku Tagihan as PDF
     */
    public function downloadBukuTagihanPdf(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get filters
        $selectedWorkUnitId = $request->get('work_unit_id', null);
        $search = $request->get('search', null);
        $dateFrom = $request->get('date_from', null);
        $dateTo = $request->get('date_to', null);
        $verifiedFilter = $request->get('verified_filter', 'pending');
        $sortField = $request->get('sort_field', 'total_tagihan');
        $sortDirection = $request->get('sort_direction', 'desc');

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'verified_filter' => $verifiedFilter,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ];

        return $this->bukuTagihanPdfService->generatePdf($params, $user, $workUnits);
    }
}
