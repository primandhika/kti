<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\WorkUnit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = WorkUnit::tokoOrKantin()
            ->withCount('barangs')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $tenants = Supplier::where('tipe_mitra', 'tenant')
            ->with('penempatanWorkUnit')
            ->orderBy('nama')
            ->get()
            ->map(fn($s) => [
                'id'                       => $s->id,
                'kode_supplier'            => $s->kode_supplier,
                'nama'                     => $s->nama,
                'perusahaan'               => $s->perusahaan,
                'telepon'                  => $s->telepon,
                'biaya_kontribusi'         => $s->biaya_kontribusi,
                'penempatan_work_unit_id'  => $s->penempatan_work_unit_id,
                'penempatan_work_unit_nama'=> $s->penempatanWorkUnit?->name,
                'is_active'                => $s->is_active,
            ]);

        return Inertia::render('Admin/OpnameStock/Index', [
            'tokos'   => $tokos,
            'tenants' => $tenants,
        ]);
    }

    public function printKuitansi(Request $request, Supplier $supplier)
    {
        if ($supplier->tipe_mitra !== 'tenant') {
            abort(404);
        }

        $supplier->load('penempatanWorkUnit');

        $rawBulan = $request->query('bulan');
        if ($rawBulan && preg_match('/^\d{4}-\d{2}$/', $rawBulan)) {
            $bulan = \Carbon\Carbon::createFromFormat('Y-m', $rawBulan)->translatedFormat('F Y');
        } else {
            $bulan = now()->translatedFormat('F Y');
        }

        $tenant = [
            'kode_supplier'            => $supplier->kode_supplier,
            'nama'                     => $supplier->nama,
            'perusahaan'               => $supplier->perusahaan,
            'biaya_kontribusi'         => $supplier->biaya_kontribusi ?? 0,
            'penempatan_work_unit_nama'=> $supplier->penempatanWorkUnit?->name,
        ];

        $periodeCarbon = ($rawBulan && preg_match('/^\d{4}-\d{2}$/', $rawBulan))
            ? \Carbon\Carbon::createFromFormat('Y-m', $rawBulan)
            : now();
        $nomorKuitansi = 'BPPU/' . $periodeCarbon->format('Y') . '/' . $periodeCarbon->format('m') . '/TNT/' . str_pad($supplier->id, 4, '0', STR_PAD_LEFT);
        $tanggal = now()->translatedFormat('d F Y');
        $terbilang = $this->terbilang((int) $tenant['biaya_kontribusi']) . ' Rupiah';

        $pdf = Pdf::loadView('pdf.kuitansi-kontribusi-tenant', compact('tenant', 'bulan', 'nomorKuitansi', 'tanggal', 'terbilang'))
            ->setPaper('a5', 'landscape');

        return $pdf->stream('kuitansi-kontribusi-' . $supplier->kode_supplier . '.pdf');
    }

    private function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
                  'Sepuluh', 'Sebelas'];

        if ($angka < 12) return $huruf[$angka];
        if ($angka < 20) return $huruf[$angka - 10] . ' Belas';
        if ($angka < 100) return $huruf[(int)($angka / 10)] . ' Puluh' . ($angka % 10 ? ' ' . $huruf[$angka % 10] : '');
        if ($angka < 200) return 'Seratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        if ($angka < 1000) return $huruf[(int)($angka / 100)] . ' Ratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        if ($angka < 2000) return 'Seribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        if ($angka < 1000000) return $this->terbilang((int)($angka / 1000)) . ' Ribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        if ($angka < 1000000000) return $this->terbilang((int)($angka / 1000000)) . ' Juta' . ($angka % 1000000 ? ' ' . $this->terbilang($angka % 1000000) : '');
        return $this->terbilang((int)($angka / 1000000000)) . ' Miliar' . ($angka % 1000000000 ? ' ' . $this->terbilang($angka % 1000000000) : '');
    }
}
