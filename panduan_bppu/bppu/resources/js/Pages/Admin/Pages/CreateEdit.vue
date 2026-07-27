<template>
  <AdminLayout :page-title="page ? 'Edit Halaman' : 'Tambah Halaman Baru'">
    <div class="max-w-7xl mx-auto space-y-4">
      <nav class="flex text-xs md:text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="inline-flex items-center">
            <Link :href="route('admin.pages.index')" class="inline-flex items-center hover:text-[#996600]">
              <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              Daftar Halaman
            </Link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 font-medium text-gray-800">{{ page ? page.title : 'Halaman Baru' }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content Area -->
          <div class="lg:col-span-2 space-y-4">
            <!-- Title, Category & Slug -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Judul Halaman <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  placeholder="Masukkan judul halaman..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
                <div v-if="errors.title" class="mt-2 text-sm text-red-600">
                  {{ errors.title }}
                </div>
              </div>

              <!-- Category & Slug in 1 line -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                  <input
                    v-model="form.category"
                    type="text"
                    placeholder="profil, layanan, dll"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                  <div v-if="errors.category" class="mt-2 text-sm text-red-600">
                    {{ errors.category }}
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                  <input
                    v-model="form.slug"
                    type="text"
                    placeholder="slug-halaman"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                  <div v-if="errors.slug" class="mt-2 text-sm text-red-600">
                    {{ errors.slug }}
                  </div>
                </div>
              </div>
              <p class="text-xs text-gray-500">
                URL: {{ baseUrl }}/{{ form.category ? form.category + '/' : '' }}{{ form.slug || 'auto-generated' }}
              </p>
            </div>

            <!-- Content Editor -->
            <div>
              <TipTapEditor
                v-model="form.content"
                placeholder="Mulai menulis konten halaman Anda di sini..."
                upload-route="admin.pages.uploadContentImage"
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

                <div class="pt-3 border-t grid grid-cols-2 gap-2">
                  <Link
                    :href="route('admin.pages.index')"
                    class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center"
                  >
                    Batal
                  </Link>
                  <button
                    type="submit"
                    :disabled="processing"
                    class="px-4 py-2 text-sm bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
                  >
                    {{ processing ? 'Menyimpan...' : (page ? 'Perbarui' : 'Simpan') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Featured Image -->
            <MediaPicker
              title="Gambar Unggulan"
              :current-image="page?.featured_image"
              v-model="form.featured_image"
              @update:imagePreview="imagePreview = $event"
            />

            <!-- SEO Meta -->
            <div class="bg-white rounded-lg shadow-sm p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">SEO Meta</h3>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Meta Title</label>
                  <input
                    v-model="form.meta_title"
                    type="text"
                    placeholder="SEO title"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Meta Description</label>
                  <textarea
                    v-model="form.meta_description"
                    rows="2"
                    placeholder="SEO description"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Meta Keywords</label>
                  <input
                    v-model="form.meta_keywords"
                    type="text"
                    placeholder="keyword1, keyword2"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TipTapEditor from '@/Components/Editor/TipTapEditor.vue';
import MediaPicker from '@/Components/MediaPicker.vue';

const props = defineProps({
  page: Object,
  users: Array,
  errors: Object,
});

const imagePreview = ref(null);
const processing = ref(false);

const baseUrl = computed(() => {
  return window.location.origin;
});

const form = reactive({
  title: props.page?.title || '',
  slug: props.page?.slug || '',
  category: props.page?.category || '',
  content: props.page?.content || '',
  status: props.page?.status || 'published',
  user_id: props.page?.user_id || '',
  meta_title: props.page?.meta_title || '',
  meta_description: props.page?.meta_description || '',
  meta_keywords: props.page?.meta_keywords || '',
  featured_image: null,
});

const submitForm = () => {
  processing.value = true;

  const formData = new FormData();

  Object.keys(form).forEach(key => {
    if (key === 'featured_image') {
      if (form.featured_image instanceof File) {
        formData.append('featured_image', form.featured_image);
      } else if (typeof form.featured_image === 'number') {
        formData.append('arsip_id', form.featured_image);
      }
    } else if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key]);
    }
  });

  if (props.page) {
    formData.append('_method', 'PUT');
    router.post(route('admin.pages.update', props.page.id), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  } else {
    router.post(route('admin.pages.store'), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};
</script>
