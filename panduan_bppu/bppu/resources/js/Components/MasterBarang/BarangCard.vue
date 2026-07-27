<template>
  <div class="md:hidden space-y-3">
    <div
      v-for="barang in barangs"
      :key="barang.id"
      class="bg-white rounded-lg shadow-md p-3"
    >
      <div class="flex items-start gap-3 mb-2">
        <!-- Checkbox -->
        <div class="pt-1">
          <input
            type="checkbox"
            :checked="selectedIds.includes(barang.id)"
            @change="$emit('toggle-select', barang.id)"
            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
          />
        </div>

        <!-- Thumbnail -->
        <button
          @click="$emit('open-image', barang)"
          class="flex-shrink-0 w-12 h-12 rounded overflow-hidden border border-gray-200 hover:border-[#996600] transition-colors bg-gray-50"
          title="Foto produk"
        >
          <img v-if="barang.gambar" :src="barang.gambar" :alt="barang.nama_barang" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </button>

        <div class="flex-1 min-w-0">
          <div class="flex items-center space-x-2 mb-1">
            <span class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ barang.kode_barang }}</span>
            <span
              v-if="barang.kategori"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
            >
              {{ barang.kategori }}
            </span>
          </div>
          <h3 class="font-semibold text-gray-900 text-sm">{{ barang.nama_barang }}</h3>
          <p v-if="barang.deskripsi" class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ barang.deskripsi }}</p>
          <div v-if="barang.varians && barang.varians.length > 0" class="mt-1 space-y-0.5">
            <div v-for="varian in barang.varians" :key="varian.id" class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded">
              <span class="font-medium">{{ varian.nama_varian }}</span>
              <span class="text-purple-500 ml-1">{{ formatCurrency(varian.harga_jual) }}</span>
            </div>
          </div>
        </div>
        <button
          type="button"
          @click="$emit('pengajuan', barang)"
          :class="[
            'inline-flex items-center px-2 py-1 rounded-full text-xs font-bold ml-2 cursor-pointer hover:opacity-80 transition-opacity',
            barang.stok <= barang.minimum_stok
              ? 'bg-red-100 text-red-800'
              : 'bg-green-100 text-green-800'
          ]"
          title="Klik untuk mengajukan perubahan stok"
        >
          {{ barang.stok }}
          <svg v-if="barang.stok <= barang.minimum_stok" class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-2 gap-2 text-xs mb-2 bg-gray-50 p-2 rounded">
        <div>
          <span class="text-gray-600">Satuan:</span>
          <span class="font-medium text-gray-900 ml-1">{{ barang.satuan }}</span>
        </div>
        <div>
          <div class="flex flex-col">
            <div>
              <span class="text-gray-600">Harga Awal:</span>
              <span class="font-medium text-gray-900 ml-1">{{ getHargaAwal(barang) }}</span>
            </div>
            <span class="text-[9px] text-gray-500 mt-0.5">{{ getLabelHargaAwal(barang) }}</span>
          </div>
        </div>
        <div>
          <span class="text-gray-600">Harga Jual:</span>
          <span class="font-medium text-gray-900 ml-1">{{ formatCurrency(barang.harga_jual) }}</span>
          <span v-if="barang.diskon_aktif" class="ml-1 text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">DISKON AKTIF</span>
        </div>
        <div v-if="barang.diskon_tipe" class="col-span-2">
          <span class="text-gray-600">Diskon:</span>
          <span class="font-medium ml-1" :class="barang.diskon_aktif ? 'text-green-700' : 'text-gray-500'">
            <template v-if="barang.diskon_tipe === 'persen'">{{ barang.diskon_nilai }}%</template>
            <template v-else-if="barang.diskon_tipe === 'nominal'">- {{ formatCurrency(barang.diskon_nilai) }}</template>
            <template v-else-if="barang.diskon_tipe === 'harga_menjadi'">menjadi {{ formatCurrency(barang.diskon_nilai) }}</template>
            <span class="text-gray-500 font-normal ml-1">
              <template v-if="barang.diskon_mulai && barang.diskon_selesai">({{ barang.diskon_mulai }} s/d {{ barang.diskon_selesai }})</template>
              <template v-else-if="barang.diskon_mulai">(mulai {{ barang.diskon_mulai }})</template>
              <template v-else-if="barang.diskon_selesai">(s/d {{ barang.diskon_selesai }})</template>
              <template v-else>(tanpa batas)</template>
            </span>
          </span>
        </div>
        <div v-if="barang.supplier?.nama" class="col-span-2">
          <span class="text-gray-600">Mitra:</span>
          <span class="font-medium text-gray-900 ml-1">{{ barang.supplier.nama }}</span>
        </div>
        <div v-if="barang.created_by && barang.created_by.name" class="col-span-2">
          <span class="text-gray-600">Pengentri:</span>
          <span class="font-medium text-gray-900 ml-1">{{ barang.created_by.name }}</span>
        </div>
      </div>

      <div class="flex space-x-2">
        <button
          @click="$emit('print-single', barang.id)"
          class="flex items-center justify-center px-3 py-1.5 text-xs bg-purple-50 text-purple-700 rounded hover:bg-purple-100 transition-colors"
          title="Print Price Tag"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
        </button>
        <button
          @click="$emit('opname', barang)"
          class="flex-1 flex items-center justify-center space-x-1 px-3 py-1.5 text-xs bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
          <span>Opname</span>
        </button>
        <button
          @click="$emit('duplicate', barang)"
          class="flex items-center justify-center px-3 py-1.5 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors"
          title="Duplikat"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
        </button>
        <button
          @click="$emit('edit', barang)"
          class="flex-1 flex items-center justify-center space-x-1 px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <span>Edit</span>
        </button>
        <button
          @click="$emit('delete', barang)"
          class="flex items-center justify-center px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Empty State (Mobile) -->
    <div v-if="barangs.length === 0" class="bg-white rounded-lg shadow-md p-8 text-center">
      <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <p class="text-sm text-gray-500">{{ emptyMessage }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  barangs: Array,
  emptyMessage: String,
  selectedIds: {
    type: Array,
    default: () => []
  }
});

defineEmits(['opname', 'duplicate', 'edit', 'delete', 'toggle-select', 'print-single', 'open-image', 'pengajuan']);

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
