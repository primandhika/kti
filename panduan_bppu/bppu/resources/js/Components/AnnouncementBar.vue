<template>
    <!-- Flash Toast -->
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <div
            v-if="flashMessage"
            class="fixed bottom-5 left-1/2 -translate-x-1/2 z-[10002] px-5 py-3 rounded-xl shadow-xl text-sm font-semibold flex items-center gap-2 whitespace-nowrap"
            :class="flashType === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
        >
            <svg v-if="flashType === 'success'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ flashMessage }}
        </div>
    </Transition>

    <Transition name="slide-down">
        <div v-if="isVisible" class="bg-gradient-to-r from-[#6b4700] to-[#5b3d00] text-white fixed top-0 left-0 right-0 shadow-md" style="z-index: 10001 !important;">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-1.5">
                <div class="flex items-center justify-between gap-2 sm:gap-3">
                    <!-- Content Area -->
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <!-- Icon -->
                        <svg v-if="!$page.props.auth.user" class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>

                        <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>

                        <!-- Not Logged In -->
                        <template v-if="!$page.props.auth.user">
                            <p class="text-xs sm:text-sm font-medium flex-1 truncate sm:whitespace-normal">
                                Daftar sebagai member BPPU dan dapatkan poin untuk potongan menarik!
                            </p>

                            <!-- CTA Button -->
                            <a
                                :href="route('member.register.form')"
                                class="px-2.5 py-1 bg-white text-[#6b4700] rounded text-xs font-semibold hover:bg-[#f4efe5] transition-colors duration-200 whitespace-nowrap flex-shrink-0"
                            >
                                Daftar
                            </a>
                        </template>

                        <!-- Logged In as Member -->
                        <template v-else-if="$page.props.auth.user.role_name === 'member' || $page.props.auth.user.role_name === 'buyer'">
                            <p class="text-xs sm:text-sm font-medium flex-1 truncate sm:whitespace-normal">
                                Halo, {{ $page.props.auth.user.name }} <span v-if="$page.props.auth.user.nomor_induk">({{ $page.props.auth.user.nomor_induk }})</span>!
                            </p>

                            <!-- CTA Buttons -->
                            <div class="flex gap-1.5 flex-shrink-0">
                                <a
                                    :href="route('belanja.index')"
                                    class="px-2.5 py-1 bg-white text-[#6b4700] rounded text-xs font-semibold hover:bg-[#f4efe5] transition-colors duration-200 whitespace-nowrap"
                                >
                                    Belanja
                                </a>
                                <a
                                    :href="route('self-order.index')"
                                    class="px-2.5 py-1 bg-white text-[#6b4700] rounded text-xs font-semibold hover:bg-[#f4efe5] transition-colors duration-200 whitespace-nowrap"
                                >
                                    Pesan Makanan
                                </a>
                                <button
                                    @click="logout"
                                    class="p-1 bg-white/20 hover:bg-white/30 rounded transition-colors duration-200 flex-shrink-0"
                                    title="Keluar"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <!-- Logged In as Admin/Staff -->
                        <template v-else-if="$page.props.auth.user.role_name">
                            <p class="text-xs sm:text-sm font-medium flex-1 truncate sm:whitespace-normal">
                                Halo, {{ $page.props.auth.user.name }}!
                            </p>

                            <!-- CTA Button -->
                            <a
                                :href="getDashboardRoute($page.props.auth.user.role_name)"
                                class="px-2.5 py-1 bg-white text-[#6b4700] rounded text-xs font-semibold hover:bg-[#f4efe5] transition-colors duration-200 whitespace-nowrap flex-shrink-0"
                            >
                                ke Dasbor
                            </a>
                        </template>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="close"
                        class="p-0.5 hover:bg-white/10 rounded transition-colors duration-200 flex-shrink-0"
                        aria-label="Tutup pengumuman"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();

// Flash toast
const flashMessage = ref('');
const flashType = ref('success');
let flashTimeout = null;

watch(() => page.props.flash?.success, (v) => {
    if (v) {
        flashMessage.value = v;
        flashType.value = 'success';
        clearTimeout(flashTimeout);
        flashTimeout = setTimeout(() => { flashMessage.value = ''; }, 4000);
    }
});
watch(() => page.props.flash?.error, (v) => {
    if (v) {
        flashMessage.value = v;
        flashType.value = 'error';
        clearTimeout(flashTimeout);
        flashTimeout = setTimeout(() => { flashMessage.value = ''; }, 4000);
    }
});

const getDashboardRoute = (role) => {
    switch(role) {
        case 'sysadmin':
        case 'officer':
        case 'data_clerk':
        case 'admin':
            return route('admin.dashboard');
        case 'pimpinan':
            return route('dashboard.pimpinan');
        case 'kasir':
            return route('pos.rekap');
        case 'mitra':
            return route('mitra.dashboard');
        default:
            return route('admin.dashboard');
    }
};

const isLoggedIn = () => !!page.props.auth?.user;

// User yang sudah login: bar selalu tampil (tidak bisa ditutup permanen)
// Guest: bisa ditutup dan diingat via localStorage
const isClosed = () => {
    if (isLoggedIn()) return false;
    return localStorage.getItem('announcementClosed_guest') === 'true';
};

const isVisible = ref(!isClosed());

// Re-evaluate saat status login berubah (SPA navigation)
watch(() => page.props.auth?.user?.id, () => {
    isVisible.value = !isClosed();
});

const logout = () => {
    const role = page.props.auth?.user?.role_name;
    const logoutRoute = (role === 'buyer' || role === 'member')
        ? route('buyer.logout')
        : route('logout');
    router.post(logoutRoute);
};

const close = () => {
    isVisible.value = false;
    if (!isLoggedIn()) {
        localStorage.setItem('announcementClosed_guest', 'true');
    }
    window.dispatchEvent(new CustomEvent('announcement-closed'));
};
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from {
    transform: translateY(-100%);
    opacity: 0;
}

.slide-down-leave-to {
    transform: translateY(-100%);
    opacity: 0;
}
</style>
