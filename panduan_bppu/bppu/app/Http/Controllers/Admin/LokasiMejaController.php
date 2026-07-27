<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiMeja;
use App\Models\WorkUnit;
use Illuminate\Http\Request;

class LokasiMejaController extends Controller
{
    public function index()
    {
        $lokasis = LokasiMeja::withCount('mejas')
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get();

        return response()->json($lokasis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20|unique:lokasi_mejas,kode',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'default_work_unit_id' => 'nullable|exists:work_units,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
        ]);

        $validated['display_order'] = (LokasiMeja::max('display_order') ?? 0) + 1;

        $lokasi = LokasiMeja::create($validated);

        return response()->json($lokasi, 201);
    }

    public function update(Request $request, LokasiMeja $lokasiMeja)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20|unique:lokasi_mejas,kode,' . $lokasiMeja->id,
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'default_work_unit_id' => 'nullable|exists:work_units,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
        ]);

        $lokasiMeja->update($validated);

        return response()->json($lokasiMeja);
    }

    public function destroy(LokasiMeja $lokasiMeja)
    {
        if ($lokasiMeja->mejas()->count() > 0) {
            return response()->json([
                'message' => 'Lokasi tidak dapat dihapus karena masih memiliki meja.'
            ], 422);
        }

        $lokasiMeja->delete();

        return response()->json(['message' => 'Lokasi berhasil dihapus']);
    }

    public function toggleStatus(LokasiMeja $lokasiMeja)
    {
        $lokasiMeja->update(['is_active' => !$lokasiMeja->is_active]);

        return response()->json($lokasiMeja);
    }
}
