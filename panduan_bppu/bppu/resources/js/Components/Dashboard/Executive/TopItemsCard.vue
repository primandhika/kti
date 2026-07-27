<template>
  <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ title }}</h3>
    <div class="space-y-3">
      <div
        v-for="(item, index) in items"
        :key="item.barang_id || index"
        class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors"
      >
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
            :style="{ backgroundColor: getRankColor(index) }"
          >
            {{ index + 1 }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ item.nama_barang }}
            </p>
            <p class="text-xs text-gray-500">
              {{ item.total_qty }} {{ item.satuan }} terjual
            </p>
          </div>
        </div>
        <div class="text-right ml-4">
          <p class="text-sm font-semibold text-gray-900">
            {{ formatCurrency(item.total_revenue) }}
          </p>
          <p class="text-xs text-gray-500">
            {{ item.transaction_count }} transaksi
          </p>
        </div>
      </div>
      <div v-if="items.length === 0" class="text-center py-8 text-sm text-gray-500">
        Belum ada data penjualan
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: 'Item Terlaris'
  },
  items: {
    type: Array,
    required: true
  }
});

const formatCurrency = (amount) => {
  return `Rp ${Math.round(amount).toLocaleString('id-ID')}`;
};

const getRankColor = (index) => {
  if (index === 0) return '#996600';
  if (index === 1) return '#6b4700';
  if (index === 2) return '#7a5100';
  return '#b7934c';
};
</script>
