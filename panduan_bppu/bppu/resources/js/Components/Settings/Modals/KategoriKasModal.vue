<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-bold text-gray-900">
          {{ editingKategori ? 'Edit Kategori Kas' : 'Tambah Kategori Kas Baru' }}
        </h3>
      </div>

      <form @submit.prevent="$emit('save')" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Nama Kategori <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.nama"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            placeholder="Contoh: Penjualan"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Tipe <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.tipe"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
          >
            <option value="">Pilih Tipe</option>
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Kode Kategori</label>
          <input
            v-model="form.kode"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            placeholder="Contoh: IN-01"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
          <textarea
            v-model="form.deskripsi"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            placeholder="Deskripsi singkat tentang kategori"
          ></textarea>
        </div>

        <div class="flex items-center space-x-2">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="kk_is_active"
            class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900"
          />
          <label for="kk_is_active" class="text-sm font-medium text-gray-700">
            Kategori Aktif
          </label>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
  editingKategori: Object,
  form: Object,
  loading: Boolean,
});

defineEmits(['close', 'save']);
</script>
