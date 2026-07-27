<template>
  <AdminLayout
    pageTitle="Master Barang"
    :pageSubtitle="`${toko.name}${toko.location ? ' - ' + toko.location : ''}`"
  >
    <div class="space-y-4 pb-24 md:pb-6">
      <!-- Header Actions & Filters - Desktop -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm p-3">
        <div class="flex flex-wrap items-center gap-2 lg:gap-3">
          <!-- Back Button -->
          <Link
            href="/pengelola/opname-stock"
            class="inline-flex items-center text-[#996600] hover:text-[#7a5100] text-sm font-medium whitespace-nowrap"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
          </Link>

          <div class="hidden xl:block h-6 w-px bg-gray-300"></div>

          <!-- Action Buttons -->
          <Link
            :href="`/pengelola/opname-stock/${toko.id}`"
            class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 text-sm whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Laporan
          </Link>
          <button
            @click="showImportModal = true"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 text-sm whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Impor
          </button>
          <button
            @click="showAddModal = true"
            class="bg-[#996600] hover:bg-[#7a5100] text-white px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 text-sm whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah
          </button>

          <div class="hidden xl:block h-6 w-px bg-gray-300"></div>

          <!-- Search Input -->
          <div class="flex-1 basis-full min-w-0 lg:basis-72 xl:basis-80">
            <input
              ref="searchInput"
              v-model="searchQuery"
              type="text"
              placeholder="Cari barang..."
              class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            />
          </div>

          <!-- Kategori Select -->
          <select
            v-model="filterKategori"
            class="w-44 flex-none px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
          >
            <option value="">Semua</option>
            <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
              {{ kategori.nama }}
            </option>
          </select>

          <!-- Toggle Active -->
          <label class="flex-none flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg cursor-pointer hover:bg-amber-100 transition-colors">
            <input
              v-model="showActiveOnly"
              type="checkbox"
              class="w-4 h-4 text-amber-600 border-amber-300 rounded focus:ring-amber-600"
            />
            <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Aktif</span>
          </label>

          <!-- Toggle Ajuan -->
          <label class="flex-none flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition-colors">
            <input
              v-model="filterAjuan"
              type="checkbox"
              class="w-4 h-4 text-red-500 border-red-300 rounded focus:ring-red-500"
            />
            <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Ajuan</span>
          </label>

          <!-- Toggle Ada Stok -->
          <label class="flex-none flex items-center gap-2 px-3 py-1.5 bg-green-50 border border-green-200 rounded-lg cursor-pointer hover:bg-green-100 transition-colors">
            <input
              v-model="filterHasStok"
              type="checkbox"
              class="w-4 h-4 text-green-600 border-green-300 rounded focus:ring-green-600"
            />
            <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Ada Stok</span>
          </label>

          <label class="flex-none flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg cursor-pointer hover:bg-amber-100 transition-colors">
            <input
              v-model="filterMenuKantin"
              type="checkbox"
              class="sr-only"
            />
            <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Kantin</span>
            <span :class="[
              'relative inline-flex h-5 w-10 items-center rounded-full transition-colors duration-200',
              filterMenuKantin ? 'bg-[#996600]' : 'bg-gray-300'
            ]">
              <span :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200',
                filterMenuKantin ? 'translate-x-5' : 'translate-x-0.5'
              ]"></span>
            </span>
          </label>

          <!-- Info -->
          <div class="flex items-center gap-2 text-xs text-gray-600 whitespace-nowrap border-l pl-3 border-gray-300">
            <span>{{ barangs.total }} barang</span>
            <span class="text-gray-400">•</span>
            <span>Hal {{ barangs.current_page }}/{{ barangs.last_page }}</span>
          </div>
        </div>
      </div>

      <!-- Mobile Header - Back Button -->
      <div class="md:hidden bg-white rounded-lg shadow-sm p-3">
        <Link
          href="/pengelola/opname-stock"
          class="inline-flex items-center text-[#996600] hover:text-[#7a5100] text-sm font-medium"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </Link>
      </div>

      <!-- Mobile Filters -->
      <div class="md:hidden bg-white rounded-lg shadow-sm p-3 space-y-3">
        <div class="flex items-center justify-between text-sm text-gray-600">
          <span class="font-medium">{{ barangs?.total || 0 }} barang</span>
          <span>Hal {{ barangs?.current_page || 1 }}/{{ barangs?.last_page || 1 }}</span>
        </div>

        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari barang..."
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
        />

        <div class="flex items-center gap-2">
          <select
            v-model="filterKategori"
            class="flex-1 min-w-0 px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-xs"
          >
            <option value="">Semua</option>
            <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
              {{ kategori.nama }}
            </option>
          </select>

          <label class="flex items-center gap-1.5 px-2 py-2 bg-amber-50 border border-amber-200 rounded-lg cursor-pointer whitespace-nowrap">
            <input
              v-model="showActiveOnly"
              type="checkbox"
              class="w-3.5 h-3.5 text-amber-600 border-amber-300 rounded focus:ring-amber-600"
            />
            <span class="text-xs font-medium text-gray-700">Aktif</span>
          </label>
          <label class="flex items-center gap-1.5 px-2 py-2 bg-red-50 border border-red-200 rounded-lg cursor-pointer whitespace-nowrap">
            <input
              v-model="filterAjuan"
              type="checkbox"
              class="w-3.5 h-3.5 text-red-500 border-red-300 rounded focus:ring-red-500"
            />
            <span class="text-xs font-medium text-gray-700">Ajuan</span>
          </label>
          <label class="flex items-center gap-1.5 px-2 py-2 bg-green-50 border border-green-200 rounded-lg cursor-pointer whitespace-nowrap">
            <input
              v-model="filterHasStok"
              type="checkbox"
              class="w-3.5 h-3.5 text-green-600 border-green-300 rounded focus:ring-green-600"
            />
            <span class="text-xs font-medium text-gray-700">Ada Stok</span>
          </label>
          <label class="flex items-center gap-1.5 px-2 py-2 bg-amber-50 border border-amber-200 rounded-lg cursor-pointer whitespace-nowrap">
            <input
              v-model="filterMenuKantin"
              type="checkbox"
              class="sr-only"
            />
            <span class="text-xs font-medium text-gray-700">Kantin</span>
            <span :class="[
              'relative inline-flex h-4 w-8 items-center rounded-full transition-colors duration-200',
              filterMenuKantin ? 'bg-[#996600]' : 'bg-gray-300'
            ]">
              <span :class="[
                'inline-block h-3 w-3 transform rounded-full bg-white shadow-sm transition-transform duration-200',
                filterMenuKantin ? 'translate-x-4' : 'translate-x-0.5'
              ]"></span>
            </span>
          </label>
        </div>
      </div>

      <!-- Alert pengajuan tambah barang dari kantin -->
      <PengajuanTambahBarangAlert :work-unit-id="toko.id" :kategoris="kategoris" />

      <!-- Desktop Table -->
      <BarangTable
        :barangs="filteredBarangs"
        :empty-message="emptyMessage"
        :selected-ids="selectedBarangsForPrint"
        @opname="showOpnameModal"
        @duplicate="duplicateBarang"
        @edit="editBarang"
        @delete="confirmDelete"
        @toggle-select="toggleSelectBarang"
        @toggle-select-all="toggleSelectAllBarangs"
        @print-single="printSinglePriceTag"
        @open-image="openImageModal"
        @pengajuan="openPengajuanModal"
      />

      <!-- Mobile Cards -->
      <BarangCard
        :barangs="filteredBarangs"
        :empty-message="emptyMessage"
        :selected-ids="selectedBarangsForPrint"
        @opname="showOpnameModal"
        @duplicate="duplicateBarang"
        @edit="editBarang"
        @delete="confirmDelete"
        @toggle-select="toggleSelectBarang"
        @print-single="printSinglePriceTag"
        @open-image="openImageModal"
        @pengajuan="openPengajuanModal"
      />

      <!-- Pagination (Desktop) -->
      <div v-if="barangs?.last_page > 1" class="hidden md:block bg-white rounded-lg shadow-sm px-6 py-3">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Menampilkan {{ barangs?.from || 0 }} - {{ barangs?.to || 0 }} dari {{ barangs?.total || 0 }} barang
          </div>
          <div class="flex gap-2">
            <Link
              v-for="(link, index) in barangs.links"
              :key="index"
              :href="link.url"
              preserve-state
              :disabled="!link.url"
              :class="paginationClass(link)"
            >
              <span v-if="index === 0">Prev</span>
              <span v-else-if="index === barangs.links.length - 1">Next</span>
              <span v-else>{{ link.label }}</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Pagination (Mobile) -->
      <div v-if="barangs?.last_page > 1" class="md:hidden bg-white rounded-lg shadow-sm p-3">
        <div class="flex flex-col space-y-2">
          <div class="text-xs text-gray-600 text-center">
            Halaman {{ barangs?.current_page || 1 }} dari {{ barangs?.last_page || 1 }} ({{ barangs?.total || 0 }} barang)
          </div>
          <div class="flex justify-center gap-1">
            <Link
              v-for="(link, index) in barangs.links"
              :key="index"
              :href="link.url"
              preserve-state
              :disabled="!link.url"
              :class="paginationClassMobile(link)"
            >
              <span v-if="index === 0">&lt;</span>
              <span v-else-if="index === barangs.links.length - 1">&gt;</span>
              <span v-else>{{ link.label }}</span>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Action Bar for Bulk Actions -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="selectedBarangsForPrint.length > 0"
        class="fixed bottom-24 md:bottom-6 right-6 z-50 flex flex-col gap-2 items-end"
      >
        <!-- Approve Pengajuan (hanya muncul jika ada barang dengan pengajuan) -->
        <button
          v-if="selectedWithPengajuanCount > 0"
          @click="bulkApprovePengajuan"
          :disabled="approvingPengajuan"
          class="relative bg-[#996600] hover:bg-[#7a5100] disabled:bg-gray-400 text-white rounded-full shadow-2xl transition-all duration-300 flex items-center space-x-3 px-5 py-3.5"
        >
          <div class="absolute -top-2 -left-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white">
            {{ selectedWithPengajuanCount }}
          </div>
          <svg v-if="!approvingPengajuan" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="font-semibold whitespace-nowrap text-sm">
            {{ approvingPengajuan ? 'Memproses...' : 'Approve Pengajuan' }}
          </span>
        </button>

        <!-- Cetak Price Tag -->
        <button
          @click="printPriceTags"
          :disabled="printing"
          class="relative bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white rounded-full shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 flex items-center space-x-3 px-6 py-4"
        >
          <div class="absolute -top-2 -left-2 bg-red-500 text-white text-xs font-bold rounded-full w-7 h-7 flex items-center justify-center border-2 border-white">
            {{ selectedBarangsForPrint.length }}
          </div>
          <svg v-if="!printing" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <svg v-else class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="font-semibold whitespace-nowrap">
            {{ printing ? 'Mencetak...' : 'Cetak Price Tag' }}
          </span>
        </button>
      </div>
    </transition>

    <!-- Sticky Bottom Actions (Mobile Only) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
      <div class="grid grid-cols-3 gap-2 p-3">
        <Link
          :href="`/pengelola/opname-stock/${toko.id}`"
          class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-3 rounded-lg transition-colors flex flex-col items-center justify-center space-y-1 text-xs font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Laporan</span>
        </Link>
        <button
          @click="showImportModal = true"
          class="bg-green-600 hover:bg-green-700 text-white px-3 py-3 rounded-lg transition-colors flex flex-col items-center justify-center space-y-1 text-xs font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
          <span>Impor</span>
        </button>
        <button
          @click="showAddModal = true"
          class="bg-[#996600] hover:bg-[#7a5100] text-white px-3 py-3 rounded-lg transition-colors flex flex-col items-center justify-center space-y-1 text-xs font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah</span>
        </button>
      </div>
    </div>

    <!-- Modals -->
    <AddBarangModal
      :show="showAddModal || showEditModal"
      :is-edit-mode="showEditModal"
      :form="form"
      :kategoris="kategoris"
      :suppliers="suppliers"
      :other-work-units="otherWorkUnits"
      :users="users"
      :available-barangs="availableBarangs"
      :loading-barangs="loadingBarangs"
      :importing-barangs="importingBarangs"
      :generating-p-l-u="generatingPLU"
      :import-form="importForm"
      :editing-barang="editingBarang"
      :work-unit-id="toko.id"
      @close="closeModal"
      @submit="submitForm"
      @generate-plu="generatePLU"
      @load-barangs="loadBarangsFromUnit"
      @toggle-select-all="toggleSelectAll"
      @import="importBarangs"
    />

    <StockOpnameModal
      :show="showStockOpnameModal"
      :barang="opnameBarang"
      :form="opnameForm"
      :user-name="currentUserName"
      @close="closeOpnameModal"
      @submit="submitOpname"
    />

    <DeleteConfirmModal
      :show="showDeleteModal"
      :barang="barangToDelete"
      @close="showDeleteModal = false"
      @confirm="deleteBarang"
    />

    <!-- Import Barang Modal -->
    <ImportBarangModal
      :show="showImportModal"
      :kategoris="kategoris"
      @close="showImportModal = false"
      @import="handleImportBarang"
    />

    <!-- Pengajuan Barang Modal -->
    <PengajuanBarangModal
      :show="showPengajuanModal"
      :barang="pengajuanBarang"
      :work-unit-id="toko.id"
      @close="showPengajuanModal = false"
    />

    <!-- Image Modal -->
    <ImageModal
      :show="showImageModal"
      :selected-barang="imageModalBarang"
      :image-preview="imageModalPreview"
      :selected-file="imageModalSelectedFile"
      :is-uploading="imageModalUploading"
      @close="closeImageModal"
      @upload-image="uploadBarangImage"
      @delete-image="deleteBarangImage"
      @file-change="handleImageFileChange"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BarangTable from '@/Components/MasterBarang/BarangTable.vue';
