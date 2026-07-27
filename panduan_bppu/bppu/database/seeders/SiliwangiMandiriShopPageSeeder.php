<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class SiliwangiMandiriShopPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'title' => 'Siliwangi Mandiri Shop',
            'slug' => 'siliwangi-shop',
            'category' => 'usaha-mandiri',
            'content' => $this->getContent(),
            'featured_image' => 'pages/siliwangi-mandiri-shop.png',
            'status' => 'published',
            'meta_title' => 'Siliwangi Mandiri Shop - Unit Usaha Mandiri IKIP Siliwangi',
            'meta_description' => 'Siliwangi Mandiri Shop adalah unit usaha mandiri kampus yang didirikan oleh IKIP Siliwangi sebagai pusat kegiatan ekonomi dan inkubator bisnis bagi mahasiswa dan civitas akademika.',
            'meta_keywords' => 'siliwangi mandiri shop, unit usaha kampus, kewirausahaan, IKIP Siliwangi, inkubator bisnis, indogrosir',
            'user_id' => 2,
        ]);
    }

    private function getContent(): string
    {
        return <<<'HTML'
<div class="space-y-6">
    <div class="rounded-lg overflow-hidden">
        <img src="/storage/pages/siliwangi-mandiri-shop.png" alt="Siliwangi Mandiri Shop" class="w-full h-auto object-cover">
    </div>

    <div class="prose max-w-none">
        <h2 style="color: #996600;" class="text-3xl font-bold mb-4">Tentang Siliwangi Mandiri Shop</h2>

        <p class="text-lg leading-relaxed mb-4">
            <strong>Siliwangi Mandiri Shop</strong> adalah unit usaha mandiri kampus yang diresmikan oleh IKIP Siliwangi pada tanggal 8 Oktober 2025 di Kota Cimahi. Toko ini didirikan melalui Business Incubator IKIP Siliwangi dan bertujuan untuk menjadi pusat kegiatan ekonomi yang mendukung kebutuhan sehari-hari civitas akademika.
        </p>

        <div style="background-color: #f4efe5; border-left: 4px solid #996600;" class="p-6 rounded-r-lg mb-6">
            <p class="text-base italic">
                "Siliwangi Mandiri Shop merupakan implementasi dari visi kampus untuk menjadi perguruan tinggi yang mandiri dan kompetitif, sekaligus memfasilitasi mahasiswa dalam praktik bisnis nyata."
            </p>
            <p class="text-sm mt-2" style="color: #996600;">
                - Prof. Dr. Hj. Euis Eti Rohaeti, M.Pd., Rektor IKIP Siliwangi
            </p>
        </div>

        <h3 style="color: #996600;" class="text-2xl font-bold mb-3 mt-8">Tujuan dan Manfaat</h3>

        <ul class="space-y-3 mb-6">
            <li class="flex items-start">
                <span style="color: #996600;" class="mr-2 mt-1">&#10148;</span>
                <span><strong>Pusat Ekonomi Kampus:</strong> Menyediakan kebutuhan sehari-hari civitas akademika dengan harga kompetitif dan lokasi yang strategis di dalam kampus.</span>
            </li>
            <li class="flex items-start">
                <span style="color: #996600;" class="mr-2 mt-1">&#10148;</span>
                <span><strong>Inkubator Bisnis:</strong> Menjadi wadah pembelajaran bisnis nyata bagi mahasiswa dan alumni, mulai dari manajemen supply chain hingga pemasaran.</span>
            </li>
            <li class="flex items-start">
                <span style="color: #996600;" class="mr-2 mt-1">&#10148;</span>
                <span><strong>Pengembangan Kewirausahaan:</strong> Mendukung program kewirausahaan kampus dan memfasilitasi mahasiswa untuk belajar mengelola usaha secara profesional.</span>
            </li>
            <li class="flex items-start">
                <span style="color: #996600;" class="mr-2 mt-1">&#10148;</span>
                <span><strong>Kemandirian Ekonomi:</strong> Mendorong kemandirian ekonomi kampus melalui unit usaha yang dikelola secara profesional dan berkelanjutan.</span>
            </li>
        </ul>

        <h3 style="color: #996600;" class="text-2xl font-bold mb-3 mt-8">Kerjasama dengan Indogrosir</h3>

        <p class="mb-4">
            Siliwangi Mandiri Shop menjalin kemitraan strategis dengan <strong>Indogrosir</strong>, salah satu distributor grosir terkemuka di Indonesia. Kerjasama ini memastikan:
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div style="background-color: #eae0cc;" class="p-4 rounded-lg">
                <h4 style="color: #996600;" class="font-bold mb-2">Ketersediaan Produk</h4>
                <p class="text-sm">Supply produk yang stabil dan beragam untuk memenuhi kebutuhan kampus</p>
            </div>
            <div style="background-color: #eae0cc;" class="p-4 rounded-lg">
                <h4 style="color: #996600;" class="font-bold mb-2">Harga Kompetitif</h4>
                <p class="text-sm">Harga yang bersaing dan terjangkau bagi seluruh civitas akademika</p>
            </div>
            <div style="background-color: #eae0cc;" class="p-4 rounded-lg">
                <h4 style="color: #996600;" class="font-bold mb-2">Kualitas Terjamin</h4>
                <p class="text-sm">Produk berkualitas dari distributor resmi dan terpercaya</p>
            </div>
            <div style="background-color: #eae0cc;" class="p-4 rounded-lg">
                <h4 style="color: #996600;" class="font-bold mb-2">Sistem Bisnis Profesional</h4>
                <p class="text-sm">Pembelajaran sistem retail dan distribusi yang profesional</p>
            </div>
        </div>

        <h3 style="color: #996600;" class="text-2xl font-bold mb-3 mt-8">Peran dalam Ekosistem Kampus</h3>

        <p class="mb-4">
            Sebagai bagian dari Business Incubator IKIP Siliwangi, Siliwangi Mandiri Shop berperan sebagai:
        </p>

        <ol class="space-y-3 mb-6 list-decimal list-inside">
            <li><strong>Laboratorium Bisnis:</strong> Tempat mahasiswa belajar praktik bisnis retail secara langsung</li>
            <li><strong>Sumber Pendapatan Kampus:</strong> Kontribusi terhadap pendapatan kampus melalui usaha mandiri yang berkelanjutan</li>
            <li><strong>Pengembangan Soft Skills:</strong> Melatih mahasiswa dalam customer service, manajemen inventory, dan operasional toko</li>
            <li><strong>Networking dan Kemitraan:</strong> Membangun jejaring bisnis dengan mitra industri seperti Indogrosir</li>
        </ol>

        <div style="background-color: #d6c199; border: 2px solid #996600;" class="p-6 rounded-lg mt-8">
            <h4 style="color: #3d2800;" class="text-xl font-bold mb-3 text-center">Visi Siliwangi Mandiri Shop</h4>
            <p class="text-center mb-4">
                Menjadi unit usaha mandiri kampus yang profesional, berkelanjutan, dan memberikan kontribusi nyata terhadap pengembangan kewirausahaan civitas akademika IKIP Siliwangi.
            </p>
            <h4 style="color: #3d2800;" class="text-xl font-bold mb-3 text-center mt-6">Misi</h4>
            <ul class="space-y-2">
                <li>1. Menyediakan produk kebutuhan sehari-hari berkualitas dengan harga kompetitif</li>
                <li>2. Menjadi wadah pembelajaran bisnis praktis bagi mahasiswa dan alumni</li>
                <li>3. Mengembangkan kemitraan strategis dengan pelaku industri</li>
                <li>4. Mendukung program kewirausahaan dan kemandirian ekonomi kampus</li>
            </ul>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Untuk informasi lebih lanjut, hubungi Business Incubator IKIP Siliwangi
            </p>
        </div>
    </div>
</div>
HTML;
    }
}
