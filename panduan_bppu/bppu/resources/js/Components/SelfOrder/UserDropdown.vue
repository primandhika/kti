<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
        >
            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                <p class="text-xs text-gray-600">{{ user.total_points }} poin</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-primary-900 text-white flex items-center justify-center font-semibold text-sm">
                {{ userInitials }}
            </div>
            <svg
                :class="{'rotate-180': isOpen}"
                class="w-4 h-4 text-gray-600 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="isOpen"
                class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
            >
                <!-- User Info Mobile -->
                <div class="px-4 py-3 border-b border-gray-100 sm:hidden">
                    <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                    <p class="text-xs text-gray-600">{{ user.member_code }}</p>
                    <p class="text-xs text-primary-900 font-medium mt-1">{{ user.total_points }} poin</p>
                </div>

                <!-- Desktop Info -->
                <div class="px-4 py-3 border-b border-gray-100 hidden sm:block">
                    <p class="text-xs text-gray-600">{{ user.member_code }}</p>
                </div>

                <!-- Menu Items -->
                <Link
                    href="/member-area"
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Area Member</span>
                </Link>


                <!-- Divider -->
                <div class="border-t border-gray-100 my-2"></div>

                <!-- Logout -->
                <button
                    @click="handleLogout"
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 transition-colors w-full text-left"
                >
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-sm text-red-600 font-medium">Keluar</span>
                </button>
            </div>
        </transition>

        <!-- Logout Countdown Overlay -->
        <teleport to="body">
            <div v-if="showLogoutCountdown" class="fixed inset-0 z-[100] overflow-y-auto">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

                <!-- Countdown Content -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all">
                        <div class="text-center">
                            <svg class="w-20 h-20 mx-auto text-primary-900 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <p class="text-xl font-semibold text-gray-900 mb-2">
                                Anda akan keluar dan diarahkan ke laman utama
                            </p>
                            <p class="text-6xl font-bold text-primary-900 mt-6">
                                {{ logoutCountdown }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const isOpen = ref(false);
const dropdownRef = ref(null);
const showLogoutCountdown = ref(false);
const logoutCountdown = ref(5);
let logoutInterval = null;

const userInitials = computed(() => {
    if (!props.user?.name) return '?';
    const names = props.user.name.split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[1][0]).toUpperCase();
    }
    return props.user.name.substring(0, 2).toUpperCase();
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

const handleLogout = () => {
    isOpen.value = false;
    showLogoutCountdown.value = true;
    logoutCountdown.value = 5;

    logoutInterval = setInterval(() => {
        logoutCountdown.value--;

        if (logoutCountdown.value <= 0) {
            clearInterval(logoutInterval);
            sessionStorage.removeItem('bppu-kantin-picked');
            router.post('/buyer/logout');
        }
    }, 1000);
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
    if (logoutInterval) {
        clearInterval(logoutInterval);
    }
});
</script>
