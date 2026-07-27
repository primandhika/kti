<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Manajemen Kategori Barang</h3>
        <p class="text-sm text-gray-500 mt-1">Kelola kategori untuk pengelompokan barang</p>
      </div>
      <button
        @click="openAddModal"
        class="px-4 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Kategori</span>
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Barang</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td>
            </tr>
            <tr v-else-if="kategoris.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada kategori barang</td>
            </tr>
            <tr v-else v-for="kategori in kategoris" :key="kategori.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-mono font-semibold text-primary-900">{{ kategori.kode || '-' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ kategori.nama }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500">{{ kategori.deskripsi || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                  {{ kategori.barangs_count || 0 }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="$emit('toggle-status', kategori)"
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full transition-colors',
                    kategori.is_active
                      ? 'bg-green-100 text-green-800 hover:bg-green-200'
                      : 'bg-red-100 text-red-800 hover:bg-red-200'
                  ]"
                >
                  {{ kategori.is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openEditModal(kategori)"
                  class="text-primary-900 hover:text-primary-950 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="$emit('delete', kategori)"
                  class="text-red-600 hover:text-red-900"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ isEditing ? 'Edit Kategori Barang' : 'Tambah Kategori Barang' }}
          </h3>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
            <input
              v-model="form.nama"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kode</label>
            <input
              v-model="form.kode"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea
              v-model="form.deskripsi"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            ></textarea>
          </div>
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900"
            />
            <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors disabled:opacity-50"
            >
              {{ submitting ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  kategoris: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['save', 'delete', 'toggle-status']);

const showModal = ref(false);
const isEditing = ref(false);
const editingKategori = ref(null);
const submitting = ref(false);

const form = ref({
  nama: '',
  kode: '',
  deskripsi: '',
  is_active: true
});

const openAddModal = () => {
  isEditing.value = false;
  editingKategori.value = null;
  form.value = {
    nama: '',
    kode: '',
    deskripsi: '',
    is_active: true
  };
  showModal.value = true;
};

const openEditModal = (kategori) => {
  isEditing.value = true;
  editingKategori.value = kategori;
  form.value = {
    nama: kategori.nama,
    kode: kategori.kode || '',
    deskripsi: kategori.deskripsi || '',
    is_active: kategori.is_active
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  isEditing.value = false;
  editingKategori.value = null;
};

const handleSubmit = async () => {
  submitting.value = true;
  try {
    await emit('save', {
      data: form.value,
      id: editingKategori.value?.id,
      isEditing: isEditing.value
    });
    closeModal();
  } catch (error) {
    // Error handled by parent
  } finally {
    submitting.value = false;
  }
};
</script>
