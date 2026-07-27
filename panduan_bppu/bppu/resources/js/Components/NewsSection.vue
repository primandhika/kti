<template>
    <section id="news" class="py-12 md:py-16 bg-white dark:bg-[#1e1400]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4">Berita Terkini</h2>
                <div class="w-24 h-1 bg-primary-900 dark:bg-primary-700 mx-auto mb-4"></div>
                <p class="text-lg text-gray-600 dark:text-[#d6c199]">Informasi dan kegiatan terbaru dari BPPU IKIP Siliwangi</p>
            </div>

            <!-- News Grid -->
            <div v-if="posts && posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <article
                    v-for="(post, index) in posts"
                    :key="post.id"
                    class="group relative h-96 rounded-2xl overflow-hidden cursor-pointer"
                    data-aos="fade-up"
                    :data-aos-delay="(index + 1) * 100"
                >
                    <!-- Background Image with Zoom Effect -->
                    <div class="absolute inset-0 transition-transform duration-700 ease-out group-hover:scale-110">
                        <img
                            v-if="post.featured_image"
                            :src="`/storage/${post.featured_image}`"
                            :alt="post.title"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full" :class="getGradientClass(index)">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white/10" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path>
                                    <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-500"></div>

                    <!-- Content Overlay -->
                    <div class="absolute inset-0 p-6 flex flex-col justify-end transform transition-all duration-500">
                        <!-- Date Badge -->
                        <div class="absolute top-6 right-6 bg-white/95 dark:bg-[#996600]/95 backdrop-blur-sm text-primary-900 dark:text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-lg">
                            {{ formatDate(post.published_at) }}
                        </div>

                        <!-- Category Badge -->
                        <div v-if="post.category" class="mb-3 transition-transform duration-500">
                            <span class="inline-block bg-primary-900/90 dark:bg-[#996600]/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                {{ post.category.name }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-white mb-3 line-clamp-2 transition-all duration-500">
                            {{ post.title }}
                        </h3>

                        <!-- Excerpt -->
                        <p class="text-gray-200 text-sm mb-4 line-clamp-2 transition-all duration-500">
                            {{ post.excerpt || stripHtml(post.content) }}
                        </p>

                        <!-- Read More Link -->
                        <a
                            :href="getPostUrl(post)"
                            class="inline-flex items-center text-white font-semibold transition-all duration-500 hover:text-[#f4efe5]"
                        >
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Subtle Border on Hover -->
                    <div class="absolute inset-0 border-2 border-white/0 group-hover:border-white/20 rounded-2xl transition-all duration-500 pointer-events-none"></div>
                </article>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-[#6b4700] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <p class="text-gray-600 dark:text-[#d6c199] text-lg">Belum ada berita tersedia</p>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="400">
                <a href="/berita" class="inline-block bg-primary-900 dark:bg-primary-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-primary-800 dark:hover:bg-primary-600 transition-all duration-300 transform hover:scale-105">
                    Lihat Semua Berita
                </a>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    posts: {
        type: Array,
        default: () => []
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

const getGradientClass = (index) => {
    const gradients = [
        'bg-gradient-to-br from-primary-900 to-primary-600',
        'bg-gradient-to-br from-amber-600 to-primary-700',
        'bg-gradient-to-br from-primary-700 to-amber-500'
    ];
    return gradients[index % gradients.length];
};
</script>
