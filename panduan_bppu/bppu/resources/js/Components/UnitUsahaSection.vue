<template>
    <section class="py-8 bg-white dark:bg-[#1e1400]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-[#f4efe5] mb-2">Unit Usaha</h2>
                <div class="w-20 h-1 bg-primary-900 dark:bg-primary-700 mx-auto"></div>
            </div>

            <!-- Carousel -->
            <div v-if="unitUsaha && unitUsaha.length > 0" class="relative">
                <swiper
                    :modules="modules"
                    :slides-per-view="1"
                    :space-between="20"
                    :loop="true"
                    :autoplay="{
                        delay: 4000,
                        disableOnInteraction: false,
                    }"
                    :pagination="{ clickable: true }"
                    :navigation="false"
                    :breakpoints="{
                        640: { slidesPerView: 2, spaceBetween: 20 },
                        1024: { slidesPerView: 3, spaceBetween: 30 },
                    }"
                    class="unit-usaha-swiper"
                >
                    <swiper-slide v-for="unit in unitUsaha" :key="unit.id">
                        <div class="relative overflow-hidden rounded-xl shadow-md hover:shadow-2xl transition-all duration-500 h-64 md:h-72 group cursor-pointer">
                            <!-- Background Image -->
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-[#4c3300] dark:to-[#3d2800]">
                                <div v-if="unit.logo" class="w-full h-full">
                                    <img
                                        :src="`/storage/${unit.logo}`"
                                        :alt="unit.name"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    >
                                </div>
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-primary-700 dark:text-[#ccb27f] opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

                            <!-- Content Overlay -->
                            <div class="absolute inset-0 flex flex-col justify-end p-5 md:p-6">
                                <h3 class="text-xl md:text-2xl font-bold text-white mb-2 drop-shadow-lg line-clamp-1">
                                    {{ unit.name }}
                                </h3>

                                <div class="space-y-1.5 text-sm">
                                    <div v-if="unit.location" class="flex items-start text-white/90">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="line-clamp-1 drop-shadow">{{ unit.location }}</span>
                                    </div>

                                    <div v-if="unit.operating_hours" class="flex items-start text-white/90">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="line-clamp-1 drop-shadow">{{ unit.operating_hours }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- WhatsApp Button -->
                            <a
                                v-if="unit.contact_phone"
                                :href="getWhatsAppLink(unit.contact_phone)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="absolute bottom-5 right-5 md:bottom-6 md:right-6 w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-white flex items-center justify-center hover:bg-white/20 transition-all duration-300 group/wa z-10"
                                @click.stop
                            >
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white group-hover/wa:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                        </div>
                    </swiper-slide>
                </swiper>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-[#6b4700] mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-gray-600 dark:text-[#d6c199]">Belum ada unit usaha yang tersedia</p>
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
    unitUsaha: {
        type: Array,
        default: () => []
    }
});

const modules = [Autoplay, Pagination];

const getWhatsAppLink = (phone) => {
    if (!phone) return '#';

    // Remove all non-numeric characters
    let cleanPhone = phone.replace(/\D/g, '');

    // If starts with 0, replace with 62 (Indonesia country code)
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }

    // If doesn't start with 62, add it
    if (!cleanPhone.startsWith('62')) {
        cleanPhone = '62' + cleanPhone;
    }

    return `https://wa.me/${cleanPhone}`;
};
</script>

<style scoped>
.unit-usaha-swiper {
    padding-bottom: 50px;
}

.unit-usaha-swiper :deep(.swiper-pagination-bullet) {
    width: 10px;
    height: 10px;
    background: #996600;
    opacity: 0.5;
    transition: all 0.3s;
}

.unit-usaha-swiper :deep(.swiper-pagination-bullet-active) {
    opacity: 1;
    width: 28px;
    border-radius: 5px;
}

.dark .unit-usaha-swiper :deep(.swiper-pagination-bullet) {
    background: #ccb27f;
}
</style>
