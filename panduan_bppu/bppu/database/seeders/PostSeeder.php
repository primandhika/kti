<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama sebagai author
        $admin = User::first();

        if (!$admin) {
            // Jika belum ada user, buat user default
            $admin = User::create([
                'name' => 'Admin BPPU',
                'email' => 'admin@bppu.ikipsiliwangi.ac.id',
                'password' => bcrypt('password'),
            ]);
        }

        $beritaUmum = Category::where('slug', 'berita-umum')->first();
        $pengumuman = Category::where('slug', 'pengumuman')->first();
        $kegiatan = Category::where('slug', 'kegiatan')->first();
        $artikel = Category::where('slug', 'artikel')->first();

        $tagBPPU = Tag::where('slug', 'bppu')->first();
        $tagIKIP = Tag::where('slug', 'ikip-siliwangi')->first();
        $tagKewirausahaan = Tag::where('slug', 'kewirausahaan')->first();
        $tagPengembanganUsaha = Tag::where('slug', 'pengembangan-usaha')->first();
        $tagUnitUsaha = Tag::where('slug', 'unit-usaha')->first();

        $posts = [
            [
                'title' => 'Peluncuran Unit Usaha Koperasi Mahasiswa IKIP Siliwangi',
                'slug' => 'peluncuran-unit-usaha-koperasi-mahasiswa-ikip-siliwangi',
                'excerpt' => 'BPPU IKIP Siliwangi resmi meluncurkan Unit Usaha Koperasi Mahasiswa sebagai wadah pembelajaran kewirausahaan dan pemberdayaan ekonomi mahasiswa.',
                'content' => '<p>Bandung, 10 Januari 2026 - Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi dengan bangga mengumumkan peluncuran Unit Usaha Koperasi Mahasiswa yang berlokasi di Gedung Student Center lantai 1.</p>

<p>Koperasi Mahasiswa ini didirikan dengan tujuan untuk memberikan wadah bagi mahasiswa dalam mengembangkan jiwa kewirausahaan sekaligus membantu perekonomian mahasiswa melalui berbagai layanan seperti:</p>

<ul>
<li>Penjualan alat tulis dan perlengkapan kuliah dengan harga terjangkau</li>
<li>Layanan fotokopi dan percetakan</li>
<li>Mini market dengan produk kebutuhan sehari-hari</li>
<li>Kantin yang menyediakan makanan dan minuman sehat</li>
</ul>

<p>Ketua BPPU, Dr. Ahmad Suryana, M.M., mengatakan, "Koperasi Mahasiswa ini bukan hanya sekedar unit usaha, tetapi juga merupakan laboratorium kewirausahaan bagi mahasiswa. Kami berharap mahasiswa dapat belajar langsung tentang manajemen bisnis, keuangan, dan pelayanan pelanggan."</p>

<p>Koperasi ini dikelola oleh tim profesional dengan melibatkan mahasiswa sebagai pengurus dan karyawan, sehingga mahasiswa mendapatkan pengalaman kerja nyata sambil menempuh pendidikan.</p>',
                'category_id' => $beritaUmum?->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'keywords' => 'koperasi mahasiswa, unit usaha, kewirausahaan, BPPU',
                'tags' => [$tagBPPU?->id, $tagIKIP?->id, $tagKewirausahaan?->id, $tagUnitUsaha?->id],
            ],
            [
                'title' => 'Workshop Manajemen Keuangan untuk Unit Usaha BPPU',
                'slug' => 'workshop-manajemen-keuangan-untuk-unit-usaha-bppu',
                'excerpt' => 'BPPU menyelenggarakan workshop manajemen keuangan untuk meningkatkan kapasitas pengelola unit usaha dalam mengelola keuangan secara profesional dan akuntabel.',
                'content' => '<p>Cimahi, 8 Januari 2026 - BPPU IKIP Siliwangi menyelenggarakan Workshop Manajemen Keuangan yang diikuti oleh seluruh pengelola unit usaha di lingkungan kampus. Workshop ini menghadirkan narasumber dari praktisi keuangan profesional.</p>

<p>Materi yang disampaikan dalam workshop meliputi:</p>

<ol>
<li>Prinsip-prinsip dasar akuntansi untuk usaha kecil dan menengah</li>
<li>Penyusunan laporan keuangan yang akurat dan transparan</li>
<li>Pengelolaan arus kas (cash flow) yang efektif</li>
<li>Strategi pengendalian biaya operasional</li>
<li>Pemanfaatan teknologi dalam pencatatan keuangan</li>
</ol>

<p>Narasumber, Ibu Siti Nurhaliza, S.E., M.Ak., menekankan pentingnya pencatatan keuangan yang rapi dan teratur. "Dengan pencatatan keuangan yang baik, unit usaha dapat mengidentifikasi area yang menguntungkan dan area yang perlu diperbaiki," ujarnya.</p>

<p>Workshop ini juga memperkenalkan aplikasi Buku Kas Digital yang telah dikembangkan oleh BPPU untuk memudahkan pengelola unit usaha dalam melakukan pencatatan keuangan secara real-time dan terintegrasi.</p>

<p>Sekretaris BPPU, Bambang Hermanto, S.E., M.M., menyatakan bahwa workshop ini merupakan bagian dari komitmen BPPU untuk meningkatkan profesionalisme dalam pengelolaan unit usaha kampus.</p>',
                'category_id' => $kegiatan?->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'keywords' => 'workshop, manajemen keuangan, pelatihan, akuntansi',
                'tags' => [$tagBPPU?->id, $tagPengembanganUsaha?->id],
            ],
            [
                'title' => 'Pengumuman: Rekrutmen Pengelola Unit Usaha Baru',
                'slug' => 'pengumuman-rekrutmen-pengelola-unit-usaha-baru',
                'excerpt' => 'BPPU membuka kesempatan bagi mahasiswa dan alumni IKIP Siliwangi untuk bergabung sebagai pengelola unit usaha yang akan segera diluncurkan.',
                'content' => '<p>BPPU IKIP Siliwangi membuka kesempatan kepada mahasiswa aktif dan alumni untuk bergabung sebagai pengelola unit usaha baru yang akan diluncurkan pada semester genap tahun akademik 2025/2026.</p>

<h3>Posisi yang Dibuka:</h3>

<p><strong>1. Manajer Kafe Kampus</strong></p>
<ul>
<li>Pendidikan minimal D3 atau mahasiswa semester 6 ke atas</li>
<li>Memiliki pengalaman di bidang F&B minimal 1 tahun</li>
<li>Mampu memimpin tim dan bekerja di bawah tekanan</li>
<li>Kreatif dan inovatif dalam mengembangkan menu</li>
</ul>

<p><strong>2. Supervisor Toko Buku dan Alat Tulis</strong></p>
<ul>
<li>Mahasiswa semester 5 ke atas atau alumni</li>
<li>Teliti dan memiliki kemampuan manajemen inventory</li>
<li>Mampu mengoperasikan komputer dan aplikasi kasir</li>
<li>Komunikatif dan ramah</li>
</ul>

<p><strong>3. Koordinator Layanan Percetakan dan Fotokopi</strong></p>
<ul>
<li>Memahami operasional mesin cetak dan fotokopi</li>
<li>Mampu melayani pelanggan dengan baik</li>
<li>Bertanggung jawab dan jujur</li>
</ul>

<h3>Benefit yang Ditawarkan:</h3>
<ul>
<li>Gaji kompetitif sesuai posisi</li>
<li>Pengalaman kerja nyata di bidang kewirausahaan</li>
<li>Pelatihan dan pengembangan kapasitas</li>
<li>Sertifikat pengalaman kerja</li>
<li>Kesempatan pengembangan karir</li>
</ul>

<h3>Cara Melamar:</h3>
<p>Kirimkan CV dan surat lamaran ke email: <strong>rekrutmen@bppu.ikipsiliwangi.ac.id</strong> dengan subjek: <strong>Posisi yang Dilamar - Nama Lengkap</strong></p>

<p>Batas waktu pendaftaran: <strong>31 Januari 2026</strong></p>

<p>Informasi lebih lanjut hubungi: Sekretariat BPPU, Gedung Rektorat Lt. 2, atau telepon (022) 6629913 ext. 205</p>',
                'category_id' => $pengumuman?->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'keywords' => 'rekrutmen, lowongan kerja, unit usaha, mahasiswa',
                'tags' => [$tagBPPU?->id, $tagUnitUsaha?->id],
            ],
            [
                'title' => 'Strategi Pengembangan UMKM di Era Digital',
                'slug' => 'strategi-pengembangan-umkm-di-era-digital',
                'excerpt' => 'Artikel tentang strategi efektif mengembangkan UMKM dengan memanfaatkan teknologi digital dan platform online untuk meningkatkan penjualan.',
                'content' => '<p>Di era digital saat ini, pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) dituntut untuk beradaptasi dengan perkembangan teknologi. BPPU IKIP Siliwangi berbagi beberapa strategi efektif untuk mengembangkan UMKM di era digital.</p>

<h3>1. Manfaatkan Media Sosial untuk Promosi</h3>
<p>Media sosial seperti Instagram, Facebook, dan TikTok merupakan platform efektif untuk mempromosikan produk. Buatlah konten yang menarik, konsisten, dan relevan dengan target pasar Anda.</p>

<h3>2. Optimalkan Marketplace</h3>
<p>Daftarkan produk Anda di berbagai marketplace seperti Tokopedia, Shopee, atau Bukalapak. Ini akan memperluas jangkauan pasar tanpa perlu membuka toko fisik.</p>

<h3>3. Tingkatkan Kualitas Foto Produk</h3>
<p>Foto produk yang berkualitas tinggi akan meningkatkan kepercayaan konsumen. Investasikan waktu untuk mengambil foto produk dengan pencahayaan yang baik dan angle yang menarik.</p>

<h3>4. Kelola Keuangan dengan Aplikasi Digital</h3>
<p>Gunakan aplikasi pencatatan keuangan digital untuk memantau arus kas, penjualan, dan pengeluaran. Ini akan membantu Anda membuat keputusan bisnis yang lebih baik berdasarkan data.</p>

<h3>5. Bangun Relasi dengan Pelanggan</h3>
<p>Berikan pelayanan terbaik dan responsif terhadap pertanyaan pelanggan. Pelanggan yang puas akan menjadi marketing gratis untuk bisnis Anda melalui word-of-mouth.</p>

<h3>6. Ikuti Tren Pasar</h3>
<p>Selalu update dengan tren terkini di industri Anda. Adaptasi cepat terhadap perubahan tren akan membuat bisnis Anda tetap relevan.</p>

<h3>7. Kolaborasi dan Networking</h3>
<p>Jalin kerjasama dengan UMKM lain atau influencer untuk memperluas jangkauan. Kolaborasi dapat membuka peluang pasar baru.</p>

<p>BPPU IKIP Siliwangi berkomitmen untuk mendampingi unit usaha dan UMKM binaan dalam menerapkan strategi-strategi ini. Untuk konsultasi lebih lanjut, silakan hubungi Divisi Pengembangan Usaha BPPU.</p>',
                'category_id' => $artikel?->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'keywords' => 'UMKM, digital marketing, kewirausahaan, strategi bisnis',
                'tags' => [$tagKewirausahaan?->id, $tagPengembanganUsaha?->id],
            ],
            [
                'title' => 'Rapat Evaluasi Kinerja Unit Usaha Semester Ganjil 2025/2026',
                'slug' => 'rapat-evaluasi-kinerja-unit-usaha-semester-ganjil-2025-2026',
                'excerpt' => 'BPPU mengadakan rapat evaluasi kinerja seluruh unit usaha untuk menilai pencapaian dan merencanakan pengembangan di semester berikutnya.',
                'content' => '<p>Cimahi, 15 Januari 2026 - BPPU IKIP Siliwangi menggelar Rapat Evaluasi Kinerja Unit Usaha Semester Ganjil 2025/2026 yang dihadiri oleh seluruh pengelola unit usaha, pengurus BPPU, dan perwakilan dari Rektorat.</p>

<h3>Pencapaian Semester Ganjil 2025/2026:</h3>

<p><strong>Unit Usaha Kantin Kampus:</strong></p>
<ul>
<li>Peningkatan omzet sebesar 25% dibandingkan semester sebelumnya</li>
<li>Penambahan variasi menu makanan sehat dan bergizi</li>
<li>Tingkat kepuasan pelanggan mencapai 87%</li>
</ul>

<p><strong>Unit Usaha Fotokopi dan Percetakan:</strong></p>
<ul>
<li>Peningkatan layanan dengan penambahan mesin baru</li>
<li>Implementasi sistem antrian digital</li>
<li>Peningkatan omzet sebesar 18%</li>
</ul>

<p><strong>Unit Usaha Toko Alat Tulis:</strong></p>
<ul>
<li>Penambahan produk-produk baru sesuai kebutuhan mahasiswa</li>
<li>Sistem inventory yang lebih terorganisir</li>
<li>Peningkatan omzet sebesar 15%</li>
</ul>

<h3>Target Semester Genap 2025/2026:</h3>

<ol>
<li>Peluncuran Kafe Kampus dengan konsep modern dan cozy</li>
<li>Pengembangan aplikasi mobile untuk pemesanan online</li>
<li>Peningkatan standar pelayanan di semua unit usaha</li>
<li>Program pelatihan berkelanjutan untuk karyawan</li>
<li>Implementasi sistem pembayaran digital di semua unit usaha</li>
</ol>

<p>Ketua BPPU menyampaikan apresiasi kepada seluruh pengelola unit usaha atas kinerja yang baik di semester ganjil. "Pencapaian ini merupakan hasil kerja keras bersama. Mari kita tingkatkan lagi di semester genap dengan inovasi dan pelayanan yang lebih baik," ungkap Dr. Ahmad Suryana, M.M.</p>

<p>Rapat juga membahas tentang transparansi laporan keuangan dan rencana pengembangan infrastruktur untuk meningkatkan kenyamanan pelanggan.</p>',
                'category_id' => $beritaUmum?->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'keywords' => 'evaluasi kinerja, rapat, unit usaha, laporan',
                'tags' => [$tagBPPU?->id, $tagUnitUsaha?->id],
            ],
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'] ?? [];
            unset($postData['tags']);

            $post = Post::create($postData);

            if (!empty($tags)) {
                $post->tags()->sync(array_filter($tags));
            }
        }
    }
}
