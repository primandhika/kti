<template>
  <AdminLayout
    pageTitle="Arsip Media"
    :pageSubtitle="`${stats.total} file (${stats.images} gambar, ${stats.documents} dokumen)`"
  >
    <div class="space-y-4 pb-24 md:pb-6">
      <!-- Filters & Upload - Desktop -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm p-3">
        <div class="flex items-center gap-3">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              @input="handleSearch"
              type="text"
              placeholder="Cari file berdasarkan nama, tags, atau deskripsi..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>
          <select
            v-model="kategoriFilter"
            @change="handleFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option value="">Semua Kategori</option>
            <option value="gambar_artikel">Gambar Artikel</option>
            <option value="gambar_produk">Gambar Produk</option>
            <option value="gambar_kegiatan">Foto Kegiatan</option>
            <option value="gambar_profil">Gambar Profil</option>
            <option value="dokumen_resmi">Dokumen Resmi</option>
            <option value="dokumen_laporan">Dokumen Laporan</option>
            <option value="dokumen_sk">Surat Keputusan</option>
            <option value="dokumen_surat">Surat Menyurat</option>
            <option value="media_promosi">Media Promosi</option>
            <option value="template">Template</option>
            <option value="lainnya">Lainnya</option>
          </select>
          <select
            v-model="sortBy"
            @change="handleSort"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option value="created_at">Terbaru</option>
            <option value="nama_file">Nama (A-Z)</option>
            <option value="ukuran">Ukuran</option>
            <option value="kategori_arsip">Kategori</option>
          </select>
          <button
            @click="showUploadModal = true"
            class="bg-[#996600] hover:bg-[#7a5100] text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span>Upload</span>
          </button>
        </div>
      </div>

      <!-- Filters - Mobile -->
      <div class="md:hidden bg-white rounded-lg shadow-sm p-3 space-y-3">
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Cari file..."
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
        />
        <div class="flex items-center gap-2">
          <select
            v-model="kategoriFilter"
            @change="handleFilter"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
          >
            <option value="">Semua</option>
            <option value="gambar_artikel">Artikel</option>
            <option value="gambar_produk">Produk</option>
            <option value="gambar_kegiatan">Kegiatan</option>
            <option value="gambar_profil">Profil</option>
            <option value="dokumen_resmi">Dokumen</option>
            <option value="dokumen_laporan">Laporan</option>
            <option value="dokumen_sk">SK</option>
            <option value="dokumen_surat">Surat</option>
            <option value="media_promosi">Promosi</option>
            <option value="template">Template</option>
            <option value="lainnya">Lainnya</option>
          </select>
          <select
            v-model="sortBy"
            @change="handleSort"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
          >
            <option value="created_at">Terbaru</option>
            <option value="nama_file">Nama</option>
            <option value="ukuran">Ukuran</option>
          </select>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Preview</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama File</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Folder</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Ukuran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Upload Oleh</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in arsip.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center">
                    <div v-if="item.jenis === 'image'" class="w-12 h-12 rounded overflow-hidden">
                      <img :src="item.url" :alt="item.alt_text || item.nama_file" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm font-medium text-gray-900">{{ item.nama_file }}</div>
                  <div v-if="item.deskripsi" class="text-xs text-gray-500 mt-1">{{ truncate(item.deskripsi, 50) }}</div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    {{ formatKategori(item.kategori_arsip) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  <span v-if="item.folder" class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    {{ item.folder }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ item.ukuran_readable }}</td>
                <td class="px-4 py-3 text-sm text-gray-900">{{ item.uploaded_by || 'System' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(item.created_at) }}</td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <a
                      :href="item.url"
                      target="_blank"
                      class="text-blue-600 hover:text-blue-800 transition-colors"
                      title="Lihat"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </a>
                    <button
                      @click="showDetail(item)"
                      class="text-[#996600] hover:text-[#7a5100] transition-colors"
                      title="Detail"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </button>
                    <button
                      @click="editArsip(item)"
                      class="text-green-600 hover:text-green-800 transition-colors"
                      title="Edit"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(item)"
                      :disabled="item.usages_count > 0"
                      class="text-red-600 hover:text-red-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                      :title="item.usages_count > 0 ? 'Tidak dapat dihapus, file masih digunakan' : 'Hapus'"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="arsip.data.length === 0">
                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                  <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                  <p class="text-sm">Belum ada file yang diupload</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="arsip.data.length > 0 && arsip.last_page > 1" class="border-t border-gray-200 px-4 py-3 bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              Menampilkan {{ arsip.from }} - {{ arsip.to }} dari {{ arsip.total }} file
            </div>
            <div class="flex gap-1">
              <Link
                v-for="(link, index) in arsip.links"
                :key="index"
                :href="link.url || '#'"
                preserve-state
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 text-sm border rounded transition-colors',
                  link.active
                    ? 'bg-[#996600] text-white border-[#996600]'
                    : link.url
                    ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                ]"
              >
                <span v-if="index === 0">&laquo;</span>
                <span v-else-if="index === arsip.links.length - 1">&raquo;</span>
                <span v-else>{{ link.label }}</span>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Cards -->
      <ArsipCard
        :arsip="arsip.data"
        @detail="showDetail"
        @edit="editArsip"
        @delete="confirmDelete"
      />

      <!-- Pagination (Mobile) -->
      <div v-if="arsip.data.length > 0 && arsip.last_page > 1" class="md:hidden bg-white rounded-lg shadow-sm p-3">
        <div class="flex flex-col space-y-2">
          <div class="text-xs text-gray-600 text-center">
            Halaman {{ arsip.current_page }} dari {{ arsip.last_page }} ({{ arsip.total }} file)
          </div>
          <div class="flex justify-center gap-1">
            <Link
              v-for="(link, index) in arsip.links"
              :key="index"
              :href="link.url || '#'"
              preserve-state
              :disabled="!link.url"
              :class="[
                'px-3 py-1 text-xs border rounded transition-colors',
                link.active
                  ? 'bg-[#996600] text-white border-[#996600]'
                  : link.url
                  ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                  : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
              ]"
            >
              <span v-if="index === 0">&laquo;</span>
              <span v-else-if="index === arsip.links.length - 1">&raquo;</span>
              <span v-else>{{ link.label }}</span>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <UploadArsipModal
      :show="showUploadModal"
      @close="showUploadModal = false"
      @uploaded="handleUploaded"
    />

    <EditArsipModal
      :show="showEditModal"
      :arsip="selectedArsip"
      @close="showEditModal = false"
      @updated="handleUpdated"
    />

    <DetailArsipModal
      :show="showDetailModal"
      :arsip="selectedArsip"
      @close="showDetailModal = false"
    />

    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6">
          <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-center text-gray-900 mb-2">Hapus File?</h3>
          <p class="text-sm text-gray-600 text-center mb-6">
            Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex space-x-3">
            <button
              @click="closeDeleteModal"
              class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              @click="deleteArsip"
              :disabled="processing"
              class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Menghapus...' : 'Hapus' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sticky Bottom Action (Mobile Only) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
      <div class="p-3">
        <button
          @click="showUploadModal = true"
          class="w-full bg-[#996600] hover:bg-[#7a5100] text-white px-4 py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
          <span>Upload File</span>
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UploadArsipModal from '@/Components/Arsip/UploadArsipModal.vue';
import EditArsipModal from '@/Components/Arsip/EditArsipModal.vue';
import DetailArsipModal from '@/Components/Arsip/DetailArsipModal.vue';
import ArsipCard from '@/Components/Arsip/ArsipCard.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
  arsip: Object,
  stats: Object,
  filters: Object,
});

