<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\TransaksiKas;
use App\Models\KategoriTransaksi;
use App\Models\JenisBukuKas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\BukuKasExport;
use App\Exports\TransaksiKasExport;
use Maatwebsite\Excel\Facades\Excel;

class BukuKasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Sysadmin dan Head bisa melihat semua buku kas
        $query = BukuKas::with(['user', 'jenisBukuKas'])->withCount('transaksiKas');

        if (!$user->hasRole('sysadmin') && !$user->hasRole('head')) {
            // User biasa hanya bisa lihat buku kas miliknya sendiri
            $query->where('user_id', $user->id);
        }

        // Filter by jenis_buku_kas_id
        if ($request->filled('jenis_buku_kas_id')) {
            $query->where('jenis_buku_kas_id', $request->jenis_buku_kas_id);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pre-fetch relasi induk-anak untuk semua buku kas sekaligus (hindari N+1)
        $indukIds = TransaksiKas::where('source_type', 'kas-lain')
            ->distinct()
            ->pluck('buku_kas_id')
            ->flip();

        $sumberPerInduk = TransaksiKas::where('source_type', 'kas-lain')
            ->select('buku_kas_id', 'deskripsi')
            ->get()
            ->groupBy('buku_kas_id')
            ->map(function ($rows) {
                return $rows->map(function ($t) {
                    preg_match('/Pencatatan dari Buku Kas: (.+)/', $t->deskripsi, $m);
                    return $m[1] ?? null;
                })->filter()->unique()->values()->all();
            });

        // Cari buku kas yang sudah di-import ke buku lain (status anak)
        // Map: nama_buku_kas -> induk_buku_kas_id
        $namaToIndukId = [];
        TransaksiKas::where('source_type', 'kas-lain')
            ->select('deskripsi', 'buku_kas_id')
            ->get()
            ->each(function ($t) use (&$namaToIndukId) {
                preg_match('/Pencatatan dari Buku Kas: (.+)/', $t->deskripsi, $m);
                if (!empty($m[1])) {
                    $namaToIndukId[trim($m[1])] = $t->buku_kas_id;
                }
            });

        // Map: anak_buku_kas_id -> induk_buku_kas_id
        $anakToIndukId = [];
        if (!empty($namaToIndukId)) {
            BukuKas::withoutGlobalScopes()
                ->whereIn('nama', array_keys($namaToIndukId))
                ->get(['id', 'nama'])
                ->each(function ($buku) use ($namaToIndukId, &$anakToIndukId) {
                    if (isset($namaToIndukId[$buku->nama])) {
                        $anakToIndukId[$buku->id] = $namaToIndukId[$buku->nama];
                    }
                });
        }

        // Paginate
        $bukuKas = $query->paginate(12)->through(function ($buku) use ($indukIds, $sumberPerInduk, $anakToIndukId) {
            $isInduk = isset($indukIds[$buku->id]);
            $isAnak = isset($anakToIndukId[$buku->id]);

            return [
                'id' => $buku->id,
                'nama' => $buku->nama,
                'keterangan' => $buku->keterangan,
                'created_at' => $buku->created_at->format('d M Y'),
                'user_name' => $buku->user->name,
                'user_username' => $buku->user->username,
                'total_pemasukan' => $buku->total_pemasukan,
                'total_pengeluaran' => $buku->total_pengeluaran,
                'saldo' => $buku->saldo,
                'jumlah_transaksi' => $buku->transaksi_kas_count,
                'jenis_buku_kas' => $buku->jenisBukuKas ? [
                    'id' => $buku->jenisBukuKas->id,
                    'nama' => $buku->jenisBukuKas->nama,
                    'kode' => $buku->jenisBukuKas->kode,
                    'warna' => $buku->jenisBukuKas->warna,
                ] : null,
                'is_induk' => $isInduk,
                'is_anak' => $isAnak,
                'sumber_kas' => $isInduk ? ($sumberPerInduk[$buku->id] ?? []) : [],
                'induk_kas_id' => $isAnak ? $anakToIndukId[$buku->id] : null,
            ];
        });

        // Get all jenis buku kas for filter
        $jenisBukuKasList = \App\Models\JenisBukuKas::active()->ordered()->get();

        return Inertia::render('BukuKas/Index', [
            'bukuKas' => $bukuKas,
            'jenisBukuKasList' => $jenisBukuKasList,
            'filters' => $request->only(['jenis_buku_kas_id', 'month', 'year', 'search', 'sort_field', 'sort_direction']),
            'isSuperAdmin' => $user->hasRole('sysadmin') || $user->hasRole('head'),
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::with(['user'])->findOrFail($id);

        // Sysadmin dan Head bisa lihat semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Build query with filters
        $query = TransaksiKas::where('buku_kas_id', $id)->with('unitKerja');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by jenis_transaksi
        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        // Filter by unit_kerja_id
        if ($request->filled('unit_kerja_id')) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }

        // Filter by type (pemasukan/pengeluaran)
        if ($request->filled('type')) {
            if ($request->type === 'pemasukan') {
                $query->where('pemasukan', '>', 0);
            } elseif ($request->type === 'pengeluaran') {
                $query->where('pengeluaran', '>', 0);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortField = $request->get('sort_field', 'tanggal');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate
        $transaksi = $query->paginate(10)->through(function ($t) {
            return [
                'id' => $t->id,
                'transaction_id' => $t->transaction_id,
                'tanggal' => $t->tanggal->format('d M Y'),
                'tanggal_raw' => $t->tanggal->format('Y-m-d'),
                'kategori' => $t->kategori,
                'jenis_transaksi' => $t->jenis_transaksi,
                'unit_kerja_id' => $t->unit_kerja_id,
                'unit_kerja_name' => $t->unitKerja ? $t->unitKerja->name : null,
                'deskripsi' => $t->deskripsi,
                'pemasukan' => $t->pemasukan,
                'pengeluaran' => $t->pengeluaran,
                'source_type' => $t->source_type,
                'bukti_transaksi' => $t->bukti_transaksi,
                'bukti_transaksi_type' => $t->bukti_transaksi_type,
                'bukti_transaksi_link' => $t->bukti_transaksi_link,
                'bukti_aktivitas' => $t->bukti_aktivitas,
                'bukti_aktivitas_type' => $t->bukti_aktivitas_type,
                'bukti_aktivitas_link' => $t->bukti_aktivitas_link,
            ];
        });

        // Get summary by unit
        $summaryByUnit = TransaksiKas::where('buku_kas_id', $id)
            ->whereNotNull('unit_kerja_id')
            ->selectRaw('unit_kerja_id, SUM(pemasukan) as total_pemasukan, SUM(pengeluaran) as total_pengeluaran')
            ->groupBy('unit_kerja_id')
            ->with('unitKerja')
            ->get()
            ->map(function ($item) {
                return [
                    'unit_kerja_id' => $item->unit_kerja_id,
                    'unit_kerja_name' => $item->unitKerja ? $item->unitKerja->name : 'Unknown',
                    'total_pemasukan' => $item->total_pemasukan,
                    'total_pengeluaran' => $item->total_pengeluaran,
                    'saldo' => $item->total_pemasukan - $item->total_pengeluaran,
                ];
            });

        // Get kategori from database
        $kategoriList = KategoriTransaksi::active()
            ->ordered()
            ->get()
            ->map(function ($kat) {
                return [
                    'id' => $kat->id,
                    'nama' => $kat->nama,
                    'jenis' => $kat->jenis,
                    'kode_akun' => $kat->kode_akun,
                ];
            });

        // Get unique jenis transaksi for filter (include null/empty values)
        $allTransaksi = TransaksiKas::where('buku_kas_id', $id)->get();
        $jenisTransaksiList = $allTransaksi->pluck('jenis_transaksi')->unique()->values();

        // Get all work units for dropdown
        $workUnits = \App\Models\WorkUnit::where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name', 'unit_id']);

        // Get all buku kas for "Dari Kas Lain" feature
        $allBukuKasQuery = BukuKas::with('user')->where('id', '!=', $id);

        if (!$user->hasRole('sysadmin') && !$user->hasRole('head')) {
            // User biasa hanya bisa lihat buku kas miliknya sendiri
            $allBukuKasQuery->where('user_id', $user->id);
        }

        $allBukuKas = $allBukuKasQuery->get()->map(function ($buku) {
            return [
                'id' => $buku->id,
                'nama' => $buku->nama,
                'user_name' => $buku->user->name,
                'total_pemasukan' => $buku->total_pemasukan,
                'total_pengeluaran' => $buku->total_pengeluaran,
                'saldo' => $buku->saldo,
                'created_at' => $buku->created_at->format('d M Y'),
            ];
        });

        // Get approved penjualan grouped by date and unit for "Dari Penghasilan Unit Kerja" feature
        // Group penjualan per tanggal yang belum di-record
        $approvedPenjualan = \App\Models\Penjualan::where('status', 'selesai')
            ->where('is_approved', true)
            ->where('is_recorded', false)
            ->whereNotNull('work_unit_id')
            ->with(['workUnit'])
            ->get();

        // Group by unit, date, and payment method
        $penjualanByDateAndUnit = [];
        foreach ($approvedPenjualan as $penjualan) {
            $date = $penjualan->tanggal_transaksi->format('Y-m-d');
            $unitId = $penjualan->work_unit_id;
            $metodePembayaran = $penjualan->metode_pembayaran ?: 'Tunai'; // Default to Tunai if null
            $key = $unitId . '_' . $date . '_' . $metodePembayaran;

            if (!isset($penjualanByDateAndUnit[$key])) {
                $penjualanByDateAndUnit[$key] = [
                    'unit_id' => $unitId,
                    'unit_name' => $penjualan->workUnit->name,
                    'tanggal' => $penjualan->tanggal_transaksi->format('Y-m-d'),
                    'tanggal_display' => $penjualan->tanggal_transaksi->format('d M Y'),
                    'metode_pembayaran' => $metodePembayaran,
                    'jumlah_transaksi' => 0,
                    'total_penghasilan' => 0,
                    'penjualan_ids' => [],
                ];
            }

            $penjualanByDateAndUnit[$key]['jumlah_transaksi']++;
            $penjualanByDateAndUnit[$key]['total_penghasilan'] += $penjualan->total;
            $penjualanByDateAndUnit[$key]['penjualan_ids'][] = $penjualan->id;
        }

        // Convert to array and sort by date descending
        $penjualanPerTanggal = array_values($penjualanByDateAndUnit);
        usort($penjualanPerTanggal, function($a, $b) {
            return strcmp($b['tanggal'], $a['tanggal']);
        });

        return Inertia::render('BukuKas/Show', [
            'bukuKas' => [
                'id' => $bukuKas->id,
                'nama' => $bukuKas->nama,
                'keterangan' => $bukuKas->keterangan,
                'created_at' => $bukuKas->created_at->format('d M Y'),
                'user_name' => $bukuKas->user->name,
                'user_username' => $bukuKas->user->username,
                'total_pemasukan' => $bukuKas->total_pemasukan,
                'total_pengeluaran' => $bukuKas->total_pengeluaran,
                'saldo' => $bukuKas->saldo,
            ],
            'transaksi' => $transaksi,
            'summaryByUnit' => $summaryByUnit,
            'kategoriList' => $kategoriList,
            'jenisTransaksiList' => $jenisTransaksiList,
            'workUnits' => $workUnits,
            'allBukuKas' => $allBukuKas,
            'penjualanPerTanggal' => $penjualanPerTanggal,
            'filters' => $request->only(['date_from', 'date_to', 'kategori', 'jenis_transaksi', 'unit_kerja_id', 'type', 'search', 'sort_field', 'sort_direction']),
            'isSuperAdmin' => $user->hasRole('sysadmin') || $user->hasRole('head'),
            'isOwner' => $bukuKas->user_id === $user->id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jenis_buku_kas_id' => 'nullable|exists:jenis_buku_kas,id',
        ]);

        $validated['user_id'] = auth()->id();

        BukuKas::create($validated);

        return redirect()->route('admin.bukukas.index')->with('success', 'Buku kas berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($id);

        // Sysadmin dan Head bisa update semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jenis_buku_kas_id' => 'nullable|exists:jenis_buku_kas,id',
        ]);

        $bukuKas->update($validated);

        return redirect()->route('admin.bukukas.index')->with('success', 'Buku kas berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($id);

        // Sysadmin dan Head bisa hapus semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete instead of hard delete
        $bukuKas->softDelete();

        return redirect()->route('admin.bukukas.index')->with('success', 'Buku kas berhasil dihapus');
    }

    public function exportBukuKasCsv()
    {
        $user = auth()->user();
        $fileName = 'buku-kas-' . date('Y-m-d-His') . '.csv';

        return Excel::download(new BukuKasExport($user), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportBukuKasXlsx()
    {
        $user = auth()->user();
        $fileName = 'buku-kas-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new BukuKasExport($user), $fileName);
    }

    public function exportTransaksiCsv(Request $request, $id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($id);

        // Check authorization
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $filters = $request->only(['date_from', 'date_to', 'kategori', 'jenis_transaksi', 'unit_kerja_id', 'type', 'search', 'sort_field', 'sort_direction']);
        $fileName = 'transaksi-' . str_replace(' ', '-', strtolower($bukuKas->nama)) . '-' . date('Y-m-d-His') . '.csv';

        return Excel::download(new TransaksiKasExport($id, $filters), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportTransaksiXlsx(Request $request, $id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($id);

        // Check authorization
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $filters = $request->only(['date_from', 'date_to', 'kategori', 'jenis_transaksi', 'unit_kerja_id', 'type', 'search', 'sort_field', 'sort_direction']);
        $fileName = 'transaksi-' . str_replace(' ', '-', strtolower($bukuKas->nama)) . '-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new TransaksiKasExport($id, $filters), $fileName);
    }

    public function assignJenis(Request $request, $id)
    {
        $user = auth()->user();
        $bukuKas = BukuKas::findOrFail($id);

        // Sysadmin dan Head bisa update semua buku kas
        if (!$user->hasRole('sysadmin') && !$user->hasRole('head') && $bukuKas->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'jenis_buku_kas_id' => 'nullable|exists:jenis_buku_kas,id',
        ]);

        $bukuKas->update(['jenis_buku_kas_id' => $validated['jenis_buku_kas_id']]);

        return back();
    }

    public function recycleBin(Request $request)
    {
        $user = auth()->user();

        // Only sysadmin can access recycle bin
        if (!$user->hasRole('sysadmin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get deleted buku kas
        $query = BukuKas::withoutGlobalScope('notDeleted')
            ->with(['user', 'jenisBukuKas'])
            ->withCount('transaksiKas')
            ->where('is_deleted', 1);

        // Paginate
        $bukuKas = $query->orderBy('updated_at', 'desc')->paginate(12)->through(function ($buku) {
            return [
                'id' => $buku->id,
                'nama' => $buku->nama,
                'keterangan' => $buku->keterangan,
                'created_at' => $buku->created_at->format('d M Y'),
                'user_name' => $buku->user->name,
                'user_username' => $buku->user->username,
                'total_pemasukan' => $buku->total_pemasukan,
                'total_pengeluaran' => $buku->total_pengeluaran,
                'saldo' => $buku->saldo,
                'jumlah_transaksi' => $buku->transaksi_kas_count,
                'jenis_buku_kas' => $buku->jenisBukuKas ? [
                    'id' => $buku->jenisBukuKas->id,
                    'nama' => $buku->jenisBukuKas->nama,
                    'kode' => $buku->jenisBukuKas->kode,
                    'warna' => $buku->jenisBukuKas->warna,
                ] : null,
            ];
        });

        return Inertia::render('BukuKas/RecycleBin', [
            'bukuKas' => $bukuKas,
        ]);
    }

    public function restore($id)
    {
        $user = auth()->user();

        // Only sysadmin can restore
        if (!$user->hasRole('sysadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $bukuKas = BukuKas::withoutGlobalScope('notDeleted')->findOrFail($id);
        $bukuKas->restore();

        return redirect()->route('admin.bukukas.recycle-bin')->with('success', 'Buku kas berhasil dipulihkan');
    }

    public function permanentDelete($id)
    {
        $user = auth()->user();

        // Only sysadmin can permanently delete
        if (!$user->hasRole('sysadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $bukuKas = BukuKas::withoutGlobalScope('notDeleted')->findOrFail($id);

        // Delete all related transactions first
        $bukuKas->transaksiKas()->delete();

        // Then delete the buku kas
        $bukuKas->delete();

        return redirect()->route('admin.bukukas.recycle-bin')->with('success', 'Buku kas berhasil dihapus permanen');
    }
}
