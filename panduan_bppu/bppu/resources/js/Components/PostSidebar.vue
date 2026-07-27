<template>
    <div class="space-y-6">
        <!-- Categories Widget -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                Kategori
            </h3>
            <ul class="space-y-2">
                <li v-for="category in categories" :key="category.slug">
                    <a
                        :href="`/berita?category=${category.slug}`"
                        class="flex items-center justify-between text-gray-700 hover:text-primary-900 transition-colors group"
                    >
                        <span class="group-hover:translate-x-1 transition-transform inline-block">
                            {{ category.name }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Popular Tags Widget -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                Tag Populer
            </h3>
            <div class="flex flex-wrap gap-2">
                <a
                    v-for="tag in popularTags"
                    :key="tag.slug"
                    :href="`/berita?tag=${tag.slug}`"
                    class="inline-block px-3 py-1 bg-gray-100 hover:bg-primary-900 hover:text-white text-gray-700 text-sm rounded-full transition-colors"
                >
                    {{ tag.name }}
                </a>
            </div>
        </div>

        <!-- Recent Posts Widget -->
        <div v-if="recentPosts && recentPosts.length > 0" class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                Berita Terbaru
            </h3>
            <div class="space-y-4">
                <a
                    v-for="post in recentPosts"
                    :key="post.id"
                    :href="getPostUrl(post)"
                    class="flex gap-3 group"
                >
                    <!-- Thumbnail -->
                    <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-primary-900 to-primary-600 rounded overflow-hidden">
                        <img
                            v-if="post.featured_image"
                            :src="`/storage/${post.featured_image}`"
                            :alt="post.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white/30" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-primary-900 transition-colors line-clamp-2 mb-1">
                            {{ post.title }}
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ formatDate(post.published_at) }}
                        </p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Archive Widget -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                Arsip
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="/berita" class="flex items-center justify-between text-gray-700 hover:text-primary-900 transition-colors group">
                        <span class="group-hover:translate-x-1 transition-transform inline-block">
                            Januari 2026
                        </span>
                        <span class="text-xs text-gray-500">({{ archiveCount }})</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- CTA Widget -->
        <div class="bg-gradient-to-br from-primary-900 to-primary-700 rounded-lg shadow-sm p-6 text-white">
            <h3 class="text-lg font-bold mb-2">
                Ikuti Perkembangan Terbaru
            </h3>
            <p class="text-sm text-primary-100 mb-4">
                Dapatkan informasi terkini tentang kegiatan dan pengembangan BPPU IKIP Siliwangi
            </p>
            <a
                href="/#contact"
                class="inline-block w-full text-center bg-white text-primary-900 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
            >
                Hubungi Kami
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    recentPosts: {
        type: Array,
        default: () => []
    },
    archiveCount: {
        type: Number,
        default: 0
    }
});

const categories = [
    { name: 'Berita Umum', slug: 'berita-umum' },
    { name: 'Pengumuman', slug: 'pengumuman' },
    { name: 'Kegiatan', slug: 'kegiatan' },
    { name: 'Prestasi', slug: 'prestasi' },
    { name: 'Artikel', slug: 'artikel' },
];

const popularTags = [
    { name: 'BPPU', slug: 'bppu' },
    { name: 'IKIP Siliwangi', slug: 'ikip-siliwangi' },
    { name: 'Kewirausahaan', slug: 'kewirausahaan' },
    { name: 'Pengembangan Usaha', slug: 'pengembangan-usaha' },
    { name: 'Unit Usaha', slug: 'unit-usaha' },
];

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const getPostUrl = (post) => {
    if (!post.published_at) return '#';
    const date = new Date(post.published_at);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `/berita/${year}/${month}/${day}/${post.slug}`;
};
</script>
