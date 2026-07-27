<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing menu items
        MenuItem::truncate();

        // 1. Beranda
        MenuItem::create([
            'label' => 'Beranda',
            'url' => '/',
            'type' => 'internal',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        // 2. Profil (Parent)
        $profil = MenuItem::create([
            'label' => 'Profil',
            'url' => null,
            'type' => 'internal',
            'parent_id' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        // Profil Submenu
        MenuItem::create([
            'label' => 'Tentang Kami',
            'url' => '/profil/tentang',
            'type' => 'internal',
            'parent_id' => $profil->id,
            'order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Visi & Misi',
            'url' => '/profil/visi-misi',
            'type' => 'internal',
            'parent_id' => $profil->id,
            'order' => 2,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Struktur Organisasi',
            'url' => '/profil/struktur',
            'type' => 'internal',
            'parent_id' => $profil->id,
            'order' => 3,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Tugas & Fungsi',
            'url' => '/profil/tugas-fungsi',
            'type' => 'internal',
            'parent_id' => $profil->id,
            'order' => 4,
            'is_active' => true,
        ]);

        // 3. Usaha Mandiri (Parent)
        $usaha = MenuItem::create([
            'label' => 'Usaha Mandiri',
            'url' => null,
            'type' => 'internal',
            'parent_id' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Siliwangi Mandiri Shop',
            'url' => '/usaha-mandiri/siliwangi-shop',
            'type' => 'internal',
            'parent_id' => $usaha->id,
            'order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Produk & Layanan',
            'url' => '/usaha-mandiri/produk',
            'type' => 'internal',
            'parent_id' => $usaha->id,
            'order' => 2,
            'is_active' => true,
        ]);

        // 4. Dokumen (Parent)
        $dokumen = MenuItem::create([
            'label' => 'Dokumen',
            'url' => null,
            'type' => 'internal',
            'parent_id' => null,
            'order' => 4,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Peraturan',
            'url' => '/dokumen/peraturan',
            'type' => 'internal',
            'parent_id' => $dokumen->id,
            'order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Template Kerja Sama',
            'url' => '/dokumen/template-kerjasama',
            'type' => 'internal',
            'parent_id' => $dokumen->id,
            'order' => 2,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Arsip Kerja Sama',
            'url' => '/dokumen/arsip-kerjasama',
            'type' => 'internal',
            'parent_id' => $dokumen->id,
            'order' => 3,
            'is_active' => true,
        ]);

        // 5. PPID
        $ppid = MenuItem::create([
            'label' => 'PPID',
            'url' => null,
            'type' => 'internal',
            'parent_id' => null,
            'order' => 5,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Profil PPID',
            'url' => '/ppid/profil',
            'type' => 'internal',
            'parent_id' => $ppid->id,
            'order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Informasi Publik',
            'url' => '/ppid/informasi-publik',
            'type' => 'internal',
            'parent_id' => $ppid->id,
            'order' => 2,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Prosedur Pelayanan',
            'url' => '/ppid/prosedur',
            'type' => 'internal',
            'parent_id' => $ppid->id,
            'order' => 3,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Permohonan Informasi',
            'url' => '/ppid/permohonan',
            'type' => 'internal',
            'parent_id' => $ppid->id,
            'order' => 4,
            'is_active' => true,
        ]);

        // 6. Berita
        MenuItem::create([
            'label' => 'Berita',
            'url' => '/berita',
            'type' => 'internal',
            'parent_id' => null,
            'order' => 6,
            'is_active' => true,
        ]);

        // 7. Galeri
        MenuItem::create([
            'label' => 'Galeri',
            'url' => '/galeri',
            'type' => 'internal',
            'parent_id' => null,
            'order' => 7,
            'is_active' => true,
        ]);

        // 8. Kontak
        MenuItem::create([
            'label' => 'Kontak',
            'url' => '/kontak',
            'type' => 'internal',
            'parent_id' => null,
            'order' => 8,
            'is_active' => true,
        ]);
    }
}
