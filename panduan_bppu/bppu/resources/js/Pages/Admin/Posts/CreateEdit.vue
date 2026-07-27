<template>
  <AdminLayout :page-title="post ? 'Edit Pos' : 'Tambah Pos Baru'">
    <div class="max-w-7xl mx-auto pb-24 lg:pb-0 space-y-4">
      <nav class="flex text-xs md:text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="inline-flex items-center">
            <Link :href="route('admin.posts.index')" class="inline-flex items-center hover:text-[#996600]">
              <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              Daftar Pos
            </Link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 font-medium text-gray-800">{{ post ? post.title : 'Pos Baru' }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content Area -->
          <div class="lg:col-span-2 space-y-4">
            <!-- Title -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Judul Pos <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.title"
                type="text"
                required
                placeholder="Masukkan judul pos..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
              <div v-if="errors.title" class="mt-2 text-sm text-red-600">
                {{ errors.title }}
              </div>
            </div>

            <!-- Content Editor -->
            <div>
              <TipTapEditor
                v-model="form.content"
                placeholder="Mulai menulis konten pos Anda di sini..."
              />
              <div v-if="errors.content" class="mt-2 text-sm text-red-600">
                {{ errors.content }}
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1 space-y-4">
            <!-- Publish Box -->
            <div class="bg-white rounded-lg shadow-sm p-4">
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select
                    v-model="form.status"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  >
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                  </select>
                </div>

                <div v-if="form.status === 'published'">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Publikasi</label>
                  <input
                    v-model="form.published_at"
                    type="datetime-local"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                </div>

                <div v-if="users && users.length > 0">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                  <select
                    v-model="form.user_id"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  >
                    <option value="">Pilih Penulis</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.name }}
                    </option>
                  </select>
                </div>

                <div class="hidden lg:grid pt-3 border-t grid-cols-2 gap-2">
                  <Link
                    :href="route('admin.posts.index')"
                    class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center"
                  >
                    Batal
                  </Link>
                  <button
                    type="submit"
                    :disabled="processing"
                    class="px-4 py-2 text-sm bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
                  >
                    {{ processing ? 'Menyimpan...' : (post ? 'Perbarui' : 'Simpan') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Excerpt -->
            <div class="bg-white rounded-lg shadow-sm p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Excerpt</h3>
              <textarea
                v-model="form.excerpt"
                rows="3"
                placeholder="Kutipan singkat untuk preview"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              ></textarea>
            </div>

            <!-- Category & Tags -->
            <div class="bg-white rounded-lg shadow-sm p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Kategori & Tags</h3>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                  <select
                    v-model="form.category_id"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  >
                    <option value="">Pilih Kategori</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-2">Tags</label>
                  <div class="space-y-1 max-h-32 overflow-y-auto">
                    <label v-for="tag in tags" :key="tag.id" class="flex items-center">
                      <input
                        type="checkbox"
                        :value="tag.id"
                        v-model="form.tags"
                        class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
                      />
                      <span class="ml-2 text-xs text-gray-700">{{ tag.name }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Featured Image -->
            <MediaPicker
              title="Gambar Unggulan"
              :current-image="post?.featured_image"
              v-model="form.featured_image"
              @update:imagePreview="imagePreview = $event"
            />

            <!-- Keywords SEO -->
            <div class="bg-white rounded-lg shadow-sm p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Keywords SEO</h3>
              <input
                v-model="form.keywords"
                type="text"
                placeholder="keyword1, keyword2"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>
          </div>
        </div>

        <!-- Sticky Footer Buttons for Mobile -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 lg:hidden z-40 shadow-lg">
          <div class="grid grid-cols-2 gap-3 max-w-7xl mx-auto">
            <Link
              :href="route('admin.posts.index')"
              class="px-4 py-3 text-sm bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center"
            >
              Batal
            </Link>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-3 text-sm bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
            >
              {{ processing ? 'Menyimpan...' : (post ? 'Perbarui' : 'Simpan') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TipTapEditor from '@/Components/Editor/TipTapEditor.vue';
import MediaPicker from '@/Components/MediaPicker.vue';

const props = defineProps({
  post: Object,
  categories: Array,
  tags: Array,
  users: Array,
  errors: Object,
});

const page = usePage();

const imagePreview = ref(null);
const processing = ref(false);

// Format published_at for datetime-local input
const formatDateTimeLocal = (dateString) => {
  if (!dateString || dateString === null || dateString === '') return '';

  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  } catch (error) {
    console.error('Error formatting date:', error);
    return '';
  }
};

// Get current datetime for new published posts
const getCurrentDateTimeLocal = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const form = reactive({
  title: props.post?.title || '',
  slug: props.post?.slug || '',
  excerpt: props.post?.excerpt || '',
  content: props.post?.content || '',
  category_id: props.post?.category_id || '',
  status: props.post?.status || 'draft',
  published_at: (props.post?.published_at && props.post.published_at !== null)
    ? formatDateTimeLocal(props.post.published_at) : '',
  keywords: props.post?.keywords || '',
  tags: Array.isArray(props.post?.tags) ? props.post.tags.map(tag => tag.id) : [],
  user_id: props.post?.user_id || page.props.auth?.user?.id || '',
  featured_image: null,
});

// Watch status changes and set default published_at when changing to published
watch(() => form.status, (newStatus) => {
  if (newStatus === 'published' && !form.published_at) {
    form.published_at = getCurrentDateTimeLocal();
  }
});

const submitForm = () => {
  processing.value = true;

  const formData = new FormData();

  Object.keys(form).forEach(key => {
    if (key === 'tags') {
      form.tags.forEach(tagId => {
        formData.append('tags[]', tagId);
      });
    } else if (key === 'featured_image') {
      if (form.featured_image instanceof File) {
        formData.append('featured_image', form.featured_image);
      } else if (typeof form.featured_image === 'number') {
        formData.append('arsip_id', form.featured_image);
      }
    } else if (form[key] !== null && form[key] !== '' && form[key] !== undefined) {
      formData.append(key, form[key]);
    }
  });

  if (props.post) {
    formData.append('_method', 'PUT');
    router.post(route('admin.posts.update', props.post.id), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  } else {
    router.post(route('admin.posts.store'), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};
</script>
