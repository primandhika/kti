<template>
  <div class="p-6 space-y-4">
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-700">
            Pilih unit kerja lain untuk mengimpor barang. Data barang akan disalin ke unit kerja ini dengan kode barang yang baru.
          </p>
        </div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja Sumber *</label>
      <select
        :value="importForm.source_work_unit_id"
        @change="$emit('load-barangs', $event.target.value)"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
      >
        <option value="">-- Pilih Unit Kerja --</option>
        <option v-for="unit in otherWorkUnits" :key="unit.id" :value="unit.id">
          {{ unit.name }} {{ unit.location ? `(${unit.location})` : '' }}
        </option>
      </select>
    </div>

    <div v-if="loadingBarangs" class="text-center py-8">
      <svg class="animate-spin h-8 w-8 mx-auto text-[#996600]" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-sm text-gray-600 mt-2">Memuat data barang...</p>
    </div>

    <div v-if="availableBarangs.length > 0 && !loadingBarangs" class="space-y-4">
      <div class="flex items-center justify-between">
        <label class="block text-sm font-medium text-gray-700">Pilih Barang yang Akan Diimpor *</label>
        <button
          type="button"
          @click="$emit('toggle-select-all')"
          class="text-sm text-[#996600] hover:text-[#7a5100] font-medium"
        >
          {{ importForm.selected_barang_ids.length === filteredBarangs.length ? 'Batalkan Semua' : 'Pilih Semua' }}
        </button>
      </div>

      <!-- Search Box -->
      <div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari barang (nama, kode, kategori)..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>

      <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
        <div
          v-for="barang in filteredBarangs"
          :key="barang.id"
          class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
        >
          <input
            type="checkbox"
            :id="`barang-${barang.id}`"
            :value="barang.id"
            :checked="importForm.selected_barang_ids.includes(barang.id)"
            @change="toggleBarang(barang.id)"
            class="w-4 h-4 text-[#996600] border-gray-300 rounded focus:ring-[#996600]"
          />

          <!-- Image Preview -->
          <div v-if="barang.gambar" class="ml-3 flex-shrink-0">
            <img
              :src="barang.gambar"
              :alt="barang.nama_barang"
              class="w-12 h-12 object-cover rounded border border-gray-200"
              @error="handleImageError"
            />
          </div>
          <div v-else class="ml-3 flex-shrink-0">
            <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>

          <label :for="`barang-${barang.id}`" class="ml-3 flex-1 cursor-pointer">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ barang.nama_barang }}</p>
                <p class="text-xs text-gray-500">
                  {{ barang.kode_barang }} • {{ barang.kategori || '-' }} • {{ formatCurrency(barang.harga_jual) }}
                </p>
              </div>
              <span class="ml-2 text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-800">
                Stok: {{ barang.stok }}
              </span>
            </div>
          </label>
        </div>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg">
        <p class="text-sm text-gray-700">
          <span class="font-semibold">{{ importForm.selected_barang_ids.length }}</span> barang dipilih dari
          <span class="font-semibold">{{ filteredBarangs.length }}</span> barang ditampilkan
          <span v-if="searchQuery">({{ availableBarangs.length }} total)</span>
        </p>
      </div>
    </div>

    <div v-if="availableBarangs.length === 0 && importForm.source_work_unit_id && !loadingBarangs" class="text-center py-8">
      <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <p class="text-sm text-gray-500">Tidak ada barang di unit kerja yang dipilih</p>
    </div>

    <div class="flex justify-end space-x-3 pt-4 border-t">
      <button
        type="button"
        @click="$emit('close')"
        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
      >
        Batal
      </button>
      <button
        type="button"
        @click="$emit('import')"
        :disabled="importForm.selected_barang_ids.length === 0 || importingBarangs"
        class="px-6 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
      >
        <svg v-if="importingBarangs" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ importingBarangs ? 'Mengimpor...' : `Impor ${importForm.selected_barang_ids.length} Barang` }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  otherWorkUnits: Array,
  availableBarangs: Array,
  loadingBarangs: Boolean,
  importingBarangs: Boolean,
  importForm: Object,
});

const emit = defineEmits(['load-barangs', 'toggle-select-all', 'import', 'close']);

const searchQuery = ref('');

const filteredBarangs = computed(() => {
  if (!searchQuery.value) return props.availableBarangs;

  const query = searchQuery.value.toLowerCase();
  return props.availableBarangs.filter(barang => {
    return barang.nama_barang.toLowerCase().includes(query) ||
           barang.kode_barang.toLowerCase().includes(query) ||
           (barang.kategori && barang.kategori.toLowerCase().includes(query));
  });
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

const toggleBarang = (barangId) => {
  const index = props.importForm.selected_barang_ids.indexOf(barangId);
  if (index > -1) {
    props.importForm.selected_barang_ids.splice(index, 1);
  } else {
    props.importForm.selected_barang_ids.push(barangId);
  }
};

const handleImageError = (event) => {
  event.target.style.display = 'none';
  event.target.parentElement.innerHTML = `
    <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
    </div>
  `;
};
</script>
