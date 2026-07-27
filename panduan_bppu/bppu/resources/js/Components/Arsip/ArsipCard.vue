<template>
  <div class="md:hidden space-y-3">
    <div
      v-for="item in arsip"
      :key="item.id"
      class="bg-white rounded-lg shadow-md overflow-hidden"
    >
      <div class="flex items-start gap-3 p-3">
        <div v-if="item.jenis === 'image'" class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
          <img :src="item.url" :alt="item.alt_text || item.nama_file" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded flex-shrink-0">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center space-x-1 mb-1">
            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
              {{ formatKategori(item.kategori_arsip) }}
            </span>
          </div>
          <h3 class="font-semibold text-gray-900 text-sm line-clamp-1">{{ item.nama_file }}</h3>
          <p v-if="item.deskripsi" class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ item.deskripsi }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2 text-xs px-3 pb-3 bg-gray-50">
        <div v-if="item.folder" class="col-span-2">
          <span class="text-gray-600">Folder:</span>
          <span class="font-medium text-gray-900 ml-1">{{ item.folder }}</span>
        </div>
        <div>
          <span class="text-gray-600">Ukuran:</span>
          <span class="font-medium text-gray-900 ml-1">{{ item.ukuran_readable }}</span>
        </div>
        <div>
          <span class="text-gray-600">Upload:</span>
          <span class="font-medium text-gray-900 ml-1">{{ formatDate(item.created_at) }}</span>
        </div>
        <div v-if="item.uploaded_by" class="col-span-2">
          <span class="text-gray-600">Oleh:</span>
          <span class="font-medium text-gray-900 ml-1">{{ item.uploaded_by }}</span>
        </div>
      </div>

      <div class="flex space-x-2 p-3 bg-white border-t border-gray-100">
        <a
          :href="item.url"
          target="_blank"
          class="flex items-center justify-center px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors"
          title="Lihat"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
        </a>
        <button
          @click="$emit('detail', item)"
          class="flex-1 flex items-center justify-center space-x-1 px-3 py-1.5 text-xs bg-[#996600] bg-opacity-10 text-[#996600] rounded hover:bg-opacity-20 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Detail</span>
        </button>
        <button
          @click="$emit('edit', item)"
          class="flex-1 flex items-center justify-center space-x-1 px-3 py-1.5 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <span>Edit</span>
        </button>
        <button
          @click="$emit('delete', item)"
          :disabled="item.usages_count > 0"
          class="flex items-center justify-center px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :title="item.usages_count > 0 ? 'Masih digunakan' : 'Hapus'"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Empty State (Mobile) -->
    <div v-if="arsip.length === 0" class="bg-white rounded-lg shadow-md p-8 text-center">
      <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
      </svg>
      <p class="text-sm text-gray-500">Belum ada file yang diupload</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  arsip: {
    type: Array,
    required: true,
  },
});

defineEmits(['detail', 'edit', 'delete']);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatKategori = (kategori) => {
  const map = {
    'gambar_artikel': 'Artikel',
    'gambar_produk': 'Produk',
    'gambar_kegiatan': 'Kegiatan',
    'gambar_profil': 'Profil',
    'dokumen_resmi': 'Dokumen',
    'dokumen_laporan': 'Laporan',
    'dokumen_sk': 'SK',
    'dokumen_surat': 'Surat',
    'media_promosi': 'Promosi',
    'template': 'Template',
    'lainnya': 'Lainnya',
  };
  return map[kategori] || kategori;
};
</script>
