<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Meja;
use App\Models\PesananSelfOrder;
use App\Models\Penjualan;
use App\Models\Setting;
use App\Models\WorkUnit;
use App\Services\PoS\CheckoutValidationService;
use App\Services\PoS\SelfOrderToPenjualanService;
use App\Services\PoS\PenjualanTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BelanjaSelfOrderController extends Controller
{
    public function ping()
    {
        return response()->json([
            'success' => true,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function index(Request $request)
    {
        // Resolve meja dari QR token
        $lockedMeja = null;
        if ($request->has('meja') && $request->meja) {
            $mejaModel = Meja::where('qr_token', $request->meja)
                ->with('lokasiMeja')
                ->first();
            if ($mejaModel) {
                $lockedMeja = [
                    'id'        => $mejaModel->id,
                    'kode_meja' => $mejaModel->kode_meja,
                    'nama'      => $mejaModel->nama,
                    'nomor'     => $mejaModel->nomor,
                    'lokasi'    => $mejaModel->lokasiMeja?->nama,
                    'qr_token'  => $mejaModel->qr_token,
                ];
            }
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'display_order');
        $sortOrder = $request->get('sort_order', 'asc');

        // Auto-select toko jika hanya ada 1 dan tidak ada filter dari request
        $autoWorkUnit = null;
        if (!$request->work_unit) {
            $shopCount = WorkUnit::whereHas('barangs', fn($q) => $q->where('show_in_shop', true))
                ->where('type', 'Shop')
                ->count();
            if ($shopCount === 1) {
                $autoWorkUnit = WorkUnit::whereHas('barangs', fn($q) => $q->where('show_in_shop', true))
                    ->where('type', 'Shop')
                    ->value('id');
            }
        }

        $effectiveWorkUnit = $request->work_unit ?? $autoWorkUnit;

        // Build query based on sort to avoid eager loading conflicts
        if ($sortBy === 'display_order') {
            $query = Barang::leftJoin('menu_displays', 'barangs.id', '=', 'menu_displays.barang_id')
                  ->select('barangs.*', 'menu_displays.display_order as md_display_order')
                  ->where('barangs.show_in_shop', true)
                  ->orderBy('md_display_order', $sortOrder)
                  ->orderBy('barangs.nama_barang', 'asc');
        } else {
            $query = Barang::where('show_in_shop', true);

            if ($sortBy === 'price') {
                $query->orderBy('harga_jual', $sortOrder);
            } elseif ($sortBy === 'name') {
                $query->orderBy('nama_barang', $sortOrder);
            }
        }

        // Filter by category if provided
        if ($request->has('kategori') && $request->kategori) {
            $query->where('barangs.kategori_barang_id', $request->kategori);
        }

        // Filter by sub kategori if provided
        if ($request->has('sub_kategori') && $request->sub_kategori) {
            $query->where('barangs.sub_kategori', $request->sub_kategori);
        }

        // Filter by work unit (toko) - dari request atau auto-select
        if ($effectiveWorkUnit) {
            $query->where('barangs.work_unit_id', $effectiveWorkUnit);
        }

        // Search filter - include nama barang, deskripsi, dan varian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('barangs.nama_barang', 'like', "%{$search}%")
                  ->orWhere('barangs.deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('varians', function($varianQuery) use ($search) {
                      $varianQuery->where('nama_varian', 'like', "%{$search}%")
                                  ->where('is_active', true);
                  });
            });
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('barangs.harga_jual', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('barangs.harga_jual', '<=', $request->max_price);
        }

        // Filter only available items (those without display or with is_available = true)
        $query->where(function($q) {
            $q->whereDoesntHave('menuDisplay')
              ->orWhereHas('menuDisplay', function($subQ) {
                  $subQ->where('is_available', true);
              });
        });

        // Load relationships after query is built
        $query->with(['menuDisplay', 'workUnit', 'kategoriBarang', 'varians' => function($q) {
            $q->where('is_active', true)->orderBy('display_order');
        }]);

        $menus = $query->paginate($request->get('per_page', 12))
            ->through(function($barang) {
                $display = $barang->menuDisplay;

                $diskonAktif = $barang->isDiskonAktif();
                $nominalDiskon = $diskonAktif ? $barang->nominal_diskon : 0;

                return [
                    'id' => $barang->id,
                    'name' => $barang->nama_barang,
                    'description' => $display?->deskripsi_display ?? $barang->deskripsi ?? 'Menu lezat dari ' . ($barang->workUnit?->name ?? 'kantin'),
                    'price' => $barang->harga_jual,
                    'image' => $display?->gambar ?? null,
                    'is_active' => true,
                    'is_available' => $display?->is_available ?? true,
                    'is_featured' => $barang->is_featured ?? false,
                    'stok' => $barang->stok ?? 0,
                    'satuan' => $barang->satuan ?? 'pcs',
                    'sub_kategori' => $barang->sub_kategori,
                    'diskon_tipe' => $barang->diskon_tipe,
                    'diskon_nilai' => $barang->diskon_nilai,
                    'diskon_aktif' => $diskonAktif,
                    'nominal_diskon' => $nominalDiskon,
                    'category' => [
                        'id' => $barang->kategoriBarang?->id,
                        'name' => $barang->kategoriBarang?->nama ?? $barang->kategori ?? 'Makanan & Minuman',
                    ],
                    'work_unit' => [
                        'id' => $barang->workUnit?->id,
                        'name' => $barang->workUnit?->name ?? 'Kantin BPPU',
                    ],
                    'varians' => $barang->varians ? $barang->varians->map(function($varian) {
                        return [
                            'id' => $varian->id,
                            'nama_varian' => $varian->nama_varian,
                            'deskripsi' => $varian->deskripsi,
                            'harga_jual' => $varian->harga_jual,
                            'is_active' => $varian->is_active,
                        ];
                    }) : [],
                ];
            });

        // Get all categories for filter
        $categories = KategoriBarang::whereHas('barangs', function($q) {
                $q->where('show_in_shop', true);
            })
            ->orderBy('nama')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->nama,
                ];
            });

        // Get all sub categories
        $subKategories = Barang::where('show_in_shop', true)
            ->whereNotNull('sub_kategori')
            ->distinct()
            ->pluck('sub_kategori')
            ->sort()
            ->values();

        // Get all work units (toko) that have shop items
        $workUnits = WorkUnit::whereHas('barangs', function($q) {
                $q->where('show_in_shop', true);
            })
            ->where('type', 'Shop')
            ->orderBy('name')
            ->get()
            ->map(function($workUnit) {
                return [
                    'id' => $workUnit->id,
                    'name' => $workUnit->name,
                ];
            });

        // Get featured menus (6 unggulan untuk carousel)
        $featuredMenus = Barang::where('show_in_shop', true)
            ->where('is_featured', true)
            ->where(function($q) {
                $q->whereDoesntHave('menuDisplay')
                  ->orWhereHas('menuDisplay', function($subQ) {
                      $subQ->where('is_available', true);
                  });
            })
            ->with(['menuDisplay', 'workUnit', 'kategoriBarang', 'varians' => function($q) {
                $q->where('is_active', true)->orderBy('display_order');
            }])
            ->take(6)
            ->get()
            ->map(function($barang) {
                $display = $barang->menuDisplay;
                return [
                    'id' => $barang->id,
                    'name' => $barang->nama_barang,
                    'description' => $display?->deskripsi_display ?? $barang->deskripsi ?? 'Menu lezat dari ' . ($barang->workUnit?->name ?? 'kantin'),
                    'price' => $barang->harga_jual,
                    'image' => $display?->gambar ?? null,
                    'sub_kategori' => $barang->sub_kategori,
                    'category' => [
                        'id' => $barang->kategoriBarang?->id,
                        'name' => $barang->kategoriBarang?->nama ?? 'Makanan & Minuman',
                    ],
                    'work_unit' => [
                        'id' => $barang->workUnit?->id,
                        'name' => $barang->workUnit?->name ?? 'Kantin BPPU',
                    ],
                    'varians' => $barang->varians ? $barang->varians->map(function($varian) {
                        return [
                            'id' => $varian->id,
                            'nama_varian' => $varian->nama_varian,
                            'deskripsi' => $varian->deskripsi,
                            'harga_jual' => $varian->harga_jual,
                            'is_active' => $varian->is_active,
                        ];
                    }) : [],
                ];
            });

        $user = auth()->user();
        $isAuthenticated = $user && $user->hasRole('buyer');

        return Inertia::render('Belanja/Index', [
            'menus' => $menus,
            'categories' => $categories,
            'subKategories' => $subKategories,
            'workUnits' => $workUnits,
            'featuredMenus' => $featuredMenus,
            'selfOrderConfig' => [
                'minimal_order'      => (int) Setting::get('belanja_minimal_order', 20000),
                'biaya_layanan_aktif'=> (bool) (int) Setting::get('belanja_biaya_layanan_aktif', 0),
                'biaya_layanan'      => (int) Setting::get('belanja_biaya_layanan', 0),
            ],
            'filters' => [
                'search' => $request->search,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'work_unit' => $effectiveWorkUnit,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $request->get('per_page', 12),
            ],
            'isAuthenticated' => $isAuthenticated,
            'user' => $isAuthenticated ? [
                'name' => $user->name,
                'email' => $user->email,
                'member_code' => $user->member_code,
                'total_points' => $user->total_points,
            ] : null,
            'lockedMeja' => $lockedMeja,
            'mejaQrToken' => $request->meja,
        ]);
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();
        $isAuthBuyer = $user && $user->hasRole('buyer');

        // Guest diizinkan hanya jika ada meja_qr_token valid
        $guestMeja = null;
        if (!$isAuthBuyer) {
            if (!$request->meja_qr_token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $guestMeja = Meja::where('qr_token', $request->meja_qr_token)->with('lokasiMeja')->first();
            if (!$guestMeja) {
                return response()->json(['message' => 'QR meja tidak valid'], 403);
            }
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.varian' => 'nullable|array',
            'items.*.varian.id' => 'required_with:items.*.varian|integer',
            'items.*.varian.nama_varian' => 'required_with:items.*.varian|string',
            'nama_pemesan' => $isAuthBuyer ? 'nullable|string|max:100' : 'required|string|max:100',
            'catatan' => 'nullable|string|max:255',
        ]);

        // Validasi harga, stok, dan varian dari DB
        $validator = new CheckoutValidationService();
        $validation = $validator->validate($request->items, 'show_in_shop');
        if (!$validation['valid']) {
            return response()->json([
                'message' => 'Beberapa item tidak dapat dipesan.',
                'errors'  => $validation['errors'],
            ], 422);
        }

        $items = $validation['validatedItems'];
        $subtotal = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Cek minimal order
        $minimalOrder = (int) Setting::get('belanja_minimal_order', 20000);
        if ($minimalOrder > 0 && $subtotal < $minimalOrder) {
            return response()->json([
                'message' => 'Minimal order adalah Rp ' . number_format($minimalOrder, 0, ',', '.'),
            ], 422);
        }

        // Hitung biaya layanan
        $biayaLayananAktif = (bool) (int) Setting::get('belanja_biaya_layanan_aktif', 0);
        $biayaLayanan = $biayaLayananAktif ? (int) Setting::get('belanja_biaya_layanan', 0) : 0;
        $total = $subtotal + $biayaLayanan;

        $namaPemesan = $isAuthBuyer ? $user->name : $request->nama_pemesan;
        $buyerId = $isAuthBuyer ? $user->id : null;

        // Resolve meja
        $mejaId = null;
        $namaMeja = null;
        if ($guestMeja) {
            $mejaId = $guestMeja->id;
            $namaMeja = trim(($guestMeja->lokasiMeja?->nama ? $guestMeja->lokasiMeja->nama . ' - ' : '') . ($guestMeja->nama ?: $guestMeja->kode_meja));
        } elseif ($request->meja_id) {
            $mejaId = $request->meja_id;
        }

        $pesanan = DB::transaction(function () use ($items, $subtotal, $total, $biayaLayanan, $request, $buyerId, $namaPemesan, $mejaId, $namaMeja) {
            $nomor = PesananSelfOrder::generateNomorAntrian();

            return PesananSelfOrder::create([
                'nomor_antrian'   => $nomor,
                'tanggal_antrian' => today(),
                'buyer_id'        => $buyerId,
                'nama_pemesan'    => $namaPemesan,
                'items'           => $items,
                'subtotal'        => $subtotal,
                'biaya_layanan'   => $biayaLayanan,
                'total'           => $total,
                'catatan'         => $request->catatan,
                'status'          => 'menunggu',
                'meja_id'         => $mejaId,
                'nama_meja'       => $namaMeja,
            ]);
        });

        // Hitung total antrian aktif hari ini (yang belum selesai/dibatalkan)
        $antrianAktif = PesananSelfOrder::where('tanggal_antrian', today())
            ->whereIn('status', ['menunggu', 'diproses'])
            ->count();

        return response()->json([
            'success' => true,
            'pesanan_id' => $pesanan->id,
            'nomor_antrian' => $pesanan->nomor_antrian,
            'antrian_aktif' => $antrianAktif,
        ]);
    }

    public function invoice(int $id)
    {
        $pesanan = PesananSelfOrder::with('buyer')->findOrFail($id);

        // Hitung posisi antrian (berapa antrian menunggu/diproses sebelum ini)
        $posisiAntrian = PesananSelfOrder::where('tanggal_antrian', $pesanan->tanggal_antrian)
            ->whereIn('status', ['menunggu', 'diproses'])
            ->where('nomor_antrian', '<', $pesanan->nomor_antrian)
            ->count();

        return Inertia::render('Belanja/Invoice', [
            'pesanan' => [
                'id' => $pesanan->id,
                'nomor_antrian' => $pesanan->nomor_antrian,
                'tanggal_antrian' => $pesanan->tanggal_antrian->format('d/m/Y'),
                'nama_pemesan' => $pesanan->nama_pemesan,
                'items' => $pesanan->items,
                'subtotal' => $pesanan->subtotal,
                'total' => $pesanan->total,
                'catatan' => $pesanan->catatan,
                'status' => $pesanan->status,
                'label_status' => $pesanan->label_status,
                'created_at' => $pesanan->created_at->format('H:i'),
                'alasan_batal' => $pesanan->alasan_batal,
                'bukti_bayar' => $pesanan->bukti_bayar ? asset('storage/' . $pesanan->bukti_bayar) : null,
            ],
            'posisi_antrian' => $posisiAntrian,
        ]);
    }

    public function uploadBuktiBayar(Request $request, int $id)
    {
        $request->validate([
            'bukti' => 'required|image|max:5120',
        ]);

        $pesanan = PesananSelfOrder::findOrFail($id);

        if (!in_array($pesanan->status, ['menunggu', 'diproses'])) {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah tidak aktif'], 422);
        }

        $path = $request->file('bukti')->store('bukti-bayar-toko', 'public');

        $pesanan->update(['bukti_bayar' => $path]);

        return response()->json([
            'success' => true,
            'bukti_bayar' => asset('storage/' . $path),
        ]);
    }

    public function statusApi(int $id)
    {
        $pesanan = PesananSelfOrder::findOrFail($id);

        $posisiAntrian = PesananSelfOrder::where('tanggal_antrian', $pesanan->tanggal_antrian)
            ->whereIn('status', ['menunggu', 'diproses'])
            ->where('nomor_antrian', '<', $pesanan->nomor_antrian)
            ->count();

        return response()->json([
            'status' => $pesanan->status,
            'label_status' => $pesanan->label_status,
            'posisi_antrian' => $posisiAntrian,
            'alasan_batal' => $pesanan->alasan_batal,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,siap,selesai,dibatalkan',
            'alasan_batal' => 'nullable|string|max:255',
            'kode_alasan_batal' => 'nullable|string|max:50',
        ]);

        $pesanan = PesananSelfOrder::findOrFail($id);
        $oldStatus = $pesanan->status;

        // PENTING: Cegah perubahan apapun pada pesanan yang sudah tercatat sebagai penjualan
        if ($pesanan->penjualan_id) {
            // Cek apakah ada perubahan status
            if ($request->status !== $oldStatus) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pesanan tidak dapat diubah karena sudah tercatat sebagai penjualan. Untuk membatalkan, gunakan menu Rekap Penjualan.',
                ], 422);
            }

            // Jika tidak ada perubahan, return success tanpa update
            return response()->json([
                'success' => true,
                'status' => $pesanan->status,
                'label_status' => $pesanan->label_status,
                'changed' => false,
                'penjualan' => [
                    'penjualan_id' => $pesanan->penjualan_id,
                    'nomor_transaksi' => $pesanan->penjualan?->nomor_transaksi,
                ],
            ]);
        }

        // Jika status berubah menjadi selesai, convert ke penjualan DULU sebelum update status
        $penjualanInfo = null;
        if ($request->status === 'selesai' && $oldStatus !== 'selesai') {
            $service = new SelfOrderToPenjualanService();
            $result = $service->convertToPenjualan($pesanan);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal tercatat sebagai penjualan: ' . $result['error'],
                ], 500);
            }

            $penjualanInfo = [
                'penjualan_id'    => $result['penjualan_id'],
                'nomor_transaksi' => $result['nomor_transaksi'],
            ];
        }

        $updateData = ['status' => $request->status];

        if ($request->status === 'dibatalkan') {
            $updateData['alasan_batal'] = $request->alasan_batal;
            $updateData['kode_alasan_batal'] = $request->kode_alasan_batal;
        }

        $pesanan->update($updateData);

        return response()->json([
            'success'      => true,
            'status'       => $pesanan->status,
            'label_status' => $pesanan->label_status,
            'changed'      => $oldStatus !== $request->status,
            'penjualan'    => $penjualanInfo,
        ]);
    }

    public function editItems(Request $request, int $id)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|integer',
            'items.*.name'     => 'required|string',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.varian'   => 'nullable|array',
            'catatan'          => 'nullable|string|max:255',
        ]);

        $pesanan = PesananSelfOrder::findOrFail($id);

        if ($pesanan->penjualan_id) {
            return response()->json(['success' => false, 'error' => 'Pesanan sudah tercatat sebagai penjualan, tidak dapat diubah.'], 422);
        }

        if (!in_array($pesanan->status, ['menunggu', 'diproses'])) {
            return response()->json(['success' => false, 'error' => 'Pesanan hanya dapat diedit saat status menunggu atau diproses.'], 422);
        }

        $items    = $request->items;
        $subtotal = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        $pesanan->update([
            'items'    => $items,
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'catatan'  => $request->catatan ?? $pesanan->catatan,
        ]);

        return response()->json([
            'success'      => true,
            'items'        => $pesanan->items,
            'total'        => (float) $pesanan->total,
            'catatan'      => $pesanan->catatan,
            'label_status' => $pesanan->label_status,
        ]);
    }

    public function katalogKasir()
    {
        $items = Barang::where('show_in_shop', true)
            ->where('stok', '>', 0)
            ->select('id', 'nama_barang', 'harga_jual', 'stok', 'gambar', 'kategori_barang_id', 'work_unit_id')
            ->with(['varians' => fn($q) => $q->select('id', 'barang_id', 'nama_varian', 'harga_tambahan')])
            ->orderBy('nama_barang')
            ->get()
            ->map(fn($b) => [
                'id'      => $b->id,
                'name'    => $b->nama_barang,
                'price'   => (float) $b->harga_jual,
                'stok'    => $b->stok,
                'gambar'  => $b->gambar ? asset('storage/' . $b->gambar) : null,
                'varians' => $b->varians->map(fn($v) => [
                    'id'             => $v->id,
                    'nama_varian'    => $v->nama_varian,
                    'harga_tambahan' => (float) $v->harga_tambahan,
                ])->values(),
            ]);

        return response()->json(['items' => $items]);
    }

    public function cancelPesanan(int $id)
    {
        $pesanan = PesananSelfOrder::findOrFail($id);

        // Verifikasi ownership - hanya buyer yang punya pesanan ini yang bisa cancel
        $user = auth()->user();
        if (!$user || $pesanan->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hanya bisa cancel jika status masih menunggu
        if ($pesanan->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak bisa dibatalkan karena sudah diproses.',
            ], 400);
        }

        $pesanan->update(['status' => 'dibatalkan']);

        return response()->json([
            'success' => true,
            'status' => $pesanan->status,
            'label_status' => $pesanan->label_status,
        ]);
    }

    /**
     * Batalkan pesanan yang sudah tercatat sebagai penjualan
     * Method ini akan:
     * 1. Batalkan penjualan (restore stok, refund poin)
     * 2. Update status pesanan jadi 'dibatalkan'
     */
    public function cancelPesananWithPenjualan(Request $request, int $id)
    {
        $request->validate([
            'alasan_batal' => 'nullable|string|max:255',
            'kode_alasan_batal' => 'nullable|string|max:50',
        ]);

        $pesanan = PesananSelfOrder::with('penjualan')->findOrFail($id);

        // Validasi bahwa pesanan sudah tercatat sebagai penjualan
        if (!$pesanan->penjualan_id || !$pesanan->penjualan) {
            return response()->json([
                'success' => false,
                'error' => 'Pesanan belum tercatat sebagai penjualan. Gunakan endpoint update status biasa.',
            ], 422);
        }

        // Validasi bahwa pesanan belum dibatalkan
        if ($pesanan->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'error' => 'Pesanan sudah dibatalkan sebelumnya.',
            ], 422);
        }

        // Batalkan penjualan menggunakan service
        $penjualanService = app(PenjualanTransactionService::class);
        $result = $penjualanService->cancel($pesanan->penjualan);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 500);
        }

        // Update status pesanan
        $pesanan->update([
            'status' => 'dibatalkan',
            'alasan_batal' => $request->alasan_batal,
            'kode_alasan_batal' => $request->kode_alasan_batal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan dan penjualan berhasil dibatalkan. Stok telah dikembalikan dan poin telah disesuaikan.',
            'status' => $pesanan->status,
            'label_status' => $pesanan->label_status,
        ]);
    }

    public function pesananKantin()
    {
        $pesanan = PesananSelfOrder::with('penjualan')
            ->where('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'asc')
            ->get()
            ->map(fn($p) => $this->formatPesanan($p));

        $stats = [
            'menunggu'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'menunggu')->count(),
            'diproses'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'diproses')->count(),
            'siap'       => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'siap')->count(),
            'selesai'    => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'selesai')->count(),
            'dibatalkan' => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'dibatalkan')->count(),
        ];

        return Inertia::render('PoS/PesananKantin', [
            'pesanan' => $pesanan,
            'stats' => $stats,
        ]);
    }

    public function pesananToko()
    {
        $pesanan = PesananSelfOrder::with('penjualan')
            ->where('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'asc')
            ->get()
            ->map(fn($p) => $this->formatPesanan($p));

        $stats = [
            'menunggu'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'menunggu')->count(),
            'diproses'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'diproses')->count(),
            'siap'       => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'siap')->count(),
            'selesai'    => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'selesai')->count(),
            'dibatalkan' => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'dibatalkan')->count(),
        ];

        return inertia('PoS/PesananToko', [
            'pesanan' => $pesanan,
            'stats' => $stats,
        ]);
    }

    public function pesananKantinApi()
    {
        $pesanan = PesananSelfOrder::with('penjualan')
            ->where('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'asc')
            ->get()
            ->map(fn($p) => $this->formatPesanan($p));

        $stats = [
            'menunggu'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'menunggu')->count(),
            'diproses'   => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'diproses')->count(),
            'siap'       => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'siap')->count(),
            'selesai'    => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'selesai')->count(),
            'dibatalkan' => PesananSelfOrder::where('tanggal_antrian', today())->where('status', 'dibatalkan')->count(),
        ];

        return response()->json([
            'pesanan' => $pesanan,
            'stats' => $stats,
        ]);
    }

    private function formatPesanan(PesananSelfOrder $p): array
    {
        return [
            'id'                => $p->id,
            'nomor_antrian'     => $p->nomor_antrian,
            'tanggal'           => $p->tanggal_antrian->format('d/m/Y'),
            'nama_pemesan'      => $p->nama_pemesan,
            'items'             => $p->items,
            'total'             => (float) $p->total,
            'catatan'           => $p->catatan,
            'status'            => $p->status,
            'label_status'      => $p->label_status,
            'waktu'             => $p->created_at->format('H:i'),
            'alasan_batal'      => $p->alasan_batal ?? null,
            'kode_alasan_batal' => $p->kode_alasan_batal ?? null,
            'penjualan_id'      => $p->penjualan_id ?? null,
            'nomor_transaksi'   => $p->penjualan?->nomor_transaksi ?? null,
            'bukti_bayar'       => $p->bukti_bayar ?? null,
        ];
    }
}
