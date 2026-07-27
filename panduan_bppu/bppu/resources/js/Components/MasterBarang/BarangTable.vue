<template>
  <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
              <input
                type="checkbox"
                :checked="isAllSelected"
                @change="$emit('toggle-select-all')"
                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Kode</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Nama Barang</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Kategori</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Mitra</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Stok</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">Satuan</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Harga Awal</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Harga Jual</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Pengentri</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="barang in barangs" :key="barang.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-center">
              <input
                type="checkbox"
                :checked="selectedIds.includes(barang.id)"
                @change="$emit('toggle-select', barang.id)"
                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
            </td>
            <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ barang.kode_barang }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <button
                  @click="$emit('open-image', barang)"
                  class="flex-shrink-0 w-9 h-9 rounded overflow-hidden border border-gray-200 hover:border-[#996600] transition-colors bg-gray-50"
                  title="Foto produk"
                >
                  <img v-if="barang.gambar" :src="barang.gambar" :alt="barang.nama_barang" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </button>
                <div class="min-w-0">
                  <div class="text-sm font-medium text-gray-900">{{ barang.nama_barang }}</div>
                  <div v-if="barang.deskripsi" class="text-xs text-gray-500 truncate max-w-xs">{{ barang.deskripsi }}</div>

              <!-- Varian Accordion/Collapsible -->
              <div v-if="barang.varians && barang.varians.length > 0" class="mt-1">
                <button
                  @click.stop="toggleVarian(barang.id)"
                  class="text-xs text-purple-600 hover:text-purple-800 font-medium flex items-center gap-1"
                >
                  <svg
                    :class="['w-3 h-3 transition-transform', expandedVarians.includes(barang.id) ? 'rotate-90' : '']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  {{ barang.varians.length }} Varian
                </button>

                <transition name="expand">
                  <div v-show="expandedVarians.includes(barang.id)" class="mt-1 space-y-1 pl-4">
                    <div
                      v-for="varian in barang.varians"
                      :key="varian.id"
                      class="text-xs text-purple-700 flex items-start gap-1"
                    >
                      <span class="text-purple-400">•</span>
                      <div class="flex-1 min-w-0">
                        <span class="font-medium">{{ varian.nama_varian }}</span>
                        <span class="text-purple-500 ml-1">{{ formatCurrency(varian.harga_jual) }}</span>
                      </div>
                    </div>
                  </div>
                </transition>
              </div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ barang.kategori || '-' }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">
              {{ barang.supplier?.nama || '-' }}
            </td>
            <td class="px-4 py-3">
              <button
                type="button"
                @click="$emit('pengajuan', barang)"
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer hover:opacity-80 transition-opacity',
                  barang.stok <= barang.minimum_stok
                    ? 'bg-red-100 text-red-800'
                    : 'bg-green-100 text-green-800'
                ]"
                title="Klik untuk mengajukan perubahan stok"
              >
                {{ barang.stok }}
                <svg v-if="barang.stok <= barang.minimum_stok" class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">{{ barang.satuan }}</td>
            <td class="px-4 py-3">
              <div class="text-sm text-right font-medium text-gray-900">{{ getHargaAwal(barang) }}</div>
              <div class="text-right text-[9px] text-gray-500 mt-0.5">{{ getLabelHargaAwal(barang) }}</div>
            </td>
            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ formatCurrency(barang.harga_jual) }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">
              <div v-if="barang.created_by && barang.created_by.name" class="flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate max-w-[100px]" :title="barang.created_by.name">{{ barang.created_by.name }}</span>
              </div>
              <span v-else class="text-gray-400 text-xs">-</span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <button
                  @click="$emit('print-single', barang.id)"
                  class="p-1.5 text-purple-600 hover:bg-purple-50 rounded transition-colors"
                  title="Print Price Tag"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                </button>
                <button
                  @click="$emit('opname', barang)"
                  class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                  title="Stock Opname"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                  </svg>
                </button>
                <button
                  @click="$emit('duplicate', barang)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded transition-colors"
                  title="Duplikat"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </button>
                <button
                  @click="$emit('edit', barang)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  @click="$emit('delete', barang)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors"
                  title="Hapus"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="barangs.length === 0">
            <td colspan="11" class="px-4 py-8 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-sm">{{ emptyMessage }}</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  barangs: Array,
  emptyMessage: String,
  selectedIds: {
    type: Array,
    default: () => []
  }
});

defineEmits(['opname', 'duplicate', 'edit', 'delete', 'toggle-select', 'toggle-select-all', 'print-single', 'open-image', 'pengajuan']);

const isAllSelected = computed(() => {
  return props.barangs.length > 0 && props.selectedIds.length === props.barangs.length;
});

const expandedVarians = ref([]);

const toggleVarian = (barangId) => {
  const index = expandedVarians.value.indexOf(barangId);
  if (index > -1) {
    expandedVarians.value.splice(index, 1);
  } else {
    expandedVarians.value.push(barangId);
  }
};

const formatCurrency = (value) => {
  if (value === null || value === undefined || isNaN(value)) {
    return 'Rp 0';
  }
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

const getHargaAwal = (barang) => {
  // Untuk konsinyasi, dropship_internal, preorder: harga awal = harga jual - harga konsinyasi (bagian vendor)
  if (['konsinyasi', 'dropship_internal', 'preorder'].includes(barang.jenis_barang)) {
    const hargaJual = barang.harga_jual || 0;
    const hargaKonsinyasi = barang.harga_konsinyasi || 0;
    return formatCurrency(hargaJual - hargaKonsinyasi);
  }
  // Untuk jual_langsung, bundling, langganan: gunakan harga_beli
  return formatCurrency(barang.harga_beli || 0);
};

const getLabelHargaAwal = (barang) => {
  // Untuk konsinyasi, dropship_internal, preorder: label "konsinyasi"
  if (['konsinyasi', 'dropship_internal', 'preorder'].includes(barang.jenis_barang)) {
    return 'konsinyasi';
  }
  // Untuk jual_langsung, bundling, langganan: label "harga produksi"
  return 'harga produksi';
};

</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.expand-enter-to,
.expand-leave-from {
  max-height: 200px;
  opacity: 1;
}
</style>