import BarangCard from '@/Components/MasterBarang/BarangCard.vue';
import AddBarangModal from '@/Components/MasterBarang/AddBarangModal.vue';
import StockOpnameModal from '@/Components/MasterBarang/StockOpnameModal.vue';
import DeleteConfirmModal from '@/Components/MasterBarang/DeleteConfirmModal.vue';
import ImportBarangModal from '@/Components/MasterBarang/ImportBarangModal.vue';
import ImageModal from '@/Components/PoS/ImageModal.vue';
import PengajuanBarangModal from '@/Components/MasterBarang/PengajuanBarangModal.vue';
import PengajuanTambahBarangAlert from '@/Components/MasterBarang/PengajuanTambahBarangAlert.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

// Refs for autofocus
const searchInput = ref(null);

const props = defineProps({
  toko: Object,
  barangs: Object,
  kategoris: Array,
  suppliers: { type: Array, default: () => [] },
  filters: Object,
  otherWorkUnits: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] }
});

// State management
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showStockOpnameModal = ref(false);
const showImportModal = ref(false);
const barangToDelete = ref(null);
const editingBarang = ref(null);
const opnameBarang = ref(null);
const generatingPLU = ref(false);
const loadingBarangs = ref(false);
const importingBarangs = ref(false);
const availableBarangs = ref([]);
const selectedBarangsForPrint = ref([]);
const printing = ref(false);
const approvingPengajuan = ref(false);

