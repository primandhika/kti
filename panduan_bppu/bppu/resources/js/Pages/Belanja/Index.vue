<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Toast Notification -->
        <Toast
            :message="toastMessage"
            :type="toastType"
            @close="clearToast"
        />

        <!-- Login Modal: hanya tampil jika tidak ada lockedMeja -->
        <LoginModal :show="!isAuthenticated && !lockedMeja" />

        <!-- Nama Pemesan Modal: untuk guest dengan meja -->
        <NamaPemesanModal
            v-if="lockedMeja && !isAuthenticated"
            :show="showNamaPemesanModal"
            @close="showNamaPemesanModal = false"
            @confirm="handleNamaPemesanConfirm"
        />

        <!-- Header - Fixed at top, outside blur -->
        <header class="bg-[#f4efe5] shadow-md fixed top-0 left-0 right-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-center justify-between gap-3">
                    <!-- Left: Logo & Title -->
                    <div class="flex items-center gap-3">
                        <Link href="/" class="flex items-center gap-2 group hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </Link>
                        <img src="/logo-BPPU-flat.png" alt="Logo BPPU" class="w-12 h-12 object-contain" />
                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Belanja BPPU</h1>
                            <p class="text-xs text-gray-600">IKIP Siliwangi</p>
                        </div>
                    </div>

                    <!-- Right: Search Icon & User -->
                    <div class="flex items-center gap-2">
                        <!-- Search Toggle Button -->
                        <button
                            @click="showSearch = !showSearch"
                            class="p-2 hover:bg-white/50 rounded-lg transition-colors"
                            :class="{'bg-white': showSearch}"
                        >
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div v-if="isAuthenticated && user" class="flex-shrink-0">
                            <UserDropdown :user="user" />
                        </div>
                    </div>
                </div>

                <!-- Search Bar Expandable -->
                <transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-32"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 max-h-32"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-show="showSearch" class="overflow-hidden">
                        <div class="mt-3 space-y-2">
                            <input
                                ref="searchInput"
                                type="text"
                                v-model="quickSearch"
                                @input="handleQuickSearch"
                                placeholder="Cari menu atau varian..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent bg-white"
                            />
                            <!-- Search Suggestions -->
                            <div v-if="searchSuggestions.length > 0 && quickSearch" class="text-xs text-gray-600 px-1">
                                <span>Mungkin maksud kamu: </span>
                                <button
                                    v-for="(suggestion, idx) in searchSuggestions"
                                    :key="idx"
                                    @click="applySuggestion(suggestion.text)"
                                    class="text-primary-900 hover:text-primary-800 font-semibold underline"
                                >
                                    "{{ suggestion.text }}"{{ idx < searchSuggestions.length - 1 ? ', ' : '' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </header>

        <!-- Spacer untuk offset header fixed -->
        <div class="h-[68px]"></div>

        <!-- Sub Navbar Filter - sticky tepat di bawah header -->
        <div class="sticky top-[68px] z-40">
            <FilterSubNavbar
                :filters="filters"
                :sub-kategories="subKategories"
                :meja-qr-token="mejaQrToken"
            />
        </div>

        <!-- Info meja jika akses via QR -->
        <div v-if="lockedMeja" class="bg-[#f4efe5] border-b border-[#ccb27f]/40 sticky top-[68px] z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center gap-2 text-xs text-[#7a5100]">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Pesanan akan diantar ke:</span>
                <span v-if="lockedMeja.lokasi" class="font-semibold">{{ lockedMeja.lokasi }} &mdash;</span>
                <span class="font-bold">{{ lockedMeja.nama || lockedMeja.kode_meja }}</span>
                <span v-if="lockedMeja.nomor" class="text-[#996600]">No. {{ lockedMeja.nomor }}</span>
            </div>
        </div>

        <!-- Content Blur if not authenticated and no meja -->
        <div :class="{'filter blur-sm pointer-events-none': !isAuthenticated && !lockedMeja}">

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Menu Grid -->
                <main>
                    <!-- Featured Menu Section (Carousel) -->
                    <div v-if="featuredMenus && featuredMenus.length > 0 && !filters.search && !filters.sub_kategori" class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                                <svg class="w-7 h-7 text-primary-900" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Menu Spesial Hari Ini
                            </h2>
                            <!-- Navigation Arrows (Desktop) -->
                            <div class="hidden md:flex items-center gap-2">
                                <button
                                    @click="scrollFeatured('left')"
                                    class="p-2 rounded-full bg-white hover:bg-gray-100 shadow-md transition-colors"
                                >
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button
                                    @click="scrollFeatured('right')"
                                    class="p-2 rounded-full bg-white hover:bg-gray-100 shadow-md transition-colors"
                                >
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative">
                            <div
                                ref="carouselContainer"
                                class="flex gap-4 md:gap-6 overflow-x-auto scroll-smooth scrollbar-hide pb-4"
                            >
                                <div
                                    v-for="menu in featuredMenus"
                                    :key="menu.id"
                                    class="flex-shrink-0 w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]"
                                >
                                    <FeaturedMenuCard
                                        :menu="menu"
                                        @order="handleOrder"
                                    />
                                </div>
                            </div>

                            <!-- Scroll Indicators -->
                            <div class="flex justify-center gap-2 mt-4">
                                <div
                                    v-for="(menu, index) in featuredMenus"
                                    :key="index"
                                    class="h-2 rounded-full transition-all cursor-pointer"
                                    :class="currentCarouselPage === index ? 'w-8 bg-primary-900' : 'w-2 bg-gray-300'"
                                    @click="scrollToCard(index)"
                                ></div>
                            </div>
                        </div>
                    </div>


                    <!-- Results Info -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-600">
                                Menampilkan <span class="font-semibold">{{ menus?.data?.length || 0 }}</span> dari
                                <span class="font-semibold">{{ menus?.total || 0 }}</span> menu
                            </p>
                        </div>
                        <!-- Search Info & Suggestions -->
                        <div v-if="filters.search" class="mt-2">
                            <p class="text-sm text-gray-600">
                                Hasil pencarian untuk: <span class="font-semibold">"{{ filters.search }}"</span>
                            </p>
                            <!-- Show suggestions if few or no results -->
                            <div v-if="menus?.total < 3 && searchSuggestions.length > 0" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-gray-700">
                                    <svg class="w-4 h-4 inline text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Mungkin maksud kamu:
                                    <button
                                        v-for="(suggestion, idx) in searchSuggestions.slice(0, 3)"
                                        :key="idx"
                                        @click="applySuggestion(suggestion.text)"
                                        class="ml-2 text-primary-900 hover:text-primary-800 font-semibold underline"
                                    >
                                        "{{ suggestion.text }}"{{ idx < Math.min(searchSuggestions.length, 3) - 1 ? ',' : '' }}
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!menus?.data || menus.data.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h.01M12 12h.01M12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Menu Tidak Ditemukan</h3>
                        <p class="text-gray-600 mb-4">Coba ubah filter atau kata kunci pencarian Anda</p>
                    </div>

                    <!-- Menu Grid -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                        <MenuCard
                            v-for="menu in filteredMenus"
                            :key="menu.id"
                            :menu="menu"
                            @order="handleOrder"
                        />
                    </div>

                    <!-- Pagination -->
                    <div v-if="menus?.data && menus.data.length > 0 && menus.last_page > 1" class="mt-8">
                        <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                            <!-- Mobile Pagination -->
                            <div class="md:hidden">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <!-- Previous Button -->
                                    <Link
                                        :href="addMejaToUrl(menus.prev_page_url)"
                                        :class="{'pointer-events-none opacity-50': !menus.prev_page_url}"
                                        class="px-3 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-800 transition-colors flex items-center gap-1 text-sm"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        <span class="hidden xs:inline">Prev</span>
                                    </Link>

                                    <!-- Page Info -->
                                    <div class="text-sm text-gray-600 font-medium">
                                        {{ menus.current_page }} / {{ menus.last_page }}
                                    </div>

                                    <!-- Next Button -->
                                    <Link
                                        :href="addMejaToUrl(menus.next_page_url)"
                                        :class="{'pointer-events-none opacity-50': !menus.next_page_url}"
                                        class="px-3 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-800 transition-colors flex items-center gap-1 text-sm"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        <span class="hidden xs:inline">Next</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </Link>
                                </div>

                                <!-- Page Numbers - Scrollable -->
                                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
                                    <template v-for="page in paginationPages" :key="page">
                                        <span v-if="page === '...'" class="px-2 py-1 text-gray-500 text-sm">...</span>
                                        <Link
                                            v-else
                                            :href="`/belanja/self-order?page=${page}&${queryString}`"
                                            :class="page === menus.current_page
                                                ? 'bg-primary-900 text-white'
                                                : 'bg-gray-100 text-gray-700 border border-gray-300'"
                                            class="px-3 py-1.5 rounded-lg font-semibold transition-colors text-sm flex-shrink-0"
                                            preserve-state
                                            preserve-scroll
                                        >
                                            {{ page }}
                                        </Link>
                                    </template>
                                </div>
                            </div>

                            <!-- Desktop Pagination -->
                            <div class="hidden md:block">
                                <div class="flex items-center justify-between">
                                    <!-- Previous Button -->
                                    <Link
                                        :href="addMejaToUrl(menus.prev_page_url)"
                                        :class="{'pointer-events-none opacity-50': !menus.prev_page_url}"
                                        class="px-4 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-800 transition-colors duration-300 flex items-center gap-2"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        <span>Sebelumnya</span>
                                    </Link>

                                    <!-- Page Numbers -->
                                    <div class="flex items-center gap-2">
                                        <template v-for="page in paginationPages" :key="page">
                                            <span v-if="page === '...'" class="px-3 py-2 text-gray-500">...</span>
                                            <Link
                                                v-else
                                                :href="`/belanja/self-order?page=${page}&${queryString}`"
                                                :class="page === menus.current_page
                                                    ? 'bg-primary-900 text-white'
                                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
                                                class="px-4 py-2 rounded-lg font-semibold transition-colors duration-300"
                                                preserve-state
                                                preserve-scroll
                                            >
                                                {{ page }}
                                            </Link>
                                        </template>
                                    </div>

                                    <!-- Next Button -->
                                    <Link
                                        :href="addMejaToUrl(menus.next_page_url)"
                                        :class="{'pointer-events-none opacity-50': !menus.next_page_url}"
                                        class="px-4 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-800 transition-colors duration-300 flex items-center gap-2"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        <span>Selanjutnya</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </Link>
                                </div>

                                <!-- Page Info -->
                                <div class="mt-4 text-center text-sm text-gray-600">
                                    Halaman {{ menus.current_page }} dari {{ menus.last_page }}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
        </div>

        <!-- Footer Info -->
        <footer class="bg-white border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center">
                    <p class="text-gray-600 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-primary-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Buka setiap hari Senin - Sabtu, 07:00 - 15:00 WIB
                    </p>
                </div>
            </div>
        </footer>
        </div>

        <!-- Cart Button -->
        <CartButton
            v-show="!showVarianModal && (isAuthenticated || lockedMeja)"
            :total-items="totalItems"
            :cart-items="cartItems"
            @click="toggleCart"
        />

        <!-- Cart Modal -->
        <CartModal
            :show="isCartOpen"
            :cart-items="cartItems"
            :total-items="totalItems"
            :total-price="totalPrice"
            :is-empty="isEmpty"
            :is-checking-out="isCheckingOut"
            :catatan="catatan"
            :minimal-order="selfOrderConfig?.minimal_order ?? 0"
            :biaya-layanan-aktif="selfOrderConfig?.biaya_layanan_aktif ?? false"
            :biaya-layanan="selfOrderConfig?.biaya_layanan ?? 0"
            @close="closeCart"
            @remove="removeFromCart"
            @increase="handleIncreaseItem"
            @decrease="handleDecreaseItem"
            @update:catatan="catatan = $event"
            @checkout="handleCheckout"
            @clear="handleClearCart"
        />

        <!-- Varian Modal -->
        <VarianModal
            :show="showVarianModal"
            :menu="selectedMenuForVarian"
            @close="showVarianModal = false"
            @add="handleAddToCartFromVarian"
        />
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import MenuCard from '@/Components/SelfOrder/MenuCard.vue';
import FeaturedMenuCard from '@/Components/SelfOrder/FeaturedMenuCard.vue';
import FilterSubNavbar from '@/Components/Belanja/FilterSubNavbar.vue';
import LoginModal from '@/Components/SelfOrder/LoginModal.vue';
import NamaPemesanModal from '@/Components/SelfOrder/NamaPemesanModal.vue';
import UserDropdown from '@/Components/SelfOrder/UserDropdown.vue';
import Toast from '@/Components/SelfOrder/Toast.vue';
import CartButton from '@/Components/SelfOrder/CartButton.vue';
import CartModal from '@/Components/SelfOrder/CartModal.vue';
import VarianModal from '@/Components/SelfOrder/VarianModal.vue';
import { useCartSelfOrder } from '@/composables/useCartSelfOrder';
import { findBestMatches, extractSearchableTerms } from '@/utils/fuzzySearch';

const props = defineProps({
    menus: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    subKategories: {
        type: Array,
        default: () => []
    },
    workUnits: {
        type: Array,
        default: () => []
    },
    featuredMenus: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    isAuthenticated: {
        type: Boolean,
        default: false
    },
    user: {
        type: Object,
        default: null
    },
    lockedMeja: {
        type: Object,
        default: null
    },
    mejaQrToken: {
        type: String,
        default: null
    },
    selfOrderConfig: {
        type: Object,
        default: () => ({ minimal_order: 20000, biaya_layanan_aktif: false, biaya_layanan: 0 })
    },
});

const showMobileFilter = ref(false);
const showSearch = ref(false);
const showNamaPemesanModal = ref(false);
const namaPemesan = ref('');
const searchInput = ref(null);
const quickSearch = ref(props.filters.search || '');
const carouselContainer = ref(null);
const currentCarouselPage = ref(0);
const searchSuggestions = ref([]);
const allSearchableTerms = ref([]);
let searchTimeout = null;

const {
    cartItems,
    isCartOpen,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    toggleCart,
    openCart,
    closeCart,
    totalItems,
    totalPrice,
    isEmpty,
} = useCartSelfOrder('bppu-belanja-cart');

const showVarianModal = ref(false);
const selectedMenuForVarian = ref(null);
const showOutOfStock = ref(true);

// Toast notification
const page = usePage();
const toastMessage = ref('');
const toastType = ref('success');

// Watch for flash messages
watch(() => page.props.flash?.success, (newValue) => {
    if (newValue) {
        toastMessage.value = newValue;
        toastType.value = 'success';
    }
});

watch(() => page.props.flash?.error, (newValue) => {
    if (newValue) {
        toastMessage.value = newValue;
        toastType.value = 'error';
    }
});

const clearToast = () => {
    toastMessage.value = '';
};

// Auto focus search input when opened
watch(showSearch, (newVal) => {
    if (newVal) {
        nextTick(() => {
            searchInput.value?.focus();
        });
    }
});

// Initialize searchable terms and carousel
onMounted(() => {
    // Build searchable terms from current menus, featured menus, and sub categories
    const allMenus = [
        ...(props.menus?.data || []),
        ...(props.featuredMenus || [])
    ];
    allSearchableTerms.value = extractSearchableTerms(allMenus);

    // Add sub categories to searchable terms
    if (props.subKategories) {
        allSearchableTerms.value.push(...props.subKategories);
    }

    // Track carousel scroll for indicators
    if (carouselContainer.value) {
        carouselContainer.value.addEventListener('scroll', () => {
            const container = carouselContainer.value;
            if (!container || container.children.length === 0) return;

            const cardWidth = container.children[0].offsetWidth;
            const gap = 24;
            const scrollPosition = container.scrollLeft;
            const index = Math.round(scrollPosition / (cardWidth + gap));

            currentCarouselPage.value = Math.max(0, Math.min(index, props.featuredMenus.length - 1));
        });
    }

    // Update suggestions if there's an active search
    if (props.filters.search) {
        updateSearchSuggestions(props.filters.search);
    }
});

const updateSearchSuggestions = (searchTerm) => {
    if (!searchTerm || searchTerm.length < 3) {
        searchSuggestions.value = [];
        return;
    }

    // Find best matches from all searchable terms
    const matches = findBestMatches(searchTerm, allSearchableTerms.value, 0.5, 5);
    searchSuggestions.value = matches;
};

const handleQuickSearch = () => {
    clearTimeout(searchTimeout);

    // Update suggestions immediately for better UX
    updateSearchSuggestions(quickSearch.value);

    searchTimeout = setTimeout(() => {
        const params = { ...props.filters, search: quickSearch.value };
        if (props.mejaQrToken) params.meja = props.mejaQrToken;
        router.get('/belanja/self-order', params, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 500);
};

const applySuggestion = (suggestionText) => {
    quickSearch.value = suggestionText;
    searchSuggestions.value = [];
    const params = { ...props.filters, search: suggestionText };
    if (props.mejaQrToken) params.meja = props.mejaQrToken;
    router.get('/belanja/self-order', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleOrder = (menu) => {
    if (menu.varians && menu.varians.length > 0) {
        selectedMenuForVarian.value = menu;
        showVarianModal.value = true;
    } else {
        addToCart(menu, 1, null);
        toastMessage.value = `${menu.name} ditambahkan ke keranjang`;
        toastType.value = 'success';
    }
};

const handleAddToCartFromVarian = (menu, quantity, varian) => {
    addToCart(menu, quantity, varian);
    toastMessage.value = `${menu.name} - ${varian.nama_varian} ditambahkan ke keranjang`;
    toastType.value = 'success';
};

const handleIncreaseItem = (index) => {
    updateQuantity(index, cartItems.value[index].quantity + 1);
};

const handleDecreaseItem = (index) => {
    updateQuantity(index, cartItems.value[index].quantity - 1);
};

const handleClearCart = () => {
    if (confirm('Yakin ingin mengosongkan keranjang?')) {
        clearCart();
        catatan.value = '';
        toastMessage.value = 'Keranjang telah dikosongkan';
        toastType.value = 'success';
    }
};

const isCheckingOut = ref(false);
const catatan = ref('');
const currentCsrfToken = ref(document.querySelector('meta[name="csrf-token"]')?.content || '');

// Session keep-alive ping setiap 5 menit
let pingInterval = null;

const sessionPing = async () => {
    try {
        const res = await fetch('/api/belanja/ping', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });

        if (res.ok) {
            const data = await res.json();
            if (data.csrf_token) {
                currentCsrfToken.value = data.csrf_token;
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.csrf_token);
                }
            }
        }
    } catch (error) {
        console.warn('Session ping failed:', error);
    }
};

onMounted(() => {
    // Ping session setiap 5 menit untuk keep session alive
    pingInterval = setInterval(sessionPing, 5 * 60 * 1000);

    // Cleanup on unmount
    return () => {
        if (pingInterval) {
            clearInterval(pingInterval);
        }
    };
});

const handleNamaPemesanConfirm = (nama) => {
    namaPemesan.value = nama;
    showNamaPemesanModal.value = false;
    doCheckout();
};

const handleCheckout = async () => {
    if (isCheckingOut.value || cartItems.value.length === 0) return;

    // Guest dengan meja: minta nama dulu jika belum ada
    if (!props.isAuthenticated && props.lockedMeja) {
        if (!namaPemesan.value) {
            showNamaPemesanModal.value = true;
            return;
        }
    }

    doCheckout();
};

const doCheckout = async () => {
    if (isCheckingOut.value || cartItems.value.length === 0) return;

    // Validasi minimal order di frontend
    const minOrder = props.selfOrderConfig?.minimal_order ?? 0;
    if (minOrder > 0 && totalPrice.value < minOrder) {
        toastMessage.value = `Minimal order Rp ${minOrder.toLocaleString('id-ID')}`;
        toastType.value = 'error';
        return;
    }

    isCheckingOut.value = true;
    try {
        await sessionPing();

        const payload = {
            items: cartItems.value.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity,
                varian: item.varian ?? null,
            })),
            catatan: catatan.value,
        };

        if (!props.isAuthenticated && props.mejaQrToken) {
            payload.meja_qr_token = props.mejaQrToken;
            payload.nama_pemesan = namaPemesan.value;
        }

        const res = await fetch('/belanja/self-order/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': currentCsrfToken.value,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            toastMessage.value = data.message || 'Gagal melakukan checkout';
            toastType.value = 'error';
            return;
        }

        clearCart();
        catatan.value = '';
        window.location.href = `/belanja/self-order/invoice/${data.pesanan_id}`;
    } catch {
        toastMessage.value = 'Terjadi kesalahan, coba lagi';
        toastType.value = 'error';
    } finally {
        isCheckingOut.value = false;
    }
};

