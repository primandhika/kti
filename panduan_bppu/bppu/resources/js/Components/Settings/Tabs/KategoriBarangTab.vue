<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Manajemen Kategori Barang</h3>
        <p class="text-xs text-gray-500 mt-1">Geser untuk mengubah urutan tampilan kategori</p>
      </div>
      <button
        @click="$emit('add-kategori')"
        class="px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Kategori
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-[#996600]"></div>
      <p class="text-sm text-gray-600 mt-2">Memuat...</p>
    </div>

    <div v-else-if="!kategoriList || kategoriList.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori barang</h3>
      <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat kategori pertama</p>
    </div>

    <div v-else class="space-y-2">
      <div
        v-for="(kategori, index) in kategoriList"
        :key="kategori.id"
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
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <!-- Drag Handle -->
            <div class="text-gray-400 hover:text-gray-600 flex-shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
              </svg>
            </div>

            <!-- Order Number -->
            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#f4efe5] text-[#7a5100] text-sm font-bold flex-shrink-0">
              {{ index + 1 }}
            </div>

            <!-- Kode -->
            <span v-if="kategori.kode" class="text-xs font-mono font-semibold text-[#7a5100] bg-[#f4efe5] px-2 py-1 rounded flex-shrink-0">
              {{ kategori.kode }}
            </span>

            <!-- Nama -->
            <div class="flex-1 min-w-0">
              <span class="text-sm font-medium text-gray-900 truncate block">{{ kategori.nama }}</span>
              <span v-if="kategori.deskripsi" class="text-xs text-gray-500 truncate block">{{ kategori.deskripsi }}</span>
            </div>

            <!-- Jumlah Barang -->
            <span class="text-sm text-gray-500 flex-shrink-0">
              {{ kategori.barangs_count || 0 }} barang
            </span>

            <!-- Status Toggle -->
            <button
              @click.stop="$emit('toggle-status', kategori)"
              :class="[
                'px-2 py-1 text-xs font-medium rounded-full transition-colors flex-shrink-0',
                kategori.is_active
                  ? 'bg-green-100 text-green-800 hover:bg-green-200'
                  : 'bg-red-100 text-red-800 hover:bg-red-200'
              ]"
            >
              {{ kategori.is_active ? 'Aktif' : 'Nonaktif' }}
            </button>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 ml-4 flex-shrink-0">
            <button
              @click.stop="$emit('edit-kategori', kategori)"
              class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              title="Edit"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button
              @click.stop="$emit('delete-kategori', kategori)"
              class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              :disabled="(kategori.barangs_count || 0) > 0"
              :class="{ 'opacity-50 cursor-not-allowed': (kategori.barangs_count || 0) > 0 }"
              :title="(kategori.barangs_count || 0) > 0 ? 'Tidak dapat menghapus kategori yang masih digunakan' : 'Hapus kategori'"
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
  kategoriList: {
    type: Array,
    default: () => []
  },
  loading: Boolean,
});

const emit = defineEmits(['add-kategori', 'edit-kategori', 'delete-kategori', 'toggle-status', 'reorder']);

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
  if (event.target.classList.contains('border-2')) {
    dragOverIndex.value = null;
  }
}

function handleDrop(event, dropIndex) {
  event.preventDefault();

  if (draggedIndex.value === null || draggedIndex.value === dropIndex) {
    return;
  }

  const items = [...props.kategoriList];
  const draggedItem = items[draggedIndex.value];

  items.splice(draggedIndex.value, 1);
  items.splice(dropIndex, 0, draggedItem);

  const reorderedItems = items.map((item, index) => ({
    ...item,
    display_order: index + 1,
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