// Pengajuan barang modal state
const showPengajuanModal = ref(false);
const pengajuanBarang = ref(null);

// Image modal state
const showImageModal = ref(false);
const imageModalBarang = ref(null);
const imageModalSelectedFile = ref(null);
const imageModalPreview = ref(null);
const imageModalUploading = ref(false);

const searchQuery = ref(props.filters?.search || '');
const filterKategori = ref(props.filters?.kategori_id || '');
const showActiveOnly = ref(props.filters?.active_only !== undefined ? props.filters.active_only : true);
const filterAjuan = ref(props.filters?.has_pengajuan || false);
const filterMenuKantin = ref(props.filters?.menu_kantin || false);
const filterHasStok = ref(props.filters?.has_stok || false);

const currentPage = usePage();
const currentUserName = computed(() => currentPage.props.auth?.user?.name || 'sysadmin');

const form = ref({
  kode_barang: '',
  nama_barang: '',
  kategori_id: '',
  satuan: 'pcs',
  stok: 0,
  harga_beli: 0,
  harga_jual: 0,
  harga_grosir: null,
  harga_konsinyasi: null,
  diskon_tipe: '',
  diskon_nilai: null,
  diskon_mulai: null,
  diskon_selesai: null,
  minimum_stok: 0,
  deskripsi: '',
  supplier_id: null,
  jenis_barang: 'jual_langsung',
  is_menu_item: false,
  is_featured: false,
  is_active: true,
  show_in_shop: false,
  tanggal_kadaluarsa: null,
  gambar: null,
  varians: [],
});

