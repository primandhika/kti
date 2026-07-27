<template>
    <section id="menu" class="py-12 md:py-16 bg-[#f4efe5] dark:bg-[#2d1e00]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4">Menu Kantin</h2>
                <div class="w-24 h-1 bg-primary-900 dark:bg-primary-700 mx-auto mb-4"></div>
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <p class="text-lg text-gray-600 dark:text-[#d6c199]">Hidangan lezat yang siap memanjakan lidah Anda</p>
                    <button
                        @click="randomizeMenus"
                        class="inline-flex items-center gap-1 text-primary-900 dark:text-[#ccb27f] hover:text-primary-700 dark:hover:text-[#f4efe5] text-sm font-medium transition-all duration-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span>Tawarkan saya menu lainnya</span>
                    </button>
                </div>
            </div>

            <!-- Menu Grid with Bento Box Layout -->
            <div class="menu-grid-container">
                <!-- Featured Large Card -->
                <div
                    v-if="featuredMenu"
                    data-aos="fade-up"
                    data-aos-delay="0"
                >
                    <div class="h-full min-h-[400px] lg:min-h-[600px]">
                        <FeaturedMenuCard :menu="featuredMenu" />
                    </div>
                </div>

                <!-- Regular Cards Grid -->
                <div
                    v-for="(menu, index) in regularMenus"
                    :key="`${menu.id}-${randomKey}`"
                    class="group flex flex-col h-full rounded-xl shadow-md overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 menu-card"
                    data-aos="fade-up"
                    :data-aos-delay="(index + 1) * 50"
                >
                    <!-- Image Container with Overlay -->
                    <div class="relative h-32 lg:h-48 overflow-hidden bg-primary-900 dark:bg-[#996600]">
                        <img
                            v-if="menu.image"
                            :src="menu.image"
                            :alt="menu.name"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400 dark:text-[#6b4700]">
                            <svg class="w-12 h-12 lg:w-16 lg:h-16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

                        <!-- Badge Container -->
                        <div v-if="menu.badges && menu.badges.length > 0" class="absolute top-1 right-1 lg:top-2 lg:right-2 flex flex-col gap-0.5 lg:gap-1">
                            <div
                                v-for="(badge, badgeIndex) in menu.badges"
                                :key="badgeIndex"
                                :class="getBadgeClasses(badge.color)"
                                class="px-1.5 py-0.5 lg:px-2 lg:py-1 rounded-full text-[10px] lg:text-xs font-bold backdrop-blur-md shadow-lg flex items-center gap-0.5 lg:gap-1 animate-bounce-subtle"
                            >
                                <component :is="getBadgeIcon(badge.icon)" class="w-2.5 h-2.5 lg:w-3 lg:h-3" />
                                <span>{{ badge.text }}</span>
                            </div>
                        </div>

                        <!-- Menu Name Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-2 lg:p-3">
                            <h3 class="text-sm lg:text-base font-bold text-white mb-0.5 drop-shadow-lg line-clamp-1">{{ menu.name }}</h3>
                            <p class="text-[10px] lg:text-xs text-gray-200 line-clamp-1 drop-shadow-md">{{ menu.description }}</p>
                        </div>
                    </div>

                    <!-- Price Section -->
                    <div class="bg-primary-900 dark:bg-[#996600] p-2 lg:p-4 flex-1 flex flex-col justify-end">
                        <div class="flex items-center justify-between gap-2 lg:gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lg:text-lg font-bold text-white truncate">
                                    Rp {{ formatPrice(menu.price) }}
                                </p>
                                <p v-if="menu.varians && menu.varians.length > 0" class="text-[10px] lg:text-xs text-primary-100 dark:text-[#d6c199]">
                                    {{ menu.varians.length }} varian
                                </p>
                            </div>
                            <Link
                                href="/kantin/self-order"
                                class="bg-white dark:bg-[#f4efe5] text-primary-900 dark:text-[#3d2800] px-2 py-1.5 lg:px-4 lg:py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-primary-50 dark:hover:bg-[#eae0cc] transition-all duration-300 transform hover:scale-105 shadow-md inline-block text-center whitespace-nowrap flex-shrink-0"
                            >
                                Pesan
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Footer -->
            <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="500">
                <p class="text-gray-600 dark:text-[#d6c199]">
                    <span class="inline-flex items-center gap-2 bg-white dark:bg-[#3d2800] px-4 py-2 rounded-full shadow-sm">
                        <svg class="w-5 h-5 text-primary-900 dark:text-[#ccb27f]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-900 dark:text-[#f4efe5]">Buka setiap hari Senin - Sabtu, 07:00 - 15:00 WIB</span>
                    </span>
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import FeaturedMenuCard from '@/Components/MenuKantin/FeaturedMenuCard.vue';

// Icons as inline SVG components
const StarIcon = {
    template: `
        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
    `
};

const FireIcon = {
    template: `
        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
        </svg>
    `
};

const CalendarIcon = {
    template: `
        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
        </svg>
    `
};

const props = defineProps({
    menus: {
        type: Array,
        default: () => []
    }
});

const allMenus = ref([...props.menus]);
const displayedMenus = ref([]);
const randomKey = ref(0);

const shuffleArray = (array) => {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
};

const getRandomMenus = () => {
    const shuffled = shuffleArray(allMenus.value);
    return shuffled.slice(0, 5);
};

const randomizeMenus = () => {
    const container = document.querySelector('.menu-grid-container');
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'scale(0.95)';

        setTimeout(() => {
            displayedMenus.value = getRandomMenus();
            randomKey.value++;

            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 50);
        }, 200);
    } else {
        displayedMenus.value = getRandomMenus();
        randomKey.value++;
    }
};

// Initialize with 5 random menus (1 featured + 4 regular)
displayedMenus.value = getRandomMenus();

// Featured menu: first menu
const featuredMenu = computed(() => {
    return displayedMenus.value[0] || null;
});

// Regular menus: remaining 4 menus
const regularMenus = computed(() => {
    return displayedMenus.value.slice(1);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const getBadgeClasses = (color) => {
    const colorMap = {
        blue: 'bg-blue-500/90 text-white border border-blue-300/50',
        green: 'bg-green-500/90 text-white border border-green-300/50',
        purple: 'bg-purple-500/90 text-white border border-purple-300/50',
        red: 'bg-red-500/90 text-white border border-red-300/50',
        yellow: 'bg-yellow-500/90 text-white border border-yellow-300/50',
    };
    return colorMap[color] || 'bg-gray-500/90 text-white border border-gray-300/50';
};

const getBadgeIcon = (iconName) => {
    const iconMap = {
        star: StarIcon,
        fire: FireIcon,
        calendar: CalendarIcon,
    };
    return iconMap[iconName] || StarIcon;
};
</script>

<style scoped>
@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-3px);
    }
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.menu-grid-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

/* Mobile: Featured full width, regular cards 2 columns */
.menu-grid-container > div:first-child {
    grid-column: 1 / -1;
}

/* Desktop: 4 column grid with featured spanning 2x2 */
@media (min-width: 1024px) {
    .menu-grid-container {
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: minmax(280px, auto);
    }

    .menu-grid-container > div:first-child {
        grid-column: span 2;
        grid-row: span 2;
    }
}
</style>
