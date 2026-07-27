<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Manajemen Unit Kerja</h3>
        <p class="text-sm text-gray-500 mt-1">Kelola unit-unit kerja yang ada di BPPU</p>
      </div>
      <button
        @click="$emit('add-unit')"
        class="px-4 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Unit Kerja</span>
      </button>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID Unit
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nama Unit
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipe
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Lokasi
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Pengelola
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                Memuat data...
              </td>
            </tr>
            <tr v-else-if="unitsList.length === 0">
              <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                Belum ada unit kerja
              </td>
            </tr>
            <tr v-else v-for="unit in unitsList" :key="unit.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-mono font-semibold text-primary-900">#{{ unit.unit_id }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ unit.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary-100 text-primary-900">
                  {{ unit.type }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ unit.location || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ unit.manager_name || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="$emit('toggle-unit-active', unit)"
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full transition-colors',
                    unit.is_active
                      ? 'bg-green-100 text-green-800 hover:bg-green-200'
                      : 'bg-red-100 text-red-800 hover:bg-red-200'
                  ]"
                >
                  {{ unit.is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="$emit('edit-unit', unit)"
                  class="text-primary-900 hover:text-primary-950 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="$emit('delete-unit', unit)"
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
  unitsList: {
    type: Array,
    default: () => []
  },
  loading: Boolean,
});

defineEmits(['add-unit', 'edit-unit', 'delete-unit', 'toggle-unit-active']);
</script>
