<template>
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Berita Terkait</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <article
                v-for="post in posts"
                :key="post.id"
                class="group cursor-pointer"
            >
                <a :href="getPostUrl(post)" class="block">
                    <!-- Image -->
                    <div class="relative h-48 bg-gradient-to-br from-primary-900 to-primary-600 rounded-lg overflow-hidden mb-4">
                        <img
                            v-if="post.featured_image"
                            :src="`/storage/${post.featured_image}`"
                            :alt="post.title"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path>
                                <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                            </svg>
                        </div>

                        <!-- Category Badge -->
                        <div v-if="post.category" class="absolute top-3 left-3">
                            <span class="inline-block bg-white text-primary-900 text-xs font-semibold px-2 py-1 rounded">
                                {{ post.category.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <!-- Date -->
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ formatDate(post.published_at) }}
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-900 transition-colors">
                            {{ post.title }}
                        </h3>

                        <!-- Excerpt -->
                        <p class="text-sm text-gray-600 line-clamp-2">
                            {{ post.excerpt || stripHtml(post.content) }}
                        </p>

                        <!-- Read More -->
                        <div class="mt-3 inline-flex items-center text-primary-900 text-sm font-semibold group-hover:underline">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </article>
        </div>
    </div>
</template>

<script setup>
defineProps({
    posts: {
        type: Array,
        required: true,
    }
});

const formatDate = (dateString) => {
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
</script>
