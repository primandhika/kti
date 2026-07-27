<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Meja;
use App\Models\LokasiMeja;
use App\Models\PesananSelfOrder;
use App\Models\Penjualan;
use App\Models\Setting;
use App\Models\WorkUnit;
use App\Services\ActivityLogger;
use App\Services\PoS\CheckoutValidationService;
use App\Services\PoS\SelfOrderToPenjualanService;
use App\Services\PoS\PenjualanTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SelfOrderController extends Controller
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
        // Resolve locked meja & kantin default dari URL param ?meja=QR_TOKEN (di awal agar bisa dipakai filter)
        $lockedMeja = null;
        $defaultWorkUnitFromMeja = null;
        if ($request->has('meja') && $request->meja) {
            $mejaFromParam = Meja::where('qr_token', $request->meja)
                ->where('is_active', true)
                ->with('lokasiMeja')
                ->first();
            if ($mejaFromParam) {
                $lockedMeja = [
                    'id'        => $mejaFromParam->id,
                    'kode_meja' => $mejaFromParam->kode_meja,
                    'nama'      => $mejaFromParam->nama,
                    'nomor'     => $mejaFromParam->nomor,
                    'lokasi'    => $mejaFromParam->lokasiMeja?->nama,
                    'is_public' => (bool) $mejaFromParam->lokasiMeja?->is_public,
                ];
                if ($mejaFromParam->lokasiMeja?->default_work_unit_id && !$request->work_unit) {
                    $defaultWorkUnitFromMeja = $mejaFromParam->lokasiMeja->default_work_unit_id;
                }
            }
        }

        // Effective work_unit filter (dari request atau default lokasi meja)
        $effectiveWorkUnit = $request->work_unit ?? $defaultWorkUnitFromMeja;

        // Sort options
        $sortBy = $request->get('sort_by', 'display_order');
        $sortOrder = $request->get('sort_order', 'asc');

        // Sub-query for total sold per barang
        $soldSubquery = \App\Models\PenjualanItem::selectRaw('barang_id, SUM(qty) as total_sold')
            ->groupBy('barang_id');

        // Build query based on sort to avoid eager loading conflicts
        if ($sortBy === 'display_order') {
            $query = Barang::leftJoin('menu_displays', 'barangs.id', '=', 'menu_displays.barang_id')
                  ->leftJoinSub($soldSubquery, 'sold_agg', 'barangs.id', '=', 'sold_agg.barang_id')
                  ->select('barangs.*', 'menu_displays.display_order as md_display_order', 'sold_agg.total_sold')
                  ->where('barangs.is_menu_item', true)
                  ->orderByRaw('CASE WHEN barangs.stok > 0 THEN 0 ELSE 1 END asc')
                  ->orderByRaw('COALESCE(sold_agg.total_sold, 0) DESC')
                  ->orderBy('md_display_order', $sortOrder)
                  ->orderBy('barangs.nama_barang', 'asc');
        } elseif ($sortBy === 'terjual') {
            $query = Barang::leftJoinSub($soldSubquery, 'sold_agg', 'barangs.id', '=', 'sold_agg.barang_id')
                  ->select('barangs.*', 'sold_agg.total_sold')
                  ->where('barangs.is_menu_item', true)
                  ->orderByRaw('COALESCE(sold_agg.total_sold, 0) DESC');
        } else {
            $query = Barang::leftJoinSub($soldSubquery, 'sold_agg', 'barangs.id', '=', 'sold_agg.barang_id')
                  ->select('barangs.*', 'sold_agg.total_sold')
                  ->where('barangs.is_menu_item', true);

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

        // Filter by work unit (dari request atau default lokasi meja)
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

        // By default, hide items with stok = 0 unless explicitly requested
        if (!$request->boolean('show_habis', false)) {
            $query->where('barangs.stok', '>', 0);
        }

        // Load relationships after query is built
        $query->with(['menuDisplay', 'workUnit', 'kategoriBarang', 'varians' => function($q) {
            $q->where('is_active', true)->orderBy('display_order');
        }]);

        $menus = $query->paginate($request->get('per_page', 12))->withQueryString()
            ->through(function($barang) {
                $display = $barang->menuDisplay;

                $diskonAktif = $barang->isDiskonAktif();
                $nominalDiskon = $diskonAktif ? $barang->nominal_diskon : 0;

                $totalTerjual = (int) ($barang->total_sold ?? 0);

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
                    'total_terjual' => $totalTerjual,
                    'is_best_seller' => $totalTerjual >= 150,
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
                $q->where('is_menu_item', true);
            })
            ->orderBy('nama')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->nama,
                ];
            });

        // Get sub categories — filter by work_unit aktif agar pills relevan
        $subKategoriQuery = Barang::where('is_menu_item', true)
            ->whereNotNull('sub_kategori')
            ->where('stok', '>', 0);
        if ($effectiveWorkUnit) {
            $subKategoriQuery->where('work_unit_id', $effectiveWorkUnit);
        }
        $subKategories = $subKategoriQuery->distinct()->pluck('sub_kategori')->sortBy(fn($s) => strtolower($s))->values();

        // Get all work units (kantin) that have menu items
        $workUnits = WorkUnit::whereHas('barangs', function($q) {
                $q->where('is_menu_item', true);
            })
            ->where('type', 'Kantin')
            ->orderBy('name')
            ->get()
            ->map(function($workUnit) {
                return [
                    'id' => $workUnit->id,
                    'name' => $workUnit->name,
                    'logo' => $workUnit->logo,
                    'location' => $workUnit->location,
                ];
            });

        // Get featured menus (6 unggulan untuk carousel)
        $featuredMenus = Barang::where('is_menu_item', true)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->where('stok', '>', 0)
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

        // Ambil semua lokasi meja beserta meja aktifnya untuk pilihan
        $lokasiMejas = LokasiMeja::where('is_active', true)
            ->with(['mejas' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order')->orderBy('nomor');
            }])
            ->orderBy('display_order')
            ->get()
            ->map(fn($l) => [
                'id'    => $l->id,
                'nama'  => $l->nama,
                'kode'  => $l->kode,
                'mejas' => $l->mejas->map(fn($m) => [
                    'id'        => $m->id,
                    'kode_meja' => $m->kode_meja,
                    'nama'      => $m->nama,
                    'nomor'     => $m->nomor,
                ]),
            ]);

        // Resolve locked meja dari URL param ?meja=QR_TOKEN
        // Default meja dari profil user (jika logged in)
        $defaultMejaUser = null;
        if ($isAuthenticated && $user->default_meja_id) {
            $user->load('defaultMeja.lokasi');
            $dm = $user->defaultMeja;
            if ($dm && $dm->is_active) {
                $defaultMejaUser = [
                    'id'        => $dm->id,
                    'kode_meja' => $dm->kode_meja,
                    'nama'      => $dm->nama,
                    'nomor'     => $dm->nomor,
                    'lokasi'    => $dm->lokasi?->nama,
                ];
            }
        }

        return Inertia::render('SelfOrder/Index', [
            'menus' => $menus,
            'categories' => $categories,
            'subKategories' => $subKategories,
            'workUnits' => $workUnits,
            'featuredMenus' => $featuredMenus,
            'lokasiMejas' => $lokasiMejas,
            'lockedMeja' => $lockedMeja,
            'defaultMejaUser' => $defaultMejaUser,
            'selfOrderConfig' => [
                'minimal_order'       => (int) Setting::get('kantin_minimal_order', 0),
                'biaya_layanan_aktif' => (bool) (int) Setting::get('kantin_biaya_layanan_aktif', 0),
                'biaya_layanan'       => (int) Setting::get('kantin_biaya_layanan', 0),
                'verif_lokasi_aktif'  => (bool) (int) Setting::get('kantin_verif_lokasi_aktif', 0),
            ],
            'filters' => [
                'search' => $request->search,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'work_unit' => $request->work_unit ?? $defaultWorkUnitFromMeja,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $request->get('per_page', 12),
                'show_habis' => $request->boolean('show_habis', false),
                'meja' => $request->meja,
            ],
            'isAuthenticated' => $isAuthenticated,
            'user' => $isAuthenticated ? [
                'name' => $user->name,
                'email' => $user->email,
                'member_code' => $user->member_code,
                'total_points' => $user->total_points,
            ] : null,
        ]);
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();
        $isAuthBuyer = $user && $user->hasRole('buyer');

        // Guest checkout hanya diizinkan jika ada meja_qr_token yang valid
        $guestMeja = null;
        if (!$isAuthBuyer) {
            if (!$request->meja_qr_token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $guestMeja = Meja::where('qr_token', $request->meja_qr_token)
                ->where('is_active', true)
                ->with('lokasiMeja')
                ->first();
            if (!$guestMeja) {
                return response()->json(['message' => 'Token meja tidak valid'], 401);
            }

            // Validasi lokasi untuk area publik (hanya jika fitur aktif di settings)
            $verifLokasiAktif = (bool) (int) \App\Models\Setting::get('kantin_verif_lokasi_aktif', 0);
            if ($verifLokasiAktif && $guestMeja->lokasiMeja?->is_public) {
                $lat = $request->input('latitude');
                $lng = $request->input('longitude');

                if ($lat === null || $lng === null) {
                    return response()->json([
                        'message' => 'Izin lokasi diperlukan untuk memesan di area publik.',
                        'location_required' => true,
                    ], 422);
                }

                $lokasiRef = $guestMeja->lokasiMeja;
                if ($lokasiRef->latitude !== null && $lokasiRef->longitude !== null) {
                    $radius = $lokasiRef->radius_meter ?? 200;
                    $distance = $this->haversineDistance(
                        (float) $lat, (float) $lng,
                        $lokasiRef->latitude, $lokasiRef->longitude
                    );
                    if ($distance > $radius) {
                        return response()->json([
                            'message' => 'Kamu tampaknya tidak berada di sekitar lokasi ini. Pastikan kamu memesan dari tempat yang benar.',
                            'location_too_far' => true,
                            'distance_meter' => (int) $distance,
                        ], 422);
                    }
                }
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
            'catatan'          => 'nullable|string|max:255',
            'meja_id'          => 'nullable|integer|exists:mejas,id',
            'nama_pemesan'     => $isAuthBuyer ? 'nullable|string|max:100' : 'required|string|max:100',
            'metode_bayar'     => 'nullable|in:tunai,qris',
            'tipe_pengiriman'  => 'required|in:antar,pickup',
            'estimasi_ambil'   => 'nullable|string|max:100',
        ]);

        // Validasi harga, stok, dan varian dari DB
        $validator = new CheckoutValidationService();
        $validation = $validator->validate($request->items, 'is_menu_item');
        if (!$validation['valid']) {
            return response()->json([
                'message' => 'Beberapa item tidak dapat dipesan.',
                'errors'  => $validation['errors'],
            ], 422);
        }

        $items = $validation['validatedItems'];
        $subtotal = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Cek minimal order
        $minimalOrder = (int) Setting::get('kantin_minimal_order', 0);
        if ($minimalOrder > 0 && $subtotal < $minimalOrder) {
            return response()->json([
                'message' => 'Minimal order adalah Rp ' . number_format($minimalOrder, 0, ',', '.'),
            ], 422);
        }

        // Hitung biaya layanan
        $biayaLayananAktif = (bool) (int) Setting::get('kantin_biaya_layanan_aktif', 0);
        $biayaLayanan = $biayaLayananAktif ? (int) Setting::get('kantin_biaya_layanan', 0) : 0;
        $total = $subtotal + $biayaLayanan;

        // Resolve meja: untuk guest pakai dari token, untuk auth bisa dari meja_id
        $mejaId = null;
        $namaMeja = null;
        if ($guestMeja) {
            $mejaId = $guestMeja->id;
            $namaMeja = trim(($guestMeja->lokasiMeja?->nama ? $guestMeja->lokasiMeja->nama . ' - ' : '') . ($guestMeja->nama ?: $guestMeja->kode_meja));
        } elseif ($request->meja_id) {
            $meja = Meja::with('lokasiMeja')->find($request->meja_id);
            if ($meja) {
                $mejaId = $meja->id;
                $namaMeja = trim(($meja->lokasiMeja?->nama ? $meja->lokasiMeja->nama . ' - ' : '') . ($meja->nama ?: $meja->kode_meja));
            }
        }

        $namaPemesan = $isAuthBuyer ? $user->name : $request->nama_pemesan;
        $buyerId = $isAuthBuyer ? $user->id : null;

        $metodeBayar = $request->metode_bayar ?? null;
        $tipePengiriman = $request->tipe_pengiriman;
        $estimasiAmbil = $tipePengiriman === 'pickup' ? ($request->estimasi_ambil ?? null) : null;

        $pesanan = DB::transaction(function () use ($items, $subtotal, $total, $biayaLayanan, $request, $buyerId, $namaPemesan, $mejaId, $namaMeja, $metodeBayar, $tipePengiriman, $estimasiAmbil) {
            $nomor = PesananSelfOrder::generateNomorAntrian();

            return PesananSelfOrder::create([
                'nomor_antrian'   => $nomor,
                'tanggal_antrian' => today(),
                'buyer_id'        => $buyerId,
                'meja_id'         => $mejaId,
                'nama_meja'       => $namaMeja,
                'nama_pemesan'    => $namaPemesan,
                'items'           => $items,
                'subtotal'        => $subtotal,
                'biaya_layanan'   => $biayaLayanan,
                'total'           => $total,
                'catatan'         => $request->catatan,
                'tipe_pengiriman' => $tipePengiriman,
                'estimasi_ambil'  => $estimasiAmbil,
                'metode_bayar'    => $metodeBayar,
                'status'          => 'menunggu',
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
        $pesanan = PesananSelfOrder::with(['buyer', 'meja'])->findOrFail($id);

        // Hitung posisi antrian (berapa antrian menunggu/diproses SEBELUM pesanan ini)
        $posisiAntrian = PesananSelfOrder::where('tanggal_antrian', $pesanan->tanggal_antrian)
            ->whereIn('status', ['menunggu', 'diproses'])
            ->where('nomor_antrian', '<', $pesanan->nomor_antrian)
            ->count();

        // Back URL: jika ada meja dengan qr_token, kembalikan ke URL meja
        $backUrl = '/kantin/self-order';
        if ($pesanan->meja && $pesanan->meja->qr_token) {
            $backUrl = '/kantin/self-order?meja=' . $pesanan->meja->qr_token;
        }

        return Inertia::render('SelfOrder/Invoice', [
            'pesanan' => [
                'id' => $pesanan->id,
                'nomor_antrian' => $pesanan->nomor_antrian,
                'tanggal_antrian' => $pesanan->tanggal_antrian->format('d/m/Y'),
                'nama_pemesan' => $pesanan->nama_pemesan,
                'nama_meja' => $pesanan->nama_meja,
                'items' => $pesanan->items,
                'subtotal' => $pesanan->subtotal,
                'total' => $pesanan->total,
                'catatan' => $pesanan->catatan,
                'tipe_pengiriman' => $pesanan->tipe_pengiriman,
                'estimasi_ambil' => $pesanan->estimasi_ambil,
                'status' => $pesanan->status,
                'label_status' => $pesanan->label_status,
                'created_at' => $pesanan->created_at->format('H:i'),
                'alasan_batal' => $pesanan->alasan_batal,
                'bukti_bayar'   => $pesanan->bukti_bayar ? asset('storage/' . $pesanan->bukti_bayar) : null,
                'metode_bayar'  => $pesanan->metode_bayar,
            ],
            'posisi_antrian' => $posisiAntrian,
            'back_url' => $backUrl,
            'qris_image' => asset('storage/QrisStatisKantin_BPPU-IKIP-Slw.png'),
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

        // Validasi: user canteen hanya boleh update pesanan dari work_unit mereka
        $user = auth()->user();
        if (!$user->hasRole('sysadmin')) {
            $userWorkUnitIds = $user->workUnits->pluck('id')->toArray();
            $pesananWorkUnitIds = $pesanan->workUnitIds();
            $allowed = !empty(array_intersect($userWorkUnitIds, $pesananWorkUnitIds));
            if (!$allowed) {
                return response()->json(['success' => false, 'error' => 'Anda tidak memiliki akses untuk mengubah pesanan ini.'], 403);
            }
        }

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

        if ($oldStatus !== $request->status) {
            $actor = auth()->user()->name;
            ActivityLogger::log(
                $request->status,
                'pesanan_self_order',
                "Status pesanan #{$pesanan->nomor_antrian} ({$pesanan->nama_pemesan}) diubah dari {$oldStatus} ke {$request->status} oleh {$actor}",
                $pesanan,
                ['old_status' => $oldStatus, 'new_status' => $request->status, 'alasan_batal' => $request->alasan_batal ?? null]
            );
        }

        return response()->json([
            'success'      => true,
            'status'       => $pesanan->status,
            'label_status' => $pesanan->label_status,
            'changed'      => $oldStatus !== $request->status,
            'penjualan'    => $penjualanInfo,
        ]);
    }

    public function tandaiBayar(Request $request, int $id)
    {
        $pesanan = PesananSelfOrder::findOrFail($id);

        if ($pesanan->bukti_bayar) {
            return response()->json(['success' => false, 'error' => 'Pesanan sudah ditandai lunas.'], 422);
        }

        $pesanan->update(['bukti_bayar' => 'MANUAL']);

        return response()->json(['success' => true, 'bukti_bayar' => 'MANUAL']);
    }

    public function editItems(Request $request, int $id)
    {
        $request->validate([
            'items'   => 'required|array|min:1',
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
        $items = Barang::where('is_menu_item', true)
            ->where('stok', '>', 0)
            ->select('id', 'nama_barang', 'harga_jual', 'stok', 'kategori_barang_id', 'work_unit_id')
            ->with([
                'varians' => fn($q) => $q->select('id', 'barang_id', 'nama_varian', 'harga_tambahan'),
                'menuDisplay' => fn($q) => $q->select('id', 'barang_id', 'gambar'),
            ])
            ->orderBy('nama_barang')
            ->get()
            ->map(fn($b) => [
                'id'      => $b->id,
                'name'    => $b->nama_barang,
                'price'   => (float) $b->harga_jual,
                'stok'    => $b->stok,
                'gambar'  => $b->menuDisplay?->gambar ? asset('storage/' . ltrim($b->menuDisplay->gambar, '/storage/')) : null,
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

    public function uploadBuktiBayar(Request $request, int $id)
    {
        $request->validate([
            'bukti' => 'required|image|max:5120',
        ]);

        $pesanan = PesananSelfOrder::findOrFail($id);

        if (!in_array($pesanan->status, ['menunggu', 'diproses'])) {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah tidak aktif'], 422);
        }

        $path = $request->file('bukti')->store('bukti-bayar-kantin', 'public');

        $pesanan->update(['bukti_bayar' => $path]);

        return response()->json([
            'success' => true,
            'bukti_bayar' => asset('storage/' . $path),
        ]);
    }

    private function buildLiveOrderQuery(?int $workUnitId): \Illuminate\Support\Collection
    {
        $query = PesananSelfOrder::with('meja.lokasiMeja')
            ->where('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'asc');

        if ($workUnitId) {
            $mejaIds = Meja::whereHas('lokasiMeja', fn($q) => $q->where('default_work_unit_id', $workUnitId))->pluck('id');
            $query->whereIn('meja_id', $mejaIds);
        }

        return $query->get()->map(fn($p) => [
            'id'            => $p->id,
            'nomor_antrian' => $p->nomor_antrian,
            'nama_pemesan'  => $p->nama_pemesan,
            'nama_meja'     => $p->nama_meja ?? null,
            'status'        => $p->status,
            'label_status'  => $p->label_status,
            'waktu'         => $p->created_at->format('H:i:s'),
            'items_count'   => collect($p->items)->sum('quantity'),
        ]);
    }

    public function verifyLiveOrderPasscode(Request $request)
    {
        $passcode = $request->input('passcode', '');
        $hash     = config('app.live_order_passcode');

        if ($hash && password_verify($passcode, $hash)) {
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 401);
    }

    private function renderLiveOrder(?int $workUnitId)
    {
        $workUnit = $workUnitId ? WorkUnit::find($workUnitId) : null;
        $semua    = $this->buildLiveOrderQuery($workUnitId);
        $aktif    = $semua->whereIn('status', ['menunggu', 'diproses', 'siap'])->values();
        $selesai  = $semua->where('status', 'selesai')->values();
        $kantins  = WorkUnit::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('LiveOrder', [
            'pesananAktif'  => $aktif,
            'pesananSelesai'=> $selesai,
            'namaKantin'    => $workUnit?->name ?? 'Semua Kantin',
            'kantin'        => $workUnitId,
            'kantinList'    => $kantins,
        ]);
    }

    public function liveOrder(Request $request)
    {
        return $this->renderLiveOrder(null);
    }

    public function liveOrderByUnit(Request $request, int $workUnitId)
    {
        return $this->renderLiveOrder($workUnitId);
    }

    public function liveOrderApi(Request $request, ?int $workUnitId = null)
    {
        $id = $workUnitId ?? ($request->query('kantin') ? (int) $request->query('kantin') : null);

        $semua    = $this->buildLiveOrderQuery($id);
        $aktif    = $semua->whereIn('status', ['menunggu', 'diproses', 'siap'])->values();
        $selesai  = $semua->where('status', 'selesai')->values();

        return response()->json([
            'pesananAktif'  => $aktif,
            'pesananSelesai'=> $selesai,
        ]);
    }

    private function getLiveTransactionData(?int $workUnitId): array
    {
        $query = Penjualan::with(['workUnit:id,name', 'items:id,penjualan_id,nama_barang,qty'])
            ->whereDate('tanggal_transaksi', today())
            ->orderByDesc('tanggal_transaksi');

        if ($workUnitId) {
            $query->where('work_unit_id', $workUnitId);
        }

        $transaksis = $query->get();

        $transaksiList = $transaksis->map(function ($trx) {
            $qtyTotal = (int) $trx->items->sum('qty');
            $itemNames = $trx->items->pluck('nama_barang')->filter()->unique()->values();

            return [
                'id' => $trx->id,
                'nomor_transaksi' => $trx->nomor_transaksi,
                'kantin' => $trx->workUnit?->name ?? '-',
                'waktu' => optional($trx->tanggal_transaksi)->format('H:i:s'),
                'jumlah_item' => $qtyTotal,
                'total' => (float) $trx->total,
                'ringkas_item' => $itemNames->take(2)->implode(', ') . ($itemNames->count() > 2 ? ' +' . ($itemNames->count() - 2) : ''),
            ];
        })->values();

        $soldItems = $transaksis
            ->flatMap(function ($trx) {
                return $trx->items->map(function ($item) use ($trx) {
                    return [
                        'nama_barang' => $item->nama_barang,
                        'kantin' => $trx->workUnit?->name ?? '-',
                        'qty' => (int) $item->qty,
                    ];
                });
            })
            ->groupBy(fn($row) => ($row['nama_barang'] ?? '-') . '||' . $row['kantin'])
            ->map(function ($group, $key) {
                [$namaBarang, $kantin] = explode('||', $key);
                return [
                    'nama_barang' => $namaBarang,
                    'kantin' => $kantin,
                    'qty' => $group->sum('qty'),
                ];
            })
            ->sortByDesc('qty')
            ->values();

        return [
            'transaksi' => $transaksiList,
            'soldItems' => $soldItems,
            'totalTransaksi' => $transaksis->count(),
            'totalOmzet' => (float) $transaksis->sum('total'),
        ];
    }

    private function renderLiveTransaction(?int $workUnitId)
    {
        $workUnit = $workUnitId ? WorkUnit::find($workUnitId) : null;
        $kantins = WorkUnit::select('id', 'name')->orderBy('name')->get();
        $data = $this->getLiveTransactionData($workUnitId);

        return Inertia::render('LiveTransaction', [
            'transaksi' => $data['transaksi'],
            'soldItems' => $data['soldItems'],
            'totalTransaksi' => $data['totalTransaksi'],
            'totalOmzet' => $data['totalOmzet'],
            'namaKantin' => $workUnit?->name ?? 'Semua Kantin',
            'kantin' => $workUnitId,
            'kantinList' => $kantins,
        ]);
    }

    public function liveTransaction(Request $request)
    {
        return $this->renderLiveTransaction(null);
    }

    public function liveTransactionByUnit(Request $request, int $workUnitId)
    {
        return $this->renderLiveTransaction($workUnitId);
    }

    public function liveTransactionApi(Request $request, ?int $workUnitId = null)
    {
        $id = $workUnitId ?? ($request->query('kantin') ? (int) $request->query('kantin') : null);
        return response()->json($this->getLiveTransactionData($id));
    }

    public function pesananKantin()
    {
        [$pesanan, $stats] = $this->getPesananAndStats();

        return Inertia::render('PoS/PesananKantin', [
            'pesanan' => $pesanan,
            'stats' => $stats,
        ]);
    }

    public function pesananKantinApi()
    {
        [$pesanan, $stats] = $this->getPesananAndStats();

        return response()->json([
            'pesanan' => $pesanan,
            'stats' => $stats,
        ]);
    }

    private function getPesananAndStats(): array
    {
        $user = auth()->user();
        $isSysadmin = $user->hasRole('sysadmin');
        $userWorkUnitIds = $isSysadmin ? null : $user->workUnits->pluck('id')->toArray();

        $all = PesananSelfOrder::with('penjualan')
            ->where('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        if ($userWorkUnitIds !== null) {
            $all = $all->filter(function($p) use ($userWorkUnitIds) {
                foreach ($p->items ?? [] as $item) {
                    $barang = Barang::find($item['id']);
                    if ($barang && in_array($barang->work_unit_id, $userWorkUnitIds)) {
                        return true;
                    }
                }
                return false;
            })->values();
        }

        $pesanan = $all->map(fn($p) => $this->formatPesanan($p));

        $stats = [
            'menunggu'   => $all->where('status', 'menunggu')->count(),
            'diproses'   => $all->where('status', 'diproses')->count(),
            'siap'       => $all->where('status', 'siap')->count(),
            'selesai'    => $all->where('status', 'selesai')->count(),
            'dibatalkan' => $all->where('status', 'dibatalkan')->count(),
        ];

        return [$pesanan, $stats];
    }

    private function formatPesanan(PesananSelfOrder $p): array
    {
        return [
            'id'                => $p->id,
            'nomor_antrian'     => $p->nomor_antrian,
            'tanggal'           => $p->tanggal_antrian->format('d/m/Y'),
            'nama_pemesan'      => $p->nama_pemesan,
            'nama_meja'         => $p->nama_meja ?? null,
            'items'             => $p->items,
            'total'             => (float) $p->total,
            'catatan'           => $p->catatan,
            'tipe_pengiriman'   => $p->tipe_pengiriman ?? 'antar',
            'estimasi_ambil'    => $p->estimasi_ambil ?? null,
            'status'            => $p->status,
            'label_status'      => $p->label_status,
            'waktu'             => $p->created_at->format('H:i'),
            'alasan_batal'      => $p->alasan_batal ?? null,
            'kode_alasan_batal' => $p->kode_alasan_batal ?? null,
            'penjualan_id'      => $p->penjualan_id ?? null,
            'nomor_transaksi'   => $p->penjualan?->nomor_transaksi ?? null,
            'bukti_bayar'       => $p->bukti_bayar === 'MANUAL' ? 'MANUAL' : ($p->bukti_bayar ? asset('storage/' . $p->bukti_bayar) : null),
            'metode_bayar'      => $p->metode_bayar ?? null,
        ];
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * asin(sqrt($a));
    }
}
