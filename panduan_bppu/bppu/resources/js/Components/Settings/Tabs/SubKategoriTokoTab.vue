<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Sub Kategori Toko</h3>
        <p class="text-xs text-gray-500 mt-1">Geser kartu untuk mengubah urutan tampilan</p>
      </div>
      <button
        @click="$emit('add-sub-kategori')"
        class="px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Sub Kategori
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-[#996600]"></div>
      <p class="text-sm text-gray-600 mt-2">Memuat...</p>
    </div>

    <div v-else-if="!subKategoriList || subKategoriList.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada sub kategori</h3>
      <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat sub kategori pertama</p>
    </div>

    <div v-else class="space-y-2">
      <div
        v-for="(subKat, index) in subKategoriList"
        :key="subKat.id"
        :draggable="true"
        @dragstart="handleDragStart($event, index)"
        @dragover.prevent="handleDragOver($event, index)"
        @dragenter="handleDragEnter($event, index)"
        @dragleave="handleDragLeave"
        @drop="handleDrop($event, index)"
        @dragend="handleDragEnd"
        :class="[
          'bg-white border-2 rounded-lg p-4 transition-all cursor-move',
          draggedIndex === index ? 'opacity-50 border-[#996600]' : 'border-gray-200',
          dragOverIndex === index ? 'border-[#996600] border-dashed' : ''
        ]"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 flex-1">
            <!-- Drag Handle -->
            <div class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
              </svg>
            </div>

            <!-- Order Number -->
            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#f4efe5] text-[#7a5100] text-sm font-bold">
              {{ index + 1 }}
            </div>

            <!-- Category Name -->
            <div class="flex-1">
              <div class="flex flex-col gap-1">
                <span class="px-3 py-1.5 text-sm font-medium rounded-lg bg-[#f4efe5] text-[#7a5100]">
                  {{ subKat.nama }}
                </span>
                <span v-if="subKat.kategori_nama" class="text-xs text-gray-500 ml-3">
                  {{ subKat.kategori_nama }}
                </span>
              </div>
            </div>

            <!-- Item Count -->
            <div class="text-sm text-gray-500">
              {{ subKat.jumlah_barang || 0 }} barang
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 ml-4">
            <button
              @click="$emit('edit-sub-kategori', subKat)"
              class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              title="Edit"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button
              @click="$emit('delete-sub-kategori', subKat.id)"
              class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              :disabled="subKat.jumlah_barang > 0"
              :class="{ 'opacity-50 cursor-not-allowed': subKat.jumlah_barang > 0 }"
              :title="subKat.jumlah_barang > 0 ? 'Tidak dapat menghapus kategori yang masih digunakan' : 'Hapus kategori'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  subKategoriList: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['add-sub-kategori', 'edit-sub-kategori', 'delete-sub-kategori', 'reorder']);

const draggedIndex = ref(null);
const dragOverIndex = ref(null);

function handleDragStart(event, index) {
  draggedIndex.value = index;
  event.dataTransfer.effectAllowed = 'move';
  event.dataTransfer.setData('text/html', event.target.innerHTML);
}

function handleDragOver(event, index) {
  event.preventDefault();
  event.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(event, index) {
  dragOverIndex.value = index;
}

function handleDragLeave(event) {
  // Only clear if we're leaving the container, not a child element
  if (event.target.classList.contains('border-2')) {
    dragOverIndex.value = null;
  }
}

function handleDrop(event, dropIndex) {
  event.preventDefault();

  if (draggedIndex.value === null || draggedIndex.value === dropIndex) {
    return;
  }

  const items = [...props.subKategoriList];
  const draggedItem = items[draggedIndex.value];

  // Remove dragged item
  items.splice(draggedIndex.value, 1);

  // Insert at new position
  items.splice(dropIndex, 0, draggedItem);

  // Update urutan for all items
  const reorderedItems = items.map((item, index) => ({
    ...item,
    urutan: index + 1
  }));

  emit('reorder', reorderedItems);

  draggedIndex.value = null;
  dragOverIndex.value = null;
}

function handleDragEnd() {
  draggedIndex.value = null;
  dragOverIndex.value = null;
}
</script>
