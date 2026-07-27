<template>
    <section id="shop" class="py-12 md:py-16 bg-white dark:bg-[#1e1400]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-8" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-[#f4efe5] mb-4">Belanja</h2>
                <div class="w-24 h-1 bg-primary-900 dark:bg-primary-700 mx-auto mb-4"></div>
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <p class="text-base text-gray-600 dark:text-[#d6c199]">Berbagai produk menarik khas IKIP Siliwangi dan kebutuhan lainnya</p>
                    <button
                        @click="randomizeItems"
                        class="inline-flex items-center gap-1 text-primary-900 dark:text-[#ccb27f] hover:text-primary-700 dark:hover:text-[#f4efe5] text-sm font-medium transition-all duration-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span>Tawarkan produk lainnya</span>
                    </button>
                </div>
            </div>

            <!-- Shop Items Grid - retail style -->
            <div
                class="shop-grid-container grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-2"
                style="transition: opacity 0.2s ease, transform 0.2s ease;"
            >
                <a
                    v-for="(item, index) in displayedItems"
                    :key="`${item.id}-${randomKey}`"
                    :href="route('belanja.index')"
                    class="group block bg-white dark:bg-[#2d1e00] rounded-lg border border-gray-100 dark:border-[#4c3300] hover:border-primary-400 dark:hover:border-[#996600] hover:shadow-md transition-all duration-300 overflow-hidden"
                    data-aos="fade-up"
                    :data-aos-delay="index * 30"
                >
                    <!-- Square image area: padding-top trick paksa kotak -->
                    <div class="relative w-full bg-white dark:bg-[#3d2800]" style="padding-top: 100%;">
                        <img
                            v-if="item.image"
                            :src="item.image"
                            :alt="item.name"
                            class="absolute inset-0 w-full h-full object-contain p-1.5 transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                        <div v-else class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-300 dark:text-[#6b4700]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Info area -->
                    <div class="px-1.5 py-1 border-t border-gray-100 dark:border-[#4c3300]">
                        <p class="text-[10px] leading-tight text-gray-800 dark:text-[#f4efe5] line-clamp-2 min-h-[2rem]">
                            {{ item.name }}
                        </p>
                        <p class="text-[11px] font-bold text-primary-900 dark:text-[#ccb27f] mt-0.5">
                            Rp {{ formatPrice(item.price) }}
                        </p>
                    </div>
                </a>
            </div>

            <!-- Info Footer -->
            <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="400">
                <a :href="route('belanja.index')" class="inline-flex items-center gap-2 bg-primary-900 dark:bg-[#996600] text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-primary-700 dark:hover:bg-[#7a5100] transition-all duration-300 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    shopItems: {
        type: Array,
        default: () => []
    }
});

const allItems = ref([...props.shopItems]);
const randomKey = ref(0);

const shuffleArray = (array) => {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
};

const getRandomItems = () => {
    const shuffled = shuffleArray(allItems.value);
    return shuffled.slice(0, 8);
};

const displayedItems = ref(getRandomItems());

const randomizeItems = () => {
    const container = document.querySelector('.shop-grid-container');
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'scale(0.97)';

        setTimeout(() => {
            displayedItems.value = getRandomItems();
            randomKey.value++;

            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 50);
        }, 200);
    } else {
        displayedItems.value = getRandomItems();
        randomKey.value++;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
