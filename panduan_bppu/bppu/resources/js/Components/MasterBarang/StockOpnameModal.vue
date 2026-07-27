<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl">
        <h2 class="text-xl font-bold text-gray-800">Stock Opname</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="$emit('submit', form)" class="p-6 space-y-4">
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="font-semibold text-gray-900 mb-2">{{ barang?.nama_barang }}</h3>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-600">Kode:</span>
              <span class="font-medium ml-2">{{ barang?.kode_barang }}</span>
            </div>
            <div>
              <span class="text-gray-600">Stock Sistem:</span>
              <span class="font-bold ml-2 text-blue-600">{{ barang?.stok }}</span>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Opname *</label>
          <input
            v-model="form.opname_date"
            type="datetime-local"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Stock Fisik (Hasil Hitung) *</label>
          <input
            v-model.number="form.stock_fisik"
            type="number"
            min="0"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            placeholder="Masukkan hasil perhitungan fisik"
          />
          <div v-if="form.stock_fisik !== null" class="mt-2 text-sm">
            <span class="text-gray-600">Selisih:</span>
            <span
              :class="[
                'font-bold ml-2',
                (form.stock_fisik - (barang?.stok || 0)) > 0 ? 'text-green-600' :
                (form.stock_fisik - (barang?.stok || 0)) < 0 ? 'text-red-600' : 'text-gray-600'
              ]"
            >
              {{ (form.stock_fisik - (barang?.stok || 0)) > 0 ? '+' : '' }}
              {{ form.stock_fisik - (barang?.stok || 0) }}
            </span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Pengentri</label>
          <input
            type="text"
            :value="userName"
            disabled
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
          <textarea
            v-model="form.keterangan"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            placeholder="Catatan tambahan (opsional)"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
          >
            Simpan Opname
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  barang: Object,
  form: Object,
  userName: {
    type: String,
    default: 'sysadmin',
  },
});

defineEmits(['close', 'submit']);
</script>
