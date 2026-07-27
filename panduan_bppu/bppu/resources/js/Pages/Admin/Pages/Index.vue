<template>
  <AdminLayout page-title="Kelola Halaman">
    <div class="space-y-4">
      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
            <input
              v-model="searchForm.search"
              @input="debouncedSearch"
              type="text"
              placeholder="Cari judul, slug, atau konten..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="searchForm.status"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">Semua Status</option>
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <Link
              :href="route('admin.pages.create')"
              class="flex items-center justify-center w-full px-4 py-2 bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Tambah Halaman
            </Link>
          </div>
        </div>
      </div>

      <!-- Pages Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Judul
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Kategori
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  URL
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Penulis
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tanggal
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="pages.data.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  Belum ada halaman. Klik "Tambah Halaman Baru" untuk membuat halaman pertama Anda.
                </td>
              </tr>
              <tr v-for="page in pages.data" :key="page.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <Link
                    :href="route('admin.pages.edit', page.id)"
                    class="text-sm font-medium text-gray-900 hover:text-[#996600]"
                  >
                    {{ page.title }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="page.category" class="px-2 py-1 text-xs font-medium rounded bg-primary-100 text-primary-900 capitalize">
                    {{ page.category }}
                  </span>
                  <span v-else class="text-xs text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a
                    :href="getPageUrl(page)"
                    target="_blank"
                    class="text-xs text-[#996600] hover:text-[#ad8432] bg-gray-100 px-2 py-1 rounded inline-flex items-center"
                  >
                    <code class="mr-1">{{ getPageUrl(page) }}</code>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                  </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                      page.status === 'published'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800'
                    ]"
                  >
                    {{ page.status === 'published' ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ page.user.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(page.updated_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <Link
                      :href="route('admin.pages.edit', page.id)"
                      class="text-[#996600] hover:text-[#ad8432]"
                    >
                      Edit
                    </Link>
                    <button
                      @click="confirmDelete(page)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pages.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Menampilkan <span class="font-medium">{{ pages.from }}</span> sampai
              <span class="font-medium">{{ pages.to }}</span> dari
              <span class="font-medium">{{ pages.total }}</span> halaman
            </div>
            <div class="flex space-x-2">
              <Link
                v-for="(link, index) in pages.links"
                :key="index"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm rounded-lg',
                  link.active
                    ? 'bg-[#996600] text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                  !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="showDeleteModal = false"
    >
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Hapus Halaman</h3>
          <p class="text-sm text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus halaman "<strong>{{ pageToDelete?.title }}</strong>"? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              @click="deletePage"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
            >
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  pages: Object,
  filters: Object,
});

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
});

const showDeleteModal = ref(false);
const pageToDelete = ref(null);

let debounceTimer = null;
const debouncedSearch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    applyFilters();
  }, 500);
};

const applyFilters = () => {
  router.get(route('admin.pages.index'), searchForm, {
    preserveState: true,
    preserveScroll: true,
  });
};

const confirmDelete = (page) => {
  pageToDelete.value = page;
  showDeleteModal.value = true;
};

const deletePage = () => {
  router.delete(route('admin.pages.destroy', pageToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      pageToDelete.value = null;
    },
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const getPageUrl = (page) => {
  if (page.category) {
    return `/${page.category}/${page.slug}`;
  }
  return `/${page.slug}`;
};
</script>
