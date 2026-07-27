<template>
  <AdminLayout
    page-title="Kelola Menu Kantin"
    page-subtitle="Menu diambil dari Master Barang yang ditandai 'Tampilkan di Menu'"
  >
    <div class="space-y-4">
      <!-- Filter Bar -->
      <FilterBar
        :search-query="searchQuery"
        :filter-kategori="filterKategori"
        :filter-work-unit="filterWorkUnit"
        :total-menus="totalMenus"
        :available-count="availableCount"
        :out-of-stock-count="outOfStockCount"
        :show-all-kategori="showAllKategori"
        :kategoris="kategoris"
        :work-units="workUnits"
        @update:search-query="searchQuery = $event"
        @update:filter-kategori="filterKategori = $event"
        @update:filter-work-unit="filterWorkUnit = $event"
        @toggle-show-all="toggleShowAll"
        @print-menu="printMenu"
        @open-download-modal="(wu) => openDownloadModal(wu)"
      />

      <!-- Sub Kategori Pills -->
      <SubKategoriPills
        v-if="groupedMenus && groupedMenus.length > 0"
        :show-all-kategori="showAllKategori"
        :sub-kategori-list="subKategoriList"
        :active-sub-kategori="activeSubKategori"
        :total-menus="totalMenus"
        @select-kategori="selectKategori"
        @show-all-kategori="showAll"
      />

      <!-- Empty State -->
      <div v-if="!groupedMenus || groupedMenus.length === 0">
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada menu</h3>
          <p class="mt-1 text-sm text-gray-500">Tambahkan barang dan tandai sebagai menu kantin.</p>
          <div class="mt-6">
            <Link
              href="/pengelola/opname-stock"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#996600] hover:bg-[#7a5100]"
            >
              Ke Master Barang
            </Link>
          </div>
        </div>
      </div>

      <!-- Menus Grid -->
      <div v-else class="relative bg-white rounded-b-xl shadow-md p-4">
        <div v-for="group in paginatedGroups" :key="group.label" class="mb-6">
          <!-- Group Header -->
          <div class="mb-3 pb-2 border-b-2 border-[#996600]">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-base font-bold text-[#6b4700]">{{ group.label }}</h2>
                <p class="text-xs text-gray-600">{{ group.count }} menu</p>
              </div>
            </div>
          </div>

          <!-- Cards Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <MenuCard
              v-for="menu in group.items"
              :key="menu.id"
              :menu="menu"
              :active-dropdown="activeDropdown"
              @toggle-dropdown="toggleDropdown"
              @edit-menu="openEditMenuModal"
              @edit-display="openEditDisplayModal"
              @upload-image="openUploadModal"
              @toggle-availability="toggleAvailability"
            />
          </div>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMoreItems" class="relative mt-4">
          <div class="absolute -top-24 left-0 right-0 h-24 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none"></div>
          <div class="relative z-10 flex justify-center pt-8 pb-4">
            <button
              @click="loadMore"
              class="group px-6 py-3 bg-white border-2 border-[#996600] text-[#996600] rounded-lg font-medium hover:bg-[#996600] hover:text-white transition-all shadow-md hover:shadow-lg flex items-center gap-2"
            >
              <span>Muat Lebih Banyak</span>
              <svg class="w-5 h-5 transform group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <UploadImageModal
      :show="showUploadModal"
      :selected-menu="selectedMenu"
      :image-preview="imagePreview"
      :uploading="uploading"
      @close="closeUploadModal"
      @submit="uploadImage"
      @image-select="handleImageSelect"
    />

    <EditDisplayModal
      :show="showEditModal"
      :selected-menu="selectedMenu"
      :display-form="displayForm"
      :updating="updating"
      @close="closeEditModal"
      @submit="updateDisplay"
    />

    <EditMenuModal
      :show="showEditMenuModal"
      :selected-menu="selectedMenu"
      :menu-form="menuForm"
      :updating="updating"
      :kategoris="kategoris"
      :all-sub-kategoris="allSubKategoris"
      @close="closeEditMenuModal"
      @submit="updateMenu"
    />

    <AvailabilityModal
      :show="showAvailabilityModal"
      @close="closeAvailabilityModal"
      @confirm="confirmAction"
    />
  </AdminLayout>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FilterBar from '@/Components/MenuKantin/FilterBar.vue';
import SubKategoriPills from '@/Components/MenuKantin/SubKategoriPills.vue';
import MenuCard from '@/Components/MenuKantin/MenuCard.vue';
import UploadImageModal from '@/Components/MenuKantin/UploadImageModal.vue';
import EditDisplayModal from '@/Components/MenuKantin/EditDisplayModal.vue';
import EditMenuModal from '@/Components/MenuKantin/EditMenuModal.vue';
import AvailabilityModal from '@/Components/MenuKantin/AvailabilityModal.vue';
import { useMenuFilters } from '@/composables/useMenuFilters';
import { useMenuPagination } from '@/composables/useMenuPagination';
import { useMenuActions } from '@/composables/useMenuActions';
import { useMenuDownload } from '@/composables/useMenuDownload';

const props = defineProps({
  groupedMenus: Array,
  workUnits: Array,
  kategoris: Array,
  allSubKategoris: Array,
  subKategoris: Array,
  filters: Object,
});

// Use composables
const {
  searchQuery,
  filterWorkUnit,
  filterKategori,
  totalMenus,
  availableCount,
  outOfStockCount,
} = useMenuFilters(props);

const {
  activeSubKategori,
  showAllKategori,
  subKategoriList,
  paginatedGroups,
  hasMoreItems,
  loadMore,
  toggleShowAll,
  showAll,
  selectKategori,
  initDefaultKategori,
} = useMenuPagination(props);

const {
  showUploadModal,
  imageInput,
  imagePreview,
  uploading,
  openUploadModal,
  closeUploadModal,
  handleImageSelect,
  uploadImage,
  showEditModal,
  displayForm,
  openEditDisplayModal,
  closeEditModal,
  updateDisplay,
  showEditMenuModal,
  menuForm,
  openEditMenuModal,
  closeEditMenuModal,
  updateMenu,
  selectedMenu,
  updating,
  toggleAvailability,
  activeDropdown,
  toggleDropdown,
  closeDropdown,
} = useMenuActions();

const {
  showAvailabilityModal,
  printMenu,
  openDownloadModal,
  confirmAction,
  closeAvailabilityModal,
} = useMenuDownload();

// Handle click outside to close menu item dropdowns
const handleClickOutside = (event) => {
  if (activeDropdown.value !== null) {
    const target = event.target;
    if (!target.closest('[title="Kelola Menu"]') && !target.closest('.z-50')) {
      closeDropdown();
    }
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  initDefaultKategori();
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
