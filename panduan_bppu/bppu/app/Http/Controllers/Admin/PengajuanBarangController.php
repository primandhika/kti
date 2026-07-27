<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PengajuanBarang;
use Illuminate\Http\Request;

class PengajuanBarangController extends Controller
{
    /**
     * Kantin mengajukan perubahan stok atau nama barang
     */
    public function store(Request $request, $workUnitId, $barangId)
    {
        $barang = Barang::where('id', $barangId)
            ->where('work_unit_id', $workUnitId)
            ->firstOrFail();

        $validated = $request->validate([
            'jenis' => 'required|in:stok,nama_barang,kode_barang',
            'nilai_baru' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cek apakah sudah ada pengajuan pending dengan jenis yang sama
        $existing = PengajuanBarang::where('barang_id', $barang->id)
            ->where('jenis', $validated['jenis'])
            ->pending()
            ->first();

        if ($existing) {
            $labelJenis = match($validated['jenis']) {
                'stok' => 'stok',
                'nama_barang' => 'nama barang',
                'kode_barang' => 'kode barcode',
            };
            return back()->withErrors([
                'pengajuan' => "Sudah ada pengajuan {$labelJenis} yang menunggu persetujuan."
            ]);
        }

        $nilaiLama = match($validated['jenis']) {
            'stok'        => (string) $barang->stok,
            'nama_barang' => $barang->nama_barang,
            'kode_barang' => $barang->kode_barang,
        };

        PengajuanBarang::create([
            'barang_id'     => $barang->id,
            'work_unit_id'  => $workUnitId,
            'diajukan_oleh' => auth()->id(),
            'jenis'         => $validated['jenis'],
            'nilai_lama'    => $nilaiLama,
            'nilai_baru'    => $validated['nilai_baru'],
            'keterangan'    => $validated['keterangan'] ?? null,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim dan menunggu persetujuan pengelola.');
    }

    /**
     * Officer/sysadmin menyetujui atau menolak pengajuan
     */
    public function proses(Request $request, $workUnit, $barangId, $pengajuan)
    {
        $record = PengajuanBarang::where('id', $pengajuan)
            ->where('work_unit_id', $workUnit)
            ->where('status', 'pending')
            ->firstOrFail();

        $validated = $request->validate([
            'aksi' => 'required|in:setujui,tolak',
            'catatan_pengelola' => 'nullable|string|max:500',
        ]);

        if ($validated['aksi'] === 'setujui') {
            $barang = $record->barang;

            $labelJenis = match($record->jenis) {
                'stok'        => 'Stok',
                'nama_barang' => 'Nama barang',
                'kode_barang' => 'Kode barcode',
            };

            if ($record->jenis === 'stok') {
                $barang->stok = (int) $record->nilai_baru;
            } elseif ($record->jenis === 'nama_barang') {
                $barang->nama_barang = $record->nilai_baru;
            } elseif ($record->jenis === 'kode_barang') {
                $duplikat = Barang::where('work_unit_id', $record->work_unit_id)
                    ->where('kode_barang', $record->nilai_baru)
                    ->where('id', '!=', $barang->id)
                    ->exists();

                if ($duplikat) {
                    return back()->withErrors(['pengajuan' => 'Kode barcode sudah digunakan oleh barang lain.']);
                }

                $barang->kode_barang = $record->nilai_baru;
            }

            $barang->save();

            $record->update([
                'status'            => 'disetujui',
                'diproses_oleh'     => auth()->id(),
                'catatan_pengelola' => $validated['catatan_pengelola'] ?? null,
                'diproses_at'       => now(),
            ]);

            return back()->with('success', "{$labelJenis} diperbarui: \"{$record->nilai_lama}\" → \"{$record->nilai_baru}\".");
        } else {
            $record->update([
                'status'            => 'ditolak',
                'diproses_oleh'     => auth()->id(),
                'catatan_pengelola' => $validated['catatan_pengelola'] ?? null,
                'diproses_at'       => now(),
            ]);

            return back()->with('success', 'Pengajuan ditolak.');
        }
    }

    /**
     * Bulk approve semua pengajuan pending dari barang-barang yang dipilih
     */
    public function bulkApprove(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'barang_ids'   => 'required|array|min:1',
            'barang_ids.*' => 'integer|exists:barangs,id',
        ]);

        $pengajuans = PengajuanBarang::whereIn('barang_id', $validated['barang_ids'])
            ->where('work_unit_id', $workUnitId)
            ->where('status', 'pending')
            ->with('barang')
            ->get();

        if ($pengajuans->isEmpty()) {
            return back()->withErrors(['pengajuan' => 'Tidak ada pengajuan pending yang ditemukan.']);
        }

        $approved = 0;
        $skipped  = 0;

        foreach ($pengajuans as $record) {
            $barang = $record->barang;

            if ($record->jenis === 'stok') {
                $barang->stok = (int) $record->nilai_baru;
                $barang->save();
            } elseif ($record->jenis === 'nama_barang') {
                $barang->nama_barang = $record->nilai_baru;
                $barang->save();
            } elseif ($record->jenis === 'kode_barang') {
                $duplikat = Barang::where('work_unit_id', $workUnitId)
                    ->where('kode_barang', $record->nilai_baru)
                    ->where('id', '!=', $barang->id)
                    ->exists();

                if ($duplikat) {
                    $skipped++;
                    continue;
                }

                $barang->kode_barang = $record->nilai_baru;
                $barang->save();
            }

            $record->update([
                'status'        => 'disetujui',
                'diproses_oleh' => auth()->id(),
                'diproses_at'   => now(),
            ]);

            $approved++;
        }

        $msg = "Berhasil menyetujui {$approved} pengajuan.";
        if ($skipped > 0) {
            $msg .= " {$skipped} pengajuan kode barcode dilewati karena duplikat.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Ambil daftar pengajuan pending untuk barang tertentu (dipakai di modal edit)
     */
    public function forBarang($workUnitId, $barangId)
    {
        $pengajuans = PengajuanBarang::where('barang_id', $barangId)
            ->where('work_unit_id', $workUnitId)
            ->pending()
            ->with('pengaju:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'jenis'         => $p->jenis,
                'nilai_lama'    => $p->nilai_lama,
                'nilai_baru'    => $p->nilai_baru,
                'keterangan'    => $p->keterangan,
                'status'        => $p->status,
                'diajukan_oleh' => $p->pengaju?->name,
                'created_at'    => $p->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json(['pengajuans' => $pengajuans]);
    }
}
