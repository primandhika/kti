<?php

namespace App\Http\Controllers;

use App\Services\LaporanKeuanganService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanKeuanganController extends Controller
{
    public function __construct(protected LaporanKeuanganService $service) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['sysadmin', 'head', 'officer'])) {
            abort(403);
        }

        $filters = $this->normalizeFilters($request);

        return Inertia::render('Admin/Dashboard/LaporanKeuangan/Index', [
            'filters'         => $filters,
            'summary'         => $this->service->getSummary($filters),
            'rekapBukuKas'    => $this->service->getRekapPerBukuKas($filters),
            'rekapJenis'      => $this->service->getRekapPerJenisBukuKas($filters),
            'rekapKategori'   => $this->service->getRekapPerKategori($filters),
            'rekapJenisTrx'   => $this->service->getRekapPerJenisTransaksi($filters),
            'trenBulanan'     => $this->service->getTrenBulanan($filters),
            'bukuKasList'     => $this->service->getBukuKasList(),
            'jenisBukuKasList' => $this->service->getJenisBukuKasList(),
            'kategoriList'    => $this->service->getKategoriList(),
            'jenisTransaksiList' => $this->service->getJenisTransaksiList(),
            'tahunList'       => $this->service->getTahunList(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['sysadmin', 'head', 'officer'])) {
            abort(403);
        }

        $filters = $this->normalizeFilters($request);

        $rows = $this->service->buildQuery($filters)
            ->selectRaw('
                transaksi_kas.transaction_id,
                transaksi_kas.tanggal,
                buku_kas.nama as nama_buku_kas,
                COALESCE(jenis_buku_kas.nama, "") as jenis_nama,
                transaksi_kas.kategori,
                transaksi_kas.jenis_transaksi,
                transaksi_kas.deskripsi,
                transaksi_kas.pemasukan,
                transaksi_kas.pengeluaran,
                transaksi_kas.source_type
            ')
            ->orderBy('transaksi_kas.tanggal', 'desc')
            ->get();

        $filename = 'laporan-keuangan-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'ID Transaksi', 'Tanggal', 'Buku Kas', 'Jenis Buku Kas',
                'Kategori', 'Jenis Transaksi', 'Deskripsi',
                'Pemasukan', 'Pengeluaran', 'Sumber',
            ]);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->transaction_id,
                    $row->tanggal,
                    $row->nama_buku_kas,
                    $row->jenis_nama,
                    $row->kategori,
                    $row->jenis_transaksi,
                    $row->deskripsi,
                    $row->pemasukan,
                    $row->pengeluaran,
                    $row->source_type,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detail(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['sysadmin', 'head', 'officer'])) {
            abort(403);
        }

        $filters   = $this->normalizeFilters($request);
        $sortField = $request->get('sort_field', 'tanggal');
        $sortDir   = $request->get('sort_direction', 'desc');
        $perPage   = (int) $request->get('per_page', 20);

        $transaksi = $this->service->getDetailTransaksi($filters, $perPage, $sortField, $sortDir);

        return response()->json([
            'transaksi' => $transaksi,
            'filters'   => $filters,
        ]);
    }

    protected function normalizeFilters(Request $request): array
    {
        return array_filter([
            'date_from'         => $request->get('date_from'),
            'date_to'           => $request->get('date_to'),
            'bulan'             => $request->get('bulan'),
            'tahun'             => $request->get('tahun'),
            'buku_kas_id'       => $request->get('buku_kas_id'),
            'jenis_buku_kas_id' => $request->get('jenis_buku_kas_id'),
            'kategori'          => $request->get('kategori'),
            'jenis_transaksi'   => $request->get('jenis_transaksi'),
            'tipe'              => $request->get('tipe'),
            'source_type'       => $request->get('source_type'),
            'include_rekap'     => $request->boolean('include_rekap') ? '1' : null,
        ], fn($v) => $v !== null && $v !== '');
    }
}
