<template>
    <section
        id="home"
        class="relative h-[80vh] min-h-[600px] lg:min-h-[700px] overflow-hidden"
        itemscope
        itemtype="https://schema.org/Organization"
    >
        <!-- Background with Gradient Overlay -->
        <div class="absolute inset-0">
            <img
                src="/storage/galeri/20260108_110616.jpg"
                alt="Area Kantin IKIP Siliwangi BPPU"
                class="w-full h-full object-cover blur-sm"
                loading="eager"
                fetchpriority="high"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-[#996600]/40 lg:from-black/85 lg:via-black/60 lg:to-transparent"></div>
        </div>

        <!-- Animated Background Shapes -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-[#996600]/10 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-[#996600]/10 rounded-full blur-3xl animate-float-slower"></div>
        </div>

        <!-- Main Content Grid -->
        <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div class="grid lg:grid-cols-2 gap-8 h-full items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <!-- Logo -->
                    <img
                        src="/logo-white.png"
                        alt="Logo IKIP Siliwangi"
                        class="h-12 sm:h-14 md:h-16 mx-auto lg:mx-0 mb-4 animate-fade-in drop-shadow-lg"
                        itemprop="logo"
                    >

                    <!-- Title with Gradient Text - Fixed overflow -->
                    <h1
                        class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-3 animate-slide-up leading-tight tracking-tight"
                        itemprop="name"
                    >
                        <span class="inline-block bg-gradient-to-r from-white to-[#f4efe5] bg-clip-text text-transparent">
                            Badan Pengelola
                        </span>
                        <br>
                        <span class="inline-block bg-gradient-to-r from-[#f4efe5] to-[#ccb27f] bg-clip-text text-transparent">
                            dan Pengembangan Usaha
                        </span>
                    </h1>

                    <!-- Subtitle -->
                    <p
                        class="text-base sm:text-lg md:text-xl text-[#f4efe5]/90 mb-6 animate-slide-up-delay font-light"
                        itemprop="alternateName"
                    >
                        IKIP Siliwangi
                    </p>

                    <!-- Search Bar -->
                    <div class="mb-6 max-w-lg mx-auto lg:mx-0 animate-fade-in-delay">
                        <form @submit.prevent="handleSearch" class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari berita, menu, produk, atau layanan..."
                                class="w-full px-4 py-3 pl-12 rounded-full bg-white/10 backdrop-blur-md border-2 border-white/30 text-white placeholder-white/60 focus:outline-none focus:border-[#ccb27f] focus:bg-white/20 transition-all duration-300"
                                @focus="$event.target.classList.add('search-focused')"
                                @blur="$event.target.classList.remove('search-focused')"
                            >
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </form>
                    </div>

                    <!-- CTA Buttons Outline Style - Smaller -->
                    <div class="grid grid-cols-2 gap-3 max-w-md mx-auto lg:mx-0">
                        <!-- Pesan Makanan -->
                        <a
                            :href="route('self-order.index')"
                            class="cta-button cta-outline group relative overflow-hidden animate-button-1"
                        >
                            <span class="ripple"></span>
                            <div class="relative z-10 flex flex-col items-center gap-1.5 py-3">
                                <div class="icon-wrapper">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">Pesan Makanan</span>
                            </div>
                        </a>

                        <!-- Belanja -->
                        <a
                            :href="route('belanja.index')"
                            class="cta-button cta-outline group relative overflow-hidden animate-button-2"
                        >
                            <span class="ripple"></span>
                            <div class="relative z-10 flex flex-col items-center gap-1.5 py-3">
                                <div class="icon-wrapper">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">Belanja</span>
                            </div>
                        </a>

                        <!-- Ajukan Kerjasama -->
                        <a
                            href="#contact"
                            class="cta-button cta-outline group relative overflow-hidden animate-button-3"
                        >
                            <span class="ripple"></span>
                            <div class="relative z-10 flex flex-col items-center gap-1.5 py-3">
                                <div class="icon-wrapper">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">Kerjasama</span>
                            </div>
                        </a>

                        <!-- Proposal Bisnis -->
                        <a
                            href="mailto:bppu@ikipsiliwangi.ac.id?subject=Proposal Bisnis"
                            class="cta-button cta-outline group relative overflow-hidden animate-button-4"
                        >
                            <span class="ripple"></span>
                            <div class="relative z-10 flex flex-col items-center gap-1.5 py-3">
                                <div class="icon-wrapper">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">Proposal Bisnis</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Right Content - Model Image (Hidden on mobile, visible on large screens) -->
                <div class="hidden lg:block h-full relative">
                    <!-- Glow effects -->
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-to-t from-[#996600]/30 via-transparent to-transparent blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-48 h-48 bg-[#996600]/20 rounded-full blur-3xl animate-pulse-slow pointer-events-none"></div>

                    <!-- Model Image - Absolute positioning to stick to bottom -->
                    <img
                        src="/model.png"
                        alt="Mahasiswa IKIP Siliwangi menikmati layanan kantin"
                        class="absolute bottom-0 right-0 h-[70%] w-auto object-contain object-bottom drop-shadow-2xl"
                        loading="eager"
                    >
                </div>
            </div>

            <!-- Scroll Down Indicator - Right side on desktop -->
            <a
                href="#content"
                class="absolute bottom-8 left-1/2 -translate-x-1/2 lg:left-auto lg:right-8 lg:translate-x-0 inline-flex flex-col items-center text-white/80 hover:text-white transition-colors duration-300 animate-bounce-slow"
                aria-label="Scroll to content"
            >
                <span class="text-sm mb-2 hidden sm:inline">Jelajahi</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </a>
        </div>

        <!-- Hidden metadata for SEO -->
        <div class="hidden" itemprop="logo">https://bppu.ikipsiliwangi.ac.id/storage/logo-round_ijokuning.png</div>
        <meta itemprop="url" content="https://bppu.ikipsiliwangi.ac.id">
        <meta itemprop="telephone" content="+62226629913">
        <meta itemprop="email" content="bppu@ikipsiliwangi.ac.id">
        <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress" class="hidden">
            <meta itemprop="streetAddress" content="Jl. Terusan Jenderal Sudirman">
            <meta itemprop="addressLocality" content="Cimahi">
            <meta itemprop="addressRegion" content="Jawa Barat">
            <meta itemprop="postalCode" content="40526">
            <meta itemprop="addressCountry" content="ID">
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.visit(`/berita?search=${encodeURIComponent(searchQuery.value)}`);
    }
};
</script>

<style scoped>
/* CTA Button Outline Styles */
.cta-button {
    @apply relative rounded-xl font-semibold transition-all duration-500;
    @apply backdrop-blur-sm cursor-pointer;
}

/* Outline Style with Pulsing Animation */
.cta-outline {
    @apply bg-transparent text-white border-2 border-white/40;
    animation: button-pulse 3s ease-in-out infinite;
}

.cta-outline::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, rgba(153, 102, 0, 0.3), rgba(244, 239, 229, 0.3));
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.cta-outline:hover {
    @apply bg-white/10 border-[#ccb27f] scale-105 -translate-y-1;
    box-shadow: 0 10px 30px rgba(153, 102, 0, 0.3), 0 0 20px rgba(204, 178, 127, 0.2);
}

.cta-outline:hover::before {
    opacity: 1;
}

.cta-outline:active {
    @apply scale-95 translate-y-0;
}

/* Ripple Effect */
.ripple {
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    overflow: hidden;
    pointer-events: none;
}

.cta-button:hover .ripple::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    animation: ripple-animation 0.8s ease-out;
}

