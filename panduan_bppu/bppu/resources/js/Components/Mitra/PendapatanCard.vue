<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <!-- Header + Filter -->
    <div class="p-4 border-b border-gray-100">
      <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
        <h3 class="text-sm font-semibold text-gray-700">Pendapatan</h3>
        <div class="flex items-center flex-wrap gap-1">
          <button
            v-for="opt in filterOptions"
            :key="opt.value"
            @click="selectFilter(opt.value)"
            :class="[
              'text-xs px-2.5 py-1 rounded-full font-medium transition-colors',
              currentFilter === opt.value
                ? 'bg-[#996600] text-white'
                : 'text-gray-500 hover:bg-gray-100'
            ]"
          >
            {{ opt.label }}
          </button>
        </div>
      </div>

      <!-- Input tanggal custom -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div v-if="currentFilter === 'custom'" class="flex items-center gap-2 mt-2">
          <div class="flex-1">
            <label class="text-xs text-gray-500 block mb-1">Dari</label>
            <input
              v-model="customDari"
              type="date"
              class="w-full text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            />
          </div>
          <div class="flex-1">
            <label class="text-xs text-gray-500 block mb-1">Sampai</label>
            <input
              v-model="customSampai"
              type="date"
              class="w-full text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            />
          </div>
          <div class="pt-4">
            <button
              @click="applyCustomFilter"
              :disabled="!customDari || !customSampai"
              class="text-xs px-3 py-1.5 bg-[#996600] text-white rounded-lg hover:bg-[#895b00] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
              Tampilkan
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-0 divide-x divide-y divide-gray-100">
      <!-- Pendapatan Mitra -->
      <div class="p-4">
        <p class="text-xs text-gray-500 mb-1">Pendapatan Anda</p>
        <p class="text-lg font-bold text-[#996600]">{{ formatRupiah(pendapatan.pendapatan_mitra) }}</p>
      </div>

      <!-- Total Penjualan -->
      <div class="p-4">
        <p class="text-xs text-gray-500 mb-1">Total Penjualan</p>
        <p class="text-lg font-bold text-gray-800">{{ formatRupiah(pendapatan.total_penjualan) }}</p>
      </div>

      <!-- Jumlah Transaksi -->
      <div class="p-4">
        <p class="text-xs text-gray-500 mb-1">Transaksi</p>
        <p class="text-lg font-bold text-gray-800">{{ pendapatan.jumlah_transaksi }}</p>
        <p class="text-xs text-gray-400">kali</p>
      </div>

      <!-- Item Terjual -->
      <div class="p-4">
        <p class="text-xs text-gray-500 mb-1">Item Terjual</p>
        <p class="text-lg font-bold text-gray-800">{{ pendapatan.total_item_terjual }}</p>
        <p class="text-xs text-gray-400">pcs</p>
      </div>
    </div>

    <!-- Ajukan Pencairan button -->
    <div class="p-4 border-t border-gray-100">
      <button
        @click="$emit('tarik-dana')"
        class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 bg-[#996600] hover:bg-[#895b00] text-white text-sm font-medium rounded-xl transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Ajukan Pencairan</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  pendapatan: {
    type: Object,
    required: true,
  },
  currentFilter: {
    type: String,
    default: 'hari_ini',
  },
  currentDari: {
    type: String,
    default: '',
  },
  currentSampai: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['filter-change', 'tarik-dana']);

const filterOptions = [
  { value: 'hari_ini',   label: 'Hari ini' },
  { value: 'minggu_ini', label: 'Minggu' },
  { value: 'bulan_ini',  label: 'Bulan' },
  { value: 'tahun_ini',  label: 'Tahun' },
  { value: 'custom',     label: 'Pilih Tanggal' },
];

const customDari   = ref(props.currentDari || '');
const customSampai = ref(props.currentSampai || '');

const selectFilter = (value) => {
  if (value !== 'custom') {
    emit('filter-change', { filter: value });
  } else {
    // Hanya tampilkan input, belum kirim request
    emit('filter-change', { filter: 'custom', skipRequest: true });
  }
};

const applyCustomFilter = () => {
  if (!customDari.value || !customSampai.value) return;
  emit('filter-change', {
    filter: 'custom',
    dari: customDari.value,
    sampai: customSampai.value,
  });
};

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};
</script>
