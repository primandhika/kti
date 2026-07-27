<template>
    <Transition name="slide-up">
        <div v-if="isVisible" class="fixed bottom-4 right-4 z-50 w-80 sm:w-96">
            <div class="bg-white dark:bg-[#3d2800] rounded-lg shadow-2xl border border-[#996600]/20">
                <div class="p-4">
                    <!-- Header with Close -->
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-[#996600] to-[#7a5100] rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-[#f4efe5]">
                                Cookies
                            </h3>
                        </div>
                        <button
                            @click="rejectCookies"
                            class="p-1 hover:bg-gray-100 dark:hover:bg-[#4c3300] rounded transition-colors"
                            aria-label="Tutup"
                        >
                            <svg class="w-4 h-4 text-gray-500 dark:text-[#d6c199]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <p class="text-xs text-gray-600 dark:text-[#d6c199] mb-3 leading-relaxed">
                        Kami menggunakan cookies untuk meningkatkan pengalaman Anda di situs ini.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button
                            @click="acceptCookies"
                            class="flex-1 px-3 py-1.5 bg-gradient-to-r from-[#996600] to-[#7a5100] text-white rounded text-xs font-semibold hover:from-[#a37519] hover:to-[#895b00] transition-all duration-200"
                        >
                            Terima
                        </button>
                        <button
                            @click="rejectCookies"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-[#4c3300] text-[#996600] dark:text-[#f4efe5] border border-[#996600]/30 dark:border-[#996600] rounded text-xs font-semibold hover:bg-[#f4efe5] dark:hover:bg-[#5b3d00] transition-all duration-200"
                        >
                            Tolak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isVisible = ref(false);

onMounted(() => {
    // Check if user has already responded to cookie consent
    const cookieConsent = localStorage.getItem('cookieConsent');
    if (!cookieConsent) {
        // Show after a short delay for better UX
        setTimeout(() => {
            isVisible.value = true;
        }, 1000);
    }
});

const acceptCookies = () => {
    localStorage.setItem('cookieConsent', 'accepted');
    isVisible.value = false;
};

const rejectCookies = () => {
    localStorage.setItem('cookieConsent', 'rejected');
    isVisible.value = false;
};
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from {
    transform: translateY(100%);
    opacity: 0;
}

.slide-up-leave-to {
    transform: translateY(100%);
    opacity: 0;
}
</style>
