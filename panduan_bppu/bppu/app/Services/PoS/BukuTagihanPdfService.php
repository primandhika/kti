<?php

namespace App\Services\PoS;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuTagihanPdfService
{
    protected $supplierReportService;

    public function __construct(SupplierReportService $supplierReportService)
    {
        $this->supplierReportService = $supplierReportService;
    }

    public function generatePdf(array $params, User $user, $workUnits)
    {
        $data = $this->supplierReportService->getBukuTagihan($params, $user, $workUnits);

        $workUnitId = $params['work_unit_id'] ?? null;
        $workUnit = $workUnitId ? $workUnits->firstWhere('id', $workUnitId) : null;

        $periode = $this->formatPeriode($params);
        $filters = $this->formatFilters($params);

        $pdfData = [
            'tagihan' => $data['transaksi'],
            'summary' => $data['summary'],
            'workUnit' => $workUnit,
            'periode' => $periode,
            'filters' => $filters,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'generatedBy' => $user->name,
        ];

        $pdf = Pdf::loadView('pdf.buku-tagihan', $pdfData);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'buku-tagihan-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    protected function formatPeriode($params)
    {
        if (!empty($params['date_from']) && !empty($params['date_to'])) {
            $dateFrom = date('d/m/Y', strtotime($params['date_from']));
            $dateTo = date('d/m/Y', strtotime($params['date_to']));
            return $dateFrom . ' - ' . $dateTo;
        }

        return 'Semua Periode';
    }

    protected function formatFilters($params)
    {
        $filters = [];

        if (!empty($params['search'])) {
            $filters[] = 'Pencarian: ' . $params['search'];
        }

        if (!empty($params['verified_filter'])) {
            $status = $params['verified_filter'] === 'verified' ? 'Terverifikasi' : 'Belum Diverifikasi';
            $filters[] = 'Status: ' . $status;
        }

        return $filters;
    }
}