const showUploadModal = ref(false);
const showEditModal = ref(false);
const showDetailModal = ref(false);
const showDeleteModal = ref(false);
const selectedArsip = ref(null);
const itemToDelete = ref(null);
const processing = ref(false);

const searchQuery = ref(props.filters.search || '');
const kategoriFilter = ref(props.filters.kategori_arsip || '');
const sortBy = ref(props.filters.sort_by || 'created_at');

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
    month: 'short',
    day: 'numeric',
  });
};

const truncate = (str, length) => {
  if (!str) return '';
  return str.length > length ? str.substring(0, length) + '...' : str;
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

let searchTimeout;
const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 500);
};

const handleFilter = () => {
  applyFilters();
};

const handleSort = () => {
  applyFilters();
};

const applyFilters = () => {
  router.get('/pengelola/arsip', {
    search: searchQuery.value,
    kategori_arsip: kategoriFilter.value,
    sort_by: sortBy.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const showDetail = (item) => {
  selectedArsip.value = item;
  showDetailModal.value = true;
};

const editArsip = (item) => {
  selectedArsip.value = item;
  showEditModal.value = true;
};

const confirmDelete = (item) => {
  itemToDelete.value = item;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  itemToDelete.value = null;
};

const deleteArsip = () => {
  processing.value = true;

  router.delete(`/pengelola/arsip/${itemToDelete.value.id}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('File berhasil dihapus');
      closeDeleteModal();
    },
    onError: (errors) => {
      console.error(errors);
      toast.error('Gagal menghapus file');
    },
    onFinish: () => {
      processing.value = false;
    },
  });
};

const handleUploaded = () => {
  router.reload({ preserveScroll: true });
};

const handleUpdated = () => {
  router.reload({ preserveScroll: true });
};
</script>
