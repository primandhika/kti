<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangVarian;
use App\Models\BarangVarianKomponen;
use App\Models\WorkUnit;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarangController extends Controller
{
    public function index(Request $request, $workUnitId)
    {
        $workUnit = WorkUnit::findOrFail($workUnitId);

        $query = Barang::where('work_unit_id', $workUnitId)
            ->orderBy('nama_barang');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Kategori filter
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Active only filter (default: true)
        $activeOnly = $request->has('active_only') ? filter_var($request->active_only, FILTER_VALIDATE_BOOLEAN) : true;
        if ($activeOnly) {
            $query->where('is_active', true);
        }

        // Filter barang yang punya pengajuan pending
        if ($request->boolean('has_pengajuan')) {
            $query->whereHas('pengajuanBarang', fn($q) => $q->where('status', 'pending'));
        }

        // Filter barang yang ditandai tampil di menu kantin
        if ($request->boolean('menu_kantin')) {
            $query->where('is_menu_item', true);
        }

        // Filter barang yang memiliki stok > 0
        if ($request->boolean('has_stok')) {
            $query->where('stok', '>', 0);
        }

        // Get kategori from database (active only)
        $kategoris = KategoriBarang::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('nama')
            ->get()
            ->map(function($kategori) {
                return [
                    'id' => $kategori->id,
                    'nama' => $kategori->nama,
                    'kode' => $kategori->kode,
                ];
            });

        // Get other work units for import feature
        $otherWorkUnits = WorkUnit::where('id', '!=', $workUnitId)
            ->orderBy('name')
            ->get()
            ->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'location' => $unit->location,
                ];
            });

        $barangs = $query->with(['createdBy:id,name', 'supplier:id,kode_supplier,nama', 'menuDisplay', 'varians' => function($q) {
            $q->orderBy('display_order')->orderBy('nama_varian')
              ->with(['komponens.barangKomponen:id,nama_barang,satuan,harga_beli,stok']);
        }, 'pengajuanBarang' => function($q) {
            $q->where('status', 'pending');
        }])->paginate(25)->withQueryString();

        // Ensure numeric fields are not null
        $barangs->getCollection()->transform(function ($barang) {
            $barang->harga_beli = $barang->harga_beli ?? 0;
            $barang->harga_jual = $barang->harga_jual ?? 0;
            $barang->harga_grosir = $barang->harga_grosir ?? 0;
            $barang->harga_konsinyasi = $barang->harga_konsinyasi ?? 0;
            $barang->stok = $barang->stok ?? 0;
            $barang->minimum_stok = $barang->minimum_stok ?? 0;
            $barang->diskon_tipe = $barang->diskon_tipe;
            $barang->diskon_nilai = $barang->diskon_nilai;
            $barang->diskon_mulai = $barang->diskon_mulai?->toDateString();
            $barang->diskon_selesai = $barang->diskon_selesai?->toDateString();
            $barang->diskon_aktif = $barang->isDiskonAktif();
            $barang->has_pengajuan = $barang->pengajuanBarang->isNotEmpty();
            unset($barang->pengajuanBarang);

            // Ambil gambar dari menuDisplay (sumber utama)
            $barang->gambar = $barang->menuDisplay?->gambar ?? $barang->gambar;

            // Transform varians
            if ($barang->varians) {
                $barang->varians->transform(function ($varian) {
                    $varian->harga_jual = $varian->harga_jual ?? 0;
                    $varian->harga_grosir = $varian->harga_grosir ?? 0;
                    // Flatten komponens untuk frontend
                    $varian->komponens = $varian->komponens->map(function ($k) {
                        return [
                            'barang_komponen_id' => $k->barang_komponen_id,
                            'nama_barang' => $k->barangKomponen?->nama_barang ?? '',
                            'qty' => (float) $k->qty,
                            'satuan' => $k->satuan ?? $k->barangKomponen?->satuan ?? '',
                            'harga_beli' => (float) ($k->barangKomponen?->harga_beli ?? 0),
                        ];
                    })->values()->toArray();
                    return $varian;
                });
            }

            return $barang;
        });

        // Get users for created_by dropdown (sysadmin only)
        $users = [];
        if (auth()->user()->hasRole('sysadmin')) {
            $users = \App\Models\User::select('id', 'name')
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['officer', 'canteen']);
                })
                ->orderBy('name')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })
                ->values()
                ->toArray();
        }

        // Get suppliers
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('nama')
            ->get()
            ->map(function($supplier) {
                return [
                    'id' => $supplier->id,
                    'kode_supplier' => $supplier->kode_supplier,
                    'nama' => $supplier->nama,
                    'perusahaan' => $supplier->perusahaan,
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Admin/OpnameStock/MasterBarang', [
            'toko' => $workUnit,
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'suppliers' => $suppliers,
            'otherWorkUnits' => $otherWorkUnits,
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'kategori_id' => $request->kategori_id,
                'active_only' => $activeOnly,
                'has_pengajuan' => $request->boolean('has_pengajuan'),
                'menu_kantin' => $request->boolean('menu_kantin'),
                'has_stok' => $request->boolean('has_stok'),
            ]
        ]);
    }

    public function store(Request $request, $workUnitId)
    {
        // Decode varians jika dikirim sebagai JSON string (FormData upload)
        if ($request->has('varians') && is_string($request->input('varians'))) {
            $request->merge(['varians' => json_decode($request->input('varians'), true)]);
        }

        // Custom validation for kode_barang - check uniqueness within work_unit_id
        $validated = $request->validate([
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategori_barangs,id',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'harga_grosir' => 'nullable|numeric|min:0',
            'harga_konsinyasi' => 'nullable|numeric|min:0',
            'diskon_tipe' => 'nullable|in:persen,nominal,harga_menjadi',
            'diskon_nilai' => 'nullable|numeric|min:0',
            'diskon_mulai' => 'nullable|date',
            'diskon_selesai' => 'nullable|date|after_or_equal:diskon_mulai',
            'minimum_stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'jenis_barang' => 'nullable|in:jual_langsung,konsinyasi,preorder,bundling,langganan,dropship_internal',
            'tanggal_kadaluarsa' => 'nullable|date',
            'is_menu_item' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'show_in_shop' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'varians' => 'nullable|array',
            'varians.*.nama_varian' => 'required|string|max:255',
            'varians.*.deskripsi' => 'nullable|string',
            'varians.*.harga_jual' => 'required|numeric|min:0',
            'varians.*.harga_grosir' => 'nullable|numeric|min:0',
            'varians.*.hpp_tambahan' => 'nullable|numeric|min:0',
            'varians.*.display_order' => 'nullable|integer|min:0',
            'varians.*.komponens' => 'nullable|array',
            'varians.*.komponens.*.barang_komponen_id' => 'required|exists:barangs,id',
            'varians.*.komponens.*.qty' => 'required|numeric|min:0.001',
            'varians.*.komponens.*.satuan' => 'nullable|string|max:50',
        ]);

        // Check if kode_barang already exists in this work_unit
        $existingBarang = Barang::where('work_unit_id', $workUnitId)
            ->where('kode_barang', $validated['kode_barang'])
            ->first();

        if ($existingBarang) {
            return back()->withErrors([
                'kode_barang' => "Kode barang sudah digunakan oleh {$existingBarang->nama_barang} ({$existingBarang->kode_barang})"
            ]);
        }

        $validated['work_unit_id'] = $workUnitId;
        $validated['created_by'] = $request->user()->id;

        // Set kategori based on kategori_id for backward compatibility
        if (!empty($validated['kategori_id'])) {
            $kategori = KategoriBarang::find($validated['kategori_id']);
            $validated['kategori'] = $kategori ? $kategori->kode : null;
        }

        // Handle image upload with compression
        if ($request->hasFile('gambar')) {
            $compression = new ImageCompressionService();
            $path = $compression->compressAndStore($request->file('gambar'), 'barangs', 800, 85, 150);
            $validated['gambar'] = '/storage/' . $path;
        }

        // Ambil varians dari raw request agar komponens tidak dibuang validator
        $varianRaws = $request->input('varians', []);
        unset($validated['varians']);

        $barang = Barang::create($validated);

        // Create varians if provided
        if (!empty($varianRaws)) {
            foreach ($varianRaws as $index => $varian) {
                $newVarian = $barang->varians()->create([
                    'nama_varian' => $varian['nama_varian'],
                    'deskripsi' => $varian['deskripsi'] ?? null,
                    'harga_jual' => $varian['harga_jual'],
                    'harga_grosir' => $varian['harga_grosir'] ?? null,
                    'hpp_tambahan' => $varian['hpp_tambahan'] ?? 0,
                    'display_order' => $varian['display_order'] ?? $index,
                    'is_active' => true,
                ]);

                $this->syncVarianKomponens($newVarian->id, $varian['komponens'] ?? []);
            }
        }

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $workUnitId, Barang $barang)
    {
        // Decode varians jika dikirim sebagai JSON string (FormData upload)
        if ($request->has('varians') && is_string($request->input('varians'))) {
            $request->merge(['varians' => json_decode($request->input('varians'), true)]);
        }

        $validated = $request->validate([
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategori_barangs,id',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'harga_grosir' => 'nullable|numeric|min:0',
            'harga_konsinyasi' => 'nullable|numeric|min:0',
            'diskon_tipe' => 'nullable|in:persen,nominal,harga_menjadi',
            'diskon_nilai' => 'nullable|numeric|min:0',
            'diskon_mulai' => 'nullable|date',
            'diskon_selesai' => 'nullable|date|after_or_equal:diskon_mulai',
            'minimum_stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'jenis_barang' => 'nullable|in:jual_langsung,konsinyasi,preorder,bundling,langganan,dropship_internal',
            'tanggal_kadaluarsa' => 'nullable|date',
            'is_menu_item' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'show_in_shop' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'created_by' => 'nullable|exists:users,id',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'varians' => 'nullable|array',
            'varians.*.id' => 'nullable|exists:barang_varians,id',
            'varians.*.nama_varian' => 'required|string|max:255',
            'varians.*.deskripsi' => 'nullable|string',
            'varians.*.harga_jual' => 'required|numeric|min:0',
            'varians.*.harga_grosir' => 'nullable|numeric|min:0',
            'varians.*.hpp_tambahan' => 'nullable|numeric|min:0',
            'varians.*.display_order' => 'nullable|integer|min:0',
            'varians.*.is_active' => 'nullable|boolean',
            'varians.*.komponens' => 'nullable|array',
            'varians.*.komponens.*.barang_komponen_id' => 'required|exists:barangs,id',
            'varians.*.komponens.*.qty' => 'required|numeric|min:0.001',
            'varians.*.komponens.*.satuan' => 'nullable|string|max:50',
        ]);

        // Check if kode_barang already exists in this work_unit (excluding current barang)
        $existingBarang = Barang::where('work_unit_id', $workUnitId)
            ->where('kode_barang', $validated['kode_barang'])
            ->where('id', '!=', $barang->id)
            ->first();

        if ($existingBarang) {
            return back()->withErrors([
                'kode_barang' => "Kode barang sudah digunakan oleh {$existingBarang->nama_barang} ({$existingBarang->kode_barang})"
            ]);
        }

        // Set kategori based on kategori_id for backward compatibility
        if (!empty($validated['kategori_id'])) {
            $kategori = KategoriBarang::find($validated['kategori_id']);
            $validated['kategori'] = $kategori ? $kategori->kode : null;
        }

        // Only sysadmin can change created_by
        if (!$request->user()->hasRole('sysadmin')) {
            unset($validated['created_by']);
        }

        // Handle image upload with compression
        if ($request->hasFile('gambar')) {
            $compression = new ImageCompressionService();

            if ($barang->gambar) {
                $compression->deleteImage($barang->gambar);
            }

            $path = $compression->compressAndStore($request->file('gambar'), 'barangs', 800, 85, 150);
            $validated['gambar'] = '/storage/' . $path;
        }

        // Ambil varians dari raw request (bukan $validated) agar komponens tidak dibuang validator
        $varianRaws = $request->input('varians', []);
        unset($validated['varians']);

        $barang->update($validated);

        // Update varians
        if (!empty($varianRaws)) {
            $varianIds = [];

            foreach ($varianRaws as $index => $varian) {
                $komponens = $varian['komponens'] ?? [];

                if (!empty($varian['id'])) {
                    $existingVarian = BarangVarian::find($varian['id']);
                    if ($existingVarian && $existingVarian->barang_id == $barang->id) {
                        $existingVarian->update([
                            'nama_varian' => $varian['nama_varian'],
                            'deskripsi' => $varian['deskripsi'] ?? null,
                            'harga_jual' => $varian['harga_jual'],
                            'harga_grosir' => $varian['harga_grosir'] ?? null,
                            'hpp_tambahan' => $varian['hpp_tambahan'] ?? 0,
                            'display_order' => $varian['display_order'] ?? $index,
                            'is_active' => $varian['is_active'] ?? true,
                        ]);
                        $varianIds[] = $existingVarian->id;
                        $this->syncVarianKomponens($existingVarian->id, $komponens);
                    }
                } else {
                    $newVarian = $barang->varians()->create([
                        'nama_varian' => $varian['nama_varian'],
                        'deskripsi' => $varian['deskripsi'] ?? null,
                        'harga_jual' => $varian['harga_jual'],
                        'harga_grosir' => $varian['harga_grosir'] ?? null,
                        'hpp_tambahan' => $varian['hpp_tambahan'] ?? 0,
                        'display_order' => $varian['display_order'] ?? $index,
                        'is_active' => true,
                    ]);
                    $varianIds[] = $newVarian->id;
                    $this->syncVarianKomponens($newVarian->id, $komponens);
                }
            }

            // Delete varians that are not in the list anymore (cascade hapus komponens juga)
            $barang->varians()->whereNotIn('id', $varianIds)->delete();
        }

        return redirect()->back()->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($workUnitId, Barang $barang)
    {
        try {
            $barang->delete();
            return redirect()->back()->with('success', 'Barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == '23000') {
                return redirect()->back()->withErrors([
                    'delete' => 'Barang tidak dapat dihapus karena sudah pernah digunakan dalam transaksi penjualan. Anda dapat menonaktifkan barang ini sebagai gantinya.'
                ]);
            }

            // Other database errors
            return redirect()->back()->withErrors([
                'delete' => 'Terjadi kesalahan saat menghapus barang: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'delete' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique PLU for work unit
     */
    public function generatePLU($workUnitId)
    {
        $workUnit = WorkUnit::findOrFail($workUnitId);

        // Get work unit code (first 3 letters of name)
        $unitCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $workUnit->name), 0, 3));
        if (strlen($unitCode) < 3) {
            $unitCode = str_pad($unitCode, 3, 'X');
        }

        // Get current year
        $year = date('y');

        // Find last PLU for this work unit with current format
        $lastBarang = Barang::where('work_unit_id', $workUnitId)
            ->where('kode_barang', 'like', "{$unitCode}{$year}%")
            ->orderBy('kode_barang', 'desc')
            ->first();

        if ($lastBarang) {
            // Extract number from last PLU and increment
            $lastNumber = (int) substr($lastBarang->kode_barang, 5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: ABC24-0001 (unit code + year + sequential number)
        $plu = sprintf('%s%s-%04d', $unitCode, $year, $newNumber);

        return response()->json(['plu' => $plu]);
    }

    /**
     * Get list of barangs from a work unit (for import feature)
     */
    public function listBarangs($workUnitId)
    {
        $barangs = Barang::where('work_unit_id', $workUnitId)
            ->with('menuDisplay')
            ->orderBy('nama_barang')
            ->get()
            ->map(function($barang) {
                return [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'kategori' => $barang->kategori,
                    'satuan' => $barang->satuan,
                    'stok' => $barang->stok,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $barang->harga_jual,
                    'harga_grosir' => $barang->harga_grosir,
                    'minimum_stok' => $barang->minimum_stok,
                    'gambar' => $barang->menuDisplay?->gambar,
                ];
            });

        return response()->json(['barangs' => $barangs]);
    }

    /**
     * Import barangs from CSV
     */
    public function importBarangsFromCsv(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'data' => 'required|array|min:1',
            'data.*.kode_barang' => 'required|string',
            'data.*.nama_barang' => 'required|string|max:255',
            'data.*.kategori_id' => 'required|exists:kategori_barangs,id',
            'data.*.satuan' => 'required|string|max:50',
            'data.*.stok' => 'required|integer|min:0',
            'data.*.harga_beli' => 'required|numeric|min:0',
            'data.*.harga_jual' => 'required|numeric|min:0',
            'data.*.minimum_stok' => 'required|integer|min:0',
            'data.*.deskripsi' => 'nullable|string',
            'data.*.supplier_id' => 'nullable|exists:suppliers,id',
            'data.*.jenis_barang' => 'nullable|in:jual_langsung,konsinyasi,preorder,bundling,langganan,dropship_internal',
            'data.*.tanggal_kadaluarsa' => 'nullable|date',
        ]);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        \DB::beginTransaction();

        try {
            foreach ($validated['data'] as $index => $item) {
                try {
                    // Check if kode_barang already exists
                    $existingBarang = Barang::where('work_unit_id', $workUnitId)
                        ->where('kode_barang', $item['kode_barang'])
                        ->first();

                    if ($existingBarang) {
                        $errors[] = "Baris " . ($index + 2) . ": Kode barang {$item['kode_barang']} sudah ada";
                        $skipped++;
                        continue;
                    }

                    // Set kategori based on kategori_id
                    $kategori = KategoriBarang::find($item['kategori_id']);
                    $item['kategori'] = $kategori ? $kategori->kode : null;
                    $item['work_unit_id'] = $workUnitId;
                    $item['created_by'] = $request->user()->id;
                    $item['is_active'] = true;

                    Barang::create($item);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            \DB::commit();

            $message = "Berhasil mengimpor {$imported} barang";
            if ($skipped > 0) {
                $message .= " ({$skipped} dilewati)";
            }

            if (!empty($errors)) {
                return redirect()->back()->with('success', $message)->with('import_errors', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Import barangs CSV error', [
                'work_unit_id' => $workUnitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors([
                'import' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Import barangs from another work unit
     */
    public function importBarangs(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'source_work_unit_id' => 'required|exists:work_units,id',
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'exists:barangs,id',
        ]);

        try {
            $importService = new \App\Services\BarangImportService();
            $result = $importService->importBarangs(
                $workUnitId,
                $validated['source_work_unit_id'],
                $validated['barang_ids']
            );

            if (!$result['success']) {
                $errorMessage = 'Gagal mengimpor barang';
                if (!empty($result['errors'])) {
                    $errorMessage .= ': ' . implode(', ', array_slice($result['errors'], 0, 3));
                    if (count($result['errors']) > 3) {
                        $errorMessage .= ' (dan ' . (count($result['errors']) - 3) . ' error lainnya)';
                    }
                }
                return redirect()->back()->withErrors(['import' => $errorMessage]);
            }

            $message = "Berhasil mengimpor {$result['imported']} barang";
            if ($result['skipped'] > 0) {
                $message .= " ({$result['skipped']} dilewati)";

                // Add detailed error info if available
                if (!empty($result['errors'])) {
                    return redirect()->back()->with('success', $message)->with('warnings', $result['errors']);
                }
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Import barangs controller error', [
                'work_unit_id' => $workUnitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors([
                'import' => 'Terjadi kesalahan saat mengimpor barang: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate price tags PDF for selected items
     */
    public function printPriceTags(Request $request, $workUnitId)
    {
        $validated = $request->validate([
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'exists:barangs,id',
        ]);

        $workUnit = WorkUnit::findOrFail($workUnitId);

        // Get selected barangs with their suppliers
        $barangs = Barang::whereIn('id', $validated['barang_ids'])
            ->where('work_unit_id', $workUnitId)
            ->with('supplier')
            ->orderBy('nama_barang')
            ->get();

        // Initialize barcode generator
        $barcodeGenerator = new BarcodeGeneratorPNG();

        // Transform data for PDF
        $barangs->transform(function ($barang) use ($barcodeGenerator) {
            // Ensure numeric fields are not null
            $barang->harga_jual = $barang->harga_jual ?? 0;

            // Generate barcode as base64 image
            try {
                $barcodeImage = $barcodeGenerator->getBarcode(
                    $barang->kode_barang,
                    $barcodeGenerator::TYPE_CODE_128,
                    2,
                    40
                );
                $barang->barcode_base64 = base64_encode($barcodeImage);
            } catch (\Exception $e) {
                // Fallback: create empty barcode if generation fails
                $barang->barcode_base64 = '';
            }

            // Get supplier name if exists
            $barang->supplier_name = null;
            if ($barang->supplier_id && $barang->supplier && is_object($barang->supplier)) {
                $barang->supplier_name = $barang->supplier->nama ?? null;
            }

            return $barang;
        });

        // Generate PDF
        $pdf = Pdf::loadView('pdf.price-tags', [
            'barangs' => $barangs,
            'workUnit' => $workUnit
        ]);

        // Set paper size to A4
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('price-tags-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Upload image for barang (Kantin only)
     */
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);
        $user = auth()->user();

        // Check if user can access this barang's work unit
        if (!$user->canAccessWorkUnit($barang->work_unit_id)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk mengubah barang ini.']);
        }

        // Use BarangService to upload image
        $barangService = app(\App\Services\PoS\BarangService::class);
        $barangService->uploadImage($barang, $request->file('image'));

        $barang->refresh();

        return back()->with('success', 'Foto berhasil diupload')
                     ->with('updatedBarang', ['id' => $barang->id, 'gambar' => $barang->gambar ?: null]);
    }

    /**
     * Delete image for barang (Kantin only)
     */
    public function deleteImage(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);
        $user = auth()->user();

        // Check if user can access this barang's work unit
        if (!$user->canAccessWorkUnit($barang->work_unit_id)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk mengubah barang ini.']);
        }

        // Use BarangService to delete image
        $barangService = app(\App\Services\PoS\BarangService::class);
        $barangService->deleteImage($barang);

        return back()->with('success', 'Foto berhasil dihapus')
                     ->with('updatedBarang', ['id' => $barang->id, 'gambar' => null]);
    }

    /**
     * Simpan komponen varian sekaligus update harga jual — dipanggil saat klik "Pakai"
     */
    public function saveVarianKomponens(Request $request, $workUnitId, BarangVarian $varian)
    {
        // Pastikan varian milik work unit ini
        if ($varian->barang->work_unit_id != $workUnitId) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'harga_jual' => 'required|numeric|min:0',
            'komponens' => 'nullable|array',
            'komponens.*.barang_komponen_id' => 'required|exists:barangs,id',
            'komponens.*.qty' => 'required|numeric|min:0.001',
            'komponens.*.satuan' => 'nullable|string|max:50',
        ]);

        $varian->update(['harga_jual' => $validated['harga_jual']]);
        $this->syncVarianKomponens($varian->id, $validated['komponens'] ?? []);

        return response()->json(['success' => true, 'message' => 'Komponen berhasil disimpan']);
    }

    /**
     * Cari barang untuk dijadikan komponen varian (autocomplete)
     */
    public function searchKomponen(Request $request, $workUnitId)
    {
        $search = $request->get('q', '');

        $barangs = Barang::where('work_unit_id', $workUnitId)
            ->where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            })
            ->select('id', 'nama_barang', 'satuan', 'harga_beli', 'stok', 'kode_barang')
            ->orderBy('nama_barang')
            ->limit(20)
            ->get();

        return response()->json($barangs);
    }

    /**
     * Sync komponen-komponen sebuah varian — delete semua lalu insert ulang.
     */
    protected function syncVarianKomponens(int $varianId, array $komponens): void
    {
        BarangVarianKomponen::where('varian_id', $varianId)->delete();

        foreach ($komponens as $komponen) {
            if (empty($komponen['barang_komponen_id']) || empty($komponen['qty'])) {
                continue;
            }

            $barang = Barang::find($komponen['barang_komponen_id']);

            BarangVarianKomponen::create([
                'varian_id' => $varianId,
                'barang_komponen_id' => $komponen['barang_komponen_id'],
                'qty' => $komponen['qty'],
                'satuan' => $komponen['satuan'] ?? $barang?->satuan,
            ]);
        }
    }
}
