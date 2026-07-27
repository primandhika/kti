<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class KategoriBarangController extends Controller
{
    /**
     * Display a listing of kategori barang
     */
    public function index()
    {
        $kategoris = KategoriBarang::withCount('barangs')
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get();

        return response()->json($kategoris);
    }

    /**
     * Store a newly created kategori barang
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode' => 'nullable|string|max:50|unique:kategori_barangs,kode',
            'is_active' => 'boolean',
        ]);

        // Set display_order to max + 1
        $maxOrder = KategoriBarang::max('display_order') ?? 0;
        $validated['display_order'] = $maxOrder + 1;

        $kategori = KategoriBarang::create($validated);

        return response()->json($kategori, 201);
    }

    /**
     * Update the specified kategori barang
     */
    public function update(Request $request, KategoriBarang $kategoriBarang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode' => 'nullable|string|max:50|unique:kategori_barangs,kode,' . $kategoriBarang->id,
            'is_active' => 'boolean',
        ]);

        $kategoriBarang->update($validated);

        return response()->json($kategoriBarang);
    }

    /**
     * Remove the specified kategori barang
     */
    public function destroy(KategoriBarang $kategoriBarang)
    {
        // Check if kategori has barangs
        if ($kategoriBarang->barangs()->count() > 0) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki barang.'
            ], 422);
        }

        $kategoriBarang->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(KategoriBarang $kategoriBarang)
    {
        $kategoriBarang->update([
            'is_active' => !$kategoriBarang->is_active
        ]);

        return response()->json($kategoriBarang);
    }

    /**
     * Reorder kategori barang
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:kategori_barangs,id',
            'items.*.display_order' => 'required|integer|min:1',
        ]);

        \DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                KategoriBarang::where('id', $item['id'])
                    ->update(['display_order' => $item['display_order']]);
            }
            \DB::commit();

            $kategoris = KategoriBarang::withCount('barangs')
                ->orderBy('display_order')
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Urutan berhasil diperbarui',
                'data' => $kategoris,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan',
            ], 422);
        }
    }
}
