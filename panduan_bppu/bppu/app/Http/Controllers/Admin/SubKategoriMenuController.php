<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\SubKategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubKategoriMenuController extends Controller
{
    public function index()
    {
        $subKategoris = SubKategoriMenu::orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_menu' => $item->jumlah_menu,
                ];
            });

        return response()->json($subKategoris);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:sub_kategori_menu,nama',
        ]);

        $maxUrutan = SubKategoriMenu::max('urutan') ?? 0;

        $subKategori = SubKategoriMenu::create([
            'nama' => trim($request->nama),
            'urutan' => $maxUrutan + 1,
        ]);

        // Return updated list
        $subKategoris = SubKategoriMenu::orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_menu' => $item->jumlah_menu,
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
        $subKategori = SubKategoriMenu::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:sub_kategori_menu,nama,' . $id,
            'urutan' => 'nullable|integer|min:1',
        ]);

        $oldNama = $subKategori->nama;
        $newNama = trim($request->nama);

        DB::beginTransaction();
        try {
            // Update the reference table
            $subKategori->update([
                'nama' => $newNama,
                'urutan' => $request->urutan ?? $subKategori->urutan,
            ]);

            // Update all barang with this sub_kategori
            if ($oldNama !== $newNama) {
                Barang::where('is_menu_item', true)
                    ->where('sub_kategori', $oldNama)
                    ->update(['sub_kategori' => $newNama]);
            }

            DB::commit();

            // Return updated list
            $subKategoris = SubKategoriMenu::orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_menu' => $item->jumlah_menu,
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
        $subKategori = SubKategoriMenu::findOrFail($id);

        // Check if any menu is using this sub kategori
        $count = Barang::where('is_menu_item', true)
            ->where('sub_kategori', $subKategori->nama)
            ->count();

        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus sub kategori yang masih digunakan oleh ' . $count . ' menu',
            ], 422);
        }

        $subKategori->delete();

        // Return updated list
        $subKategoris = SubKategoriMenu::orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'urutan' => $item->urutan,
                    'jumlah_menu' => $item->jumlah_menu,
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
            'items.*.id' => 'required|exists:sub_kategori_menu,id',
            'items.*.urutan' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                SubKategoriMenu::where('id', $item['id'])
                    ->update(['urutan' => $item['urutan']]);
            }

            DB::commit();

            // Return updated list
            $subKategoris = SubKategoriMenu::orderBy('urutan')
                ->orderBy('nama')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'urutan' => $item->urutan,
                        'jumlah_menu' => $item->jumlah_menu,
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
