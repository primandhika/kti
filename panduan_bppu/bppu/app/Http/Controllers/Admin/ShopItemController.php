<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ShopItemController extends Controller
{
    public function index()
    {
        $items = ShopItem::orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/ShopItem/Index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ShopItem/CreateEdit', [
            'item' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable',
            'display_order' => 'nullable|integer',
        ]);

        // Handle image upload with compression
        if ($request->hasFile('image')) {
            $compression = new ImageCompressionService();
            $path = $compression->compressAndStore($request->file('image'), 'shop-items', 800, 85, 150);
            $validated['image'] = '/storage/' . $path;
        }

        // Set default display order if not provided
        if (!isset($validated['display_order'])) {
            $maxOrder = ShopItem::max('display_order') ?? 0;
            $validated['display_order'] = $maxOrder + 1;
        }

        // Handle boolean is_active
        $validated['is_active'] = $request->input('is_active', '1') === '1';

        ShopItem::create($validated);

        return redirect()->route('admin.shop-items.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(ShopItem $shopItem)
    {
        return Inertia::render('Admin/ShopItem/CreateEdit', [
            'item' => $shopItem,
        ]);
    }

    public function update(Request $request, ShopItem $shopItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable',
            'display_order' => 'nullable|integer',
        ]);

        // Handle image upload with compression
        if ($request->hasFile('image')) {
            $compression = new ImageCompressionService();

            if ($shopItem->image) {
                $compression->deleteImage($shopItem->image);
            }

            $path = $compression->compressAndStore($request->file('image'), 'shop-items', 800, 85, 150);
            $validated['image'] = '/storage/' . $path;
        }

        // Handle boolean is_active
        $validated['is_active'] = $request->input('is_active', '1') === '1';

        $shopItem->update($validated);

        return redirect()->route('admin.shop-items.index')
            ->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(ShopItem $shopItem)
    {
        // Delete image if exists
        if ($shopItem->image) {
            $path = str_replace('/storage/', '', $shopItem->image);
            Storage::disk('public')->delete($path);
        }

        $shopItem->delete();

        return redirect()->route('admin.shop-items.index')
            ->with('success', 'Barang berhasil dihapus!');
    }

    public function toggleActive(ShopItem $shopItem)
    {
        $shopItem->update([
            'is_active' => !$shopItem->is_active,
        ]);

        $status = $shopItem->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Barang berhasil {$status}!");
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:shop_items,id',
            'items.*.display_order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $itemData) {
            ShopItem::where('id', $itemData['id'])
                ->update(['display_order' => $itemData['display_order']]);
        }

        return back()->with('success', 'Urutan barang berhasil diperbarui!');
    }
}
