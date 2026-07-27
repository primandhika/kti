<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Potongan;
use App\Models\User;
use App\Models\Voucher;
use App\Services\PoS\VoucherService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorPNG;

class VoucherController extends Controller
{
    public function __construct(private VoucherService $voucherService) {}

    public function index(Request $request, Potongan $potongan)
    {
        $query = $potongan->vouchers()
            ->with(['usedBy:id,name', 'penjualan:id,nomor_transaksi', 'assignedTo:id,name,nim,member_code']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_voucher', 'like', "%{$request->search}%");
        }

        $vouchers = $query->latest()->paginate(20)->withQueryString();

        $barangList = [];
        if (!empty($potongan->barang_ids)) {
            $barangList = \App\Models\Barang::whereIn('id', $potongan->barang_ids)
                ->select('id', 'nama_barang', 'kode_barang')
                ->get();
        }

        return Inertia::render('Admin/Diskon/Vouchers', [
            'potongan' => $potongan,
            'vouchers' => $vouchers,
            'filters' => $request->only(['status', 'search']),
            'barangList' => $barangList,
            'stats' => [
                'draft' => $potongan->vouchers()->where('status', 'draft')->count(),
                'printed' => $potongan->vouchers()->where('status', 'printed')->count(),
                'used' => $potongan->vouchers()->where('status', 'used')->count(),
                'assigned' => $potongan->vouchers()->whereNotNull('assigned_to')->count(),
                'sisa_kuota' => $potongan->kuota_voucher - $potongan->vouchers()->count(),
            ],
        ]);
    }