const opnameForm = ref({
  barang_id: null,
  opname_date: new Date().toISOString().slice(0, 16),
  stock_fisik: null,
  keterangan: '',
});

const importForm = ref({
  source_work_unit_id: '',
  selected_barang_ids: []
});

// Computed
const filteredBarangs = computed(() => props.barangs?.data || []);

const selectedWithPengajuanCount = computed(() => {
  return filteredBarangs.value.filter(
    b => selectedBarangsForPrint.value.includes(b.id) && b.has_pengajuan
  ).length;
});

const emptyMessage = computed(() => {
  return searchQuery.value || filterKategori.value || filterMenuKantin.value
    ? 'Tidak ada barang yang sesuai dengan pencarian'
    : 'Belum ada barang di toko ini';
});


// Watchers
watch([searchQuery, filterKategori, showActiveOnly, filterAjuan, filterMenuKantin, filterHasStok], () => {
  router.get(`/pengelola/opname-stock/${props.toko.id}/barang`, {
    search: searchQuery.value,
    kategori_id: filterKategori.value,
    active_only: showActiveOnly.value,
    has_pengajuan: filterAjuan.value || undefined,
    menu_kantin: filterMenuKantin.value || undefined,
    has_stok: filterHasStok.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  });
}, { debounce: 300 });

