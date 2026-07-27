<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\PengajuanTambahBarang;
use Illuminate\Http\Request;

class PengajuanTambahBarangController extends Controller
{
    /**
     * Kantin mengajukan tambah barang baru
     */
    public function store(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'nama_barang'  => 'required|string|max:255',
            'kode_barang'  => 'nullable|string|max:100',
            'kategori_id'  => 'nullable|exists:kategori_barangs,id',
            'satuan'       => 'required|string|max:50',
            'stok_awal'    => 'required|integer|min:0',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        $kategoriNama = null;
        if (!empty($validated['kategori_id'])) {
            $kategoriNama = KategoriBarang::find($validated['kategori_id'])?->nama;
        }

        PengajuanTambahBarang::create([
            'work_unit_id'  => $workUnitId,
            'diajukan_oleh' => auth()->id(),
            'nama_barang'   => $validated['nama_barang'],
            'kode_barang'   => $validated['kode_barang'] ?? null,
            'kategori'      => $kategoriNama,
            'satuan'        => $validated['satuan'],
            'stok_awal'     => $validated['stok_awal'],
            'harga_beli'    => $validated['harga_beli'],
            'harga_jual'    => $validated['harga_jual'],
            'keterangan'    => $validated['keterangan'] ?? null,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Pengajuan tambah barang berhasil dikirim.');
    }

    /**
     * Officer/sysadmin: ambil list pending untuk work unit
     */
    public function index($workUnitId)
    {
        $pengajuans = PengajuanTambahBarang::where('work_unit_id', $workUnitId)
            ->pending()
            ->with('pengaju:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'nama_barang' => $p->nama_barang,
                'kode_barang' => $p->kode_barang,
                'kategori'    => $p->kategori,
                'satuan'      => $p->satuan,
                'stok_awal'   => (int) $p->stok_awal,
                'harga_beli'  => (float) $p->harga_beli,
                'harga_jual'  => (float) $p->harga_jual,
                'keterangan'  => $p->keterangan,
                'diajukan_oleh' => $p->pengaju?->name,
                'created_at'  => $p->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json(['pengajuans' => $pengajuans]);
    }

    /**
     * Officer/sysadmin: setujui → buat barang baru, atau tolak
     */
    public function proses(Request $request, $workUnitId, $pengajuanId)
    {
        $pengajuan = PengajuanTambahBarang::where('id', $pengajuanId)
            ->where('work_unit_id', $workUnitId)
            ->where('status', 'pending')
            ->firstOrFail();

        $validated = $request->validate([
            'aksi'              => 'required|in:setujui,tolak',
            'catatan_pengelola' => 'nullable|string|max:500',
            // Field tambahan saat setujui (officer bisa lengkapi)
            'kode_barang'       => 'nullable|string|max:100',
            'kategori_id'       => 'nullable|exists:kategori_barangs,id',
            'satuan'            => 'nullable|string|max:50',
            'stok_awal'         => 'nullable|integer|min:0',
            'harga_beli'        => 'nullable|numeric|min:0',
            'harga_jual'        => 'nullable|numeric|min:0',
            'minimum_stok'      => 'nullable|integer|min:0',
        ]);

        if ($validated['aksi'] === 'setujui') {
            // Tentukan kode barang final
            $kodeBarang = $validated['kode_barang'] ?? $pengajuan->kode_barang;

            // Cek duplikat kode jika ada
            if ($kodeBarang) {
                $duplikat = Barang::where('work_unit_id', $workUnitId)
                    ->where('kode_barang', $kodeBarang)
                    ->exists();

                if ($duplikat) {
                    return back()->withErrors(['pengajuan' => 'Kode barang sudah digunakan. Ubah kode sebelum menyetujui.']);
                }
            }

            // Resolve kategori nama dari id jika dikirim
            $kategoriNama = null;
            $kategoriId   = null;
            if (!empty($validated['kategori_id'])) {
                $kategoriId   = $validated['kategori_id'];
                $kategoriNama = KategoriBarang::find($kategoriId)?->nama ?? $pengajuan->kategori;
            } else {
                $kategoriNama = $pengajuan->kategori;
            }

            $stokAwal = $validated['stok_awal'] ?? $pengajuan->stok_awal ?? 0;

            $barang = Barang::create([
                'work_unit_id'  => $workUnitId,
                'nama_barang'   => $pengajuan->nama_barang,
                'kode_barang'   => $kodeBarang ?? ('BARU-' . $pengajuan->id),
                'kategori'      => $kategoriNama,
                'kategori_id'   => $kategoriId,
                'satuan'        => $validated['satuan'] ?? $pengajuan->satuan,
                'harga_beli'    => $validated['harga_beli'] ?? $pengajuan->harga_beli,
                'harga_jual'    => $validated['harga_jual'] ?? $pengajuan->harga_jual,
                'stok'          => $stokAwal,
                'minimum_stok'  => $validated['minimum_stok'] ?? 0,
                'deskripsi'     => $pengajuan->keterangan,
                'jenis_barang'  => 'jual_langsung',
                'is_active'     => true,
                'created_by'    => $pengajuan->diajukan_oleh,
            ]);

            $pengajuan->update([
                'status'            => 'disetujui',
                'barang_id'         => $barang->id,
                'diproses_oleh'     => auth()->id(),
                'catatan_pengelola' => $validated['catatan_pengelola'] ?? null,
                'diproses_at'       => now(),
            ]);

            return back()->with('success', "Pengajuan disetujui. Barang \"{$barang->nama_barang}\" berhasil ditambahkan.");
        } else {
            $pengajuan->update([
                'status'            => 'ditolak',
                'diproses_oleh'     => auth()->id(),
                'catatan_pengelola' => $validated['catatan_pengelola'] ?? null,
                'diproses_at'       => now(),
            ]);

            return back()->with('success', 'Pengajuan ditolak.');
        }
    }
}
