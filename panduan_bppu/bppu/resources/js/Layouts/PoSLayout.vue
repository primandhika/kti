<template>
  <div class="min-h-screen bg-[#f4efe5] flex flex-col">
    <!-- Top Navigation Bar - Compact & Mobile Friendly -->
    <header class="sticky top-0 z-30 bg-[#f4efe5]/95 border-b border-[#e0d1b2] shadow-sm backdrop-blur">
      <div class="flex items-center justify-between h-14 px-4">
        <!-- Logo & Title - Clickable to go back to PoS -->
        <Link
          href="/pengelola/penjualan"
          class="flex items-center space-x-3 hover:opacity-90 transition-opacity"
        >
          <img src="/logo-BPPU-flat.png" alt="BPPU" class="h-8 w-auto" />
          <div class="text-[#5b3d00]">
            <h1 class="text-sm font-bold leading-tight">{{ title }}</h1>
            <p class="text-[10px] text-[#7a5100] leading-tight">
              {{ page.props.auth.user?.name }} - {{ currentDateTime }}
            </p>
          </div>
        </Link>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
          <!-- Fullscreen Toggle -->
          <button
            @click="toggleFullscreen"
            class="p-2 rounded-lg text-[#6b4700] hover:bg-[#eae0cc] transition-colors"
            :title="isFullscreen ? 'Keluar Fullscreen (ESC)' : 'Fullscreen'"
          >
            <!-- Expand icon -->
            <svg v-if="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
            <!-- Compress icon -->
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 9h4.5M15 9V4.5M15 9l5.25-5.25M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
            </svg>
          </button>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button
              @click="profileDropdownOpen = !profileDropdownOpen"
              class="flex items-center space-x-2 p-1.5 rounded-lg text-[#5b3d00] hover:bg-[#eae0cc] transition-colors"
            >
              <div class="w-8 h-8 bg-[#996600] rounded-full flex items-center justify-center text-white font-semibold text-sm">
                {{ (page.props.auth.user?.name || 'U').charAt(0).toUpperCase() }}
              </div>
              <div class="hidden sm:block text-left">
                <p class="text-xs font-medium leading-tight">{{ page.props.auth.user?.name }}</p>
                <p class="text-xs text-[#7a5100] leading-tight">{{ page.props.auth.user?.username }}</p>
              </div>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <Transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div
                v-if="profileDropdownOpen"
                @click.away="profileDropdownOpen = false"
                class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5"
              >
                <div class="py-1">
                  <!-- Profile Info -->
                  <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-[#996600] rounded-full flex items-center justify-center text-white font-semibold">
                        {{ (page.props.auth.user?.name || 'U').charAt(0).toUpperCase() }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ page.props.auth.user?.name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ page.props.auth.user?.username }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Change Password -->
                  <button
                    @click="openChangePasswordModal"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Ubah Password</span>
                  </button>

                  <!-- Logout -->
                  <button
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center space-x-2 border-t border-gray-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </header>

    <!-- Sub Navbar Slot -->
    <slot name="sub-navbar" />

    <!-- Page Content - Full Width, No Sidebar -->
    <main class="flex-1 w-full pb-16">
      <slot />
    </main>

    <!-- Bottom Navigation - Mobile App Style -->
    <nav class="fixed bottom-0 left-0 right-0 z-30 bg-[#f4efe5] border-t border-[#e0d1b2] shadow-lg">
      <div class="flex items-center justify-around h-14 max-w-lg mx-auto px-2">
        <!-- PoS -->
        <Link
          href="/pengelola/penjualan"
          class="flex-1 flex flex-col items-center justify-center py-2 transition-colors"
          :class="page.url.startsWith('/pengelola/penjualan')
            ? 'text-[#996600]'
            : 'text-gray-400 hover:text-gray-600'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="text-[10px] font-medium mt-0.5">PoS</span>
        </Link>

        <!-- Pesanan -->
        <Link
          :href="pesananUrl"
          class="flex-1 flex flex-col items-center justify-center py-2 relative transition-colors"
          :class="page.url.startsWith(pesananUrl)
            ? 'text-[#996600]'
            : 'text-gray-400 hover:text-gray-600'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span
            v-if="antrianAktif > 0"
            class="absolute top-0.5 right-1/4 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[9px] font-bold bg-red-500 text-white rounded-full"
          >
            {{ antrianAktif > 99 ? '99+' : antrianAktif }}
          </span>
          <span class="text-[10px] font-medium mt-0.5">Pesanan</span>
        </Link>

        <!-- Rekap -->
        <Link
          href="/pengelola/rekap-penjualan"
          class="flex-1 flex flex-col items-center justify-center py-2 transition-colors"
          :class="page.url.startsWith('/pengelola/rekap-penjualan')
            ? 'text-[#996600]'
            : 'text-gray-400 hover:text-gray-600'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
          <span class="text-[10px] font-medium mt-0.5">Rekap</span>
        </Link>
      </div>
    </nav>

    <!-- Change Password Modal -->
    <ChangePasswordModal
      :show="showChangePasswordModal || page.props.auth.user?.must_change_password || false"
      :errors="page.props.errors || {}"
      @close="showChangePasswordModal = false"
    />

    <!-- Flash Message Notification -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div
        v-if="showFlash"
        class="fixed bottom-4 right-4 z-50 max-w-md"
      >
        <div class="bg-green-50 border-l-4 border-green-400 p-4 shadow-lg rounded-lg">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">
                {{ page.props.flash?.success }}
              </p>
            </div>
            <div class="ml-auto pl-3">
              <button
                @click="showFlash = false"
                class="inline-flex text-green-400 hover:text-green-600 focus:outline-none"
              >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ChangePasswordModal from '@/Components/ChangePasswordModal.vue';

const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Point of Sale'
  },
  title: {
    type: String,
    default: 'BPPU PoS'
  },
  pesananUrl: {
    type: String,
    required: true
  },
  apiUrl: {
    type: String,
    required: true
  },
  notifTitle: {
    type: String,
    default: 'Pesanan Baru Masuk!'
  }
});

