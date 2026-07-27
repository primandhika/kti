<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <!-- Header + Sort -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-2 flex-wrap">
      <h3 class="text-sm font-semibold text-gray-700">
        Daftar Produk
        <span class="text-gray-400 font-normal">({{ produk.length }})</span>
      </h3>
      <div class="flex items-center gap-1">
        <span class="text-xs text-gray-400 mr-1">Urutkan:</span>
        <button
          v-for="opt in sortOptions"
          :key="opt.value"
          @click="sortBy = opt.value"
          :class="[
            'text-xs px-2.5 py-1 rounded-full font-medium transition-colors',
            sortBy === opt.value
              ? 'bg-[#996600] text-white'
              : 'text-gray-500 hover:bg-gray-100'
          ]"
        >
          {{ opt.label }}
        </button>
      </div>
    </div>

    <div v-if="produk.length === 0" class="p-8 text-center text-sm text-gray-400">
      Belum ada produk terdaftar
    </div>

    <ul v-else class="divide-y divide-gray-50">
      <li
        v-for="(item, idx) in paginatedProduk"
        :key="item.id"
        class="flex items-center gap-3 px-4 py-2.5"
      >
        <!-- Nomor urut -->
        <span class="flex-shrink-0 w-5 text-xs text-gray-400 text-right">
          {{ (currentPage - 1) * perPage + idx + 1 }}.
        </span>

        <!-- Nama + harga -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-800 truncate">{{ item.nama_barang }}</p>
          <p class="text-xs text-gray-400">{{ formatRupiah(item.harga_jual) }}</p>
        </div>

        <!-- Stok -->
        <div class="flex-shrink-0 text-right hidden sm:block">
          <p class="text-xs text-gray-500">Stok</p>
          <p class="text-xs font-medium text-gray-700">{{ item.stok }}</p>
        </div>

        <!-- Terjual -->
        <div class="flex-shrink-0 text-right">
          <p class="text-xs text-gray-500">Terjual</p>
          <p class="text-xs font-semibold text-[#996600]">{{ item.terjual }}</p>
        </div>

        <!-- Status -->
        <span
          :class="[
            'flex-shrink-0 text-xs px-2 py-0.5 rounded-full font-medium',
            item.is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'
          ]"
        >
          {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
      </li>
    </ul>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
      <p class="text-xs text-gray-500">
        {{ (currentPage - 1) * perPage + 1 }}-{{ Math.min(currentPage * perPage, sortedProduk.length) }}
        dari {{ sortedProduk.length }}
      </p>
      <div class="flex items-center gap-1">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-2 py-1 text-xs rounded-lg text-gray-500 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        >
          &lsaquo;
        </button>
        <span class="text-xs text-gray-600 px-1">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-2 py-1 text-xs rounded-lg text-gray-500 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        >
          &rsaquo;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  produk: {
    type: Array,
    default: () => [],
  },
});

const perPage     = 10;
const currentPage = ref(1);
const sortBy      = ref('terjual');

const sortOptions = [
  { value: 'terjual',    label: 'Terlaris' },
  { value: 'nama',       label: 'Nama' },
  { value: 'harga_jual', label: 'Harga' },
  { value: 'stok',       label: 'Stok' },
];

watch(() => props.produk, () => { currentPage.value = 1; });
watch(sortBy, () => { currentPage.value = 1; });

const sortedProduk = computed(() => {
  return [...props.produk].sort((a, b) => {
    if (sortBy.value === 'nama')       return a.nama_barang.localeCompare(b.nama_barang, 'id');
    if (sortBy.value === 'harga_jual') return b.harga_jual - a.harga_jual;
    if (sortBy.value === 'stok')       return b.stok - a.stok;
    return b.terjual - a.terjual; // default: terlaris
  });
});

const totalPages = computed(() => Math.max(1, Math.ceil(sortedProduk.value.length / perPage)));

const paginatedProduk = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return sortedProduk.value.slice(start, start + perPage);
});

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};
</script>
