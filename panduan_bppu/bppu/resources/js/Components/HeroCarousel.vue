<template>
    <section id="home" class="relative h-[70vh] min-h-[500px]">
        <!-- Swiper Carousel -->
        <swiper
            :modules="modules"
            :slides-per-view="1"
            :space-between="0"
            :loop="false"
            :autoplay="{
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            }"
            :pagination="{
                clickable: true,
                dynamicBullets: false,
            }"
            :navigation="true"
            :speed="800"
            :rewind="true"
            class="h-full"
        >
            <!-- Slide 1 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110407.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover z-0">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-black/60 z-10"></div>
                    <div class="relative h-full flex items-center justify-center z-20">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <img src="/logo-white.png" alt="IKIP Siliwangi" class="h-16 sm:h-20 md:h-24 mx-auto mb-4 animate-fade-in">
                            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4 animate-slide-up">
                                Badan Pengelola dan Pengembangan Usaha
                            </h1>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 mb-4 sm:mb-6 animate-slide-up-delay">
                                IKIP Siliwangi Bandung
                            </p>
                            <a href="#content" class="inline-block bg-white text-primary-900 px-6 sm:px-8 py-2 sm:py-3 rounded-full font-semibold hover:bg-primary-50 transition-all duration-300 transform hover:scale-105 animate-fade-in-delay">
                                Jelajahi
                            </a>
                        </div>
                    </div>
                </div>
            </swiper-slide>

            <!-- Slide 2 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110913.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-black/60 to-black/70"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4">
                                Komitmen pada Kualitas
                            </h2>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                                Mengembangkan berbagai unit usaha yang berkelanjutan untuk kemajuan institusi
                            </p>
                        </div>
                    </div>
                </div>
            </swiper-slide>

            <!-- Slide 3 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110554.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/60 via-primary-900/60 to-black/70"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4">
                                Unggul dan Berdaya Saing
                            </h2>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                                Mewujudkan pendidikan tinggi yang bermutu, relevan, dan berdaya saing global
                            </p>
                        </div>
                    </div>
                </div>
            </swiper-slide>

            <!-- Slide 4 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110918.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/60 via-black/50 to-primary-900/70"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4">
                                Bersama Menuju Akreditasi Unggul
                            </h2>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                                Meningkatkan kualitas institusi melalui standar mutu yang tinggi
                            </p>
                        </div>
                    </div>
                </div>
            </swiper-slide>

            <!-- Slide 5 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110616.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-black/60 to-black/70"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4">
                                Inovasi dan Pengembangan
                            </h2>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                                Menghadirkan solusi inovatif dalam pengelolaan usaha
                            </p>
                        </div>
                    </div>
                </div>
            </swiper-slide>

            <!-- Slide 6 -->
            <swiper-slide>
                <div class="relative h-full">
                    <img src="/storage/galeri/20260108_110750.jpg" alt="BPPU IKIP Siliwangi" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-primary-900/50 to-black/60"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-3 sm:mb-4">
                                Profesionalisme dan Integritas
                            </h2>
                            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                                Mengelola setiap unit usaha dengan profesional dan berintegritas
                            </p>
                        </div>
                    </div>
                </div>
            </swiper-slide>
        </swiper>
    </section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const modules = [Navigation, Pagination, Autoplay];
</script>

<style scoped>
/* Arrow buttons */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    color: white;
    background: rgba(153, 102, 0, 0.5);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    z-index: 10;
}

:deep(.swiper-button-next):hover,
:deep(.swiper-button-prev):hover {
    background: rgba(153, 102, 0, 0.8);
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
    font-size: 20px;
}

/* Mobile arrow size */
@media (max-width: 768px) {
    :deep(.swiper-button-next),
    :deep(.swiper-button-prev) {
        width: 28px;
        height: 28px;
    }

    :deep(.swiper-button-next::after),
    :deep(.swiper-button-prev::after) {
        font-size: 12px;
    }
}

/* Pagination */
:deep(.swiper-pagination) {
    bottom: 20px !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    z-index: 10 !important;
}

/* Hide pagination dots on mobile */
@media (max-width: 768px) {
    :deep(.swiper-pagination) {
        display: none !important;
    }
}

:deep(.swiper-pagination-bullet) {
    background: white;
    opacity: 0.5;
    width: 12px;
    height: 12px;
}

:deep(.swiper-pagination-bullet-active) {
    background: #996600;
    opacity: 1;
}

/* Animations */
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
    animation: fadeIn 1s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.8s ease-out 0.2s both;
}

.animate-slide-up-delay {
    animation: slideUp 0.8s ease-out 0.4s both;
}

.animate-fade-in-delay {
    animation: fadeIn 1s ease-out 0.6s both;
}
</style>
