<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Manajemen Kategori Kas</h3>
        <p class="text-sm text-gray-500 mt-1">Kelola kategori untuk transaksi kas</p>
      </div>
      <button
        @click="$emit('add-kategori')"
        class="px-4 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Kategori</span>
      </button>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td>
            </tr>
            <tr v-else-if="kategoriList.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada kategori kas</td>
            </tr>
            <tr v-else v-for="kategori in kategoriList" :key="kategori.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-mono font-semibold text-primary-900">{{ kategori.kode || '-' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ kategori.nama }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  kategori.tipe === 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ kategori.tipe }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500">{{ kategori.deskripsi || '-' }}</div>
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
                  @click="$emit('edit-kategori', kategori)"
                  class="text-primary-900 hover:text-primary-950 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="$emit('delete-kategori', kategori)"
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
  </div>
</template>

<script setup>
defineProps({
  kategoriList: {
    type: Array,
    default: () => []
  },
  loading: Boolean,
});

defineEmits(['add-kategori', 'edit-kategori', 'delete-kategori', 'toggle-status']);
</script>
