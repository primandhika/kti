<template>
  <div class="bg-white rounded-xl shadow-md border border-gray-100">
    <div class="p-6 border-b border-gray-100">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
        <div class="flex space-x-2">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="selectedTab = tab.value"
            class="px-3 py-1 text-xs font-medium rounded-lg transition-colors"
            :class="selectedTab === tab.value
              ? 'bg-[#996600] text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Unit Kerja
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Pendapatan
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Transaksi
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Kas Masuk
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Kas Keluar
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Kas Bersih
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr
            v-for="(unit, index) in sortedUnits"
            :key="unit.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  v-if="unit.logo"
                  class="w-10 h-10 rounded-lg mr-3 flex-shrink-0 overflow-hidden bg-gray-100"
                >
                  <img
                    :src="unit.logo"
                    :alt="unit.name"
                    class="w-full h-full object-cover"
                    @error="(e) => e.target.style.display = 'none'"
                  />
                </div>
                <div
                  v-else
                  class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-xs font-bold mr-3 flex-shrink-0"
                  :style="{ backgroundColor: getUnitColor(index) }"
                >
                  {{ unit.unit_id }}
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ unit.name }}</div>
                  <div class="text-xs text-gray-500">ID: {{ unit.unit_id }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="text-sm font-semibold text-gray-900">
                {{ formatCurrency(unit.revenue) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="text-sm text-gray-900">{{ unit.transaction_count }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="text-sm text-green-600 font-medium">
                {{ formatCurrency(unit.cash_income) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="text-sm text-red-600 font-medium">
                {{ formatCurrency(unit.cash_expenses) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div
                class="text-sm font-semibold"
                :class="unit.net_cash >= 0 ? 'text-green-600' : 'text-red-600'"
              >
                {{ formatCurrency(unit.net_cash) }}
              </div>
            </td>
          </tr>
          <tr v-if="units.length === 0">
            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
              Tidak ada data unit kerja
            </td>
          </tr>
        </tbody>
        <tfoot v-if="units.length > 0" class="bg-gray-50 font-semibold">
          <tr>
            <td class="px-6 py-4 text-sm text-gray-900">TOTAL</td>
            <td class="px-6 py-4 text-right text-sm text-gray-900">
              {{ formatCurrency(totals.revenue) }}
            </td>
            <td class="px-6 py-4 text-right text-sm text-gray-900">
              {{ totals.transactions }}
            </td>
            <td class="px-6 py-4 text-right text-sm text-green-600">
              {{ formatCurrency(totals.income) }}
            </td>
            <td class="px-6 py-4 text-right text-sm text-red-600">
              {{ formatCurrency(totals.expenses) }}
            </td>
            <td class="px-6 py-4 text-right text-sm" :class="totals.net >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(totals.net) }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Performa Unit Kerja'
  },
  units: {
    type: Array,
    required: true
  }
});

const selectedTab = ref('revenue');

const tabs = [
  { label: 'Pendapatan', value: 'revenue' },
  { label: 'Transaksi', value: 'transaction' },
  { label: 'Kas Bersih', value: 'net' }
];

const sortedUnits = computed(() => {
  const sortKey = selectedTab.value === 'revenue' ? 'revenue'
    : selectedTab.value === 'transaction' ? 'transaction_count'
    : 'net_cash';

  return [...props.units].sort((a, b) => b[sortKey] - a[sortKey]);
});

const totals = computed(() => {
  return {
    revenue: props.units.reduce((sum, u) => sum + u.revenue, 0),
    transactions: props.units.reduce((sum, u) => sum + u.transaction_count, 0),
    income: props.units.reduce((sum, u) => sum + u.cash_income, 0),
    expenses: props.units.reduce((sum, u) => sum + u.cash_expenses, 0),
    net: props.units.reduce((sum, u) => sum + u.net_cash, 0),
  };
});

const formatCurrency = (amount) => {
  return `Rp ${Math.round(amount).toLocaleString('id-ID')}`;
};

const getUnitColor = (index) => {
  const colors = [
    '#996600', '#6b4700', '#7a5100', '#895b00', '#5b3d00',
    '#4c3300', '#3d2800', '#2d1e00'
  ];
  return colors[index % colors.length];
};
</script>
