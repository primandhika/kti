<template>
    <Head>
        <!-- Primary Meta Tags -->
        <title>{{ page.meta_title || page.title }} - BPPU IKIP Siliwangi</title>
        <meta name="title" :content="page.meta_title || page.title">
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="page.meta_keywords || defaultKeywords">
        <meta name="author" :content="page.user ? page.user.name : 'BPPU IKIP Siliwangi'">
        <link rel="canonical" :href="canonicalUrl">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="article">
        <meta property="og:url" :content="canonicalUrl">
        <meta property="og:title" :content="page.meta_title || page.title">
        <meta property="og:description" :content="metaDescription">
        <meta property="og:image" :content="ogImage">
        <meta property="og:image:secure_url" :content="ogImage">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" :content="page.meta_title || page.title">
        <meta property="og:site_name" content="BPPU IKIP Siliwangi">
        <meta property="og:locale" content="id_ID">
        <meta property="article:published_time" :content="page.created_at">
        <meta property="article:modified_time" :content="page.updated_at">
        <meta property="article:author" :content="page.user ? page.user.name : ''">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" :content="canonicalUrl">
        <meta name="twitter:title" :content="page.meta_title || page.title">
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
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
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
                        <a v-if="page.category" :href="`/${page.category}`" class="hover:text-primary-900 dark:hover:text-primary-700 transition-colors capitalize">
                            {{ page.category }}
                        </a>
                        <template v-if="page.category">
                            <svg class="w-3 h-3 text-gray-400 dark:text-[#6b4700]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-[#d6c199] truncate">{{ page.title }}</span>
                        </template>
                    </div>
                </nav>

                <!-- Article Header -->
                <header class="px-6 sm:px-8 md:px-10 pt-6 pb-4">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4 leading-tight">
                        {{ page.title }}
                    </h1>

                    <!-- Meta Info -->
                    <div v-if="page.user" class="flex items-center gap-3 text-xs sm:text-sm text-gray-600 dark:text-[#c1a366] pb-4 border-b border-gray-200 dark:border-[#3d2800]">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-gradient-to-br from-primary-900 to-primary-700 dark:from-primary-700 dark:to-primary-900 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                {{ getInitials(page.user.name) }}
                            </div>
                            <span class="font-medium">{{ page.user.name }}</span>
                        </div>
                        <span class="text-gray-300 dark:text-[#6b4700]">•</span>
                        <time :datetime="page.updated_at">{{ formatDate(page.updated_at) }}</time>
                    </div>
                </header>

                <!-- Article Content -->
                <div class="px-6 sm:px-8 md:px-10 py-6">
                    <div class="prose prose-lg max-w-none dark:prose-invert" v-html="page.content"></div>
                </div>

                <!-- Share Footer - Sticky on Mobile -->
                <footer class="sticky bottom-0 md:relative px-4 py-3 bg-white dark:bg-[#2d1e00] border-t border-gray-200 dark:border-[#3d2800] md:bg-gray-50 md:dark:bg-[#1e1400] md:px-6 md:py-4 shadow-lg md:shadow-none">
                    <div class="flex items-center justify-center md:justify-between gap-3">
                        <span class="text-xs text-gray-600 dark:text-[#b7934c] hidden md:inline">Bagikan:</span>
                        <div class="flex items-center gap-2">
                            <button @click="shareWhatsApp" class="share-btn bg-[#25D366] hover:bg-[#20BA5A]">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </button>
                            <button @click="shareFacebook" class="share-btn bg-[#1877F2] hover:bg-[#166FE5]">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </button>
                            <button @click="shareTwitter" class="share-btn bg-[#1DA1F2] hover:bg-[#1A94DA]">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </button>
                            <button @click="copyLink" class="share-btn bg-gray-600 hover:bg-gray-700 dark:bg-[#6b4700] dark:hover:bg-[#5b3d00]">
                                <svg v-if="!linkCopied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </footer>
            </article>
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
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    page: Object,
});

const { initDarkMode } = useDarkMode();
const linkCopied = ref(false);

// Computed Properties
const canonicalUrl = computed(() => {
    const baseUrl = window.location.origin;
    if (props.page.category) {
        return `${baseUrl}/${props.page.category}/${props.page.slug}`;
    }
    return `${baseUrl}/${props.page.slug}`;
});

const metaDescription = computed(() => {
    if (props.page.meta_description) {
        return props.page.meta_description;
    }
    const stripped = stripHtml(props.page.content);
    return stripped.substring(0, 160) + (stripped.length > 160 ? '...' : '');
});

const defaultKeywords = computed(() => {
    return `BPPU, IKIP Siliwangi, ${props.page.category || 'informasi'}, ${props.page.title}`;
});

const ogImage = computed(() => {
    if (props.page.featured_image) {
        return window.location.origin + '/storage/' + props.page.featured_image;
    }
    return window.location.origin + '/storage/logo-round_ijokuning.png';
});

const structuredData = computed(() => {
    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": props.page.title,
        "description": metaDescription.value,
        "image": ogImage.value,
        "datePublished": props.page.created_at,
        "dateModified": props.page.updated_at,
        "author": {
            "@type": "Person",
            "name": props.page.user ? props.page.user.name : "BPPU IKIP Siliwangi"
        },
        "publisher": {
            "@type": "Organization",
            "name": "BPPU IKIP Siliwangi",
            "logo": {
                "@type": "ImageObject",
                "url": window.location.origin + "/storage/logo-round_ijokuning.png"
            }
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": canonicalUrl.value
        }
    });
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

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
};

// Share Functions
const shareWhatsApp = () => {
    const text = `${props.page.title}\n\n${canonicalUrl.value}`;
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const shareFacebook = () => {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(canonicalUrl.value)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const shareTwitter = () => {
    const text = props.page.title;
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

/* Prose styling for page content */
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
