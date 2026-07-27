<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPencairan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengajuanPencairanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'menunggu');

        $pengajuan = PengajuanPencairan::with(['supplier', 'diprosesOleh:id,name'])
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'menunggu', 'diproses', 'selesai', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $counts = [
            'menunggu' => PengajuanPencairan::where('status', 'menunggu')->count(),
            'diproses' => PengajuanPencairan::where('status', 'diproses')->count(),
            'selesai'  => PengajuanPencairan::where('status', 'selesai')->count(),
            'ditolak'  => PengajuanPencairan::where('status', 'ditolak')->count(),
        ];

        return Inertia::render('PoS/PengajuanPencairan', [
            'pengajuan'    => $pengajuan,
            'activeStatus' => $status,
            'counts'       => $counts,
        ]);
    }

    public function proses($id)
    {
        $pengajuan = PengajuanPencairan::findOrFail($id);
        $pengajuan->update([
            'status'      => 'diproses',
            'diproses_oleh' => auth()->id(),
            'diproses_at'   => now(),
        ]);
        return redirect()->back()->with('success', 'Pengajuan ditandai sedang diproses.');
    }

    public function selesai($id)
    {
        $pengajuan = PengajuanPencairan::findOrFail($id);
        $pengajuan->update([
            'status'      => 'selesai',
            'diproses_oleh' => auth()->id(),
            'diproses_at'   => now(),
        ]);
        return redirect()->back()->with('success', 'Pengajuan ditandai selesai.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan_pengelola' => 'nullable|string|max:500']);
        $pengajuan = PengajuanPencairan::findOrFail($id);
        $pengajuan->update([
            'status'            => 'ditolak',
            'catatan_pengelola' => $request->catatan_pengelola,
            'diproses_oleh'     => auth()->id(),
            'diproses_at'       => now(),
        ]);
        return redirect()->back()->with('success', 'Pengajuan ditolak.');
    }
}
