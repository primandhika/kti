<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuppliersToUsersSeeder extends Seeder
{
    public function run(): void
    {
        $validRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];

        // Ambil semua supplier yang belum punya user aktif
        $suppliers = Supplier::all();
        $processed = 0;
        $skipped = 0;

        foreach ($suppliers as $supplier) {
            // Skip jika sudah punya user yang valid
            if ($supplier->user_id) {
                $userExists = User::find($supplier->user_id);
                if ($userExists) {
                    $skipped++;
                    continue;
                }
            }

            // Buat username dari kode_supplier atau nama
            $base = $supplier->kode_supplier
                ? strtolower(preg_replace('/[^a-z0-9]/i', '', $supplier->kode_supplier))
                : strtolower(preg_replace('/[^a-z0-9]/i', '', $supplier->nama));

            $base = $base ?: 'mitra' . $supplier->id;
            $username = $base;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $base . $counter;
                $counter++;
            }

            // Tentukan email
            $email = $supplier->email;
            if (!$email || User::where('email', $email)->exists()) {
                $email = $username . '@mitra.bppu.ac.id';
                $emailCounter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $username . $emailCounter . '@mitra.bppu.ac.id';
                    $emailCounter++;
                }
            }

            $user = User::create([
                'name'                 => $supplier->nama,
                'username'             => $username,
                'email'                => $email,
                'phone'                => $supplier->telepon ?? null,
                'password'             => Hash::make('mitra123'),
                'must_change_password' => true,
            ]);

            $role = in_array($supplier->tipe_mitra, $validRoles) ? $supplier->tipe_mitra : 'supplier';
            $user->assignRole($role);

            $supplier->update(['user_id' => $user->id]);

            $processed++;
            $this->command->line("  [OK] {$username} ({$role}) => {$supplier->nama}");
        }

        $this->command->info("Selesai. Dibuat: {$processed} akun, Dilewati: {$skipped} (sudah punya akun).");
        $this->command->info("Password default: mitra123 (wajib ganti saat login pertama).");
    }
}
