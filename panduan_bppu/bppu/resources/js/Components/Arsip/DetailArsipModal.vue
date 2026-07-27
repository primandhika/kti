<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white rounded-t-xl">
        <h2 class="text-xl font-bold text-gray-800">Detail File</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div v-if="arsip" class="p-6 space-y-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <div v-if="arsip.jenis === 'image'" class="mb-4">
            <img :src="arsip.url" :alt="arsip.alt_text || arsip.nama_file" class="max-w-full h-auto rounded-lg" />
          </div>
          <div v-else class="flex items-center justify-center py-12 bg-white rounded-lg">
            <div class="text-center">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              <p class="text-sm text-gray-600">{{ arsip.nama_file }}</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Nama File</label>
            <p class="text-sm text-gray-900">{{ arsip.nama_file }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Jenis</label>
            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getJenisBadgeClass(arsip.jenis)">
              {{ arsip.jenis }}
            </span>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Arsip</label>
            <p class="text-sm text-gray-900">{{ formatKategori(arsip.kategori_arsip) }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Folder</label>
            <p class="text-sm text-gray-900">{{ arsip.folder || '-' }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Ukuran</label>
            <p class="text-sm text-gray-900">{{ arsip.ukuran_readable }}</p>
          </div>
          <div v-if="arsip.original_dimensions">
            <label class="block text-xs font-medium text-gray-500 mb-1">Dimensi</label>
            <p class="text-sm text-gray-900">{{ arsip.original_dimensions }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Upload Oleh</label>
            <p class="text-sm text-gray-900">{{ arsip.uploaded_by || 'System' }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Upload</label>
            <p class="text-sm text-gray-900">{{ formatDate(arsip.created_at) }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status Akses</label>
            <span :class="[
              'inline-flex px-2 py-1 text-xs font-medium rounded-full',
              arsip.is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
            ]">
              {{ arsip.is_public ? 'Publik' : 'Private' }}
            </span>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
            <a :href="arsip.url" target="_blank" class="text-sm text-[#996600] hover:text-[#7a5100] break-all">
              Lihat File
            </a>
          </div>
        </div>

        <div v-if="arsip.tags && arsip.tags.length > 0" class="pt-3 border-t border-gray-200">
          <label class="block text-xs font-medium text-gray-500 mb-2">Tags</label>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in arsip.tags"
              :key="tag"
              class="inline-flex px-3 py-1 bg-[#996600] text-white text-xs rounded-full"
            >
              {{ tag }}
            </span>
          </div>
        </div>

        <div v-if="arsip.deskripsi || arsip.alt_text || arsip.caption" class="space-y-3 pt-3 border-t border-gray-200">
          <div v-if="arsip.deskripsi">
            <label class="block text-xs font-medium text-gray-500 mb-1">Deskripsi</label>
            <p class="text-sm text-gray-900">{{ arsip.deskripsi }}</p>
          </div>
          <div v-if="arsip.alt_text">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alt Text</label>
            <p class="text-sm text-gray-900">{{ arsip.alt_text }}</p>
          </div>
          <div v-if="arsip.caption">
            <label class="block text-xs font-medium text-gray-500 mb-1">Caption</label>
            <p class="text-sm text-gray-900">{{ arsip.caption }}</p>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            Digunakan Di ({{ loadingUsages ? '...' : usages.length }})
          </h3>
          <div v-if="loadingUsages" class="text-center py-4">
            <p class="text-sm text-gray-500">Memuat...</p>
          </div>
          <div v-else-if="usages.length > 0" class="space-y-2">
            <div
              v-for="usage in usages"
              :key="usage.type + usage.name"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center space-x-3">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded" :class="getUsageBadgeClass(usage.context)">
                  {{ usage.label || usage.type }}
                </span>
                <span class="text-sm text-gray-900">{{ usage.name }}</span>
              </div>
              <a
                v-if="usage.url"
                :href="usage.url"
                class="text-xs text-[#996600] hover:text-[#7a5100] font-medium"
              >
                Lihat
              </a>
            </div>
          </div>
          <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-sm text-gray-500">File belum digunakan</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  show: Boolean,
  arsip: Object,
});

const emit = defineEmits(['close']);

const usages = ref([]);
const loadingUsages = ref(false);

const getUsageBadgeClass = (context) => {
  const classes = {
    post:    'bg-blue-100 text-blue-800',
    page:    'bg-purple-100 text-purple-800',
    kantin:  'bg-orange-100 text-orange-800',
    pos:     'bg-green-100 text-green-800',
    belanja: 'bg-teal-100 text-teal-800',
    other:   'bg-gray-100 text-gray-700',
  };
  return classes[context] || classes.other;
};

const getJenisBadgeClass = (jenis) => {
  const classes = {
    image: 'bg-purple-100 text-purple-800',
    pdf: 'bg-red-100 text-red-800',
    document: 'bg-blue-100 text-blue-800',
    video: 'bg-green-100 text-green-800',
    audio: 'bg-yellow-100 text-yellow-800',
    other: 'bg-gray-100 text-gray-800',
  };
  return classes[jenis] || classes.other;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatKategori = (kategori) => {
  const map = {
    'gambar_artikel': 'Gambar Artikel',
    'gambar_produk': 'Gambar Produk',
    'gambar_kegiatan': 'Foto Kegiatan',
    'gambar_profil': 'Gambar Profil',
    'dokumen_resmi': 'Dokumen Resmi',
    'dokumen_laporan': 'Dokumen Laporan',
    'dokumen_sk': 'Surat Keputusan',
    'dokumen_surat': 'Surat Menyurat',
    'media_promosi': 'Media Promosi',
    'template': 'Template',
    'lainnya': 'Lainnya',
  };
  return map[kategori] || kategori;
};

const fetchUsages = async () => {
  if (!props.arsip?.id) return;

  usages.value = [];
  loadingUsages.value = true;
  try {
    const response = await axios.get(`/pengelola/arsip/${props.arsip.id}/usage`);
    usages.value = response.data.usages;
  } catch (error) {
    console.error('Error fetching usages:', error);
  } finally {
    loadingUsages.value = false;
  }
};

watch([() => props.show, () => props.arsip?.id], ([show]) => {
  if (show && props.arsip?.id) {
    fetchUsages();
  }
});
</script>
