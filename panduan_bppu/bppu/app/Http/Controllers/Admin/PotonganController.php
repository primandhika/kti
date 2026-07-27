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
use Symfony\Component\HttpFoundation\StreamedResponse;

class PotonganController extends Controller
{
    public function __construct(private VoucherService $voucherService) {}

    public function index(Request $request)
    {
        $query = Potongan::withCount([
            'vouchers',
            'vouchers as draft_count' => fn($q) => $q->where('status', 'draft'),
            'vouchers as printed_count' => fn($q) => $q->where('status', 'printed'),
            'vouchers as used_count' => fn($q) => $q->where('status', 'used'),
        ])->with('createdBy:id,name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode_potongan', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $potongan = $query->latest()->paginate(15)->withQueryString();

        $barangOptions = Barang::where('is_active', true)
            ->select('id', 'nama_barang', 'kode_barang')
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('Admin/Diskon/Index', [
            'potongan' => $potongan,
            'filters' => $request->only(['search', 'tipe', 'status']),
            'barangOptions' => $barangOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'tipe' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0.01',
            'minimum_transaksi' => 'nullable|numeric|min:0',
            'maksimum_potongan' => 'nullable|numeric|min:0',
            'berlaku_mulai' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_mulai',
            'kuota_voucher' => 'required|integer|min:1|max:1000',
            'is_active' => 'boolean',
            'barang_ids' => 'nullable|array',
            'barang_ids.*' => 'integer|exists:barangs,id',
        ]);

        if ($validated['tipe'] === 'persen' && $validated['nilai'] > 100) {
            return back()->withErrors(['nilai' => 'Nilai persen tidak boleh lebih dari 100.']);
        }

        $validated['kode_potongan'] = $this->voucherService->generateKodePotongan();
        $validated['created_by'] = auth()->id();
        $validated['minimum_transaksi'] = $validated['minimum_transaksi'] ?? 0;
        $validated['barang_ids'] = !empty($validated['barang_ids']) ? $validated['barang_ids'] : null;

        Potongan::create($validated);

        return back()->with('success', 'Potongan berhasil dibuat.');
    }

    public function update(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'tipe' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0.01',
            'minimum_transaksi' => 'nullable|numeric|min:0',
            'maksimum_potongan' => 'nullable|numeric|min:0',
            'berlaku_mulai' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_mulai',
            'kuota_voucher' => 'required|integer|min:1|max:1000',
            'is_active' => 'boolean',
            'barang_ids' => 'nullable|array',
            'barang_ids.*' => 'integer|exists:barangs,id',
        ]);

        if ($validated['tipe'] === 'persen' && $validated['nilai'] > 100) {
            return back()->withErrors(['nilai' => 'Nilai persen tidak boleh lebih dari 100.']);
        }

        $jumlahVoucher = $potongan->vouchers()->count();
        if ($validated['kuota_voucher'] < $jumlahVoucher) {
            return back()->withErrors(['kuota_voucher' => "Kuota tidak boleh kurang dari jumlah voucher yang sudah ada ({$jumlahVoucher})."]);
        }

        $validated['minimum_transaksi'] = $validated['minimum_transaksi'] ?? 0;
        $validated['barang_ids'] = !empty($validated['barang_ids']) ? $validated['barang_ids'] : null;

        $potongan->update($validated);

        return back()->with('success', 'Potongan berhasil diperbarui.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'File CSV kosong atau tidak valid.']);
        }

        $headerNorm = array_map(fn($h) => strtolower(trim($h)), $header);
        $required = ['nama', 'tipe', 'nilai'];

        foreach ($required as $col) {
            if (!in_array($col, $headerNorm)) {
                fclose($handle);
                return back()->withErrors(['file' => "Kolom wajib tidak ditemukan: {$col}. Kolom yang diperlukan: nama, tipe, nilai."]);
            }
        }

