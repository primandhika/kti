<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use App\Models\KategoriTransaksi;
use App\Models\MemberPoint;
use App\Models\TransaksiKas;
use App\Models\Voucher;
use App\Models\WorkUnit;
use App\Services\RedeemService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RedeemPoinController extends Controller
{
    protected RedeemService $redeemService;

    public function __construct(RedeemService $redeemService)
    {
        $this->redeemService = $redeemService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'dari', 'sampai']);
        $mode = $request->get('mode') === 'campaign' ? 'campaign' : 'kode';

        $redeems = $mode === 'campaign'
            ? $this->redeemService->getRedeemsByCampaign($filters)
            : $this->redeemService->getRedeems($filters);
        $stats = $this->redeemService->getStats($filters);

        // Paginate manual (data digabung dari 2 sumber)
        $perPage = 20;
        $currentPage = (int) $request->get('page', 1);
        $paginated = new LengthAwarePaginator(
            $redeems->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $redeems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Diskon/RedeemPoin', [
            'redeems' => $paginated,
            'stats' => $stats,
            'mode' => $mode,
            'filters' => $filters,
            'bukuKasList' => $this->getBukuKasList(),
            'workUnits' => $this->getWorkUnits(),
            'kategoriList' => $this->getKategoriList(),
        ]);
    }

    /**
     * Catat 1 entri redeem (voucher/poin) ke buku kas sebagai transaksi.
     */
    public function catatKeBukuKas(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|in:voucher,poin',
            'redeem_id' => 'required|integer',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'jenis_transaksi' => 'nullable|string|max:255',
            'unit_kerja_id' => 'nullable|exists:work_units,id',
            'deskripsi' => 'required|string',
            'pemasukan' => 'nullable|numeric|min:0',
            'pengeluaran' => 'nullable|numeric|min:0',
            'bukti_transaksi_type' => 'nullable|in:upload,link',
            'bukti_transaksi' => 'nullable|image|max:2048',
            'bukti_transaksi_link' => 'nullable|url|max:500',
            'bukti_aktivitas_type' => 'nullable|in:upload,link',
            'bukti_aktivitas' => 'nullable|image|max:2048',
            'bukti_aktivitas_link' => 'nullable|url|max:500',
        ]);

        $user = auth()->user();

        // Ambil record sumber & pastikan belum tercatat
        $sumber = $validated['source'] === 'voucher'
            ? Voucher::find($validated['redeem_id'])
            : MemberPoint::where('type', 'redeem')->find($validated['redeem_id']);

        if (!$sumber) {
            return back()->with('error', 'Data redeem tidak ditemukan.');
        }
        if ($sumber->transaksi_kas_id !== null) {
            return back()->with('error', 'Redeem ini sudah dicatat ke buku kas.');
        }

        $transaksi = TransaksiKas::create($this->buildTransaksiData($request, $validated));

        $sumber->update(['transaksi_kas_id' => $transaksi->id]);

        return back()->with('success', 'Potongan berhasil dicatat ke buku kas.');
    }

    /**
     * Catat 1 campaign (Potongan) sekaligus: semua voucher terpakai yang belum
     * tercatat dilink ke satu transaksi buku kas.
     */
    public function catatCampaignKeBukuKas(Request $request)
    {
        $validated = $request->validate([
            'potongan_id' => 'required|exists:potongan,id',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'jenis_transaksi' => 'nullable|string|max:255',
            'unit_kerja_id' => 'nullable|exists:work_units,id',
            'deskripsi' => 'required|string',
            'pemasukan' => 'nullable|numeric|min:0',
            'pengeluaran' => 'nullable|numeric|min:0',
            'bukti_transaksi_type' => 'nullable|in:upload,link',
            'bukti_transaksi' => 'nullable|image|max:2048',
            'bukti_transaksi_link' => 'nullable|url|max:500',
            'bukti_aktivitas_type' => 'nullable|in:upload,link',
            'bukti_aktivitas' => 'nullable|image|max:2048',
            'bukti_aktivitas_link' => 'nullable|url|max:500',
        ]);

        $vouchers = Voucher::where('potongan_id', $validated['potongan_id'])
            ->where('status', 'used')
            ->whereNull('transaksi_kas_id')
            ->get();

        if ($vouchers->isEmpty()) {
            return back()->with('error', 'Tidak ada voucher campaign ini yang bisa dicatat (semua sudah tercatat).');
        }

        $transaksi = TransaksiKas::create($this->buildTransaksiData($request, $validated));

        Voucher::whereIn('id', $vouchers->pluck('id'))
            ->update(['transaksi_kas_id' => $transaksi->id]);

        return back()->with('success', 'Campaign berhasil dicatat: ' . $vouchers->count() . ' voucher ke buku kas.');
    }

    /**
     * Susun payload TransaksiKas dari request (termasuk handling bukti).
     */
    protected function buildTransaksiData(Request $request, array $validated): array
    {
        $data = [
            'buku_kas_id' => $validated['buku_kas_id'],
            'source_type' => 'redeem',
            'tanggal' => $validated['tanggal'],
            'kategori' => $validated['kategori'],
            'jenis_transaksi' => $validated['jenis_transaksi'] ?? null,
            'unit_kerja_id' => $validated['unit_kerja_id'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'pemasukan' => $validated['pemasukan'] ?? 0,
            'pengeluaran' => $validated['pengeluaran'] ?? 0,
        ];

        // Bukti transaksi
        if ($request->input('bukti_transaksi_type') === 'link') {
            $data['bukti_transaksi_type'] = 'link';
            $data['bukti_transaksi_link'] = $request->input('bukti_transaksi_link');
        } elseif ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti-transaksi', 'public');
            $data['bukti_transaksi_type'] = 'upload';
        }

        // Bukti aktivitas
        if ($request->input('bukti_aktivitas_type') === 'link') {
            $data['bukti_aktivitas_type'] = 'link';
            $data['bukti_aktivitas_link'] = $request->input('bukti_aktivitas_link');
        } elseif ($request->hasFile('bukti_aktivitas')) {
            $data['bukti_aktivitas'] = $request->file('bukti_aktivitas')->store('bukti-aktivitas', 'public');
            $data['bukti_aktivitas_type'] = 'upload';
        }

        return $data;
    }

    /**
     * Batalkan pencatatan redeem dari buku kas.
     */
    public function hapusCatatan(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|in:voucher,poin',
            'redeem_id' => 'required|integer',
        ]);

        $sumber = $validated['source'] === 'voucher'
            ? Voucher::find($validated['redeem_id'])
            : MemberPoint::where('type', 'redeem')->find($validated['redeem_id']);

        if (!$sumber || $sumber->transaksi_kas_id === null) {
            return back()->with('error', 'Catatan buku kas tidak ditemukan.');
        }

        $transaksiId = $sumber->transaksi_kas_id;
        $transaksi = TransaksiKas::find($transaksiId);

        // Lepas SEMUA voucher/poin yang terhubung ke transaksi ini
        // (satu transaksi bisa mencakup banyak voucher untuk mode campaign).
        Voucher::where('transaksi_kas_id', $transaksiId)->update(['transaksi_kas_id' => null]);
        MemberPoint::where('transaksi_kas_id', $transaksiId)->update(['transaksi_kas_id' => null]);
        $transaksi?->delete();

        return back()->with('success', 'Catatan buku kas berhasil dibatalkan.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['search', 'dari', 'sampai']);
        $redeems = $this->redeemService->getRedeems($filters);

        $filename = 'redeem-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($redeems) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'Sumber', 'Member', 'Email', 'Kode Voucher', 'Poin', 'Nilai Potongan (Rp)', 'No. Transaksi', 'Unit Kerja', 'Keterangan']);

            foreach ($redeems as $r) {
                fputcsv($handle, [
                    $r['tanggal'] ? \Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y H:i') : '-',
                    $r['source'] === 'voucher' ? 'Voucher' : 'Redeem Poin',
                    $r['member_name'] ?? '-',
                    $r['member_email'] ?? '-',
                    $r['kode_voucher'] ?? '-',
                    $r['poin'] ?? '-',
                    $r['nilai_potongan'] ?? 0,
                    $r['nomor_transaksi'] ?? '-',
                    $r['work_unit_name'] ?? '-',
                    $r['keterangan'] ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function getBukuKasList()
    {
        return BukuKas::with(['jenisBukuKas', 'user'])->get()->map(fn ($buku) => [
            'id' => $buku->id,
            'nama' => $buku->nama,
            'keterangan' => $buku->keterangan,
            'user_name' => $buku->user?->name,
            'jenis_buku_kas' => $buku->jenisBukuKas ? [
                'id' => $buku->jenisBukuKas->id,
                'nama' => $buku->jenisBukuKas->nama,
                'kode' => $buku->jenisBukuKas->kode,
                'warna' => $buku->jenisBukuKas->warna,
            ] : null,
        ]);
    }

    protected function getWorkUnits()
    {
        return WorkUnit::where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name', 'unit_id']);
    }

    protected function getKategoriList()
    {
        return KategoriTransaksi::active()->ordered()->get()->map(fn ($kat) => [
            'id' => $kat->id,
            'nama' => $kat->nama,
            'jenis' => $kat->jenis,
            'kode_akun' => $kat->kode_akun,
        ]);
    }
}
