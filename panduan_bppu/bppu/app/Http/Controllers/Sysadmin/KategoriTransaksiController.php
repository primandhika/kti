<?php

namespace App\Http\Controllers\Sysadmin;

use App\Http\Controllers\Controller;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $kategori = KategoriTransaksi::ordered()->get();

        // If request wants JSON (for AJAX calls from Settings page)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($kategori);
        }

        // Otherwise render the dedicated page
        return Inertia::render('Sysadmin/KategoriTransaksi/Index', [
            'kategori' => $kategori,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'kode' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $kategori = KategoriTransaksi::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($kategori, 201);
        }

        return redirect()->back()->with('success', 'Kategori transaksi berhasil ditambahkan');
    }

    public function update(Request $request, KategoriTransaksi $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'kode' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $kategori->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($kategori);
        }

        return redirect()->back()->with('success', 'Kategori transaksi berhasil diperbarui');
    }

    public function destroy(Request $request, KategoriTransaksi $kategori)
    {
        $kategori->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Kategori transaksi berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Kategori transaksi berhasil dihapus');
    }

    public function toggleStatus(Request $request, KategoriTransaksi $kategori)
    {
        $kategori->update(['is_active' => !$kategori->is_active]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($kategori);
        }

        return redirect()->back()->with('success', 'Status kategori berhasil diubah');
    }
}