        $colIdx = array_flip($headerNorm);
        $created = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) < count($required)) continue;

            $nama = trim($data[$colIdx['nama']] ?? '');
            $tipe = strtolower(trim($data[$colIdx['tipe']] ?? ''));
            $nilai = (float) ($data[$colIdx['nilai']] ?? 0);
            $deskripsi = trim($data[$colIdx['deskripsi'] ?? -1] ?? '');
            $minimumTransaksi = (float) ($data[$colIdx['minimum_transaksi'] ?? -1] ?? 0);
            $maksimumPotongan = isset($colIdx['maksimum_potongan']) ? ($data[$colIdx['maksimum_potongan']] ?: null) : null;
            $berlakuMulai = isset($colIdx['berlaku_mulai']) ? ($data[$colIdx['berlaku_mulai']] ?: null) : null;
            $berlakuSampai = isset($colIdx['berlaku_sampai']) ? ($data[$colIdx['berlaku_sampai']] ?: null) : null;
            $kuotaVoucher = (int) ($data[$colIdx['kuota_voucher'] ?? -1] ?? 1) ?: 1;

            if (empty($nama)) { $errors[] = "Baris {$row}: nama kosong."; continue; }
            if (!in_array($tipe, ['persen', 'nominal'])) { $errors[] = "Baris {$row}: tipe harus 'persen' atau 'nominal'."; continue; }
            if ($nilai <= 0) { $errors[] = "Baris {$row}: nilai harus lebih dari 0."; continue; }
            if ($tipe === 'persen' && $nilai > 100) { $errors[] = "Baris {$row}: nilai persen tidak boleh lebih dari 100."; continue; }

            // Tangani user yang di-assign dari kolom member_code / nim / email
            $assignedUsers = [];
            if (isset($colIdx['member_codes']) && !empty($data[$colIdx['member_codes']])) {
                $codes = array_map('trim', explode(';', $data[$colIdx['member_codes']]));
                $assignedUsers = User::whereIn('member_code', $codes)->pluck('id')->toArray();
            }

            $potongan = Potongan::create([
                'kode_potongan' => $this->voucherService->generateKodePotongan(),
                'nama' => $nama,
                'deskripsi' => $deskripsi ?: null,
                'tipe' => $tipe,
                'nilai' => $nilai,
                'minimum_transaksi' => $minimumTransaksi,
                'maksimum_potongan' => $maksimumPotongan ? (float) $maksimumPotongan : null,
                'berlaku_mulai' => $berlakuMulai ?: null,
                'berlaku_sampai' => $berlakuSampai ?: null,
                'kuota_voucher' => max(1, min($kuotaVoucher, 1000)),
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // Auto-generate voucher sejumlah kuota dan assign ke member jika ada
            $generatedVouchers = $this->voucherService->generateVouchers($potongan, $potongan->kuota_voucher, auth()->id());

            if (!empty($assignedUsers)) {
                foreach ($generatedVouchers as $idx => $voucher) {
                    if (isset($assignedUsers[$idx])) {
                        $voucher->update([
                            'assigned_to' => $assignedUsers[$idx],
                            'assigned_at' => now(),
                        ]);
                    }
                }
            }

            $created++;
        }

        fclose($handle);

        $msg = "{$created} potongan berhasil diimpor.";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' baris dilewati.';
        }

        return back()->with('success', $msg)->with('import_errors', $errors);
    }

    public function exportVoucher(Request $request): StreamedResponse
    {
        $query = Voucher::with(['potongan:id,nama,kode_potongan,tipe,nilai', 'usedBy:id,name', 'penjualan:id,nomor_transaksi', 'assignedTo:id,name,member_code'])
            ->join('potongan', 'vouchers.potongan_id', '=', 'potongan.id')
            ->select('vouchers.*');

        if ($request->filled('potongan_id')) {
            $query->where('vouchers.potongan_id', $request->potongan_id);
        }

        if ($request->filled('status')) {
            $query->where('vouchers.status', $request->status);
        }

        $vouchers = $query->orderBy('potongan.kode_potongan')->orderBy('vouchers.kode_voucher')->get();

        $filename = 'voucher-diskon-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($vouchers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode Voucher', 'Program Potongan', 'Kode Potongan', 'Tipe', 'Nilai', 'Status', 'Assigned Ke', 'Digunakan Oleh', 'No. Transaksi', 'Tgl Digunakan']);

            foreach ($vouchers as $v) {
                fputcsv($handle, [
                    $v->kode_voucher,
                    $v->potongan?->nama ?? '-',
                    $v->potongan?->kode_potongan ?? '-',
                    $v->potongan?->tipe ?? '-',
                    $v->potongan?->nilai ?? 0,
                    $v->status,
                    $v->assignedTo?->name ?? '-',
                    $v->usedBy?->name ?? '-',
                    $v->penjualan?->nomor_transaksi ?? '-',
                    $v->used_at ? \Carbon\Carbon::parse($v->used_at)->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroy(Potongan $potongan)
    {
        if ($potongan->vouchers()->whereIn('status', ['printed', 'used'])->exists()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus potongan yang vouchernya sudah dicetak atau dipakai.']);
        }

        $potongan->vouchers()->delete();
        $potongan->delete();

        return back()->with('success', 'Potongan berhasil dihapus.');
    }
}
