<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\LokasiMeja;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MejaController extends Controller
{
    public function index()
    {
        $lokasis = LokasiMeja::with(['mejas' => function ($q) {
                $q->orderBy('display_order')->orderBy('kode_meja');
            }, 'defaultWorkUnit'])
            ->withCount('mejas')
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get()
            ->map(function ($lokasi) {
                $workUnitName = $lokasi->defaultWorkUnit?->name ?? 'Toko BPPU';
                $lokasi->mejas->transform(function ($meja) use ($workUnitName) {
                    $meja->self_order_url    = url('/kantin/self-order?meja=' . $meja->qr_token);
                    $meja->belanja_order_url = url('/belanja/self-order?meja=' . $meja->qr_token);
                    $meja->work_unit_name    = $workUnitName;
                    return $meja;
                });
                return $lokasi;
            });

        $workUnits = WorkUnit::where('type', 'Kantin')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Meja/Index', [
            'lokasis'   => $lokasis,
            'workUnits' => $workUnits,
        ]);
    }

    public function apiIndex()
    {
        $lokasis = LokasiMeja::with(['mejas' => function ($q) {
                $q->orderBy('display_order')->orderBy('kode_meja');
            }, 'defaultWorkUnit'])
            ->withCount('mejas')
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get()
            ->map(function ($lokasi) {
                $workUnitName = $lokasi->defaultWorkUnit?->name ?? 'Toko BPPU';
                $lokasi->mejas->transform(function ($meja) use ($workUnitName) {
                    $meja->self_order_url    = url('/kantin/self-order?meja=' . $meja->qr_token);
                    $meja->belanja_order_url = url('/belanja/self-order?meja=' . $meja->qr_token);
                    $meja->work_unit_name    = $workUnitName;
                    return $meja;
                });
                return $lokasi;
            });

        return response()->json($lokasis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_meja_id' => 'required|exists:lokasi_mejas,id',
            'kode_meja' => 'required|string|max:20',
            'nama' => 'nullable|string|max:100',
            'nomor' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $exists = Meja::where('lokasi_meja_id', $validated['lokasi_meja_id'])
            ->where('kode_meja', $validated['kode_meja'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Kode meja sudah digunakan di lokasi ini.'], 422);
        }

        $validated['display_order'] = (Meja::where('lokasi_meja_id', $validated['lokasi_meja_id'])->max('display_order') ?? 0) + 1;

        $meja = Meja::create($validated);
        $meja->load('lokasi');

        return response()->json($meja, 201);
    }

    public function update(Request $request, Meja $meja)
    {
        $validated = $request->validate([
            'lokasi_meja_id' => 'required|exists:lokasi_mejas,id',
            'kode_meja' => 'required|string|max:20',
            'nama' => 'nullable|string|max:100',
            'nomor' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $exists = Meja::where('lokasi_meja_id', $validated['lokasi_meja_id'])
            ->where('kode_meja', $validated['kode_meja'])
            ->where('id', '!=', $meja->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Kode meja sudah digunakan di lokasi ini.'], 422);
        }

        $meja->update($validated);
        $meja->load('lokasi');

        return response()->json($meja);
    }

    public function destroy(Meja $meja)
    {
        $meja->delete();

        return response()->json(['message' => 'Meja berhasil dihapus']);
    }

    public function toggleStatus(Meja $meja)
    {
        $meja->update(['is_active' => !$meja->is_active]);

        return response()->json($meja);
    }

    public function cetakPdf(Request $request)
    {
        $mejaIds   = $request->input('meja_ids', []);
        $lokasiIds = $request->input('lokasi_ids', []);
        $type      = $request->input('type', 'kantin'); // 'kantin' | 'belanja'

        $query = LokasiMeja::with(['mejas' => function ($q) use ($mejaIds) {
                $q->where('is_active', true)->orderBy('display_order')->orderBy('kode_meja');
                if (!empty($mejaIds)) {
                    $q->whereIn('id', $mejaIds);
                }
            }, 'defaultWorkUnit'])
            ->where('is_active', true)
            ->orderBy('display_order');

        if (!empty($lokasiIds)) {
            $query->whereIn('id', $lokasiIds);
        }

        $lokasis = $query->get()
            ->filter(fn($lokasi) => $lokasi->mejas->isNotEmpty())
            ->map(function ($lokasi) use ($type) {
                $workUnitName = $lokasi->defaultWorkUnit?->name ?? 'Toko BPPU';
                $lokasi->mejas->transform(function ($meja) use ($lokasi, $type, $workUnitName) {
                    $meja->lokasi_nama    = $lokasi->nama;
                    $meja->work_unit_name = $workUnitName;
                    $meja->qr_url = $type === 'belanja'
                        ? url('/belanja/self-order?meja=' . $meja->qr_token)
                        : url('/kantin/self-order?meja=' . $meja->qr_token);
                    return $meja;
                });
                return $lokasi;
            })
            ->values();

        return Inertia::render('Admin/Meja/CetakPdf', [
            'lokasis'       => $lokasis,
            'type'          => $type,
            'baseUrl'       => url('/'),
        ]);
    }
}
