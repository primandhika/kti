<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class KantinBAiSeblakSeeder extends Seeder
{
    public function run(): void
    {
        $workUnit = WorkUnit::query()
            ->where(function ($q) {
                $q->where('name', 'Kantin Gedung B')
                  ->orWhere('name', 'Kantin B')
                  ->orWhere('name', 'like', '%Kantin%Gedung B%')
                  ->orWhere('name', 'like', '%Kantin%B%');
            })
            ->first();

        if (!$workUnit) {
            $this->command?->error('Work unit Kantin B tidak ditemukan.');
            return;
        }

        $supplier = $this->resolveAiSeblakSupplier();
        $items = $this->buildItems();

        $created = 0;
        $updated = 0;

        foreach ($items as $item) {
            $existing = Barang::query()
                ->where('work_unit_id', $workUnit->id)
                ->where('nama_barang', $item['nama_barang'])
                ->first();

            $payload = [
                'work_unit_id' => $workUnit->id,
                'nama_barang' => $item['nama_barang'],
                'kategori' => $item['kategori'],
                'satuan' => $item['satuan'],
                'stok' => 100,
                'minimum_stok' => 10,
                'harga_jual' => $item['harga_jual'],
                'harga_konsinyasi' => $item['harga_konsinyasi'],
                'harga_beli' => $item['harga_jual'] - $item['harga_konsinyasi'],
                'harga_grosir' => null,
                'supplier' => $supplier->nama,
                'supplier_id' => $supplier->id,
                'jenis_barang' => 'konsinyasi',
                'is_menu_item' => true,
                'is_active' => true,
                'show_in_shop' => false,
            ];

            if ($existing) {
                $existing->update($payload);
                $updated++;
                continue;
            }

            $payload['kode_barang'] = $this->generateNextPlu($workUnit->id, $workUnit->name);
            Barang::create($payload);
            $created++;
        }

        $this->command?->info("Seeder selesai. Dibuat: {$created}, diperbarui: {$updated}.");
    }

    private function resolveAiSeblakSupplier(): Supplier
    {
        $supplier = Supplier::query()->where('nama', 'Ai Seblak')->first();

        if ($supplier) {
            return $supplier;
        }

        $lastId = Supplier::max('id') ?? 0;
        $kode = 'SUP-' . str_pad((string) ($lastId + 1), 4, '0', STR_PAD_LEFT);

        return Supplier::create([
            'kode_supplier' => $kode,
            'nama' => 'Ai Seblak',
            'perusahaan' => 'Ai Seblak',
            'kontak_person' => 'Ai Seblak',
            'tipe_mitra' => 'konsinyasi',
            'is_active' => true,
            'catatan' => 'Mitra konsinyasi untuk menu seblak Kantin B',
        ]);
    }

    private function generateNextPlu(int $workUnitId, string $workUnitName): string
    {
        $unitCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $workUnitName), 0, 3));
        if (strlen($unitCode) < 3) {
            $unitCode = str_pad($unitCode, 3, 'X');
        }

        $year = date('y');
        $last = Barang::query()
            ->where('work_unit_id', $workUnitId)
            ->where('kode_barang', 'like', "{$unitCode}{$year}-%")
            ->orderBy('kode_barang', 'desc')
            ->first();

        $nextNumber = 1;
        if ($last && preg_match('/-(\d+)$/', $last->kode_barang, $m)) {
            $nextNumber = ((int) $m[1]) + 1;
        }

        return sprintf('%s%s-%04d', $unitCode, $year, $nextNumber);
    }

    private function buildItems(): array
    {
        $items = [];

        $add = function (string $name, int $price, string $category = 'Seblak') use (&$items) {
            $items[] = [
                'nama_barang' => $name,
                'harga_jual' => $price,
                'harga_konsinyasi' => $this->suggestMargin($price),
                'kategori' => $category,
                'satuan' => 'pcs',
            ];
        };

        // Kerupuk 1rb
        foreach ([
            'Udang Oren', 'Udang Warna', 'Tangga', 'Mawar', 'Segi Tiga',
            'Asbes', 'Jaat', 'Makaroni Coklat', 'Makaroni Kuning', 'Jendela',
        ] as $name) {
            $add($name, 1000, 'Seblak - Kerupuk');
        }

        // 1rb-an keringan
        foreach (['Siomay', 'Cuangki Lidah', 'Otak Otak Kering', 'Siomay Krikil'] as $name) {
            $add($name, 1000, 'Seblak - Keringan');
        }

        // 1rb
        foreach (['Mie Kering', 'Mie Basah', 'Kwetiau', 'Cilok'] as $name) {
            $add($name, 1000, 'Seblak - Dasar');
        }

        // 2rb-an
        foreach (['Dumpling Keju', 'Dumpling Ayam', 'Cikua', 'Sosis Geboy', 'Sosis Prima', 'Odeng', 'Bakso'] as $name) {
            $add($name, 2000, 'Seblak - Topping');
        }

        // Harga khusus
        $add('Sosis Biasa', 1000, 'Seblak - Topping');
        $add('Bakso Kecil', 1000, 'Seblak - Topping');
        $add('Jamur Enoki (Porsi Kecil)', 2000, 'Seblak - Jamur');
        $add('Jamur Enoki (Porsi Besar)', 7000, 'Seblak - Jamur');
        $add('Jamur Salju', 3000, 'Seblak - Jamur');
        $add('Sosis Besar', 5000, 'Seblak - Topping');

        // Sayur
        foreach (['Sosin', 'Toge', 'Kiciwis', 'Pakcoy'] as $name) {
            $add($name, 1000, 'Seblak - Sayur');
        }

        // Telur
        $add('Telur Puyuh', 1000, 'Seblak - Telur');
        $add('Telur Ayam', 3000, 'Seblak - Telur');

        return $items;
    }

    private function suggestMargin(int $price): int
    {
        // Margin konsinyasi yang relatif masuk akal (~20%)
        return (int) round($price * 0.2, 0);
    }
}

