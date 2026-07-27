<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="$emit('close')">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Display Menu</h3>
      <p class="text-sm text-gray-600 mb-4">{{ selectedMenu?.nama_barang }}</p>

      <form @submit.prevent="$emit('submit')">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Display</label>
          <textarea
            v-model="localDisplayForm"
            rows="4"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            placeholder="Deskripsi marketing untuk display menu..."
          ></textarea>
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
            :disabled="updating"
            class="px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg disabled:opacity-50"
          >
            {{ updating ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: { type: Boolean, required: true },
  selectedMenu: { type: Object, default: null },
  displayForm: { type: Object, required: true },
  updating: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'submit']);

const localDisplayForm = computed({
  get: () => props.displayForm.deskripsi_display,
  set: (value) => {
    props.displayForm.deskripsi_display = value;
  }
});
</script>
