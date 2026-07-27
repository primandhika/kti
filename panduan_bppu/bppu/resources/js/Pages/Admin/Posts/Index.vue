<template>
  <AdminLayout page-title="Kelola Pos">
    <div class="space-y-4">
      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
            <input
              v-model="searchForm.search"
              @input="debouncedSearch"
              type="text"
              placeholder="Cari judul, konten, atau keyword..."
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select
              v-model="searchForm.category"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">Semua Kategori</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <Link
              :href="route('admin.posts.create')"
              class="flex items-center justify-center w-full px-4 py-2 bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Tambah Pos
            </Link>
          </div>
        </div>
      </div>

      <!-- Posts Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  @click="sortBy('title')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                >
                  <div class="flex items-center space-x-1">
                    <span>Judul</span>
                    <svg v-if="searchForm.sort === 'title'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path v-if="searchForm.direction === 'asc'" d="M5 10l5-5 5 5H5z"/>
                      <path v-else d="M5 10l5 5 5-5H5z"/>
                    </svg>
                  </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tags
                </th>
                <th
                  @click="sortBy('status')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                >
                  <div class="flex items-center space-x-1">
                    <span>Status</span>
                    <svg v-if="searchForm.sort === 'status'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path v-if="searchForm.direction === 'asc'" d="M5 10l5-5 5 5H5z"/>
                      <path v-else d="M5 10l5 5 5-5H5z"/>
                    </svg>
                  </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Penulis
                </th>
                <th
                  @click="sortBy('created_at')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                >
                  <div class="flex items-center space-x-1">
                    <span>Tanggal</span>
                    <svg v-if="searchForm.sort === 'created_at'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path v-if="searchForm.direction === 'asc'" d="M5 10l5-5 5 5H5z"/>
                      <path v-else d="M5 10l5 5 5-5H5z"/>
                    </svg>
                  </div>
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="posts.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                  Belum ada pos. Klik "Tambah Pos Baru" untuk membuat pos pertama Anda.
                </td>
              </tr>
              <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 max-w-xs">
                  <Link
                    :href="route('admin.posts.edit', post.id)"
                    class="text-sm font-medium text-gray-900 hover:text-[#996600] line-clamp-2"
                  >
                    {{ post.title }}
                  </Link>
                </td>
                <td class="px-6 py-4">
                  <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-1 max-w-xs">
                    <span
                      v-for="tag in post.tags"
                      :key="tag.id"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 truncate max-w-[100px]"
                      :title="tag.name"
                    >
                      {{ tag.name }}
                    </span>
                  </div>
                  <span v-else class="text-sm text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                      post.status === 'published'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800'
                    ]"
                  >
                    {{ post.status === 'published' ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <div class="truncate max-w-[120px]" :title="post.user.name">
                    {{ post.user.name }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(post.published_at || post.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <Link
                      :href="route('admin.posts.edit', post.id)"
                      class="text-[#996600] hover:text-[#ad8432]"
                    >
                      Edit
                    </Link>
                    <button
                      @click="confirmDelete(post)"
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
        <div v-if="posts.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Menampilkan <span class="font-medium">{{ posts.from }}</span> sampai
              <span class="font-medium">{{ posts.to }}</span> dari
              <span class="font-medium">{{ posts.total }}</span> pos
            </div>
            <div class="flex space-x-2">
              <Link
                v-for="(link, index) in posts.links"
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
          <h3 class="text-lg font-medium text-gray-900 mb-4">Hapus Pos</h3>
          <p class="text-sm text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus pos "<strong>{{ postToDelete?.title }}</strong>"? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              @click="deletePost"
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
  posts: Object,
  filters: Object,
});

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  category: props.filters.category || '',
  sort: props.filters.sort || 'created_at',
  direction: props.filters.direction || 'desc',
});

const showDeleteModal = ref(false);
const postToDelete = ref(null);

let debounceTimer = null;
const debouncedSearch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    applyFilters();
  }, 500);
};

const applyFilters = () => {
  router.get(route('admin.posts.index'), searchForm, {
    preserveState: true,
    preserveScroll: true,
  });
};

const sortBy = (field) => {
  if (searchForm.sort === field) {
    // Toggle direction if same field
    searchForm.direction = searchForm.direction === 'asc' ? 'desc' : 'asc';
  } else {
    // New field, default to desc
    searchForm.sort = field;
    searchForm.direction = 'desc';
  }
  applyFilters();
};

const confirmDelete = (post) => {
  postToDelete.value = post;
  showDeleteModal.value = true;
};

const deletePost = () => {
  router.delete(route('admin.posts.destroy', postToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      postToDelete.value = null;
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
</script>
