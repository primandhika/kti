<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\MenuDisplay;
use App\Models\WorkUnit;
use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Services\PoS\BarangService;
use App\Services\PoS\BuyerService;
use App\Services\PoS\PenjualanTransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PoSController extends Controller
{
    protected $barangService;
    protected $buyerService;
    protected $transactionService;

    public function __construct(
        BarangService $barangService,
        BuyerService $buyerService,
        PenjualanTransactionService $transactionService
    ) {
        $this->barangService = $barangService;
        $this->buyerService = $buyerService;
        $this->transactionService = $transactionService;
    }

    /**
     * Display PoS interface for user kantin
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get work units yang bisa diakses user
        if ($user->hasRole('sysadmin')) {
            $workUnits = WorkUnit::where('is_active', true)->get();
        } else {
            $workUnits = $user->workUnits()->where('is_active', true)->get();
        }

        // Support multi-select work units (work_unit_ids[]) or single (work_unit_id)
        $requestedIds = $request->get('work_unit_ids', null);
        if ($requestedIds) {
            // Multi-select: filter hanya yang boleh diakses user
            $allowedIds = $workUnits->pluck('id')->toArray();
            $selectedIds = array_intersect((array) $requestedIds, $allowedIds);
        } else {
            // Fallback ke single work_unit_id atau first
            $singleId = $request->get('work_unit_id', $workUnits->first()?->id);
            $selectedIds = $singleId ? [$singleId] : [];
        }

        $selectedWorkUnits = $workUnits->whereIn('id', $selectedIds)->values();
        // Primary work unit (untuk checkout & form pengajuan)
        $selectedWorkUnit = $selectedWorkUnits->first();

        // Get barangs from selected work units
        $isMultiple = $selectedWorkUnits->count() > 1;
        $barangs = $this->barangService->getBarangs($isMultiple ? $selectedWorkUnits->all() : $selectedWorkUnit);

        // Get buyers for customer search
        $buyers = $this->buyerService->getBuyers();

        // Get points settings
        $pointsSettings = [
            'minimal_poin_redeem' => (int) Setting::get('minimal_poin_redeem', 5000),
            'kurs_poin_ke_rupiah' => (int) Setting::get('kurs_poin_ke_rupiah', 2),
        ];

        // Untuk form pengajuan tambah barang (kantin)
        $kategoris = KategoriBarang::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $satuans = Barang::whereIn('work_unit_id', $selectedIds)
            ->distinct()
            ->orderBy('satuan')
            ->pluck('satuan')
            ->filter()
            ->values();

        return Inertia::render('PoS/Index', [
            'workUnits' => $workUnits,
            'selectedWorkUnit' => $selectedWorkUnit,
            'selectedWorkUnitIds' => array_values($selectedIds),
            'barangs' => $barangs,
            'buyers' => $buyers,
            'pointsSettings' => $pointsSettings,
            'kategoris' => $kategoris,
            'satuans' => $satuans,
        ]);
    }

    /**
     * Refresh barangs data (untuk sync tanpa reload halaman)
     */
    public function refreshBarangs(Request $request)
    {
        $user = auth()->user();

        // Support multi work_unit_ids atau single work_unit_id
        $requestedIds = $request->get('work_unit_ids', null);
        if ($requestedIds) {
            $workUnitIds = (array) $requestedIds;
        } else {
            $singleId = $request->get('work_unit_id');
            $workUnitIds = $singleId ? [$singleId] : [];
        }

        if (empty($workUnitIds)) {
            return response()->json(['error' => 'Work unit not specified'], 400);
        }

        // Validate authorization for each work unit
        foreach ($workUnitIds as $id) {
            if (!$user->canAccessWorkUnit($id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $selectedWorkUnits = WorkUnit::whereIn('id', $workUnitIds)->get();

        if ($selectedWorkUnits->isEmpty()) {
            return response()->json(['error' => 'Work unit not found'], 404);
        }

        $isMultiple = $selectedWorkUnits->count() > 1;
        $barangs = $this->barangService->getBarangs($isMultiple ? $selectedWorkUnits->all() : $selectedWorkUnits->first());

        return response()->json([
            'barangs' => $barangs,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Store a new transaction (single atau multi-toko)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_unit_id' => 'required|exists:work_units,id',
            'tanggal_transaksi' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.varian_id' => 'nullable|exists:barang_varians,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.diskon_per_item' => 'nullable|numeric|min:0',
            'items.*.work_unit_id' => 'nullable|exists:work_units,id',
            'subtotal' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'total' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,debit,kredit',
            'buyer_id' => 'nullable|exists:users,id',
            'nama_pelanggan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        $user = auth()->user();

        // Resolusi work_unit_id per item: pakai item.work_unit_id jika ada, fallback ke header work_unit_id
        $items = collect($validated['items'])->map(function ($item) use ($validated) {
            $item['work_unit_id'] = $item['work_unit_id'] ?? $validated['work_unit_id'];
            return $item;
        });

        // Cek apakah multi-toko
        $uniqueWorkUnitIds = $items->pluck('work_unit_id')->unique()->values();
        $isMultiToko = $uniqueWorkUnitIds->count() > 1;

        // Validasi akses user ke semua work unit yang terlibat
        foreach ($uniqueWorkUnitIds as $wuid) {
            if (!$user->canAccessWorkUnit($wuid)) {
                return back()->withErrors(['error' => 'Anda tidak memiliki akses ke salah satu unit kerja.']);
            }
        }

        // Validasi: multi-toko tidak boleh pakai voucher/redeem poin (buyer_id tetap boleh untuk poin)
        if ($isMultiToko && (!empty($validated['voucher_id']) || !empty($validated['redeem_points']))) {
            return back()->withErrors(['error' => 'Voucher dan redeem poin tidak dapat digunakan pada transaksi multi-toko.']);
        }

        // Validasi item milik work unit yang benar
        foreach ($items as $item) {
            $barang = Barang::find($item['barang_id']);
            if (!$barang || $barang->work_unit_id != $item['work_unit_id']) {
                return back()->withErrors(['error' => 'Barang dengan ID ' . $item['barang_id'] . ' tidak ditemukan di unit kerja yang ditentukan.']);
            }
        }

        if ($isMultiToko) {
            $result = $this->transactionService->storeMulti($validated, $items->all(), $uniqueWorkUnitIds->all(), $user, $validated['buyer_id'] ?? null);
        } else {
            $validated['items'] = $items->all();
            $result = $this->transactionService->store($validated, $user);
        }

        if ($result['success']) {
            $penjualan = $result['data'];
            $logLabel = is_array($penjualan) && isset($penjualan[0])
                ? implode(', ', array_column($penjualan, 'nomor_transaksi'))
                : $penjualan['nomor_transaksi'];
            ActivityLogger::log(
                'create', 'penjualan',
                "Transaksi PoS: {$logLabel}",
                null,
                ['nomor_transaksi' => $logLabel]
            );
            return back()->with('transaction', $penjualan);
        } else {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $result['error']]);
        }
    }

    /**
     * Get buyer points information
     */
    public function getBuyerPoints($buyerId)
    {
        $data = $this->buyerService->getBuyerPoints($buyerId);

        if (!$data) {
            return response()->json(['error' => 'Buyer not found'], 404);
        }

        return response()->json($data);
    }

    /**
     * Preview redeem points to rupiah discount
     */
    public function redeemPreview(Request $request)
    {
        $validated = $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'points_to_redeem' => 'required|integer|min:1',
        ]);

        $result = $this->buyerService->redeemPreview($validated['buyer_id'], $validated['points_to_redeem']);

        if (!$result) {
            return response()->json(['error' => 'Buyer not found'], 404);
        }

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Upload image for barang
     */
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'barang_id' => 'required|exists:barangs,id',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        // Check if user can access this barang
        $user = auth()->user();
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            // Check if barang belongs to user's work unit
            $hasAccess = $user->workUnits()->where('work_units.id', $barang->work_unit_id)->exists();
            if (!$hasAccess) {
                return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk barang ini']);
            }
        }

        // Get or create menu display
        $menuDisplay = $barang->menuDisplay;
        if (!$menuDisplay) {
            $menuDisplay = new MenuDisplay();
            $menuDisplay->barang_id = $barang->id;
            $menuDisplay->is_available = true;
            $menuDisplay->display_order = 0;
        }

        // Delete old image if exists
        if ($menuDisplay->gambar) {
            $oldPath = str_replace('/storage/', '', $menuDisplay->gambar);
            \Storage::disk('public')->delete($oldPath);
        }

        // Store new image to storage/barangs with product name
        $file = $request->file('image');
        $barangName = \Illuminate\Support\Str::slug($barang->nama_barang);
        $filename = time() . '_' . $barangName . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('barangs', $filename, 'public');

        $menuDisplay->gambar = '/storage/' . $path;
        $menuDisplay->save();

        // Reload barang dengan relasi terbaru
        $barang->load('menuDisplay', 'varians');

        // Return dengan data barang yang sudah diupdate
        return back()->with([
            'success' => 'Foto berhasil diupload',
            'updatedBarang' => [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'kategori' => $barang->kategori,
                'harga_jual' => $barang->harga_jual,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'deskripsi' => $barang->deskripsi,
                'image' => $menuDisplay->gambar,
                'varians' => $barang->varians->map(function($v) {
                    return [
                        'id' => $v->id,
                        'nama_varian' => $v->nama_varian,
                        'deskripsi' => $v->deskripsi,
                        'harga_jual' => $v->harga_jual,
                        'stok' => $v->stok,
                    ];
                })
            ]
        ]);
    }

    /**
     * Delete image for barang
     */
    public function deleteImage(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        // Check if user can access this barang
        $user = auth()->user();
        if (!$user->hasRole('sysadmin') && !$user->hasRole('officer')) {
            // Check if barang belongs to user's work unit
            $hasAccess = $user->workUnits()->where('work_units.id', $barang->work_unit_id)->exists();
            if (!$hasAccess) {
                return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk barang ini']);
            }
        }

        // Get menu display
        $menuDisplay = $barang->menuDisplay;
        if (!$menuDisplay) {
            return back()->withErrors(['error' => 'Barang tidak memiliki foto']);
        }

        // Delete image if exists
        if ($menuDisplay->gambar) {
            $oldPath = str_replace('/storage/', '', $menuDisplay->gambar);
            \Storage::disk('public')->delete($oldPath);
        }

        $menuDisplay->gambar = null;
        $menuDisplay->save();

        return back()->with('success', 'Foto berhasil dihapus');
    }
}
