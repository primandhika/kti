<template>
  <div class="bg-white rounded-lg shadow-sm p-4">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ title }}</h3>

    <!-- Preview -->
    <div v-if="selectedImageUrl" class="mb-3">
      <img
        :src="selectedImageUrl"
        alt="Preview"
        class="w-full h-32 object-cover rounded-lg"
      />
      <button
        type="button"
        @click="removeImage"
        class="mt-2 text-xs text-red-600 hover:text-red-800"
      >
        Hapus Gambar
      </button>
    </div>

    <!-- Tab Selection -->
    <div class="flex border-b border-gray-200 mb-3">
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

    <!-- Upload Tab -->
    <div v-if="activeTab === 'upload'">
      <input
        ref="fileInput"
        type="file"
        @change="handleFileUpload"
        accept="image/*"
        class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#996600] file:text-white hover:file:bg-[#ad8432]"
      />
    </div>

    <!-- Arsip Tab -->
    <div v-else>
      <div class="space-y-3">
        <!-- Search -->
        <input
          v-model="searchQuery"
          @input="searchArsip"
          type="text"
          placeholder="Cari gambar di arsip..."
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />

        <!-- Loading -->
        <div v-if="loading" class="text-center py-4">
          <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#996600]"></div>
          <p class="text-xs text-gray-500 mt-2">Memuat...</p>
        </div>

        <!-- Grid -->
        <div v-else-if="arsipImages.length > 0" class="grid grid-cols-3 gap-2 max-h-64 overflow-y-auto">
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
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
          </button>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <p class="text-xs text-gray-500">Tidak ada gambar di arsip</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  title: {
    type: String,
    default: 'Gambar Unggulan',
  },
  currentImage: String,
  modelValue: [File, String, Number],
});

const emit = defineEmits(['update:modelValue', 'update:imagePreview']);

const activeTab = ref('upload');
const fileInput = ref(null);
const searchQuery = ref('');
const loading = ref(false);
const arsipImages = ref([]);
const selectedArsipId = ref(null);

const selectedImageUrl = computed(() => {
  if (props.modelValue instanceof File) {
    return URL.createObjectURL(props.modelValue);
  }
  if (typeof props.modelValue === 'string' && props.modelValue.startsWith('http')) {
    return props.modelValue;
  }
  if (props.currentImage) {
    return props.currentImage.startsWith('http') ? props.currentImage : `/storage/${props.currentImage}`;
  }
  return null;
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    emit('update:modelValue', file);
    emit('update:imagePreview', URL.createObjectURL(file));
    selectedArsipId.value = null;
  }
};

const removeImage = () => {
  emit('update:modelValue', null);
  emit('update:imagePreview', null);
  selectedArsipId.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const loadArsip = async () => {
  loading.value = true;
  try {
    const params = {
      kategori_arsip: 'gambar_artikel',
      search: searchQuery.value,
      per_page: 20,
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
  emit('update:modelValue', image.id);
  emit('update:imagePreview', image.url);
};

onMounted(() => {
  loadArsip();

  // Check if current value is an arsip ID
  if (typeof props.modelValue === 'number') {
    selectedArsipId.value = props.modelValue;
  }
});
</script>