// Methods - Barang CRUD
const duplicateBarang = (barang) => {
  // Generate new kode_barang
  let newKode = (barang?.kode_barang ?? '').toString().trim();
  if (!newKode) {
    newKode = String(Date.now()).slice(-6);
  }

  // Check if kode contains any letters
  const hasLetters = /[a-zA-Z]/.test(newKode);

  if (hasLetters) {
    // If has letters, add (1) or increment the number in parentheses
    const match = newKode.match(/^(.+?)\((\d+)\)$/);
    if (match) {
      // Already has (n), increment it
      const base = match[1];
      const num = parseInt(match[2]) + 1;
      newKode = `${base}(${num})`;
    } else {
      // Add (1) at the end
      newKode = `${newKode}(1)`;
    }
  } else {
    // Pure numeric, add 1
    const parsed = parseInt(newKode, 10);
    newKode = Number.isNaN(parsed) ? `${newKode}-1` : (parsed + 1).toString();
  }

  // Extract created_by ID
  let createdById = null;
  if (barang.created_by) {
    if (typeof barang.created_by === 'object' && barang.created_by.id) {
      createdById = barang.created_by.id;
    } else {
      createdById = parseInt(barang.created_by);
    }
  }

  // Extract supplier_id
  let supplierId = null;
  if (barang.supplier_id) {
    supplierId = parseInt(barang.supplier_id);
  }

  form.value = {
    kode_barang: newKode,
    nama_barang: barang.nama_barang,
    kategori_id: barang.kategori_id || '',
    satuan: barang.satuan,
    stok: 0, // Reset stock for duplicate
    harga_beli: barang.harga_beli ?? 0,
    harga_jual: barang.harga_jual ?? 0,
    harga_grosir: barang.harga_grosir ?? 0,
    harga_konsinyasi: barang.harga_konsinyasi ?? 0,
    minimum_stok: barang.minimum_stok ?? 0,
    deskripsi: barang.deskripsi || '',
    supplier_id: supplierId,
    jenis_barang: barang.jenis_barang || 'jual_langsung',
    created_by: createdById,
    is_menu_item: barang.is_menu_item || false,
    is_featured: barang.is_featured || false,
    is_active: barang.is_active ?? true,
    show_in_shop: barang.show_in_shop ?? false,
    tanggal_kadaluarsa: barang.tanggal_kadaluarsa || null,
    gambar: null,
    varians: barang.varians ? JSON.parse(JSON.stringify(barang.varians)) : [],
  };
  showAddModal.value = true;
};

const editBarang = (barang) => {
  editingBarang.value = barang;

  // Extract created_by ID - could be an object {id, name} or just an integer
  let createdById = null;
  if (barang.created_by) {
    if (typeof barang.created_by === 'object' && barang.created_by.id) {
      createdById = barang.created_by.id;
    } else {
      createdById = parseInt(barang.created_by);
    }
  }

  // Extract supplier_id - same logic as created_by
  let supplierId = null;
  if (barang.supplier_id) {
    supplierId = parseInt(barang.supplier_id);
  }

  // Format tanggal kadaluarsa ke yyyy-MM-dd untuk input date
  let formattedExpDate = null;
  if (barang.tanggal_kadaluarsa) {
    try {
      // Ambil hanya bagian tanggal dari ISO string atau date object
      formattedExpDate = barang.tanggal_kadaluarsa.split('T')[0];
    } catch (e) {
      formattedExpDate = null;
    }
  }

  form.value = {
    kode_barang: barang.kode_barang,
    nama_barang: barang.nama_barang,
    kategori_id: barang.kategori_id || '',
    satuan: barang.satuan,
    stok: barang.stok ?? 0,
    harga_beli: barang.harga_beli ?? 0,
    harga_jual: barang.harga_jual ?? 0,
    harga_grosir: barang.harga_grosir ?? 0,
    harga_konsinyasi: barang.harga_konsinyasi ?? 0,
    minimum_stok: barang.minimum_stok ?? 0,
    deskripsi: barang.deskripsi || '',
    supplier_id: supplierId,
    jenis_barang: barang.jenis_barang || 'jual_langsung',
    created_by: createdById,
    is_menu_item: barang.is_menu_item || false,
    is_featured: barang.is_featured || false,
    is_active: barang.is_active ?? true,
    show_in_shop: barang.show_in_shop ?? false,
    tanggal_kadaluarsa: formattedExpDate,
    diskon_tipe: barang.diskon_tipe || '',
    diskon_nilai: barang.diskon_nilai ?? null,
    diskon_mulai: barang.diskon_mulai || null,
    diskon_selesai: barang.diskon_selesai || null,
    gambar: null,
    varians: barang.varians ? JSON.parse(JSON.stringify(barang.varians)) : [],
  };
  showEditModal.value = true;
};

const confirmDelete = (barang) => {
  barangToDelete.value = barang;
  showDeleteModal.value = true;
};

// Image modal handlers
const openImageModal = (barang) => {
  // Adapter: ImageModal expects selectedBarang.image, tapi model pakai gambar
  imageModalBarang.value = { ...barang, image: barang.gambar };
  imageModalSelectedFile.value = null;
  imageModalPreview.value = null;
  showImageModal.value = true;
};

const closeImageModal = () => {
  showImageModal.value = false;
  imageModalBarang.value = null;
  imageModalSelectedFile.value = null;
  imageModalPreview.value = null;
};

const handleImageFileChange = async (event) => {
  const file = event.target?.files?.[0];
  if (!file) return;

  imageModalSelectedFile.value = file;
  const reader = new FileReader();
  reader.onload = (e) => { imageModalPreview.value = e.target.result; };
  reader.readAsDataURL(file);
};