const scrollFeatured = (direction) => {
    if (!carouselContainer.value) return;

    const container = carouselContainer.value;
    const cards = container.children;
    if (cards.length === 0) return;

    const cardWidth = cards[0].offsetWidth;
    const gap = 24; // 6 * 4px (gap-6)
    const scrollAmount = cardWidth + gap;

    if (direction === 'left') {
        container.scrollLeft -= scrollAmount;
    } else {
        container.scrollLeft += scrollAmount;
    }
};

const scrollToCard = (index) => {
    if (!carouselContainer.value) return;

    const container = carouselContainer.value;
    const cards = container.children;
    if (cards.length === 0 || !cards[index]) return;

    const card = cards[index];
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
};

const queryString = computed(() => {
    const params = new URLSearchParams();
    Object.keys(props.filters).forEach(key => {
        if (props.filters[key]) {
            params.append(key, props.filters[key]);
        }
    });
    if (props.mejaQrToken) params.append('meja', props.mejaQrToken);
    return params.toString();
});

const addMejaToUrl = (url) => {
    if (!url || !props.mejaQrToken) return url;
    const separator = url.includes('?') ? '&' : '?';
    return `${url}${separator}meja=${props.mejaQrToken}`;
};

const filteredMenus = computed(() => {
    if (showOutOfStock.value) {
        return props.menus?.data || [];
    }
    return (props.menus?.data || []).filter(menu => menu.stok > 0);
});

const paginationPages = computed(() => {
    if (!props.menus) return [];

    const current = props.menus.current_page || 1;
    const last = props.menus.last_page || 1;
    const pages = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 3) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        } else if (current >= last - 2) {
            pages.push(1);
            pages.push('...');
            for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        }
    }

    return pages;
});
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out forwards;
    opacity: 0;
}
</style>
