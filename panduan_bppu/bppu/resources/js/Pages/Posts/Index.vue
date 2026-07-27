<template>
    <Head>
        <title>Berita - BPPU IKIP Siliwangi</title>
        <meta name="description" content="Informasi dan berita terkini dari Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi">
    </Head>

    <!-- Announcement Bar -->
    <AnnouncementBar />

    <!-- Navbar -->
    <Navbar />

    <div class="min-h-screen bg-white dark:bg-[#1e1400] transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            <!-- Search & Filters -->
            <div class="mb-8">
                <!-- Search Bar -->
                <div class="relative max-w-2xl">
                    <input
                        v-model="searchForm.search"
                        @input="debouncedSearch"
                        type="text"
                        placeholder="Cari berita, artikel, atau pengumuman..."
                        class="w-full px-5 py-3 pl-12 bg-white dark:bg-[#2d1e00] border border-gray-200 dark:border-[#3d2800] rounded-xl focus:ring-2 focus:ring-primary-700 dark:focus:ring-primary-600 focus:border-transparent text-gray-900 dark:text-[#f4efe5] placeholder-gray-400 dark:placeholder-[#b7934c] shadow-sm"
                    />
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-[#b7934c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Active Filters -->
                <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-[#c1a366]">Filter aktif:</span>
                    <button
                        v-if="searchForm.search"
                        @click="clearFilter('search')"
                        class="inline-flex items-center px-3 py-1.5 bg-primary-100 dark:bg-[#4c3300] text-primary-900 dark:text-[#f4efe5] text-sm rounded-full hover:bg-primary-200 dark:hover:bg-[#5b3d00] transition-colors"
                    >
                        "{{ searchForm.search }}"
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button
                        v-if="searchForm.category"
                        @click="clearFilter('category')"
                        class="inline-flex items-center px-3 py-1.5 bg-primary-100 dark:bg-[#4c3300] text-primary-900 dark:text-[#f4efe5] text-sm rounded-full hover:bg-primary-200 dark:hover:bg-[#5b3d00] transition-colors"
                    >
                        {{ searchForm.category }}
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Posts Grid -->
            <div v-if="posts.data && posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="bg-white dark:bg-[#2d1e00] rounded-xl border border-gray-100 dark:border-[#3d2800] overflow-hidden hover:shadow-xl dark:hover:shadow-2xl hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-300 group"
                >
                    <a :href="getPostUrl(post)" class="block">
                        <!-- Image -->
                        <div class="relative h-52 bg-gradient-to-br from-primary-900 to-primary-600 dark:from-primary-800 dark:to-primary-900 overflow-hidden">
                            <img
                                v-if="post.featured_image"
                                :src="`/storage/${post.featured_image}`"
                                :alt="post.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                            />
                            <div v-else class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/10" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path>
                                    <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                                </svg>
                            </div>

                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                            <!-- Category Badge -->
                            <div v-if="post.category" class="absolute top-3 left-3">
                                <span class="bg-white/90 dark:bg-[#f4efe5]/90 backdrop-blur-sm text-primary-900 dark:text-[#3d2800] px-2.5 py-1 rounded-md text-xs font-semibold shadow-sm">
                                    {{ post.category.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Date -->
                            <div class="flex items-center text-xs text-gray-500 dark:text-[#b7934c] mb-3">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ formatDateShort(post.published_at) }}
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-bold text-gray-900 dark:text-[#f4efe5] mb-2 line-clamp-2 group-hover:text-primary-700 dark:group-hover:text-primary-600 transition-colors leading-snug">
                                {{ post.title }}
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-sm text-gray-600 dark:text-[#c1a366] mb-4 line-clamp-2 leading-relaxed">
                                {{ post.excerpt || stripHtml(post.content) }}
                            </p>

                            <!-- Read More -->
                            <div class="inline-flex items-center text-sm text-primary-700 dark:text-primary-600 font-semibold group-hover:gap-2 transition-all">
                                Baca selengkapnya
                                <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                </article>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white dark:bg-[#2d1e00] rounded-xl border border-gray-100 dark:border-[#3d2800] p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-[#6b4700] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-[#f4efe5] mb-2">Tidak ada berita ditemukan</h3>
                <p class="text-gray-600 dark:text-[#c1a366] mb-6">Coba kata kunci lain atau hapus filter pencarian</p>
                <button
                    v-if="hasActiveFilters"
                    @click="clearAllFilters"
                    class="inline-flex items-center px-6 py-2.5 bg-primary-900 dark:bg-primary-700 text-white rounded-lg hover:bg-primary-800 dark:hover:bg-primary-600 transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Hapus Filter
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="posts.data && posts.data.length > 0 && posts.last_page > 1" class="flex justify-center">
                <nav class="flex items-center gap-2">
                    <a
                        v-for="(link, index) in posts.links"
                        :key="index"
                        :href="link.url"
                        :class="[
                            'px-4 py-2 text-sm rounded-lg transition-all',
                            link.active
                                ? 'bg-primary-900 dark:bg-primary-700 text-white shadow-sm'
                                : link.url
                                ? 'bg-white dark:bg-[#2d1e00] text-gray-700 dark:text-[#d6c199] hover:bg-gray-50 dark:hover:bg-[#3d2800] border border-gray-200 dark:border-[#3d2800]'
                                : 'bg-gray-50 dark:bg-[#1e1400] text-gray-400 dark:text-[#6b4700] cursor-not-allowed border border-gray-200 dark:border-[#2d1e00]'
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <Footer />
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AnnouncementBar from '@/Components/AnnouncementBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    posts: Object,
    filters: Object,
});

const searchForm = reactive({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
});

const hasActiveFilters = computed(() => {
    return searchForm.search || searchForm.category;
});

let debounceTimer = null;
const debouncedSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    router.get('/berita', searchForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilter = (filterName) => {
    searchForm[filterName] = '';
    applyFilters();
};

const clearAllFilters = () => {
    searchForm.search = '';
    searchForm.category = '';
    applyFilters();
};

const formatDateShort = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const stripHtml = (html) => {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const getPostUrl = (post) => {
    if (!post.published_at) return '#';
    const date = new Date(post.published_at);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `/berita/${year}/${month}/${day}/${post.slug}`;
};

const { initDarkMode } = useDarkMode();

onMounted(() => {
    initDarkMode();
});
</script>
