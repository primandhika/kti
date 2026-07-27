<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SkemaBisnis;
use App\Models\User;
use App\Models\WorkUnit;
use App\Services\PoS\MitraUsahaPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class SupplierController extends Controller
{
    /**
     * Display list of suppliers
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $tipeFilter = $request->input('tipe');

        $suppliersQuery = Supplier::query()->with(['skemaBisnisAktif', 'user', 'penempatanWorkUnit']);

        if ($search) {
            $suppliersQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_supplier', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('kontak_person', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $suppliersQuery->where('is_active', $statusFilter);
        }

        if ($tipeFilter) {
            $suppliersQuery->where('tipe_mitra', $tipeFilter);
        }

        $suppliers = $suppliersQuery->orderBy('created_at', 'desc')
            ->paginate(16)
            ->through(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'kode_supplier' => $supplier->kode_supplier,
                    'tipe_mitra' => $supplier->tipe_mitra,
                    'tipe_mitra_label' => Supplier::getTipeMitraOptions()[$supplier->tipe_mitra] ?? $supplier->tipe_mitra,
                    'nama' => $supplier->nama,
                    'perusahaan' => $supplier->perusahaan,
                    'alamat' => $supplier->alamat,
                    'kota' => $supplier->kota,
                    'provinsi' => $supplier->provinsi,
                    'kode_pos' => $supplier->kode_pos,
                    'telepon' => $supplier->telepon,
                    'email' => $supplier->email,
                    'kontak_person' => $supplier->kontak_person,
                    'telepon_kontak' => $supplier->telepon_kontak,
                    'catatan' => $supplier->catatan,
                    'penempatan_work_unit_id' => $supplier->penempatan_work_unit_id,
                    'penempatan_work_unit_nama' => $supplier->penempatanWorkUnit?->name,
                    'biaya_kontribusi' => $supplier->biaya_kontribusi,
                    'is_active' => $supplier->is_active,
                    'logo' => $supplier->logo,
                    'skema_bisnis_aktif' => $supplier->skemaBisnisAktif,
                    'akun_user' => $supplier->user ? [
                        'username' => $supplier->user->username,
                        'email'    => $supplier->user->email,
                    ] : null,
                    'created_at' => $supplier->created_at->format('d M Y'),
                ];
            });

        return Inertia::render('Admin/MitraUsaha/Index', [
            'suppliers' => $suppliers,
            'tipeMitraOptions' => Supplier::getTipeMitraOptions(),
            'jenisSkemaOptions' => SkemaBisnis::getJenisSkemaOptions(),
            'workUnits' => WorkUnit::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'tipe' => $tipeFilter,
            ],
        ]);
    }

    /**
     * Store a new supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier',
            'tipe_mitra' => 'required|in:supplier,tenant,vendor,distributor,produsen,reseller,konsinyasi,lainnya',
            'nama' => 'required|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'kontak_person' => 'nullable|string|max:255',
            'telepon_kontak' => 'nullable|string|max:20',
            'catatan' => 'nullable|string',
            'penempatan_work_unit_id' => 'nullable|exists:work_units,id',
            'biaya_kontribusi' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'logo' => 'nullable|string',
            'skema_bisnis' => 'nullable|array',
            'skema_bisnis.jenis_skema' => 'nullable|in:konsinyasi,bagi_hasil,dropshipper,supplier',
            'skema_bisnis.persentase_vendor' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.persentase_badan_usaha' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.nominal_flat' => 'nullable|numeric|min:0',
            'skema_bisnis.minimum_harga_item' => 'nullable|numeric|min:0',
            'skema_bisnis.persentase_dari_keuntungan' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.is_active' => 'boolean',
            'skema_bisnis.catatan' => 'nullable|string',
        ]);

        if ($request->filled('logo')) {
            $validated['logo'] = $this->saveBase64Image($request->logo, 'suppliers/logos');
        }

        DB::beginTransaction();
        try {
            // Generate username dan password sementara
            $username = strtolower(str_replace([' ', '-'], '_', $validated['kode_supplier']));
            $plainPassword = 'Mitra@' . substr(str_replace(['_', '-'], '', $validated['kode_supplier']), 0, 6);

            // Pastikan username unik
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Resolve email (wajib ada, gunakan placeholder jika kosong/konflik)
            $slug  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['kode_supplier']));
            $email = $validated['email'] ?? null;
            if (!$email || User::where('email', $email)->exists()) {
                $email   = "mitra.{$slug}@bppu.internal";
                $emailCt = 1;
                while (User::where('email', $email)->exists()) {
                    $email = "mitra.{$slug}{$emailCt}@bppu.internal";
                    $emailCt++;
                }
            }

            // Buat akun user untuk mitra
            $user = User::create([
                'name'     => $validated['nama'],
                'username' => $username,
                'email'    => $email,
                'phone'    => $validated['telepon'] ?? null,
                'password' => Hash::make($plainPassword),
                'must_change_password' => true,
            ]);

            // Assign role sesuai tipe mitra (lainnya tidak punya role mitra)
            $mitraRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];
            if (in_array($validated['tipe_mitra'], $mitraRoles)) {
                $role = Role::firstOrCreate(['name' => $validated['tipe_mitra'], 'guard_name' => 'web']);
                $user->assignRole($role);
            }

            $validated['user_id'] = $user->id;
            $supplier = Supplier::create($validated);

            if ($request->has('skema_bisnis') && !empty($request->skema_bisnis['jenis_skema'])) {
                $skemaData = $request->skema_bisnis;
                $numericFields = ['nominal_flat', 'minimum_harga_item', 'persentase_vendor', 'persentase_badan_usaha', 'persentase_dari_keuntungan'];
                foreach ($numericFields as $field) {
                    if (isset($skemaData[$field]) && $skemaData[$field] === '') {
                        $skemaData[$field] = null;
                    }
                }
                $supplier->skemaBisnis()->create($skemaData);
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Mitra Usaha berhasil ditambahkan!',
                'akun_mitra' => [
                    'nama'     => $user->name,
                    'username' => $user->username,
                    'password' => $plainPassword,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validated['logo']) && file_exists(public_path($validated['logo']))) {
                unlink(public_path($validated['logo']));
            }
            return redirect()->back()->with('error', 'Gagal menyimpan mitra usaha: ' . $e->getMessage());
        }
    }

    /**
     * Update existing supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'kode_supplier' => ['required', 'string', 'max:50', Rule::unique('suppliers')->ignore($supplier->id)],
            'tipe_mitra' => 'required|in:supplier,tenant,vendor,distributor,produsen,reseller,konsinyasi,lainnya',
            'nama' => 'required|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'kontak_person' => 'nullable|string|max:255',
            'telepon_kontak' => 'nullable|string|max:20',
            'catatan' => 'nullable|string',
            'penempatan_work_unit_id' => 'nullable|exists:work_units,id',
            'biaya_kontribusi' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'logo' => 'nullable|string',
            'skema_bisnis' => 'nullable|array',
            'skema_bisnis.jenis_skema' => 'nullable|in:konsinyasi,bagi_hasil,dropshipper,supplier',
            'skema_bisnis.persentase_vendor' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.persentase_badan_usaha' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.nominal_flat' => 'nullable|numeric|min:0',
            'skema_bisnis.minimum_harga_item' => 'nullable|numeric|min:0',
            'skema_bisnis.persentase_dari_keuntungan' => 'nullable|numeric|min:0|max:100',
            'skema_bisnis.is_active' => 'boolean',
            'skema_bisnis.catatan' => 'nullable|string',
        ]);

        if ($request->filled('logo')) {
            if ($supplier->logo && file_exists(public_path($supplier->logo))) {
                unlink(public_path($supplier->logo));
            }
            $validated['logo'] = $this->saveBase64Image($request->logo, 'suppliers/logos');
        }

        $supplier->update($validated);

        // Sinkronkan akun user jika ada
        if ($supplier->user_id && $supplier->user) {
            $user = $supplier->user;

            // Sinkronkan nama dan kontak
            $user->name  = $validated['nama'];
            $user->email = $validated['email'] ?? $user->email;
            $user->phone = $validated['telepon'] ?? $user->phone;
            $user->save();

            // Sinkronkan role sesuai tipe mitra
            $mitraRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];
            if (in_array($validated['tipe_mitra'], $mitraRoles)) {
                $user->syncRoles([$validated['tipe_mitra']]);
            }
        }

        if ($request->has('skema_bisnis') && !empty($request->skema_bisnis['jenis_skema'])) {
            $skemaData = $request->skema_bisnis;
            $numericFields = ['nominal_flat', 'minimum_harga_item', 'persentase_vendor', 'persentase_badan_usaha', 'persentase_dari_keuntungan'];
            foreach ($numericFields as $field) {
                if (isset($skemaData[$field]) && $skemaData[$field] === '') {
                    $skemaData[$field] = null;
                }
            }

            $skemaAktif = $supplier->skemaBisnisAktif;
            if ($skemaAktif) {
                $skemaAktif->update($skemaData);
            } else {
                $supplier->skemaBisnis()->create($skemaData);
            }
        }

        return redirect()->back()->with('success', 'Mitra Usaha berhasil diupdate!');
    }

    /**
     * Delete supplier
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->barangs()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus mitra usaha yang memiliki barang terkait!');
        }

        if ($supplier->logo && file_exists(public_path($supplier->logo))) {
            unlink(public_path($supplier->logo));
        }

        $supplier->delete();

        return redirect()->back()->with('success', 'Mitra Usaha berhasil dihapus!');
    }

    /**
     * Download PDF tabel semua mitra usaha
     */
    public function downloadPdf(Request $request)
    {
        $pdfService = new MitraUsahaPdfService();
        return $pdfService->generateAllMitraPdf();
    }

    private function saveBase64Image($base64Image, $directory)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                throw new \Exception('Invalid image type');
            }

            $base64Image = base64_decode($base64Image);

            if ($base64Image === false) {
                throw new \Exception('Base64 decode failed');
            }

            $filename = uniqid() . '.' . $type;
            $path = public_path($directory);

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $filePath = $path . '/' . $filename;
            file_put_contents($filePath, $base64Image);

            return $directory . '/' . $filename;
        }

        throw new \Exception('Invalid base64 image');
    }
}