const uploadBarangImage = () => {
  if (!imageModalSelectedFile.value || !imageModalBarang.value) return;

  imageModalUploading.value = true;
  const formData = new FormData();
  formData.append('barang_id', imageModalBarang.value.id);
  formData.append('image', imageModalSelectedFile.value);

  router.post('/pengelola/penjualan/upload-image', formData, {
    onSuccess: () => {
      toast.success('Foto berhasil diupload');
      closeImageModal();
      router.reload({ only: ['barangs'] });
    },
    onError: (errors) => {
      toast.error(errors?.error || 'Gagal mengupload foto');
      imageModalUploading.value = false;
    },
    onFinish: () => { imageModalUploading.value = false; }
  });
};

const deleteBarangImage = () => {
  if (!imageModalBarang.value) return;
  if (!confirm('Yakin ingin menghapus foto produk ini?')) return;

  imageModalUploading.value = true;

  router.post('/pengelola/penjualan/delete-image', {
    barang_id: imageModalBarang.value.id
  }, {
    onSuccess: () => {
      toast.success('Foto berhasil dihapus');
      closeImageModal();
      router.reload({ only: ['barangs'] });
    },
    onError: (errors) => {
      toast.error(errors?.error || 'Gagal menghapus foto');
      imageModalUploading.value = false;
    },
    onFinish: () => { imageModalUploading.value = false; }
  });
};

