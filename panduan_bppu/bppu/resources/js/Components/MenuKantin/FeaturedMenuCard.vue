<template>
    <div class="group relative h-full rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02]">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                v-if="menu.image"
                :src="menu.image"
                :alt="menu.name"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#895b00] to-[#6b4700]">
                <svg class="w-32 h-32 text-white/20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Dark Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/30"></div>

        <!-- Badge Container -->
        <div v-if="menu.badges && menu.badges.length > 0" class="absolute top-4 right-4 flex flex-col gap-2 z-10">
            <div
                v-for="(badge, index) in menu.badges"
                :key="index"
                :class="getBadgeClasses(badge.color)"
                class="px-3 py-1.5 rounded-full text-xs font-bold backdrop-blur-md shadow-lg flex items-center gap-1 animate-bounce-subtle"
            >
                <component :is="getBadgeIcon(badge.icon)" class="w-3.5 h-3.5" />
                <span>{{ badge.text }}</span>
            </div>
        </div>

        <!-- Content Overlay -->
        <div class="absolute inset-0 p-6 flex flex-col justify-end">
            <div class="space-y-2 transform transition-all duration-300 group-hover:translate-y-0 translate-y-2">
                <h3 class="text-2xl font-bold text-white drop-shadow-lg leading-tight">
                    {{ menu.name }}
                </h3>
                <p class="text-sm text-white/90 line-clamp-2 drop-shadow">
                    {{ menu.description }}
                </p>

                <div class="flex items-end justify-between pt-3">
                    <div>
                        <p class="text-xs text-white/80 mb-0.5">Harga</p>
                        <p class="text-2xl font-bold text-white drop-shadow-lg">
                            Rp {{ formatPrice(menu.price) }}
                        </p>
                        <p v-if="menu.varians && menu.varians.length > 0" class="text-xs text-white/80 mt-1">
                            {{ menu.varians.length }} varian tersedia
                        </p>
                    </div>
                    <Link
                        href="/kantin/self-order"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold bg-white dark:bg-[#f4efe5] text-primary-900 dark:text-[#3d2800] hover:bg-[#eae0cc] transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl inline-flex items-center gap-2"
                    >
                        <span>Pesan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

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

defineProps({
    menu: {
        type: Object,
        required: true
    }
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
