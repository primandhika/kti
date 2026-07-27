<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
      <div class="flex items-center justify-between h-14 px-4 sm:px-6">
        <!-- Logo + Brand -->
        <div class="flex items-center space-x-3">
          <img src="/logo.png" alt="BPPU" class="h-8 w-auto" />
          <div>
            <p class="text-xs font-bold text-[#996600] leading-tight">Portal Mitra</p>
            <p class="text-xs text-gray-500 leading-tight">BPPU IKIP Siliwangi</p>
          </div>
        </div>

        <!-- Right: Role badge + Dropdown -->
        <div class="flex items-center space-x-3">
          <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 capitalize">
            {{ userRole }}
          </span>

          <div class="relative">
            <button
              @click="dropdownOpen = !dropdownOpen"
              class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-8 h-8 bg-[#996600] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                {{ userName.charAt(0).toUpperCase() }}
              </div>
              <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <Transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div
                v-show="dropdownOpen"
                class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
              >
                <div class="px-4 py-2.5 border-b border-gray-100">
                  <p class="text-sm font-medium text-gray-800 truncate">{{ userName }}</p>
                  <p class="text-xs text-gray-500 truncate capitalize">{{ userRole }}</p>
                </div>

                <Link
                  :href="route('mitra.dasbor')"
                  class="w-full flex items-center space-x-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                  @click="dropdownOpen = false"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                  <span class="text-sm font-medium">Dasbor</span>
                </Link>
                <Link
                  :href="route('mitra.riwayatPencairan')"
                  class="w-full flex items-center space-x-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                  @click="dropdownOpen = false"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                  </svg>
                  <span class="text-sm font-medium">Riwayat Pencairan</span>
                </Link>
                <div class="border-t border-gray-100 my-1"></div>
                <button
                  @click="logout"
                  class="w-full flex items-center space-x-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  <span class="text-sm font-medium">Keluar</span>
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="px-4 py-5 sm:px-6 max-w-2xl mx-auto">
      <slot />
    </main>

    <!-- Flash Message -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div v-if="showFlash" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-sm z-50">
        <div :class="[
          'p-4 shadow-lg rounded-lg border-l-4 flex items-start space-x-3',
          flashType === 'error'
            ? 'bg-red-50 border-red-400'
            : 'bg-green-50 border-green-400'
        ]">
          <svg v-if="flashType !== 'error'" class="h-5 w-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <svg v-else class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <p :class="['text-sm font-medium', flashType === 'error' ? 'text-red-800' : 'text-green-800']">
            {{ flashMessage }}
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';

const page = usePage();
const dropdownOpen = ref(false);
const showFlash = ref(false);
const flashType = ref('success');
const flashMessage = ref('');

const userName = computed(() => page.props.auth?.user?.name || 'Mitra');
const userRole = computed(() => {
  const roles = page.props.auth?.user?.roles || [];
  return roles[0] || 'mitra';
});

watch(() => page.props.flash, (flash) => {
  if (flash?.success) {
    flashMessage.value = flash.success;
    flashType.value = 'success';
    showFlash.value = true;
    setTimeout(() => { showFlash.value = false; }, 4000);
  } else if (flash?.error) {
    flashMessage.value = flash.error;
    flashType.value = 'error';
    showFlash.value = true;
    setTimeout(() => { showFlash.value = false; }, 5000);
  }
}, { deep: true, immediate: true });

const logout = () => {
  dropdownOpen.value = false;
  router.post('/mitra/logout');
};
</script>
