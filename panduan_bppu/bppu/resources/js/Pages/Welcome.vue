<template>
    <Head title="BPPU IKIP Siliwangi - Badan Pengelola dan Pengembangan Usaha">
        <!-- Primary Meta Tags -->
        <meta name="title" content="BPPU IKIP Siliwangi - Badan Pengelola dan Pengembangan Usaha">
        <meta name="description" content="Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi mengelola berbagai unit usaha yang berkelanjutan, transparan, dan akuntabel untuk kemajuan institusi.">
        <meta name="keywords" content="BPPU, IKIP Siliwangi, Badan Pengelola Usaha, Kantin IKIP, Belanja IKIP, Unit Usaha, Cimahi">
        <meta name="robots" content="index, follow">
        <meta name="language" content="Indonesian">
        <meta name="author" content="BPPU IKIP Siliwangi">
        <link rel="canonical" href="https://bppu.ikipsiliwangi.ac.id">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://bppu.ikipsiliwangi.ac.id">
        <meta property="og:title" content="BPPU IKIP Siliwangi - Badan Pengelola dan Pengembangan Usaha">
        <meta property="og:description" content="Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi mengelola berbagai unit usaha yang berkelanjutan, transparan, dan akuntabel untuk kemajuan institusi.">
        <meta property="og:image" content="https://bppu.ikipsiliwangi.ac.id/storage/logo-round_ijokuning.png">
        <meta property="og:locale" content="id_ID">
        <meta property="og:site_name" content="BPPU IKIP Siliwangi">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="https://bppu.ikipsiliwangi.ac.id">
        <meta property="twitter:title" content="BPPU IKIP Siliwangi - Badan Pengelola dan Pengembangan Usaha">
        <meta property="twitter:description" content="Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi mengelola berbagai unit usaha yang berkelanjutan, transparan, dan akuntabel untuk kemajuan institusi.">
        <meta property="twitter:image" content="https://bppu.ikipsiliwangi.ac.id/storage/logo-round_ijokuning.png">

        <!-- Structured Data / JSON-LD -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi",
            "alternateName": "BPPU IKIP Siliwangi",
            "url": "https://bppu.ikipsiliwangi.ac.id",
            "logo": "https://bppu.ikipsiliwangi.ac.id/storage/logo-round_ijokuning.png",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+62-22-6629913",
                "contactType": "customer service",
                "areaServed": "ID",
                "availableLanguage": "Indonesian"
            },
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Jl. Terusan Jenderal Sudirman",
                "addressLocality": "Cimahi",
                "addressRegion": "Jawa Barat",
                "postalCode": "40526",
                "addressCountry": "ID"
            },
            "email": "bppu@ikipsiliwangi.ac.id",
            "sameAs": [
                "https://ikipsiliwangi.ac.id"
            ]
        }
        </script>
    </Head>

    <!-- Announcement Bar -->
    <AnnouncementBar />

    <!-- Navbar -->
    <Navbar />

    <div class="min-h-screen bg-white dark:bg-[#1e1400] transition-colors duration-300">
        <!-- Hero Jumbotron -->
        <HeroJumbotron />

        <!-- Quick Stats -->
        <section id="content" class="py-8 bg-white dark:bg-[#1e1400]" aria-label="Statistik BPPU">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <StatsCounter
                    :unitKerja="stats.unitKerja"
                    :yearsOperating="stats.yearsOperating"
                    :memberCount="stats.memberCount"
                />
            </div>
        </section>

        <!-- Unit Usaha Section -->
        <UnitUsahaSection :unitUsaha="unitUsaha" />

        <!-- News Section -->
        <NewsSection :posts="posts" />

        <!-- Menu Section -->
        <MenuSection :menus="menus" id="menu" />

        <!-- Shop Section -->
        <ShopSection :shopItems="shopItems" id="shop" />

        <!-- About Section -->
        <section id="about" class="py-16 md:py-20 bg-white dark:bg-[#1e1400]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4">Tentang BPPU</h2>
                    <div class="w-24 h-1 bg-primary-900 dark:bg-primary-700 mx-auto mb-4"></div>
                    <p class="text-lg text-gray-600 dark:text-[#d6c199] max-w-3xl mx-auto">
                        Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    <!-- Image/Logo -->
                    <div data-aos="fade-right" class="flex justify-center order-2 lg:order-1">
                        <div class="relative w-full max-w-sm">
                            <div class="absolute inset-0 bg-primary-900/10 rounded-full blur-3xl animate-pulse"></div>
                            <img src="/storage/logo-round_ijokuning.png" alt="Logo BPPU IKIP Siliwangi" class="relative w-full h-auto object-contain hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>

                    <!-- Content -->
                    <div data-aos="fade-left" class="order-1 lg:order-2">
                        <div class="prose prose-lg max-w-none">
                            <p class="text-base md:text-lg text-gray-700 dark:text-[#d6c199] mb-4 leading-relaxed">
                                Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi merupakan lembaga yang bertanggung jawab dalam mengelola dan mengembangkan usaha di lingkungan IKIP Siliwangi.
                            </p>
                            <p class="text-base md:text-lg text-gray-700 dark:text-[#d6c199] mb-6 leading-relaxed">
                                Kami berkomitmen untuk mengembangkan dan mengelola berbagai unit usaha yang berkelanjutan, transparan, dan akuntabel guna mendukung kemajuan IKIP Siliwangi.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-[#4c3300] dark:to-[#3d2800] p-5 rounded-xl hover:shadow-lg transition-all duration-300 border border-primary-200 dark:border-[#6b4700]">
                                <div class="text-2xl md:text-3xl font-bold text-primary-900 dark:text-[#ccb27f] mb-1">Transparan</div>
                                <div class="text-sm text-gray-700 dark:text-[#d6c199]">Pengelolaan Terbuka</div>
                            </div>
                            <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-[#4c3300] dark:to-[#3d2800] p-5 rounded-xl hover:shadow-lg transition-all duration-300 border border-primary-200 dark:border-[#6b4700]">
                                <div class="text-2xl md:text-3xl font-bold text-primary-900 dark:text-[#ccb27f] mb-1">Akuntabel</div>
                                <div class="text-sm text-gray-700 dark:text-[#d6c199]">Bertanggung Jawab</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mitra Section -->
        <MitraSection :mitraUsaha="mitraUsaha" />

        <!-- Contact Section -->
        <section id="contact" class="py-16 md:py-20 bg-gradient-to-b from-gray-50 to-white dark:from-[#2d1e00] dark:to-[#1e1400]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4">Hubungi Kami</h2>
                    <div class="w-24 h-1 bg-primary-900 dark:bg-primary-700 mx-auto mb-4"></div>
                    <p class="text-lg text-gray-600 dark:text-[#d6c199]">Kami siap membantu Anda</p>
                </div>

                <!-- Contact Info Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-12">
                    <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-md p-3 sm:p-4 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary-900 to-primary-700 rounded-lg flex items-center justify-center mb-2 sm:mb-3 mx-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-[#f4efe5] text-center mb-1 sm:mb-2">Alamat</h4>
                        <p class="text-[10px] sm:text-xs text-gray-600 dark:text-[#d6c199] text-center leading-tight sm:leading-relaxed">Jl. Terusan Jenderal Sudirman, Cimahi, Jawa Barat 40526</p>
                    </div>

                    <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-md p-3 sm:p-4 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary-900 to-primary-700 rounded-lg flex items-center justify-center mb-2 sm:mb-3 mx-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-[#f4efe5] text-center mb-1 sm:mb-2">Telepon</h4>
                        <p class="text-[10px] sm:text-xs text-gray-600 dark:text-[#d6c199] text-center">(022) 6629913</p>
                    </div>

                    <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-md p-3 sm:p-4 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="300">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary-900 to-primary-700 rounded-lg flex items-center justify-center mb-2 sm:mb-3 mx-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-[#f4efe5] text-center mb-1 sm:mb-2">Email</h4>
                        <p class="text-[10px] sm:text-xs text-gray-600 dark:text-[#d6c199] text-center break-all">bppu@ikipsiliwangi.ac.id</p>
                    </div>

                    <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-md p-3 sm:p-4 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="400">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary-900 to-primary-700 rounded-lg flex items-center justify-center mb-2 sm:mb-3 mx-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-[#f4efe5] text-center mb-1 sm:mb-2">Jam Kerja</h4>
                        <p class="text-[10px] sm:text-xs text-gray-600 dark:text-[#d6c199] text-center leading-tight sm:leading-relaxed">Senin - Jumat: 08.00 - 16.00<br>Sabtu: 08.00 - 12.00</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>

    <!-- Cookie Consent -->
    <CookieConsent />
</template>

<script setup>
import { onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AOS from 'aos';
import 'aos/dist/aos.css';

import AnnouncementBar from '@/Components/AnnouncementBar.vue';
import Navbar from '@/Components/Navbar.vue';
import HeroJumbotron from '@/Components/HeroJumbotron.vue';
import StatsCounter from '@/Components/StatsCounter.vue';
import UnitUsahaSection from '@/Components/UnitUsahaSection.vue';
import NewsSection from '@/Components/NewsSection.vue';
import MenuSection from '@/Components/MenuSection.vue';
import ShopSection from '@/Components/ShopSection.vue';
import MitraSection from '@/Components/MitraSection.vue';
import Footer from '@/Components/Footer.vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import { useDarkMode } from '@/composables/useDarkMode';

defineProps({
    laravelVersion: String,
    posts: Array,
    menus: Array,
    shopItems: Array,
    unitUsaha: Array,
    mitraUsaha: Array,
    stats: Object,
});

const { initDarkMode } = useDarkMode();

onMounted(() => {
    initDarkMode();
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
    });
});
</script>

<style>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f8f9fa;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #996600, #7a5100);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #7a5100, #6b4700);
}

/* Parallax effect */
@media (prefers-reduced-motion: no-preference) {
    [data-aos] {
        transition-property: transform, opacity;
        transition-duration: 0.6s;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
}

/* Smooth section transitions */
section {
    position: relative;
    overflow: hidden;
}

/* Selection color */
::selection {
    background-color: #996600;
    color: white;
}

::-moz-selection {
    background-color: #996600;
    color: white;
}
</style>
