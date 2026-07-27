<template>
    <Head>
        <!-- Primary Meta Tags -->
        <title>{{ post.title }} - BPPU IKIP Siliwangi</title>
        <meta name="title" :content="post.title">
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="post.keywords || defaultKeywords">
        <meta name="author" :content="post.user ? post.user.name : 'BPPU IKIP Siliwangi'">
        <link rel="canonical" :href="canonicalUrl">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="article">
        <meta property="og:url" :content="canonicalUrl">
        <meta property="og:title" :content="post.title">
        <meta property="og:description" :content="metaDescription">
        <meta property="og:image" :content="ogImage">
        <meta property="og:image:secure_url" :content="ogImage">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" :content="post.title">
        <meta property="og:site_name" content="BPPU IKIP Siliwangi">
        <meta property="og:locale" content="id_ID">
        <meta property="article:published_time" :content="post.published_at">
        <meta property="article:modified_time" :content="post.updated_at">
        <meta property="article:author" :content="post.user ? post.user.name : ''">
        <meta property="article:section" :content="post.category ? post.category.name : 'Berita'">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" :content="canonicalUrl">
        <meta name="twitter:title" :content="post.title">
        <meta name="twitter:description" :content="metaDescription">
        <meta name="twitter:image" :content="ogImage">

        <!-- Structured Data / JSON-LD -->
        <script type="application/ld+json" v-html="structuredData"></script>
    </Head>

    <!-- Announcement Bar -->
    <AnnouncementBar />

    <!-- Navbar -->
    <Navbar />

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-[#1e1400] dark:to-[#0f0a00] transition-colors duration-300 pt-[84px] md:pt-[92px]">
        <!-- Main Container with Sidebar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Article Card -->
                    <article class="bg-white dark:bg-[#2d1e00] rounded-xl shadow-lg border border-gray-200 dark:border-[#3d2800] overflow-hidden">
                        <!-- Breadcrumb -->
                        <nav class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-[#3d2800]">
                            <div class="flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 dark:text-[#b7934c]">
                                <a href="/" class="hover:text-primary-900 dark:hover:text-primary-700 transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                </a>
                                <svg class="w-3 h-3 text-gray-400 dark:text-[#6b4700]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="/berita" class="hover:text-primary-900 dark:hover:text-primary-700 transition-colors">Berita</a>
                                <svg class="w-3 h-3 text-gray-400 dark:text-[#6b4700]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-[#d6c199] truncate">{{ post.title }}</span>
                            </div>
                        </nav>

                        <!-- Article Header -->
                        <header class="px-6 sm:px-8 md:px-10 pt-6 pb-4">
                            <!-- Category & Reading Time -->
                            <div class="flex items-center gap-3 mb-3">
                                <span v-if="post.category" class="inline-flex items-center px-3 py-1 bg-primary-900 dark:bg-primary-700 text-white text-xs font-semibold rounded-full">
                                    {{ post.category.name }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-[#b7934c]">{{ readingTime }} menit baca</span>
                            </div>

                            <!-- Title -->
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4 leading-tight">
                                {{ post.title }}
                            </h1>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-gray-600 dark:text-[#c1a366] pb-4 border-b border-gray-200 dark:border-[#3d2800]">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-gradient-to-br from-primary-900 to-primary-700 dark:from-primary-700 dark:to-primary-900 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                        {{ getInitials(post.user.name) }}
                                    </div>
                                    <span class="font-medium">{{ post.user.name }}</span>
                                </div>
                                <span class="text-gray-300 dark:text-[#6b4700]">•</span>
                                <time :datetime="post.published_at">{{ formatDateShort(post.published_at) }}</time>
                                <span class="text-gray-300 dark:text-[#6b4700]">•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ formatViewCount(post.view_count) }} dilihat
                                </span>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <div v-if="post.featured_image" class="px-6 sm:px-8 md:px-10 pb-6">
                            <img :src="`/storage/${post.featured_image}`" :alt="post.title" class="w-full h-auto object-cover rounded-lg shadow-md"/>
                        </div>

                        <!-- Article Content -->
                        <div class="px-6 sm:px-8 md:px-10 py-6">
                            <div class="prose prose-lg max-w-none dark:prose-invert" v-html="post.content"></div>
                        </div>

                        <!-- Tags -->
                        <div v-if="post.tags && post.tags.length > 0" class="px-6 sm:px-8 md:px-10 pb-4">
                            <div class="flex flex-wrap gap-2">
                                <a v-for="tag in post.tags" :key="tag.id" :href="`/berita?tag=${tag.slug}`" class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-[#3d2800] hover:bg-primary-900 dark:hover:bg-primary-700 hover:text-white text-gray-700 dark:text-[#d6c199] text-xs rounded-full transition-colors">
                                    <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                    {{ tag.name }}
                                </a>
                            </div>
                        </div>

                        <!-- Reaction Bar -->
                        <div class="px-6 sm:px-8 md:px-10 py-4 border-t border-gray-100 dark:border-[#3d2800]">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500 dark:text-[#b7934c] shrink-0">Artikel ini:</span>
                                <PostReactionBar
                                    :post-id="post.id"
                                    :initial-counts="reactionCounts"
                                    :initial-user-reactions="userReactions"
                                />
                            </div>
                        </div>

                        <!-- Share Footer - Sticky on Mobile -->
                        <footer class="sticky bottom-0 md:relative px-4 py-3 bg-white dark:bg-[#2d1e00] border-t border-gray-200 dark:border-[#3d2800] md:bg-gray-50 md:dark:bg-[#1e1400] md:px-6 md:py-4 shadow-lg md:shadow-none">
                            <div class="flex items-center justify-center md:justify-between gap-3">
                                <span class="text-xs text-gray-600 dark:text-[#b7934c] hidden md:inline">Bagikan:</span>
                                <div class="flex items-center gap-2">
                                    <button @click="shareOnWhatsApp" class="share-btn bg-[#25D366] hover:bg-[#20BA5A]">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    </button>
                                    <button @click="shareOnFacebook" class="share-btn bg-[#1877F2] hover:bg-[#166FE5]">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </button>
                                    <button @click="shareOnX" class="share-btn bg-[#1DA1F2] hover:bg-[#1A94DA]">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </button>
                                    <button @click="copyLink" class="share-btn bg-gray-600 hover:bg-gray-700 dark:bg-[#6b4700] dark:hover:bg-[#5b3d00]">
                                        <svg v-if="!linkCopied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </footer>
                    </article>

                    <!-- Related Posts -->
                    <div class="mt-8">
                        <RelatedPosts v-if="relatedPosts && relatedPosts.length > 0" :posts="relatedPosts" />
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <PostSidebar :recent-posts="recentPosts" :archive-count="archiveCount" />
                </aside>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <Footer />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AnnouncementBar from '@/Components/AnnouncementBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import RelatedPosts from '@/Components/RelatedPosts.vue';
import PostSidebar from '@/Components/PostSidebar.vue';
import PostReactionBar from '@/Components/Posts/PostReactionBar.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    post: Object,
    relatedPosts: Array,
    recentPosts: Array,
    archiveCount: {
        type: Number,
        default: 0
    },
    reactionCounts: {
        type: Object,
        default: () => ({})
    },
    userReactions: {
        type: Array,
        default: () => []
    },
});

const { initDarkMode } = useDarkMode();
const linkCopied = ref(false);

// Computed Properties
const baseUrl = 'https://bppu.ikipsiliwangi.ac.id';

const canonicalUrl = computed(() => {
    const { year, month, day, slug } = props.post;
    return `${baseUrl}/berita/${year}/${month}/${day}/${slug}`;
});

const metaDescription = computed(() => {
    if (props.post.excerpt) {
        return props.post.excerpt;
    }
    const stripped = stripHtml(props.post.content);
    return stripped.substring(0, 160) + (stripped.length > 160 ? '...' : '');
});

const defaultKeywords = computed(() => {
    const category = props.post.category ? props.post.category.name : 'Berita';
    const tags = props.post.tags ? props.post.tags.map(t => t.name).join(', ') : '';
    return `BPPU, IKIP Siliwangi, ${category}, ${tags}, ${props.post.title}`;
});

const ogImage = computed(() => {
    if (props.post.featured_image) {
        return `${baseUrl}/storage/${props.post.featured_image}`;
    }
    return `${baseUrl}/storage/logo-round_ijokuning.png`;
});

const structuredData = computed(() => {
    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": props.post.title,
        "description": metaDescription.value,
        "image": ogImage.value,
        "datePublished": props.post.published_at,
        "dateModified": props.post.updated_at,
        "author": {
            "@type": "Person",
            "name": props.post.user ? props.post.user.name : "BPPU IKIP Siliwangi"
        },
        "publisher": {
            "@type": "Organization",
            "name": "BPPU IKIP Siliwangi",
            "logo": {
                "@type": "ImageObject",
                "url": `${baseUrl}/storage/logo-round_ijokuning.png`
            }
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": canonicalUrl.value
        },
        "articleSection": props.post.category ? props.post.category.name : "Berita",
        "keywords": defaultKeywords.value
    });
});

const readingTime = computed(() => {
    const wordsPerMinute = 200;
    const text = stripHtml(props.post.content);
    const wordCount = text.split(/\s+/).length;
    return Math.ceil(wordCount / wordsPerMinute);
});

// Methods
const stripHtml = (html) => {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
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

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
};

const formatViewCount = (count) => {
    if (!count) return '0';
    if (count >= 1000) return (count / 1000).toFixed(1).replace('.0', '') + 'rb';
    return count.toString();
};

// Share Functions
const shareOnWhatsApp = () => {
    const text = `${props.post.title}\n\n${canonicalUrl.value}`;
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const shareOnFacebook = () => {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(canonicalUrl.value)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const shareOnX = () => {
    const text = props.post.title;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(canonicalUrl.value)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(canonicalUrl.value);
        linkCopied.value = true;
        setTimeout(() => {
            linkCopied.value = false;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};

onMounted(() => {
    initDarkMode();
});
</script>

<style scoped>
/* Share Buttons */
.share-btn {
    @apply p-2 rounded-lg text-white transition-all duration-200 hover:scale-110 active:scale-95 shadow-sm;
}

/* Prose styling for article content */
.prose {
    color: #374151;
    line-height: 1.8;
    font-size: 1.0625rem;
}

.dark .prose {
    color: #d6c199;
}

@media (max-width: 640px) {
    .prose {
        font-size: 1rem;
    }
}

.prose :deep(h2) {
    font-size: 1.875rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #111827;
}

.dark .prose :deep(h2) {
    color: #f4efe5;
}

.prose :deep(h3) {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #111827;
}

.dark .prose :deep(h3) {
    color: #f4efe5;
}

.prose :deep(h4) {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    color: #111827;
}

.dark .prose :deep(h4) {
    color: #eae0cc;
}

.prose :deep(p) {
    margin-bottom: 1.25rem;
}

.prose :deep(ul),
.prose :deep(ol) {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}

.prose :deep(li) {
    margin-bottom: 0.5rem;
}

.prose :deep(ul li) {
    list-style-type: disc;
}

.prose :deep(ol li) {
    list-style-type: decimal;
}

.prose :deep(strong) {
    font-weight: 600;
    color: #111827;
}

.dark .prose :deep(strong) {
    color: #f4efe5;
}

.prose :deep(a) {
    color: #996600;
    text-decoration: underline;
}

.prose :deep(a:hover) {
    color: #7a5100;
}

.dark .prose :deep(a) {
    color: #ccb27f;
}

.dark .prose :deep(a:hover) {
    color: #b7934c;
}

.prose :deep(blockquote) {
    border-left: 4px solid #996600;
    padding-left: 1rem;
    font-style: italic;
    color: #6b7280;
    margin: 1.5rem 0;
}

.dark .prose :deep(blockquote) {
    border-left-color: #ccb27f;
    color: #c1a366;
}

.prose :deep(img) {
    border-radius: 0.5rem;
    margin: 1.5rem 0;
    max-width: 100%;
    height: auto;
}

.prose :deep(code) {
    background-color: #f3f4f6;
    color: #374151;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.dark .prose :deep(code) {
    background-color: #3d2800;
    color: #eae0cc;
}

.prose :deep(pre) {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.dark .prose :deep(pre) {
    background-color: #2d1e00;
    border: 1px solid #3d2800;
}

.prose :deep(pre code) {
    background-color: transparent;
    color: inherit;
    padding: 0;
}

.prose :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.prose :deep(th),
.prose :deep(td) {
    border: 1px solid #e5e7eb;
    padding: 0.75rem;
    text-align: left;
}

.dark .prose :deep(th),
.dark .prose :deep(td) {
    border-color: #3d2800;
}

.prose :deep(th) {
    background-color: #f9fafb;
    font-weight: 600;
}

.dark .prose :deep(th) {
    background-color: #3d2800;
    color: #f4efe5;
}
</style>
