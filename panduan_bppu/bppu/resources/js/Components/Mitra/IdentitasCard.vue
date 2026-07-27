<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Header strip -->
    <div class="h-2 bg-gradient-to-r from-[#996600] to-[#c1a366]"></div>

    <!-- Header accordion (selalu tampil) -->
    <div class="p-4">
      <div class="flex items-start space-x-3">
        <!-- Logo atau inisial -->
        <div class="flex-shrink-0">
          <img
            v-if="supplier.logo"
            :src="supplier.logo"
            :alt="supplier.nama"
            class="w-14 h-14 rounded-xl object-cover border border-gray-200"
          />
          <div
            v-else
            class="w-14 h-14 rounded-xl bg-[#996600]/10 border border-[#996600]/20 flex items-center justify-center"
          >
            <span class="text-xl font-bold text-[#996600]">
              {{ supplier.nama.charAt(0).toUpperCase() }}
            </span>
          </div>
        </div>

        <!-- Info utama -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between flex-wrap gap-1">
            <h2 class="text-base font-bold text-gray-800 truncate">{{ supplier.nama }}</h2>
            <span :class="['text-xs px-2 py-0.5 rounded-full font-medium capitalize', tipeBadgeClass]">
              {{ supplier.tipe_label }}
            </span>
          </div>
          <p v-if="supplier.perusahaan" class="text-xs text-gray-500 mt-0.5">{{ supplier.perusahaan }}</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ supplier.kode_supplier }}</p>
        </div>

        <!-- Tombol edit profil + toggle accordion -->
        <div class="flex items-center space-x-1 flex-shrink-0">
          <button
            @click="$emit('edit-profil')"
            class="p-1.5 text-gray-400 hover:text-[#996600] hover:bg-[#996600]/10 rounded-lg transition-colors"
            title="Edit Profil"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button
            @click="open = !open"
            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            :title="open ? 'Sembunyikan detail' : 'Lihat detail'"
          >
            <svg
              class="w-4 h-4 transition-transform duration-200"
              :class="open ? 'rotate-180' : ''"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Detail accordion -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div v-if="open">
          <!-- Info kontak -->
          <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600">
            <div v-if="supplier.telepon" class="flex items-center space-x-1.5">
              <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span class="truncate">{{ supplier.telepon }}</span>
            </div>
            <div v-if="supplier.kota" class="flex items-center space-x-1.5">
              <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="truncate">{{ supplier.kota }}</span>
            </div>
            <div v-if="supplier.email" class="flex items-center space-x-1.5 col-span-2">
              <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span class="truncate">{{ supplier.email }}</span>
            </div>
            <div v-if="supplier.alamat" class="flex items-center space-x-1.5 col-span-2">
              <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span class="truncate">{{ supplier.alamat }}</span>
            </div>
          </div>

          <!-- Skema bisnis -->
          <div v-if="supplier.skema_bisnis" class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Skema Bisnis</p>
            <div class="flex items-center space-x-2">
              <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full capitalize font-medium">
                {{ supplier.skema_bisnis.jenis_skema?.replace('_', ' ') }}
              </span>
              <span v-if="supplier.skema_bisnis.persentase_vendor" class="text-xs text-gray-600">
                Bagian Anda: {{ supplier.skema_bisnis.persentase_vendor }}%
              </span>
              <span v-else-if="supplier.skema_bisnis.nominal_flat" class="text-xs text-gray-600">
                Flat: {{ formatRupiah(supplier.skema_bisnis.nominal_flat) }}
              </span>
            </div>
          </div>

          <!-- Status tidak aktif -->
          <div v-if="!supplier.is_active" class="mt-3 flex items-center space-x-1.5 text-xs text-red-600">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>Status mitra tidak aktif. Hubungi pengelola.</span>
          </div>

          <!-- Ganti password -->
          <div class="mt-3 pt-3 border-t border-gray-100">
            <button
              @click="$emit('ganti-password')"
              class="text-xs text-[#996600] hover:text-[#895b00] font-medium flex items-center space-x-1 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
              <span>Ganti Password</span>
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  supplier: {
    type: Object,
    required: true,
  },
});

defineEmits(['edit-profil', 'ganti-password']);

const open = ref(false);

const tipeBadgeClass = computed(() => {
  const map = {
    tenant:      'bg-purple-100 text-purple-700',
    konsinyasi:  'bg-orange-100 text-orange-700',
    vendor:      'bg-teal-100 text-teal-700',
    distributor: 'bg-blue-100 text-blue-700',
    produsen:    'bg-green-100 text-green-700',
    reseller:    'bg-pink-100 text-pink-700',
    supplier:    'bg-amber-100 text-amber-700',
  };
  return map[props.supplier.tipe_mitra] || 'bg-gray-100 text-gray-700';
});

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>
