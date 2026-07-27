<?php

namespace App\Console\Commands;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SyncMitraAkun extends Command
{
    protected $signature = 'mitra:sync-akun';
    protected $description = 'Buat akun user untuk mitra usaha yang belum punya user_id';

    private const MITRA_ROLES = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];

    public function handle()
    {
        $suppliers = Supplier::whereNull('user_id')->orWhere('user_id', 0)->get();

        if ($suppliers->isEmpty()) {
            $this->info('Semua mitra usaha sudah memiliki akun.');
            return 0;
        }

        $this->info("Ditemukan {$suppliers->count()} mitra tanpa akun. Memproses...\n");

        $created = 0;
        $errors  = 0;

        foreach ($suppliers as $supplier) {
            try {
                $username      = $this->generateUsername($supplier->kode_supplier);
                $plainPassword = 'Mitra@' . substr(str_replace(['_', '-', ' '], '', $supplier->kode_supplier), 0, 6);

                $email = $this->resolveEmail($supplier->email, $supplier->kode_supplier);

                $user = User::create([
                    'name'                 => $supplier->nama,
                    'username'             => $username,
                    'email'                => $email,
                    'phone'                => $supplier->telepon ?? null,
                    'password'             => Hash::make($plainPassword),
                    'must_change_password' => true,
                ]);

                if (in_array($supplier->tipe_mitra, self::MITRA_ROLES)) {
                    $role = Role::firstOrCreate(['name' => $supplier->tipe_mitra, 'guard_name' => 'web']);
                    $user->assignRole($role);
                }

                $supplier->user_id = $user->id;
                $supplier->save();

                $this->line("  <info>OK</info>  [{$supplier->id}] {$supplier->kode_supplier} => user #{$user->id}, username: <comment>{$username}</comment>, pass: <comment>{$plainPassword}</comment>");
                $created++;
            } catch (\Exception $e) {
                $this->error("  ERR [{$supplier->id}] {$supplier->kode_supplier}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Selesai: {$created} akun dibuat, {$errors} error.");

        return 0;
    }

    private function generateUsername(string $kodeSupplier): string
    {
        $base     = strtolower(str_replace([' ', '-'], '_', $kodeSupplier));
        $username = $base;
        $counter  = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter++;
        }
        return $username;
    }

    private function resolveEmail(?string $supplierEmail, string $kodeSupplier): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $kodeSupplier));

        // Gunakan email asli jika ada dan belum dipakai
        if ($supplierEmail && !User::where('email', $supplierEmail)->exists()) {
            return $supplierEmail;
        }

        if ($supplierEmail) {
            $this->warn("  Email {$supplierEmail} sudah dipakai, [{$kodeSupplier}] akan pakai email placeholder.");
        }

        // Generate email placeholder unik
        $email   = "mitra.{$slug}@bppu.internal";
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = "mitra.{$slug}{$counter}@bppu.internal";
            $counter++;
        }
        return $email;
    }
}
