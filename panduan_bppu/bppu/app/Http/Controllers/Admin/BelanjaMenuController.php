<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanteenMenu;
use App\Models\MenuCategory;
use App\Models\Barang;
use App\Models\MenuDisplay;
use App\Models\WorkUnit;
use App\Services\PoS\CanteenMenuPdfService;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class BelanjaMenuController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get only shop work units
        $workUnits = WorkUnit::where('type', 'shop')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        // Get all active categories
        $kategoris = \App\Models\KategoriBarang::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode']);

        // Get all barangs that are marked for shop display
        $query = Barang::with(['menuDisplay', 'workUnit', 'kategoriBarang'])
            ->where('show_in_shop', true)
            ->where('is_active', true);

        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Apply work unit filter if provided
        if ($request->has('work_unit') && $request->work_unit) {
            $query->where('work_unit_id', $request->work_unit);
        }

        // Apply kategori filter if provided
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }

        $menus = $query->orderBy('sub_kategori', 'asc')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($barang) {
            $display = $barang->menuDisplay;
            return [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'kategori' => $barang->kategoriBarang?->nama ?? $barang->kategori,
                'kategori_id' => $barang->kategori_id,
                'sub_kategori' => $barang->sub_kategori,
                'harga_jual' => $barang->harga_jual,
                'harga_beli' => $barang->harga_beli,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'tampilkan_di_menu' => $barang->show_in_shop,
                'work_unit' => $barang->workUnit->name ?? '-',
                'work_unit_id' => $barang->work_unit_id,
                // Display info
                'gambar' => $display->gambar ?? null,
                'deskripsi_display' => $display->deskripsi_display ?? $barang->deskripsi,
                'is_available' => $display->is_available ?? true,
                'display_order' => $display->display_order ?? 0,
                'has_display' => $display !== null,
            ];
        });

        // Get urutan mapping from sub_kategori_menu
        $subKategoriUrutan = \App\Models\SubKategoriToko::orderBy('urutan')
            ->orderBy('nama')
            ->pluck('urutan', 'nama')
            ->toArray();

        // Group by sub_kategori
        $groupedMenus = $menus->groupBy('sub_kategori')->map(function($items, $key) use ($subKategoriUrutan) {
            return [
                'label' => $key ?: 'Belum Dikategorikan',
                'items' => $items->values()->toArray(),
                'count' => $items->count(),
                'urutan' => $subKategoriUrutan[$key] ?? 9999, // Belum dikategorikan di akhir
            ];
        })->sortBy('urutan')->values();

        // Get ALL sub categories from sub_kategori_toko table (untuk dropdown di modal edit)
        $allSubKategoris = \App\Models\SubKategoriToko::orderBy('urutan')
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();

        // Get sub kategori with kategori_barang_id for filtering
        $subKategoriData = \App\Models\SubKategoriToko::with('kategoriBarang:id,nama,kode')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'nama' => $item->nama,
                ];
            })
            ->toArray();

        // Get sub categories yang ada menu itemsnya (untuk pills filter)
        $subKategoris = \App\Models\SubKategoriToko::orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->filter(function($subKat) use ($menus) {
                // Hanya tampilkan sub kategori yang ada menu itemsnya
                return $menus->where('sub_kategori', $subKat->nama)->count() > 0;
            })
            ->pluck('nama')
            ->toArray();

        return Inertia::render('Admin/BelanjaMenu/Index', [
            'groupedMenus' => $groupedMenus,
            'workUnits' => $workUnits,
            'kategoris' => $kategoris,
            'allSubKategoris' => $allSubKategoris, // Semua sub kategori dari setting
            'subKategoriData' => $subKategoriData, // Sub kategori dengan relasi kategori
            'subKategoris' => $subKategoris, // Sub kategori yang ada menu itemsnya
            'filters' => [
                'search' => $request->search,
                'work_unit' => $request->work_unit,
                'kategori' => $request->kategori,
            ],
        ]);
    }

    public function create()
    {
        $categories = MenuCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return Inertia::render('Admin/CanteenMenu/CreateEdit', [
            'menu' => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable',
            'display_order' => 'nullable|integer',
        ]);

        // Handle JSON strings
        if ($request->has('badges')) {
            $badges = $request->input('badges');
            $validated['badges'] = is_string($badges) ? json_decode($badges, true) : $badges;
        } else {
            $validated['badges'] = [];
        }

        if ($request->has('available_days')) {
            $availableDays = $request->input('available_days');
            $validated['available_days'] = is_string($availableDays) ? json_decode($availableDays, true) : $availableDays;
        } else {
            $validated['available_days'] = [];
        }

        // Handle image upload with compression
        if ($request->hasFile('image')) {
            $compression = new ImageCompressionService();
            $path = $compression->compressAndStore($request->file('image'), 'canteen-menus', 800, 85, 150);
            $validated['image'] = '/storage/' . $path;
        }

        // Set default display order if not provided
        if (!isset($validated['display_order'])) {
            $maxOrder = CanteenMenu::max('display_order') ?? 0;
            $validated['display_order'] = $maxOrder + 1;
        }

        // Handle boolean is_active
        $validated['is_active'] = $request->input('is_active', '1') === '1';

        CanteenMenu::create($validated);

        return redirect()->route('admin.canteen-menu.index')
            ->with('success', 'Menu kantin berhasil ditambahkan!');
    }

    public function edit(CanteenMenu $canteenMenu)
    {
        $categories = MenuCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return Inertia::render('Admin/CanteenMenu/CreateEdit', [
            'menu' => $canteenMenu->load('category'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, CanteenMenu $canteenMenu)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable',
            'display_order' => 'nullable|integer',
        ]);

        // Handle JSON strings
        if ($request->has('badges')) {
            $badges = $request->input('badges');
            $validated['badges'] = is_string($badges) ? json_decode($badges, true) : $badges;
        }

        if ($request->has('available_days')) {
            $availableDays = $request->input('available_days');
            $validated['available_days'] = is_string($availableDays) ? json_decode($availableDays, true) : $availableDays;
        }

        // Handle image upload with compression
        if ($request->hasFile('image')) {
            $compression = new ImageCompressionService();

            if ($canteenMenu->image) {
                $compression->deleteImage($canteenMenu->image);
            }

            $path = $compression->compressAndStore($request->file('image'), 'canteen-menus', 800, 85, 150);
            $validated['image'] = '/storage/' . $path;
        }

        // Handle boolean is_active
        $validated['is_active'] = $request->input('is_active', '1') === '1';

        $canteenMenu->update($validated);

        return redirect()->route('admin.canteen-menu.index')
            ->with('success', 'Menu kantin berhasil diperbarui!');
    }

    public function destroy(CanteenMenu $canteenMenu)
    {
        // Delete image if exists
        if ($canteenMenu->image) {
            $path = str_replace('/storage/', '', $canteenMenu->image);
            Storage::disk('public')->delete($path);
        }

        $canteenMenu->delete();

        return redirect()->route('admin.canteen-menu.index')
            ->with('success', 'Menu kantin berhasil dihapus!');
    }

    public function toggleActive(CanteenMenu $canteenMenu)
    {
        $canteenMenu->update([
            'is_active' => !$canteenMenu->is_active,
        ]);

        $status = $canteenMenu->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Menu kantin berhasil {$status}!");
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:canteen_menus,id',
            'menus.*.display_order' => 'required|integer',
        ]);

        foreach ($validated['menus'] as $menuData) {
            CanteenMenu::where('id', $menuData['id'])
                ->update(['display_order' => $menuData['display_order']]);
        }

        return back()->with('success', 'Urutan menu berhasil diperbarui!');
    }

    /**
     * Update menu display (gambar, deskripsi, availability)
     */
    public function updateDisplay(Request $request, $barangId)
    {
        $barang = Barang::findOrFail($barangId);

        $validated = $request->validate([
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'deskripsi_display' => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $display = $barang->menuDisplay;

        // Handle image upload with compression
        if ($request->hasFile('gambar')) {
            $compression = new ImageCompressionService();

            if ($display && $display->gambar) {
                $compression->deleteImage($display->gambar);
            }

            $path = $compression->compressAndStore($request->file('gambar'), 'barangs', 800, 85, 150);
            $validated['gambar'] = '/storage/' . $path;
        }

        if ($display) {
            // Update existing display
            $display->update($validated);
        } else {
            // Create new display
            $validated['barang_id'] = $barangId;
            MenuDisplay::create($validated);
        }

        return back()->with('success', 'Display menu berhasil diperbarui!');
    }

    /**
     * Toggle menu availability
     */
    public function toggleAvailability($barangId)
    {
        $barang = Barang::findOrFail($barangId);
        $display = $barang->menuDisplay;

        if ($display) {
            $display->update(['is_available' => !$display->is_available]);
        } else {
            MenuDisplay::create([
                'barang_id' => $barangId,
                'is_available' => false,
            ]);
        }

        return back()->with('success', 'Status ketersediaan menu berhasil diubah!');
    }

    /**
     * Update barang details from menu kantin page
     */
    public function updateBarang(Request $request, $barangId)
    {
        $barang = Barang::findOrFail($barangId);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategori_barangs,id',
            'sub_kategori' => 'nullable|string|max:100',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'tampilkan_di_menu' => 'nullable|boolean',
        ]);

        // Update show_in_shop based on tampilkan_di_menu
        if (isset($validated['tampilkan_di_menu'])) {
            $validated['show_in_shop'] = $validated['tampilkan_di_menu'];
            unset($validated['tampilkan_di_menu']);
        }

        $barang->update($validated);

        return back()->with('success', 'Data makanan berhasil diperbarui!');
    }

    /**
     * Download menu as PDF
     */
    public function downloadMenu(Request $request, CanteenMenuPdfService $pdfService)
    {
        $theme = $request->input('theme', 'with-image');
        $workUnitId = $request->input('work_unit');
        $availableOnly = $request->input('available_only', false);

        return $pdfService->generateMenuPdf($theme, $workUnitId, $availableOnly);
    }
}
