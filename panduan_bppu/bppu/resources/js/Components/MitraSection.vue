<template>
    <section class="py-8 bg-gradient-to-b from-gray-50 to-white dark:from-[#2d1e00] dark:to-[#1e1400]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6" data-aos="fade-up">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-[#f4efe5] mb-2">Mitra Usaha</h2>
                <div class="w-16 h-0.5 bg-primary-900 dark:bg-primary-700 mx-auto"></div>
            </div>

            <!-- Carousel -->
            <div v-if="mitraUsaha && mitraUsaha.length > 0" class="relative">
                <swiper
                    :modules="modules"
                    :slides-per-view="2"
                    :space-between="15"
                    :loop="mitraUsaha.length > 4"
                    :autoplay="{
                        delay: 3000,
                        disableOnInteraction: false,
                    }"
                    :pagination="{ clickable: true }"
                    :navigation="false"
                    :breakpoints="{
                        640: { slidesPerView: 3, spaceBetween: 20 },
                        768: { slidesPerView: 4, spaceBetween: 20 },
                        1024: { slidesPerView: 5, spaceBetween: 25 },
                    }"
                    class="mitra-swiper"
                >
                    <swiper-slide v-for="mitra in mitraUsaha" :key="mitra.id">
                        <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-sm hover:shadow-md transition-all duration-300 p-3 flex flex-col items-center justify-center h-36 group">
                            <!-- Logo -->
                            <div class="w-20 h-20 mb-2 flex items-center justify-center flex-shrink-0">
                                <img
                                    :src="`/${mitra.logo}`"
                                    :alt="mitra.nama"
                                    class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-110"
                                >
                            </div>
                            <!-- Nama Mitra -->
                            <p class="text-xs text-center text-gray-700 dark:text-[#d6c199] line-clamp-2 font-medium w-full px-1">
                                {{ mitra.nama }}
                            </p>
                        </div>
                    </swiper-slide>
                </swiper>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-6">
                <svg class="w-10 h-10 mx-auto text-gray-400 dark:text-[#6b4700] mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
                <p class="text-xs text-gray-600 dark:text-[#d6c199]">Belum ada mitra usaha</p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

defineProps({
    mitraUsaha: {
        type: Array,
        default: () => []
    }
});

const modules = [Autoplay, Pagination];
</script>

<style scoped>
.mitra-swiper {
    padding-bottom: 40px;
}

.mitra-swiper :deep(.swiper-pagination-bullet) {
    width: 8px;
    height: 8px;
    background: #996600;
    opacity: 0.4;
    transition: all 0.3s;
}

.mitra-swiper :deep(.swiper-pagination-bullet-active) {
    opacity: 1;
    width: 20px;
    border-radius: 4px;
}

.dark .mitra-swiper :deep(.swiper-pagination-bullet) {
    background: #ccb27f;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