const showFlash = ref(false);
const profileDropdownOpen = ref(false);
const showChangePasswordModal = ref(false);
const currentTime = ref(new Date());
const antrianAktif = ref(0);
const isFullscreen = ref(false);
const previousMenungguCount = ref(null);
const page = usePage();
let timeInterval = null;
let bellPollInterval = null;

const currentDateTime = computed(() => {
  const now = currentTime.value;
  const day = String(now.getDate()).padStart(2, '0');
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const year = now.getFullYear();
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');

  return `${day}/${month}/${year} - ${hours}:${minutes} WIB`;
});

const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.relative');
  if (!dropdown && profileDropdownOpen.value) {
    profileDropdownOpen.value = false;
  }
};

async function fetchAntrianAktif() {
  try {
    const res = await fetch(props.apiUrl, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const data = await res.json();
    const currentMenunggu = data.stats?.menunggu ?? 0;
    const currentDiproses = data.stats?.diproses ?? 0;
    antrianAktif.value = currentMenunggu + currentDiproses;

    if (
      previousMenungguCount.value !== null &&
      currentMenunggu > previousMenungguCount.value
    ) {
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(props.notifTitle, {
          body: `Ada ${currentMenunggu} antrian menunggu konfirmasi`,
          icon: '/logo-BPPU-flat.png',
        });
      }
    }

    previousMenungguCount.value = currentMenunggu;
  } catch {
    // silent
  }
}

function toggleFullscreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(err => {
      console.error('Fullscreen error:', err);
    });
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
}

function handleFullscreenChange() {
  isFullscreen.value = !!document.fullscreenElement;
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('fullscreenchange', handleFullscreenChange);

  timeInterval = setInterval(() => {
    currentTime.value = new Date();
  }, 60000);

  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

  fetchAntrianAktif();
  bellPollInterval = setInterval(fetchAntrianAktif, 10000);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('fullscreenchange', handleFullscreenChange);

  if (timeInterval) clearInterval(timeInterval);
  if (bellPollInterval) clearInterval(bellPollInterval);
});

watch(() => page.props.flash?.success, (newValue) => {
  if (newValue) {
    showFlash.value = true;
    setTimeout(() => {
      showFlash.value = false;
    }, 5000);
  }
});

const openChangePasswordModal = () => {
  showChangePasswordModal.value = true;
  profileDropdownOpen.value = false;
};

const logout = () => {
  profileDropdownOpen.value = false;
  router.post('/pengelola/logout');
};
</script>