@keyframes ripple-animation {
    to {
        width: 300px;
        height: 300px;
        opacity: 0;
    }
}

/* Icon Animation - Continuous Loop */
.icon-wrapper {
    animation: icon-float 2.5s ease-in-out infinite;
}

.cta-button:hover .icon-wrapper {
    animation: icon-bounce 0.6s ease-in-out;
}

@keyframes icon-float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-4px);
    }
}

@keyframes icon-bounce {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    25% {
        transform: translateY(-8px) rotate(-5deg);
    }
    75% {
        transform: translateY(-4px) rotate(5deg);
    }
}

/* Button Pulsing Animation */
@keyframes button-pulse {
    0%, 100% {
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 0 rgba(204, 178, 127, 0);
    }
    50% {
        border-color: rgba(204, 178, 127, 0.6);
        box-shadow: 0 0 20px rgba(204, 178, 127, 0.2);
    }
}

/* Button Entrance Animations */
@keyframes button-entrance {
    0% {
        opacity: 0;
        transform: translateY(20px) scale(0.9);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-button-1 {
    animation: button-entrance 0.6s ease-out 0.8s both;
}

.animate-button-2 {
    animation: button-entrance 0.6s ease-out 1s both;
}

.animate-button-3 {
    animation: button-entrance 0.6s ease-out 1.2s both;
}

.animate-button-4 {
    animation: button-entrance 0.6s ease-out 1.4s both;
}

/* Background Animations */
@keyframes float-slow {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(-20px) translateX(10px);
    }
}

@keyframes float-slower {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(-30px) translateX(-15px);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.05);
    }
}

.animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
}

.animate-float-slower {
    animation: float-slower 10s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

/* Existing Animations */
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

@keyframes bounceSlow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
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
    animation: fadeIn 1s ease-out 0.8s both;
}

.animate-bounce-slow {
    animation: bounceSlow 2s ease-in-out infinite;
}

/* Search Bar Focus Animation */
.search-focused {
    @apply ring-2 ring-[#ccb27f]/50;
    animation: search-glow 1s ease-in-out;
}

@keyframes search-glow {
    0%, 100% {
        box-shadow: 0 0 0 rgba(204, 178, 127, 0);
    }
    50% {
        box-shadow: 0 0 20px rgba(204, 178, 127, 0.4);
    }
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .cta-button:hover {
        transform: scale(1.02) translateY(-2px);
    }
}
</style>
