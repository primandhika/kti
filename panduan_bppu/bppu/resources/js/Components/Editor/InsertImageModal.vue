<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click.self="close">
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-30 transition-opacity"></div>

        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Insert Gambar</h3>
            <button @click="close" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="p-6 overflow-y-auto max-h-[calc(80vh-140px)]">
            <div class="flex border-b border-gray-200 mb-4">
              <button
                type="button"
                @click="activeTab = 'upload'"
                :class="[
                  'px-4 py-2 text-sm font-medium border-b-2 transition-colors',
                  activeTab === 'upload'
                    ? 'border-[#996600] text-[#996600]'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                ]"
              >
                Upload Baru
              </button>
              <button
                type="button"
                @click="activeTab = 'arsip'"
                :class="[
                  'px-4 py-2 text-sm font-medium border-b-2 transition-colors',
                  activeTab === 'arsip'
                    ? 'border-[#996600] text-[#996600]'
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                ]"
              >
                Pilih dari Arsip
              </button>
            </div>

            <div v-if="activeTab === 'upload'" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Gambar</label>
                <input
                  ref="fileInput"
                  type="file"
                  @change="handleFileUpload"
                  accept="image/*"
                  class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#996600] file:text-white hover:file:bg-[#ad8432]"
                />
              </div>

              <div v-if="uploadPreview" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                <img :src="uploadPreview" alt="Preview" class="max-w-full h-auto rounded-lg border border-gray-300" />
              </div>
            </div>

            <div v-else class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Gambar</label>
                <input
                  v-model="searchQuery"
                  @input="searchArsip"
                  type="text"
                  placeholder="Cari gambar di arsip..."
                  class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
              </div>

              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#996600]"></div>
                <p class="text-sm text-gray-500 mt-2">Memuat...</p>
              </div>

              <div v-else-if="arsipImages.length > 0" class="grid grid-cols-4 gap-3 max-h-96 overflow-y-auto">
                <button
                  v-for="image in arsipImages"
                  :key="image.id"
                  type="button"
                  @click="selectFromArsip(image)"
                  :class="[
                    'relative aspect-square rounded-lg overflow-hidden border-2 transition-all hover:border-[#996600]',
                    selectedArsipId === image.id ? 'border-[#996600] ring-2 ring-[#996600]' : 'border-gray-200'
                  ]"
                >
                  <img
                    :src="image.url"
                    :alt="image.nama_file"
                    class="w-full h-full object-cover"
                  />
                  <div v-if="selectedArsipId === image.id" class="absolute inset-0 bg-[#996600] bg-opacity-20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </button>
              </div>

              <div v-else class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm text-gray-500">Tidak ada gambar di arsip</p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              type="button"
              @click="insertImage"
              :disabled="!canInsert"
              class="px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#ad8432] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Insert Gambar
            </button>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'insert']);

const activeTab = ref('upload');
const fileInput = ref(null);
const uploadPreview = ref(null);
const uploadedFile = ref(null);
const searchQuery = ref('');
const loading = ref(false);
const arsipImages = ref([]);
const selectedArsipId = ref(null);
const selectedArsipImage = ref(null);

const canInsert = computed(() => {
  if (activeTab.value === 'upload') {
    return uploadedFile.value !== null;
  }
  return selectedArsipId.value !== null;
});

watch(() => props.show, (newVal) => {
  if (newVal) {
    loadArsip();
  } else {
    resetForm();
  }
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    uploadedFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      uploadPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const loadArsip = async () => {
  loading.value = true;
  try {
    const params = {
      kategori_arsip: 'gambar_artikel',
      search: searchQuery.value,
      per_page: 50,
    };
    const response = await axios.get('/pengelola/arsip', {
      params,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    arsipImages.value = response.data.arsip.data || [];
  } catch (error) {
    console.error('Error loading arsip:', error);
  } finally {
    loading.value = false;
  }
};

let searchTimeout;
const searchArsip = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadArsip();
  }, 300);
};

const selectFromArsip = (image) => {
  selectedArsipId.value = image.id;
  selectedArsipImage.value = image;
};

const insertImage = () => {
  if (activeTab.value === 'upload' && uploadedFile.value) {
    emit('insert', { type: 'file', file: uploadedFile.value });
  } else if (activeTab.value === 'arsip' && selectedArsipImage.value) {
    emit('insert', { type: 'arsip', image: selectedArsipImage.value });
  }
  close();
};

const close = () => {
  emit('close');
};

const resetForm = () => {
  activeTab.value = 'upload';
  uploadPreview.value = null;
  uploadedFile.value = null;
  selectedArsipId.value = null;
  selectedArsipImage.value = null;
  searchQuery.value = '';
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};
</script>
