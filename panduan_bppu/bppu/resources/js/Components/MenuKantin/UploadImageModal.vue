<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Upload Gambar Menu</h3>
      <p class="text-sm text-gray-600 mb-4">{{ selectedMenu?.nama_barang }}</p>

      <form @submit.prevent="$emit('submit')">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Gambar</label>
          <input
            type="file"
            ref="imageInput"
            accept="image/*"
            @change="$emit('imageSelect', $event)"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#996600] file:text-white hover:file:bg-[#7a5100]"
            required
          />
        </div>

        <div v-if="imagePreview" class="mb-4">
          <img :src="imagePreview" class="w-full h-48 object-cover rounded-lg" alt="Preview" />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="uploading"
            class="px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg disabled:opacity-50"
          >
            {{ uploading ? 'Uploading...' : 'Upload' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: { type: Boolean, required: true },
  selectedMenu: { type: Object, default: null },
  imagePreview: { type: String, default: null },
  uploading: { type: Boolean, default: false },
});

defineEmits(['close', 'submit', 'imageSelect']);
</script>
