<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Barang;
use App\Models\PembagianMitraHistory;
use App\Models\PengajuanPencairan;
use App\Models\PenjualanItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MitraDashboardController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';
    public function index(Request $request)
    {
        $user      = Auth::user();
        $supplier  = Supplier::where('user_id', $user->id)->with('skemaBisnisAktif')->first();

        if (!$supplier) {
            return Inertia::render('Mitra/Dashboard', [
                'supplier'          => null,
                'pendapatan'        => $this->emptyPendapatan(),
                'produk'            => [],
                'riwayatPenjualan'  => [],
            ]);
        }

        $filter   = $request->input('filter', 'hari_ini');
        $dateRange = $this->getDateRange($filter, $request);

        $barangIds = Barang::where('supplier_id', $supplier->id)->pluck('id');

        $penjualanItems = PenjualanItem::whereIn('barang_id', $barangIds)
            ->whereHas('penjualan', function ($q) use ($dateRange) {
                $q->whereBetween('tanggal_transaksi', [$dateRange['from'], $dateRange['to']])
                  ->where('is_verified', true);
            })
            ->with(['penjualan:id,tanggal_transaksi,total,nomor_transaksi', 'barang:id,nama_barang,harga_jual,harga_beli,harga_konsinyasi,supplier_id'])
            ->get();

        $pendapatan = $this->hitungPendapatan($penjualanItems, $supplier);

        // Group qty terjual per barang_id (cast ke int untuk strict comparison)
        $terjualPerBarang = $penjualanItems->groupBy('barang_id')->map(fn ($items) => $items->sum('qty'));

        $produk = Barang::where('supplier_id', $supplier->id)
            ->select('id', 'nama_barang', 'harga_jual', 'harga_beli', 'harga_konsinyasi', 'stok', 'is_active')
            ->with('menuDisplay:id,barang_id,gambar')
            ->get()
            ->map(function ($barang) use ($terjualPerBarang) {
                return [
                    'id'          => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'harga_jual'  => $barang->harga_jual,
                    'stok'        => $barang->stok,
                    'is_active'   => $barang->is_active,
                    'gambar'      => $barang->menuDisplay?->gambar,
                    'terjual'     => (int) ($terjualPerBarang[$barang->id] ?? 0),
                ];
            });

        return Inertia::render('Mitra/Dashboard', [
            'supplier'   => [
                'id'              => $supplier->id,
                'kode_supplier'   => $supplier->kode_supplier,
                'nama'            => $supplier->nama,
                'perusahaan'      => $supplier->perusahaan,
                'tipe_mitra'      => $supplier->tipe_mitra,
                'tipe_label'      => ucfirst($supplier->tipe_mitra ?? 'Mitra'),
                'email'           => $supplier->email,
                'telepon'         => $supplier->telepon,
                'alamat'          => $supplier->alamat,
                'kota'            => $supplier->kota,
                'logo'            => $supplier->logo,
                'is_active'       => $supplier->is_active,
                'skema_bisnis'    => $supplier->skemaBisnisAktif ? [
                    'jenis_skema'           => $supplier->skemaBisnisAktif->jenis_skema,
                    'persentase_vendor'     => $supplier->skemaBisnisAktif->persentase_vendor,
                    'persentase_badan_usaha' => $supplier->skemaBisnisAktif->persentase_badan_usaha,
                    'nominal_flat'          => $supplier->skemaBisnisAktif->nominal_flat,
                ] : null,
            ],
            'pendapatan' => $pendapatan,
            'produk'     => $produk,
            'filter'     => $filter,
            'dari'       => $filter === 'custom' ? $request->input('dari') : null,
            'sampai'     => $filter === 'custom' ? $request->input('sampai') : null,
        ]);
    }

    private function getDateRange(string $filter, Request $request): array
    {
        $now = Carbon::now(self::TIMEZONE);

        return match ($filter) {
            'minggu_ini' => [
                'from' => $now->copy()->startOfWeek()->startOfDay(),
                'to'   => $now->copy()->endOfDay(),
            ],
            'bulan_ini' => [
                'from' => $now->copy()->startOfMonth()->startOfDay(),
                'to'   => $now->copy()->endOfDay(),
            ],
            'tahun_ini' => [
                'from' => $now->copy()->startOfYear()->startOfDay(),
                'to'   => $now->copy()->endOfDay(),
            ],
            'custom' => [
                'from' => Carbon::parse($request->input('dari', $now->toDateString()), self::TIMEZONE)->startOfDay(),
                'to'   => Carbon::parse($request->input('sampai', $now->toDateString()), self::TIMEZONE)->endOfDay(),
            ],
            default => [
                'from' => $now->copy()->startOfDay(),
                'to'   => $now->copy()->endOfDay(),
            ],
        };
    }

    private function hitungPendapatan($penjualanItems, Supplier $supplier): array
    {
        $skema = $supplier->skemaBisnisAktif;
        $totalPenjualan  = 0;
        $pendapatanMitra = 0;

        foreach ($penjualanItems as $item) {
            $subtotal = $item->subtotal;
            $totalPenjualan += $subtotal;

            if ($skema) {
                $bagian = $skema->hitungPembagian(
                    $subtotal,
                    $item->total_margin ?? 0,
                    $item->qty,
                    $item->harga_satuan,
                    $item->barang?->harga_konsinyasi ?? 0
                );
                $pendapatanMitra += $bagian['bagian_vendor'] ?? 0;
            } else {
                $pendapatanMitra += $subtotal;
            }
        }

        return [
            'total_penjualan'   => $totalPenjualan,
            'pendapatan_mitra'  => $pendapatanMitra,
            'jumlah_transaksi'  => $penjualanItems->groupBy('penjualan_id')->count(),
            'total_item_terjual' => $penjualanItems->sum('qty'),
        ];
    }

    public function updateProfil(Request $request)
    {
        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'email'      => 'nullable|email|max:255',
            'telepon'    => 'nullable|string|max:30',
            'kota'       => 'nullable|string|max:100',
            'alamat'     => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);

        return redirect()->route('mitra.dasbor')->with('success', 'Profil berhasil diperbarui.');
    }

    public function gantiPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama'       => 'required|string',
            'password_baru'       => 'required|string|min:8',
            'konfirmasi_password' => 'required|same:password_baru',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->route('mitra.dasbor')->with('success', 'Password berhasil diubah.');
    }

    public function riwayatPencairan(Request $request)
    {
        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $riwayat = PembagianMitraHistory::with(['workUnit', 'dicairkanOleh'])
            ->where('supplier_id', $supplier->id)
            ->orderByRaw("FIELD(status_pencairan, 'mengajukan', 'belum_dicairkan', 'dicairkan')")
            ->orderBy('periode_selesai', 'desc')
            ->paginate(20);

        $ringkasan = [
            'total_bagian_vendor'   => PembagianMitraHistory::where('supplier_id', $supplier->id)->sum('bagian_vendor'),
            'total_sudah_cair'      => PembagianMitraHistory::where('supplier_id', $supplier->id)->where('status_pencairan', 'dicairkan')->sum('bagian_vendor'),
            'total_belum_cair'      => PembagianMitraHistory::where('supplier_id', $supplier->id)->whereIn('status_pencairan', ['belum_dicairkan', 'mengajukan'])->sum('bagian_vendor'),
            'jumlah_sudah_cair'     => PembagianMitraHistory::where('supplier_id', $supplier->id)->where('status_pencairan', 'dicairkan')->count(),
            'jumlah_belum_cair'     => PembagianMitraHistory::where('supplier_id', $supplier->id)->whereIn('status_pencairan', ['belum_dicairkan', 'mengajukan'])->count(),
            'total_pengajuan'       => PengajuanPencairan::where('supplier_id', $supplier->id)->whereIn('status', ['menunggu', 'diproses'])->count(),
            'jumlah_pengajuan_diajukan' => PengajuanPencairan::where('supplier_id', $supplier->id)->whereIn('status', ['menunggu', 'diproses'])->sum('jumlah_diajukan'),
        ];

        $pengajuan = PengajuanPencairan::where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Mitra/RiwayatPencairan', [
            'supplier' => [
                'id'   => $supplier->id,
                'nama' => $supplier->nama,
                'kode' => $supplier->kode_supplier,
            ],
            'riwayat'   => $riwayat,
            'ringkasan' => $ringkasan,
            'pengajuan' => $pengajuan,
        ]);
    }

    public function ajukanPencairanBaru(Request $request)
    {
        $request->validate([
            'tanggal_dari'    => 'required|date',
            'tanggal_sampai'  => 'required|date|after_or_equal:tanggal_dari',
            'metode'          => 'required|in:tunai,transfer_bank',
            'detail_rekening' => 'nullable|string|max:255',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->with('skemaBisnisAktif')->firstOrFail();

        // Hitung pendapatan mitra di periode yang dipilih
        $barangIds = Barang::where('supplier_id', $supplier->id)->pluck('id');
        $penjualanItems = \App\Models\PenjualanItem::whereIn('barang_id', $barangIds)
            ->whereHas('penjualan', function ($q) use ($request) {
                $q->whereBetween('tanggal_transaksi', [
                    $request->tanggal_dari . ' 00:00:00',
                    $request->tanggal_sampai . ' 23:59:59',
                ])->where('is_verified', true);
            })
            ->with('barang:id,harga_konsinyasi')
            ->get();

        $jumlah = 0;
        $skema  = $supplier->skemaBisnisAktif;
        foreach ($penjualanItems as $item) {
            if ($skema) {
                $bagian  = $skema->hitungPembagian(
                    $item->subtotal,
                    $item->total_margin ?? 0,
                    $item->qty,
                    $item->harga_satuan,
                    $item->barang?->harga_konsinyasi ?? 0
                );
                $jumlah += $bagian['bagian_vendor'] ?? 0;
            } else {
                $jumlah += $item->subtotal;
            }
        }

        PengajuanPencairan::create([
            'supplier_id'     => $supplier->id,
            'jumlah_diajukan' => $jumlah,
            'tanggal_dari'    => $request->tanggal_dari,
            'tanggal_sampai'  => $request->tanggal_sampai,
            'metode'          => $request->metode,
            'detail_rekening' => $request->metode === 'transfer_bank' ? $request->detail_rekening : null,
            'catatan'         => $request->catatan,
            'status'          => 'menunggu',
        ]);

        return redirect()->back()->with('success', 'Pengajuan pencairan berhasil dikirim. Pengelola akan segera memproses.');
    }

    public function batalkanPengajuanBaru($id)
    {
        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $pengajuan = PengajuanPencairan::where('id', $id)
            ->where('supplier_id', $supplier->id)
            ->where('status', 'menunggu')
            ->firstOrFail();

        $pengajuan->delete();

        return redirect()->back()->with('success', 'Pengajuan pencairan dibatalkan.');
    }

    public function ajukanPencairan(Request $request, $id)
    {
        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $riwayat = PembagianMitraHistory::where('id', $id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        if ($riwayat->status_pencairan !== 'belum_dicairkan') {
            return redirect()->back()->with('error', 'Status pengajuan tidak dapat diubah.');
        }

        $riwayat->update(['status_pencairan' => 'mengajukan']);

        return redirect()->back()->with('success', 'Pengajuan pencairan berhasil dikirim.');
    }

    public function batalkanAjuan(Request $request, $id)
    {
        $user     = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $riwayat = PembagianMitraHistory::where('id', $id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        if ($riwayat->status_pencairan !== 'mengajukan') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan yang aktif.');
        }

        $riwayat->update(['status_pencairan' => 'belum_dicairkan']);

        return redirect()->back()->with('success', 'Pengajuan pencairan dibatalkan.');
    }

    private function emptyPendapatan(): array
    {
        return [
            'total_penjualan'    => 0,
            'pendapatan_mitra'   => 0,
            'jumlah_transaksi'   => 0,
            'total_item_terjual' => 0,
        ];
    }
}