const deleteBarang = () => {
  router.delete(`/pengelola/opname-stock/${props.toko.id}/barang/${barangToDelete.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
      barangToDelete.value = null;
    },
    onError: (errors) => {
      console.error('Delete error:', errors);
      showDeleteModal.value = false;
      barangToDelete.value = null;

      // Check if it's a foreign key constraint error
      if (errors.delete) {
        toast.error(errors.delete, {
          timeout: 5000
        });
      } else {
        toast.error('Gagal menghapus barang');
      }
    }
  });
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingBarang.value = null;
  availableBarangs.value = [];
  importForm.value = { source_work_unit_id: '', selected_barang_ids: [] };
  form.value = {
    kode_barang: '',
    nama_barang: '',
    kategori_id: '',
    satuan: 'pcs',
    stok: 0,
    harga_beli: 0,
    harga_jual: 0,
    harga_grosir: null,
    harga_konsinyasi: null,
    minimum_stok: 0,
    deskripsi: '',
    is_menu_item: false,
    is_featured: false,
    is_active: true,
    show_in_shop: false,
    tanggal_kadaluarsa: null,
    gambar: null,
    varians: [],
  };
};

const submitForm = () => {
  // Check if there's a file to upload
  const hasFile = form.value.gambar instanceof File;

  if (hasFile) {
    // Use FormData for file upload
    const formData = new FormData();

    // Add all form fields to FormData
    Object.keys(form.value).forEach(key => {
      if (key === 'varians') {
        // JSON stringify varians
        formData.append(key, JSON.stringify(form.value[key]));
      } else if (key === 'gambar' && form.value[key]) {
        // Add file if exists
        formData.append(key, form.value[key]);
      } else if (key === 'is_active' || key === 'is_menu_item' || key === 'is_featured' || key === 'show_in_shop') {
        // Convert boolean to 0 or 1
        formData.append(key, form.value[key] ? '1' : '0');
      } else if (form.value[key] !== null && form.value[key] !== undefined) {
        formData.append(key, form.value[key]);
      }
    });

    if (showEditModal.value) {
      // Laravel requires _method for PUT with FormData
      formData.append('_method', 'PUT');
      console.log('Submitting form data with file:', form.value);

      router.post(`/pengelola/opname-stock/${props.toko.id}/barang/${editingBarang.value.id}`, formData, {
        preserveState: true,
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          closeModal();
        },
        onError: (errors) => {
          console.error('Update error:', errors);
          const errorMessage = errors && typeof errors === 'object'
            ? Object.values(errors).flat().join(', ')
            : 'Terjadi kesalahan';
          toast.error('Gagal mengupdate barang: ' + errorMessage);
        }
      });
    } else {
      router.post(`/pengelola/opname-stock/${props.toko.id}/barang`, formData, {
        preserveState: true,
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          closeModal();
        },
        onError: (errors) => {
          console.error('Create error:', errors);
          const errorMessage = errors && typeof errors === 'object'
            ? Object.values(errors).flat().join(', ')
            : 'Terjadi kesalahan';
          toast.error('Gagal menambahkan barang: ' + errorMessage);
        }
      });
    }
  } else {
    // No file upload, use regular JSON submission
    // JSON parse/stringify untuk unwrap Vue Proxy agar nested data (komponens) ikut terkirim
    const plainForm = JSON.parse(JSON.stringify(form.value));
    if (showEditModal.value) {
      router.put(`/pengelola/opname-stock/${props.toko.id}/barang/${editingBarang.value.id}`, plainForm, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          closeModal();
        },
        onError: (errors) => {
          console.error('Update error:', errors);
          const errorMessage = errors && typeof errors === 'object'
            ? Object.values(errors).flat().join(', ')
            : 'Terjadi kesalahan';
          toast.error('Gagal mengupdate barang: ' + errorMessage);
        }
      });
    } else {
      router.post(`/pengelola/opname-stock/${props.toko.id}/barang`, plainForm, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          closeModal();
        },
        onError: (errors) => {
          console.error('Create error:', errors);
          const errorMessage = errors && typeof errors === 'object'
            ? Object.values(errors).flat().join(', ')
            : 'Terjadi kesalahan';
          toast.error('Gagal menambahkan barang: ' + errorMessage);
        }
      });
    }
  }
};

// Methods - Pengajuan Barang
const openPengajuanModal = (barang) => {
  pengajuanBarang.value = barang;
  showPengajuanModal.value = true;
};

// Methods - Stock Opname
const showOpnameModal = (barang) => {
  opnameBarang.value = barang;
  opnameForm.value = {
    barang_id: barang.id,
    opname_date: new Date().toISOString().slice(0, 16),
    stock_fisik: barang.stok,
    keterangan: '',
  };
  showStockOpnameModal.value = true;
};

const closeOpnameModal = () => {
  showStockOpnameModal.value = false;
  opnameBarang.value = null;
  opnameForm.value = {
    barang_id: null,
    opname_date: new Date().toISOString().slice(0, 16),
    stock_fisik: null,
    keterangan: '',
  };
};

const submitOpname = () => {
  router.post(`/pengelola/opname-stock/${props.toko.id}/stock-opname`, opnameForm.value, {
    onSuccess: () => {
      toast.success('Stock opname berhasil disimpan');
      closeOpnameModal();
    },
    onError: () => {
      toast.error('Gagal menyimpan stock opname');
    }
  });
};

// Methods - Generate PLU
const generatePLU = async () => {
  generatingPLU.value = true;
  try {
    const response = await fetch(`/pengelola/opname-stock/${props.toko.id}/generate-plu`);
    const data = await response.json();
    if (data.plu) {
      form.value.kode_barang = data.plu;
      toast.success('Kode PLU berhasil digenerate');
    }
  } catch (error) {
    console.error('Error generating PLU:', error);
    toast.error('Gagal generate PLU');
  } finally {
    generatingPLU.value = false;
  }
};

// Methods - Import Barangs
const loadBarangsFromUnit = async (sourceWorkUnitId) => {
  importForm.value.source_work_unit_id = sourceWorkUnitId;

  if (!sourceWorkUnitId) {
    availableBarangs.value = [];
    return;
  }

  loadingBarangs.value = true;
  try {
    const response = await fetch(`/pengelola/opname-stock/${sourceWorkUnitId}/barang/list`);
    const data = await response.json();
    availableBarangs.value = data.barangs || [];
    importForm.value.selected_barang_ids = [];
  } catch (error) {
    console.error('Error loading barangs:', error);
    alert('Gagal memuat data barang. Silakan coba lagi.');
    availableBarangs.value = [];
  } finally {
    loadingBarangs.value = false;
  }
};

const toggleSelectAll = () => {
  if (importForm.value.selected_barang_ids.length === availableBarangs.value.length) {
    importForm.value.selected_barang_ids = [];
  } else {
    importForm.value.selected_barang_ids = availableBarangs.value.map(b => b.id);
  }
};

const importBarangs = () => {
  if (importForm.value.selected_barang_ids.length === 0) {
    toast.warning('Pilih minimal 1 barang untuk diimpor');
    return;
  }

  importingBarangs.value = true;
  router.post(`/pengelola/opname-stock/${props.toko.id}/barang/import`, {
    source_work_unit_id: importForm.value.source_work_unit_id,
    barang_ids: importForm.value.selected_barang_ids
  }, {
    onSuccess: (page) => {
      importingBarangs.value = false;
      closeModal();

      // Check if there are warnings
      if (page.props.flash?.warnings && page.props.flash.warnings.length > 0) {
        const warningCount = page.props.flash.warnings.length;
        toast.warning(`Impor selesai dengan ${warningCount} peringatan. Lihat console untuk detail.`, {
          timeout: 5000
        });
        console.warn('Import warnings:', page.props.flash.warnings);
      }

      // Show success message
      if (page.props.flash?.success) {
        toast.success(page.props.flash.success, {
          timeout: 3000
        });
      }
    },
    onError: (errors) => {
      console.error('Import errors:', errors);
      importingBarangs.value = false;

      // Get detailed error message
      let errorMessage = 'Gagal mengimpor barang';

      if (errors.import) {
        errorMessage = errors.import;
      } else if (errors && typeof errors === 'object') {
        const errorList = Object.entries(errors)
          .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
          .join('; ');
        errorMessage = errorList || errorMessage;
      }

      toast.error(errorMessage, {
        timeout: 7000
      });
    }
  });
};

// Helper methods
const paginationClass = (link) => [
  'px-3 py-2 text-sm rounded-lg transition-colors',
  link.active
    ? 'bg-[#996600] text-white font-medium'
    : link.url
    ? 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
];

const paginationClassMobile = (link) => [
  'px-2.5 py-1.5 text-xs rounded transition-colors',
  link.active
    ? 'bg-[#996600] text-white font-medium'
    : link.url
    ? 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
];

// Selection handlers
const toggleSelectBarang = (barangId) => {
  const index = selectedBarangsForPrint.value.indexOf(barangId);
  if (index > -1) {
    selectedBarangsForPrint.value.splice(index, 1);
  } else {
    selectedBarangsForPrint.value.push(barangId);
  }
};

const toggleSelectAllBarangs = () => {
  if (selectedBarangsForPrint.value.length === filteredBarangs.value.length) {
    selectedBarangsForPrint.value = [];
  } else {
    selectedBarangsForPrint.value = filteredBarangs.value.map(b => b.id);
  }
};

// Price Tag Print
const printSinglePriceTag = (barangId) => {
  printing.value = true;
  submitPrintForm([barangId]);

  setTimeout(() => {
    printing.value = false;
    toast.success('Price tag berhasil digenerate');
  }, 1000);
};

const printPriceTags = () => {
  if (selectedBarangsForPrint.value.length === 0) {
    toast.warning('Pilih minimal 1 barang untuk dicetak');
    return;
  }

  printing.value = true;
  const count = selectedBarangsForPrint.value.length;
  submitPrintForm(selectedBarangsForPrint.value);

  setTimeout(() => {
    printing.value = false;
    selectedBarangsForPrint.value = [];
    toast.success(`Price tag untuk ${count} barang berhasil digenerate`);
  }, 1000);
};

const bulkApprovePengajuan = () => {
  const barangIds = filteredBarangs.value
    .filter(b => selectedBarangsForPrint.value.includes(b.id) && b.has_pengajuan)
    .map(b => b.id);

  if (barangIds.length === 0) {
    toast.warning('Tidak ada pengajuan yang bisa disetujui dari barang yang dipilih');
    return;
  }

  approvingPengajuan.value = true;
  router.post(`/pengelola/opname-stock/${props.toko.id}/barang/pengajuan/bulk-approve`, {
    barang_ids: barangIds
  }, {
    onSuccess: (page) => {
      approvingPengajuan.value = false;
      selectedBarangsForPrint.value = [];
      if (page.props.flash?.success) {
        toast.success(page.props.flash.success);
      }
    },
    onError: (errors) => {
      approvingPengajuan.value = false;
      toast.error(errors?.pengajuan || 'Gagal memproses pengajuan');
    }
  });
};

const submitPrintForm = async (barangIds) => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const formData = new FormData();
    if (csrfToken) formData.append('_token', csrfToken);
    barangIds.forEach(id => formData.append('barang_ids[]', id));

    const response = await fetch(`/pengelola/opname-stock/${props.toko.id}/barang/print-price-tags`, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    });

    if (!response.ok) {
      throw new Error(`Server error: ${response.status}`);
    }

    const blob = await response.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.target = '_blank';
    a.click();
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Error printing price tags:', error);
    printing.value = false;
    toast.error('Gagal mencetak price tag: ' + error.message);
  }
};

// Handle Import Barang from CSV
const handleImportBarang = (data) => {
  importingBarangs.value = true;

  router.post(`/pengelola/opname-stock/${props.toko.id}/barang/import-csv`, {
    data: data
  }, {
    onSuccess: (page) => {
      importingBarangs.value = false;
      showImportModal.value = false;

      if (page.props.flash?.import_errors && page.props.flash.import_errors.length > 0) {
        const errorCount = page.props.flash.import_errors.length;
        toast.warning(`Impor selesai dengan ${errorCount} error. Lihat console untuk detail.`, {
          timeout: 5000
        });
        console.warn('Import errors:', page.props.flash.import_errors);
      }

      if (page.props.flash?.success) {
        toast.success(page.props.flash.success, {
          timeout: 3000
        });
      }
    },
    onError: (errors) => {
      console.error('Import errors:', errors);
      importingBarangs.value = false;

      let errorMessage = 'Gagal mengimpor data barang';

      if (errors.import) {
        errorMessage = errors.import;
      } else if (errors && typeof errors === 'object') {
        const errorList = Object.entries(errors)
          .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
          .join('; ');
        errorMessage = errorList || errorMessage;
      }

      toast.error(errorMessage, {
        timeout: 7000
      });
    }
  });
};

// Auto-focus search input on mount
onMounted(() => {
  if (searchInput.value) {
    searchInput.value.focus();
  }
});
</script>
