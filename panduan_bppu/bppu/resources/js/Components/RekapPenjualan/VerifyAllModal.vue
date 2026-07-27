<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] text-white p-4 rounded-t-lg">
        <h3 class="text-lg font-bold">Verifikasi Semua Transaksi</h3>
      </div>
      <div class="p-4">
        <p class="text-sm text-gray-700 mb-4">
          Yakin ingin verifikasi semua transaksi yang belum diverifikasi di periode ini?
        </p>
        <div class="bg-[#eae0cc] rounded p-3 text-xs">
          <div class="flex justify-between mb-1">
            <span>Periode:</span>
            <span class="font-semibold">{{ filters.start_date }} s/d {{ filters.end_date }}</span>
          </div>
          <div v-if="filters.work_unit_id" class="flex justify-between">
            <span>Unit Kerja:</span>
            <span class="font-semibold">{{ workUnitName }}</span>
          </div>
        </div>
      </div>
      <div class="p-4 bg-gray-50 rounded-b-lg flex gap-2">
        <button
          @click="$emit('close')"
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100"
        >
          Batal
        </button>
        <button
          @click="$emit('confirm')"
          class="flex-1 px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] font-semibold"
        >
          Verifikasi Semua
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  filters: Object,
  workUnitName: String,
})

defineEmits(['close', 'confirm'])
</script>
