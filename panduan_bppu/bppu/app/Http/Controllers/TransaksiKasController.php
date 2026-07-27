<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransaksiKasController extends Controller
{
    public function store(Request $request, $bukuKasId)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($bukuKasId);

        // Sysadmin dan Head bisa menambah transaksi ke semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'source_type' => 'nullable|in:manual,kas-lain,unit-kerja',
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

        $validated['buku_kas_id'] = $bukuKasId;
        $validated['pemasukan'] = $validated['pemasukan'] ?? 0;
        $validated['pengeluaran'] = $validated['pengeluaran'] ?? 0;

        // Handle bukti transaksi
        if ($request->input('bukti_transaksi_type') === 'link') {
            $validated['bukti_transaksi'] = null;
            $validated['bukti_transaksi_type'] = 'link';
            $validated['bukti_transaksi_link'] = $request->input('bukti_transaksi_link');
        } elseif ($request->hasFile('bukti_transaksi')) {
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti-transaksi', 'public');
            $validated['bukti_transaksi_type'] = 'upload';
            $validated['bukti_transaksi_link'] = null;
        }

        // Handle bukti aktivitas
        if ($request->input('bukti_aktivitas_type') === 'link') {
            $validated['bukti_aktivitas'] = null;
            $validated['bukti_aktivitas_type'] = 'link';
            $validated['bukti_aktivitas_link'] = $request->input('bukti_aktivitas_link');
        } elseif ($request->hasFile('bukti_aktivitas')) {
            $validated['bukti_aktivitas'] = $request->file('bukti_aktivitas')->store('bukti-aktivitas', 'public');
            $validated['bukti_aktivitas_type'] = 'upload';
            $validated['bukti_aktivitas_link'] = null;
        }

        $transaksi = TransaksiKas::create($validated);

        // If source_type is 'unit-kerja', mark all approved penjualan from that unit as recorded
        if ($request->input('source_type') === 'unit-kerja' && $validated['unit_kerja_id']) {
            \App\Models\Penjualan::where('work_unit_id', $validated['unit_kerja_id'])
                ->where('status', 'selesai')
                ->where('is_approved', true)
                ->where('is_recorded', false)
                ->update([
                    'is_recorded' => true,
                    'recorded_by' => $user->id,
                    'recorded_at' => now(),
                    'transaksi_kas_id' => $transaksi->id, // Link to this transaksi kas
                ]);
        }

        return redirect()->route('admin.bukukas.show', $bukuKasId)->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function update(Request $request, $bukuKasId, $id)
    {
        $user = auth()->user();
        $transaksi = TransaksiKas::findOrFail($id);

        // Sysadmin dan Head bisa update transaksi di semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $transaksi->bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
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

        $validated['pemasukan'] = $validated['pemasukan'] ?? 0;
        $validated['pengeluaran'] = $validated['pengeluaran'] ?? 0;

        // Handle bukti transaksi
        if ($request->input('bukti_transaksi_type') === 'link') {
            if ($transaksi->bukti_transaksi) {
                Storage::disk('public')->delete($transaksi->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = null;
            $validated['bukti_transaksi_type'] = 'link';
            $validated['bukti_transaksi_link'] = $request->input('bukti_transaksi_link');
        } elseif ($request->hasFile('bukti_transaksi')) {
            if ($transaksi->bukti_transaksi) {
                Storage::disk('public')->delete($transaksi->bukti_transaksi);
            }
            $validated['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti-transaksi', 'public');
            $validated['bukti_transaksi_type'] = 'upload';
            $validated['bukti_transaksi_link'] = null;
        }

        // Handle bukti aktivitas
        if ($request->input('bukti_aktivitas_type') === 'link') {
            if ($transaksi->bukti_aktivitas) {
                Storage::disk('public')->delete($transaksi->bukti_aktivitas);
            }
            $validated['bukti_aktivitas'] = null;
            $validated['bukti_aktivitas_type'] = 'link';
            $validated['bukti_aktivitas_link'] = $request->input('bukti_aktivitas_link');
        } elseif ($request->hasFile('bukti_aktivitas')) {
            if ($transaksi->bukti_aktivitas) {
                Storage::disk('public')->delete($transaksi->bukti_aktivitas);
            }
            $validated['bukti_aktivitas'] = $request->file('bukti_aktivitas')->store('bukti-aktivitas', 'public');
            $validated['bukti_aktivitas_type'] = 'upload';
            $validated['bukti_aktivitas_link'] = null;
        }

        $transaksi->update($validated);

        return redirect()->route('admin.bukukas.show', $bukuKasId)->with('success', 'Transaksi berhasil diupdate');
    }

    public function destroy($bukuKasId, $id)
    {
        $user = auth()->user();
        $transaksi = TransaksiKas::findOrFail($id);

        // Sysadmin dan Head bisa hapus transaksi di semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $transaksi->bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // If this transaction was from 'unit-kerja' or 'penjualan', reverse the is_recorded status
        if (in_array($transaksi->source_type, ['unit-kerja', 'penjualan'])) {
            \App\Models\Penjualan::where('transaksi_kas_id', $transaksi->id)
                ->update([
                    'is_recorded' => false,
                    'recorded_by' => null,
                    'recorded_at' => null,
                    'transaksi_kas_id' => null,
                ]);
        }

        if ($transaksi->bukti_transaksi) {
            Storage::disk('public')->delete($transaksi->bukti_transaksi);
        }

        if ($transaksi->bukti_aktivitas) {
            Storage::disk('public')->delete($transaksi->bukti_aktivitas);
        }

        $transaksi->delete();

        return redirect()->route('admin.bukukas.show', $bukuKasId)->with('success', 'Transaksi berhasil dihapus');
    }

    public function recordPenjualanByDate(Request $request, $bukuKasId)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($bukuKasId);

        // Sysadmin dan Head bisa menambah transaksi ke semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'selected_dates' => 'required|array|min:1',
            'selected_dates.*.unit_id' => 'required|exists:work_units,id',
            'selected_dates.*.tanggal' => 'required|date',
            'selected_dates.*.total' => 'required|numeric|min:0',
            'selected_dates.*.penjualan_ids' => 'required|array|min:1',
            'selected_dates.*.metode_pembayaran' => 'nullable|string',
        ]);

        $recordedCount = 0;

        foreach ($validated['selected_dates'] as $dateData) {
            // Create transaksi kas for this date
            $workUnit = \App\Models\WorkUnit::find($dateData['unit_id']);
            $metodePembayaran = $dateData['metode_pembayaran'] ?? 'Tunai';

            $transaksi = TransaksiKas::create([
                'buku_kas_id' => $bukuKasId,
                'source_type' => 'unit-kerja',
                'tanggal' => $dateData['tanggal'],
                'kategori' => 'Pemasukan',
                'jenis_transaksi' => 'Penjualan - ' . $metodePembayaran,
                'unit_kerja_id' => $dateData['unit_id'],
                'deskripsi' => 'Penghasilan penjualan ' . $workUnit->name . ' tanggal ' . \Carbon\Carbon::parse($dateData['tanggal'])->format('d M Y') . ' (' . $metodePembayaran . ')',
                'pemasukan' => $dateData['total'],
                'pengeluaran' => 0,
            ]);

            // Mark all penjualan on this date as recorded
            \App\Models\Penjualan::whereIn('id', $dateData['penjualan_ids'])
                ->update([
                    'is_recorded' => true,
                    'recorded_by' => $user->id,
                    'recorded_at' => now(),
                    'transaksi_kas_id' => $transaksi->id,
                ]);

            $recordedCount++;
        }

        return redirect()->route('admin.bukukas.show', $bukuKasId)
            ->with('success', 'Berhasil merekam ' . $recordedCount . ' tanggal penjualan ke buku kas');
    }
}
