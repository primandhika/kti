<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use App\Services\PoS\BuyerService;
use App\Services\PoS\PenjualanReportService;
use App\Services\PoS\PenjualanReportPdfService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanReportController extends Controller
{
    protected $reportService;
    protected $buyerService;
    protected $pdfService;

    public function __construct(
        PenjualanReportService $reportService,
        BuyerService $buyerService,
        PenjualanReportPdfService $pdfService
    ) {
        $this->reportService = $reportService;
        $this->buyerService = $buyerService;
        $this->pdfService = $pdfService;
    }

    /**
     * Rekap Penjualan - untuk Kantin & Sysadmin
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Get parameters
        $workUnitIdParam = $request->get('work_unit_id');
        // Handle "Semua Unit" atau empty string sebagai null
        if ($workUnitIdParam === 'Semua Unit' || $workUnitIdParam === '' || $workUnitIdParam === 'null') {
            $selectedWorkUnitId = null;
        } else {
            $selectedWorkUnitId = $workUnitIdParam ?: $workUnits->first()?->id;
        }

        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $verifiedOnly = $request->get('verified_only', false);
        $search = $request->get('search');

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verified_only' => $verifiedOnly,
            'search' => $search,
        ];

        $data = $this->reportService->getRekapPenjualan($params, $user, $workUnits);

        // Get paginated results
        $penjualans = $data['query']->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20)
            ->appends($request->only(['work_unit_id', 'start_date', 'end_date', 'verified_only', 'search']));

        // Get buyers untuk assign member
        $buyers = $this->buyerService->getBuyers();

        // Get buku kas yang aktif untuk record modal
        $bukuKasQuery = \App\Models\BukuKas::with(['user', 'jenisBukuKas']);
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head')) {
            $bukuKasQuery->where('user_id', $user->id);
        }
        $bukuKasList = $bukuKasQuery->get()
            ->map(function ($buku) {
                return [
                    'id' => $buku->id,
                    'nama' => $buku->nama,
                    'keterangan' => $buku->keterangan,
                    'user_name' => $buku->user->name,
                    'jenis_buku_kas' => $buku->jenisBukuKas ? [
                        'id' => $buku->jenisBukuKas->id,
                        'nama' => $buku->jenisBukuKas->nama,
                        'kode' => $buku->jenisBukuKas->kode,
                        'warna' => $buku->jenisBukuKas->warna,
                    ] : null,
                ];
            });

        return Inertia::render('PoS/RekapPenjualan', [
            'workUnits' => $workUnits,
            'selectedWorkUnitId' => $selectedWorkUnitId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'penjualans' => $penjualans,
            'summary' => $data['summary'],
            'itemSummary' => $data['itemSummary'],
            'buyers' => $buyers,
            'bukuKasList' => $bukuKasList,
        ]);
    }

    /**
     * Export item summary to CSV
     */
    public function exportItemSummary(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'work_unit_id' => 'nullable|exists:work_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get work units
        if ($user->hasRole('sysadmin')) {
            $workUnits = WorkUnit::where('is_active', true)->pluck('id');
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->pluck('work_units.id');
        }

        $csvData = $this->reportService->exportItemSummary($validated, $user, $workUnits);
        $filename = 'ringkasan-item-' . $validated['start_date'] . '-' . $validated['end_date'] . '.csv';

        return response($csvData, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Transfer-Encoding', 'binary');
    }

    /**
     * Get summary of approved penjualan by work unit (for Buku Kas integration)
     */
    public function getApprovedSummaryByUnit()
    {
        $summary = $this->reportService->getApprovedSummaryByUnit();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get approved penjualan detail by work unit (for Buku Kas integration)
     */
    public function getApprovedByUnit($workUnitId)
    {
        $penjualan = $this->reportService->getApprovedByUnit($workUnitId);

        return response()->json([
            'success' => true,
            'penjualan' => $penjualan,
        ]);
    }

    /**
     * Generate PDF Laporan Rekap Penjualan
     */
    public function downloadPdf(Request $request)
    {
        $user = auth()->user();

        // Only officer and sysadmin can download PDF
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            abort(403, 'Anda tidak memiliki akses untuk download PDF');
        }

        // Get work units
        if ($user->hasRole('sysadmin') || $user->hasRole('officer')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        $selectedWorkUnitId = $request->get('work_unit_id');
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $params = [
            'work_unit_id' => $selectedWorkUnitId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $data = $this->pdfService->generateReportData($params, $user, $workUnits);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.rekap-penjualan', ['data' => $data]);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Rekap_Penjualan_' . $data['work_unit'] . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
