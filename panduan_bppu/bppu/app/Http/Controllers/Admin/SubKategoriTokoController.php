<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\SubKategoriToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubKategoriTokoController extends Controller
{
    public function index()
    {
        $subKategoris = SubKategoriToko::with('kategoriBarang:id,nama,kode')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'kategori_nama' => $item->kategoriBarang?->nama,
                    'kategori_kode' => $item->kategoriBarang?->kode,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_barang' => $item->jumlah_barang,
                ];
            });

        return response()->json($subKategoris);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_barang_id' => 'nullable|exists:kategori_barangs,id',
            'nama' => 'required|string|max:255|unique:sub_kategori_toko,nama',
        ]);

        $maxUrutan = SubKategoriToko::max('urutan') ?? 0;

        $subKategori = SubKategoriToko::create([
            'kategori_barang_id' => $request->kategori_barang_id,
            'nama' => trim($request->nama),
            'urutan' => $maxUrutan + 1,
        ]);

        // Return updated list
        $subKategoris = SubKategoriToko::with('kategoriBarang:id,nama,kode')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'kategori_nama' => $item->kategoriBarang?->nama,
                    'kategori_kode' => $item->kategoriBarang?->kode,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_barang' => $item->jumlah_barang,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Sub kategori berhasil ditambahkan',
            'data' => $subKategoris,
        ]);
    }

    public function update(Request $request, $id)
    {
        $subKategori = SubKategoriToko::findOrFail($id);

        $request->validate([
            'kategori_barang_id' => 'nullable|exists:kategori_barangs,id',
            'nama' => 'required|string|max:255|unique:sub_kategori_toko,nama,' . $id,
            'urutan' => 'nullable|integer|min:1',
        ]);

        $oldNama = $subKategori->nama;
        $newNama = trim($request->nama);

        DB::beginTransaction();
        try {
            // Update the reference table
            $subKategori->update([
                'kategori_barang_id' => $request->kategori_barang_id,
                'nama' => $newNama,
                'urutan' => $request->urutan ?? $subKategori->urutan,
            ]);

            // Update all barang with this sub_kategori
            if ($oldNama !== $newNama) {
                Barang::where('show_in_shop', true)
                    ->where('sub_kategori', $oldNama)
                    ->update(['sub_kategori' => $newNama]);
            }

            DB::commit();

            // Return updated list
            $subKategoris = SubKategoriToko::with('kategoriBarang:id,nama,kode')
                ->orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kategori_barang_id' => $item->kategori_barang_id,
                        'kategori_nama' => $item->kategoriBarang?->nama,
                        'kategori_kode' => $item->kategoriBarang?->kode,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_barang' => $item->jumlah_barang,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Sub kategori berhasil diperbarui',
                'data' => $subKategoris,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui sub kategori',
            ], 422);
        }
    }

    public function destroy($id)
    {
        $subKategori = SubKategoriToko::findOrFail($id);

        // Check if any shop item is using this sub kategori
        $count = Barang::where('show_in_shop', true)
            ->where('sub_kategori', $subKategori->nama)
            ->count();

        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus sub kategori yang masih digunakan oleh ' . $count . ' barang',
            ], 422);
        }

        $subKategori->delete();

        // Return updated list
        $subKategoris = SubKategoriToko::with('kategoriBarang:id,nama,kode')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'kategori_nama' => $item->kategoriBarang?->nama,
                    'kategori_kode' => $item->kategoriBarang?->kode,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_barang' => $item->jumlah_barang,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Sub kategori berhasil dihapus',
            'data' => $subKategoris,
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:sub_kategori_toko,id',
            'items.*.urutan' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                SubKategoriToko::where('id', $item['id'])
                    ->update(['urutan' => $item['urutan']]);
            }

            DB::commit();

            // Return updated list
            $subKategoris = SubKategoriToko::with('kategoriBarang:id,nama,kode')
                ->orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kategori_barang_id' => $item->kategori_barang_id,
                        'kategori_nama' => $item->kategoriBarang?->nama,
                        'kategori_kode' => $item->kategoriBarang?->kode,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_barang' => $item->jumlah_barang,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Urutan berhasil diperbarui',
                'data' => $subKategoris,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan',
            ], 422);
        }
    }
}
