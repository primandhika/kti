<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workUnits = [
            [
                'unit_id' => 'G43874',
                'name' => 'Kantin Gedung G',
                'type' => 'Kantin',
                'description' => 'Kantin yang berlokasi di Gedung G menyediakan berbagai menu makanan dan minuman untuk mahasiswa dan dosen.',
                'location' => 'Gedung G Lantai 1',
                'manager_name' => 'Budi Santoso',
                'contact_phone' => '081234567890',
                'contact_email' => 'kantin.g@ikipsiliwangi.ac.id',
                'operating_hours' => 'Senin-Sabtu, 07:00-15:00',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'unit_id' => 'B28419',
                'name' => 'Kantin Gedung B',
                'type' => 'Kantin',
                'description' => 'Kantin yang berlokasi di Gedung B melayani civitas akademika dengan menu-menu favorit setiap hari.',
                'location' => 'Gedung B Lantai 1',
                'manager_name' => 'Siti Rahayu',
                'contact_phone' => '081234567891',
                'contact_email' => 'kantin.b@ikipsiliwangi.ac.id',
                'operating_hours' => 'Senin-Sabtu, 07:00-15:00',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'unit_id' => 'I52736',
                'name' => 'Kantin Gedung I',
                'type' => 'Kantin',
                'description' => 'Kantin yang berlokasi di Gedung I menawarkan berbagai pilihan makanan sehat dan bergizi.',
                'location' => 'Gedung I Lantai 1',
                'manager_name' => 'Ahmad Fauzi',
                'contact_phone' => '081234567892',
                'contact_email' => 'kantin.i@ikipsiliwangi.ac.id',
                'operating_hours' => 'Senin-Sabtu, 07:00-15:00',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'unit_id' => 'S91847',
                'name' => 'Siliwangi Mandiri Shop',
                'type' => 'Shop',
                'description' => 'Toko yang menyediakan merchandise IKIP Siliwangi dan berbagai kebutuhan mahasiswa.',
                'location' => 'Gedung Rektorat Lantai 1',
                'manager_name' => 'Dewi Lestari',
                'contact_phone' => '081234567893',
                'contact_email' => 'shop@ikipsiliwangi.ac.id',
                'operating_hours' => 'Senin-Jumat, 08:00-16:00',
                'is_active' => true,
                'display_order' => 4,
            ],
        ];

        foreach ($workUnits as $unit) {
            \App\Models\WorkUnit::create($unit);
        }
    }
}