    public function generate(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1|max:100',
        ]);

        try {
            $this->voucherService->generateVouchers(
                $potongan,
                $validated['jumlah'],
                auth()->id()
            );

            return back()->with('success', "{$validated['jumlah']} voucher berhasil dibuat.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function assign(Request $request, Potongan $potongan, Voucher $voucher)
    {
        if ($voucher->potongan_id !== $potongan->id) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $voucher->update([
            'assigned_to' => $validated['user_id'],
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Voucher berhasil di-assign ke member.');
    }

    public function assignBulk(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'voucher_ids' => 'required|array|min:1',
            'voucher_ids.*' => 'integer|exists:vouchers,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $count = Voucher::whereIn('id', $validated['voucher_ids'])
            ->where('potongan_id', $potongan->id)
            ->update([
                'assigned_to' => $validated['user_id'],
                'assigned_at' => now(),
            ]);

        return back()->with('success', "{$count} voucher berhasil di-assign ke member.");
    }

    public function unassign(Potongan $potongan, Voucher $voucher)
    {
        if ($voucher->potongan_id !== $potongan->id) {
            abort(403);
        }

        if ($voucher->status === 'used') {
            return back()->withErrors(['error' => 'Voucher yang sudah dipakai tidak bisa di-unassign.']);
        }

        $voucher->update(['assigned_to' => null, 'assigned_at' => null]);

        return back()->with('success', 'Assignment voucher berhasil dihapus.');
    }

    public function print(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'voucher_ids' => 'required|array|min:1',
            'voucher_ids.*' => 'integer|exists:vouchers,id',
        ]);

        $valid = Voucher::whereIn('id', $validated['voucher_ids'])
            ->where('potongan_id', $potongan->id)
            ->where('status', 'draft')
            ->count();

        if ($valid !== count($validated['voucher_ids'])) {
            return back()->withErrors(['error' => 'Beberapa voucher tidak valid atau sudah dicetak.']);
        }

        $jumlah = $this->voucherService->printVouchers($validated['voucher_ids']);

        return back()->with('success', "{$jumlah} voucher berhasil ditandai sebagai dicetak.");
    }

    public function printAll(Request $request, Potongan $potongan)
    {
        $ids = $potongan->vouchers()
            ->where('status', 'draft')
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return back()->withErrors(['error' => 'Tidak ada voucher draft untuk dicetak.']);
        }

        $jumlah = $this->voucherService->printVouchers($ids);

        return back()->with('success', "{$jumlah} voucher berhasil ditandai sebagai dicetak.");
    }

    public function printPdf(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'voucher_ids' => 'required|array|min:1',
            'voucher_ids.*' => 'integer|exists:vouchers,id',
            'format' => 'required|in:pdf,thermal',
        ]);

        $vouchers = Voucher::whereIn('id', $validated['voucher_ids'])
            ->where('potongan_id', $potongan->id)
            ->with('assignedTo:id,name,nim,member_code')
            ->get();

        if ($vouchers->isEmpty()) {
            abort(404);
        }

        $format = $validated['format'];
        $view = $format === 'thermal' ? 'voucher.thermal' : 'voucher.pdf';

        // Generate barcode untuk setiap voucher
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $vouchers->each(function ($v) use ($barcodeGenerator) {
            try {
                $v->barcode_base64 = base64_encode(
                    $barcodeGenerator->getBarcode($v->kode_voucher, BarcodeGeneratorPNG::TYPE_CODE_128, 2, 50)
                );
            } catch (\Exception $e) {
                $v->barcode_base64 = '';
            }
        });

        // Mark as printed
        $this->voucherService->printVouchers($vouchers->where('status', 'draft')->pluck('id')->toArray());

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
            'potongan' => $potongan,
            'vouchers' => $vouchers,
        ]);

        if ($format === 'thermal') {
            $pdf->setPaper([0, 0, 226.77, 800], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        $filename = 'voucher-' . $potongan->kode_potongan . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function destroy(Potongan $potongan, Voucher $voucher)
    {
        if ($voucher->potongan_id !== $potongan->id) {
            abort(403);
        }

        if ($voucher->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya voucher draft yang bisa dihapus.']);
        }

        $voucher->delete();

        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    public function memberVouchers(Request $request)
    {
        $userId = $request->get('user_id');
        if (!$userId) {
            return response()->json([]);
        }

        $vouchers = Voucher::where('assigned_to', $userId)
            ->where('status', 'printed')
            ->with('potongan:id,nama,tipe,nilai,minimum_transaksi,maksimum_potongan,berlaku_mulai,berlaku_sampai,is_active')
            ->get()
            ->filter(function ($v) {
                $p = $v->potongan;
                if (!$p || !$p->is_active) return false;
                if ($p->berlaku_sampai && $p->berlaku_sampai < today()) return false;
                if ($p->berlaku_mulai && $p->berlaku_mulai > today()) return false;
                return true;
            })
            ->values()
            ->map(fn($v) => [
                'id' => $v->id,
                'kode_voucher' => $v->kode_voucher,
                'nama_potongan' => $v->potongan->nama,
                'tipe' => $v->potongan->tipe,
                'nilai' => $v->potongan->nilai,
                'minimum_transaksi' => $v->potongan->minimum_transaksi,
                'maksimum_potongan' => $v->potongan->maksimum_potongan,
                'berlaku_sampai' => $v->potongan->berlaku_sampai
                    ? \Carbon\Carbon::parse($v->potongan->berlaku_sampai)->format('d/m/Y')
                    : null,
            ]);

        return response()->json($vouchers);
    }

    public function checkVoucher(Request $request)
    {
        $validated = $request->validate([
            'kode_voucher' => 'required|string',
            'total_transaksi' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::with('potongan')
            ->where('kode_voucher', strtoupper($validated['kode_voucher']))
            ->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak ditemukan.'], 404);
        }

        if ($voucher->status !== 'printed') {
            $statusLabel = match($voucher->status) {
                'draft' => 'belum dicetak',
                'used' => 'sudah digunakan',
                default => 'tidak valid',
            };
            return response()->json(['valid' => false, 'message' => "Voucher {$statusLabel}."]);
        }

        $potongan = $voucher->potongan;

        if (!$potongan->is_active) {
            return response()->json(['valid' => false, 'message' => 'Program potongan tidak aktif.']);
        }

        if ($potongan->berlaku_sampai && $potongan->berlaku_sampai < today()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah kadaluarsa.']);
        }

        if ($potongan->berlaku_mulai && $potongan->berlaku_mulai > today()) {
            return response()->json(['valid' => false, 'message' => 'Voucher belum berlaku.']);
        }

        $nilaiPotongan = $this->voucherService->hitungPotongan($potongan, $validated['total_transaksi']);

        if ($nilaiPotongan === 0.0) {
            return response()->json([
                'valid' => false,
                'message' => 'Total transaksi tidak memenuhi minimum Rp ' . number_format($potongan->minimum_transaksi, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'valid' => true,
            'voucher_id' => $voucher->id,
            'kode_voucher' => $voucher->kode_voucher,
            'nama_potongan' => $potongan->nama,
            'tipe' => $potongan->tipe,
            'nilai' => $potongan->nilai,
            'nilai_potongan' => $nilaiPotongan,
            'barang_ids' => $potongan->barang_ids,
        ]);
    }
}
