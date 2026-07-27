<template>
    <nav class="fixed w-full transition-all duration-300" :style="{ zIndex: '10000 !important', top: hasAnnouncement ? '36px' : '0' }" :class="scrolled ? 'bg-primary-900 shadow-lg' : 'bg-primary-900/95 backdrop-blur-sm'">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-12 md:h-14">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 hover:opacity-90 transition-opacity">
                    <img src="/logo-white.png" alt="IKIP Siliwangi" class="h-7 sm:h-8 md:h-9 w-auto">
                    <div>
                        <div class="text-sm font-bold text-white leading-tight">BPPU</div>
                        <div class="text-[10px] text-white/70 leading-tight hidden sm:block">IKIP Siliwangi</div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-5">
                    <template v-for="menu in menus" :key="menu.id">
                        <!-- Menu with dropdown -->
                        <div v-if="menu.children && menu.children.length > 0" class="relative group">
                            <button class="nav-link flex items-center space-x-1 text-sm">
                                <span>{{ menu.label }}</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <!-- Dropdown -->
                            <div class="absolute left-0 mt-2 w-52 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-1.5">
                                <a v-for="child in menu.children" :key="child.id"
                                   :href="child.url"
                                   :target="child.open_new_tab ? '_blank' : '_self'"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-900 transition-colors">
                                    {{ child.label }}
                                </a>
                            </div>
                        </div>
                        <!-- Regular menu -->
                        <a v-else :href="menu.url || '#'" :target="menu.open_new_tab ? '_blank' : '_self'" class="nav-link text-sm">
                            {{ menu.label }}
                        </a>
                    </template>

                    <!-- Dark Mode Toggle (Desktop) -->
                    <button @click="toggleDarkMode" class="p-1.5 text-white/80 hover:text-white transition-colors rounded-lg hover:bg-white/10">
                        <svg v-if="!isDark" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile: Dark Mode Toggle + Menu Button -->
                <div class="md:hidden flex items-center gap-1">
                    <button @click="toggleDarkMode" class="p-1.5 text-white/80 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
                        <svg v-if="!isDark" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-1.5 text-white/80 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Menu - Teleported to body -->
    <Teleport to="body">
        <!-- Overlay -->
        <transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/60 md:hidden" style="z-index: 99998 !important;"></div>
        </transition>

        <!-- Mobile Offcanvas Panel -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div v-show="mobileMenuOpen" class="fixed inset-y-0 right-0 w-64 bg-primary-900 shadow-2xl md:hidden flex flex-col" style="z-index: 99999 !important;">

                <!-- Offcanvas Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
                    <a href="/" class="flex items-center gap-2">
                        <img src="/logo-white.png" alt="BPPU" class="h-7 w-auto">
                        <div>
                            <div class="text-sm font-bold text-white leading-tight">BPPU</div>
                            <div class="text-[10px] text-white/60 leading-tight">IKIP Siliwangi</div>
                        </div>
                    </a>
                    <button @click="mobileMenuOpen = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Menu Items -->
                <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
                    <template v-for="menu in menus" :key="menu.id">
                        <!-- Menu with submenu -->
                        <div v-if="menu.children && menu.children.length > 0">
                            <button @click="toggleMobileSubmenu(menu.id)" class="mobile-nav-btn">
                                <span>{{ menu.label }}</span>
                                <svg
                                    class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSubmenu === menu.id }"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <!-- Submenu -->
                            <div v-show="openSubmenu === menu.id" class="mt-0.5 ml-2 pl-3 border-l border-white/15 space-y-0.5">
                                <a v-for="child in menu.children" :key="child.id"
                                   :href="child.url"
                                   :target="child.open_new_tab ? '_blank' : '_self'"
                                   @click="mobileMenuOpen = false"
                                   class="mobile-sub-link">
                                    {{ child.label }}
                                </a>
                            </div>
                        </div>
                        <!-- Regular menu -->
                        <a v-else :href="menu.url || '#'" :target="menu.open_new_tab ? '_blank' : '_self'" @click="mobileMenuOpen = false" class="mobile-nav-link">
                            {{ menu.label }}
                        </a>
                    </template>
                </nav>

                <!-- Offcanvas Footer -->
                <div class="px-4 py-3 border-t border-white/10">
                    <p class="text-[10px] text-white/30 text-center">BPPU IKIP Siliwangi</p>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';

const scrolled = ref(false);
const mobileMenuOpen = ref(false);
const openSubmenu = ref(null);
const hasAnnouncement = ref(true);

const { isDark, toggleDarkMode, initDarkMode } = useDarkMode();

const page = usePage();
const menus = computed(() => page.props.navbarMenus || []);

// Cek apakah announcement bar sedang tampil
const checkAnnouncement = () => {
    const user = page.props.auth?.user;
    if (user) {
        hasAnnouncement.value = true;
        return;
    }
    hasAnnouncement.value = localStorage.getItem('announcementClosed_guest') !== 'true';
};

// Listen for storage changes (saat guest menutup announcement di tab lain)
const handleStorageChange = (e) => {
    if (e.key === 'announcementClosed_guest') {
        checkAnnouncement();
    }
};

// Listen for announcement closed event (semua user, termasuk yang login)
const handleAnnouncementClosed = () => {
    hasAnnouncement.value = false;
};

const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
};

const toggleMobileSubmenu = (menuId) => {
    openSubmenu.value = openSubmenu.value === menuId ? null : menuId;
};

watch(() => page.props.auth?.user?.id, () => {
    checkAnnouncement();
});

onMounted(() => {
    initDarkMode();
    checkAnnouncement();
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('storage', handleStorageChange);
    window.addEventListener('announcement-closed', handleAnnouncementClosed);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('storage', handleStorageChange);
    window.removeEventListener('announcement-closed', handleAnnouncementClosed);
});
</script>

<style scoped>
.nav-link {
    @apply text-white/90 hover:text-white font-medium transition-colors duration-200 relative;
}

.nav-link::after {
    content: '';
    @apply absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-300;
}

.nav-link:hover::after {
    @apply w-full;
}

/* Inline flex so chevron stays on the same row */
.mobile-nav-btn {
    @apply w-full flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 transition-all duration-200 text-left;
}

.mobile-nav-link {
    @apply flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 transition-all duration-200;
}

.mobile-sub-link {
    @apply flex items-center px-3 py-2 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200;
}
</style>
