<template>
    <Head title="404 - Halaman Tidak Ditemukan | BPPU IKIP Siliwangi">
        <meta name="description" content="Halaman yang Anda cari tidak ditemukan">
    </Head>

    <!-- Announcement Bar -->
    <AnnouncementBar />

    <!-- Navbar -->
    <Navbar />

    <div class="min-h-screen bg-white dark:bg-[#1e1400] transition-colors duration-300 px-4 pt-[84px] md:pt-[92px] pb-20">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-6">
                <!-- Gambar Maung Mini -->
                <div class="mb-3 float-animation">
                    <img src="/404-maungmini.png" alt="404 - Halaman Tidak Ditemukan" class="mx-auto max-w-[180px] sm:max-w-[200px] w-full">
                </div>

                <!-- Teks 404 -->
                <h1 class="text-5xl md:text-6xl font-bold text-primary-900 dark:text-primary-700 mb-2">404</h1>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-[#f4efe5] mb-2">Halaman Tidak Ditemukan</h2>
                <p class="text-gray-600 dark:text-[#d6c199] text-sm md:text-base mb-4">
                    Maaf, halaman yang Anda cari tidak dapat ditemukan.
                </p>

                <!-- Search Bar -->
                <form @submit.prevent="handleSearch" class="max-w-lg mx-auto mb-6">
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari halaman atau konten..."
                            class="w-full px-5 py-3 pr-12 rounded-full border-2 border-gray-300 dark:border-[#6b4700] focus:border-primary-500 dark:focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 text-gray-700 dark:text-[#f4efe5] bg-white dark:bg-[#3d2800] transition-all text-sm"
                        >
                        <button
                            type="submit"
                            class="absolute right-1 top-1/2 -translate-y-1/2 bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white p-2 rounded-full transition-all duration-200 hover:scale-105"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center mb-8">
                    <a
                        href="/"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white px-6 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                    <button
                        @click="goBack"
                        class="inline-flex items-center gap-2 bg-white dark:bg-[#3d2800] hover:bg-gray-50 dark:hover:bg-[#4c3300] text-gray-700 dark:text-[#f4efe5] px-6 py-2.5 rounded-full font-semibold text-sm border-2 border-gray-300 dark:border-[#6b4700] transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Halaman Sebelumnya
                    </button>
                </div>
            </div>

            <!-- Rekomendasi Halaman -->
            <div v-if="posts.length > 0" class="border-t border-gray-200 dark:border-[#6b4700] pt-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-[#f4efe5] mb-4 text-center">Mungkin Anda Mencari</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a
                        v-for="post in posts"
                        :key="post.id"
                        :href="`/berita/${post.year}/${post.month}/${post.day}/${post.slug}`"
                        class="block bg-white dark:bg-[#3d2800] rounded-lg shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden border border-gray-200 dark:border-[#6b4700] hover:scale-105"
                    >
                        <div v-if="post.featured_image" class="aspect-video overflow-hidden bg-gray-200 dark:bg-[#2d1e00]">
                            <img :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-[#f4efe5] text-sm mb-1 line-clamp-2">{{ post.title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-[#c1a366]">{{ formatDate(post.published_at) }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <Footer />
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AnnouncementBar from '@/Components/AnnouncementBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    posts: {
        type: Array,
        default: () => []
    }
});

const { initDarkMode } = useDarkMode();
const searchQuery = ref('');

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/';
    }
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        window.location.href = `/berita?search=${encodeURIComponent(searchQuery.value)}`;
    }
};

const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
};

onMounted(() => {
    initDarkMode();
});
</script>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}
</style>
