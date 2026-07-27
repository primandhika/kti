<template>
  <AdminLayout page-title="Dashboard Eksekutif">
    <div class="space-y-6">
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold mb-2">Dashboard Eksekutif</h1>
            <p class="text-[#d6c199]">Selamat datang, {{ $page.props.auth.user.name }}</p>
            <p class="text-sm text-[#d6c199] mt-1">
              Analitik dan Laporan Strategis Badan Pengelola Pengembangan Usaha
            </p>
          </div>
          <div class="text-right">
            <p class="text-sm text-[#d6c199]">{{ currentDate }}</p>
            <p class="text-xs text-[#d6c199]">Periode: Bulan Ini</p>
          </div>
        </div>
      </div>

      <DashboardFilter
        :period="filters.period"
        :start-date="filters.start_date"
        :end-date="filters.end_date"
      />

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pendapatan Bulan Ini</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">
                {{ formatCurrency(financial.current_month_income) }}
              </p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span :class="financial.income_growth_percentage >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
              {{ financial.income_growth_percentage >= 0 ? '+' : '' }}{{ financial.income_growth_percentage }}%
            </span>
            <span class="text-gray-600 ml-2">dari bulan lalu</span>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pengeluaran Bulan Ini</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">
                {{ formatCurrency(financial.current_month_expenses) }}
              </p>
            </div>
            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span :class="financial.expenses_growth_percentage >= 0 ? 'text-red-600' : 'text-green-600'" class="font-medium">
              {{ financial.expenses_growth_percentage >= 0 ? '+' : '' }}{{ financial.expenses_growth_percentage }}%
            </span>
            <span class="text-gray-600 ml-2">dari bulan lalu</span>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Kas Bersih Bulan Ini</p>
              <p class="text-3xl font-bold mt-2" :class="financial.current_month_net >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ formatCurrency(financial.current_month_net) }}
              </p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-600">Rasio: {{ getExpenseRatio() }}%</span>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">
                {{ formatCurrency(sales.current_month_sales) }}
              </p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span :class="sales.sales_growth_percentage >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
              {{ sales.sales_growth_percentage >= 0 ? '+' : '' }}{{ sales.sales_growth_percentage }}%
            </span>
            <span class="text-gray-600 ml-2">{{ sales.current_month_count }} transaksi</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <StatCard
          title="Ringkasan Keuangan"
          :stats="financialStats"
        />

        <StatCard
          title="Operasional"
          :stats="operationalStats"
        />

        <StatCard
          title="Inventori"
          :stats="inventoryStats"
        />
      </div>

      <TrendsChart
        title="Tren Keuangan 6 Bulan Terakhir"
        :labels="trends.months"
        :datasets="trendsDatasets"
      />

      <WorkUnitTable
        title="Performa Unit Kerja Bulan Ini"
        :units="workUnits.units"
      />

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <TopItemsCard
          title="10 Item Terlaris Bulan Ini"
          :items="sales.top_items"
        />

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Performa per Kategori</h3>
          <div class="space-y-3">
            <div
              v-for="(cat, index) in categoryPerformance"
              :key="cat.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center space-x-3 flex-1">
                <div
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                  :style="{ backgroundColor: getCategoryColor(index) }"
                >
                  {{ index + 1 }}
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ cat.category_name }}</p>
                  <p class="text-xs text-gray-500">{{ cat.transaction_count }} transaksi</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">
                  {{ formatCurrency(cat.total_revenue) }}
                </p>
                <p class="text-xs text-gray-500">{{ cat.total_qty }} unit</p>
              </div>
            </div>
            <div v-if="categoryPerformance.length === 0" class="text-center py-8 text-sm text-gray-500">
              Belum ada data kategori
            </div>
          </div>
        </div>
      </div>

      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-900">Dashboard Eksekutif - Mode Analitik</p>
            <p class="text-xs text-blue-700 mt-1">
              Dashboard ini menyediakan analitik komprehensif untuk pengambilan keputusan strategis.
              Data diambil real-time dari sistem dan diperbarui secara otomatis.
            </p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/Dashboard/Executive/StatCard.vue';
import TrendsChart from '@/Components/Dashboard/Executive/TrendsChart.vue';
import WorkUnitTable from '@/Components/Dashboard/Executive/WorkUnitTable.vue';
import TopItemsCard from '@/Components/Dashboard/Executive/TopItemsCard.vue';
import DashboardFilter from '@/Components/Dashboard/Executive/DashboardFilter.vue';

const props = defineProps({
  overview: Object,
  financial: Object,
  workUnits: Object,
  sales: Object,
  inventory: Object,
  trends: Object,
  categoryPerformance: Array,
  filters: Object,
});

const currentDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const financialStats = computed(() => [
  {
    label: 'Total Kas Masuk',
    value: props.financial.total_income,
    format: 'currency',
    icon: 'trending-up',
    iconBgClass: 'bg-green-600',
    iconClass: 'text-white',
    bgClass: 'bg-green-50',
    valueClass: 'text-green-600'
  },
  {
    label: 'Total Kas Keluar',
    value: props.financial.total_expenses,
    format: 'currency',
    icon: 'trending-down',
    iconBgClass: 'bg-red-600',
    iconClass: 'text-white',
    bgClass: 'bg-red-50',
    valueClass: 'text-red-600'
  },
  {
    label: 'Saldo Bersih',
    value: props.financial.net_balance,
    format: 'currency',
    icon: 'wallet',
    iconBgClass: 'bg-blue-600',
    iconClass: 'text-white',
    bgClass: 'bg-blue-50',
    valueClass: props.financial.net_balance >= 0 ? 'text-blue-600' : 'text-red-600'
  }
]);

const operationalStats = computed(() => [
  {
    label: 'Unit Kerja Aktif',
    value: props.overview.active_work_units,
    description: `dari ${props.overview.total_work_units} unit`,
    icon: 'chart',
    iconBgClass: 'bg-purple-600',
    iconClass: 'text-white',
    bgClass: 'bg-purple-50',
    valueClass: 'text-purple-600'
  },
  {
    label: 'Pengguna Aktif',
    value: props.overview.active_users,
    description: `dari ${props.overview.total_users} pengguna`,
    icon: 'chart',
    iconBgClass: 'bg-indigo-600',
    iconClass: 'text-white',
    bgClass: 'bg-indigo-50',
    valueClass: 'text-indigo-600'
  },
  {
    label: 'Mitra Usaha',
    value: props.overview.active_suppliers,
    description: `dari ${props.overview.total_suppliers} mitra`,
    icon: 'chart',
    iconBgClass: 'bg-yellow-600',
    iconClass: 'text-white',
    bgClass: 'bg-yellow-50',
    valueClass: 'text-yellow-600'
  }
]);

const inventoryStats = computed(() => [
  {
    label: 'Total Item Aktif',
    value: props.inventory.active_items,
    description: `dari ${props.inventory.total_items} item`,
    icon: 'chart',
    iconBgClass: 'bg-teal-600',
    iconClass: 'text-white',
    bgClass: 'bg-teal-50',
    valueClass: 'text-teal-600'
  },
  {
    label: 'Stock Rendah',
    value: props.inventory.low_stock_items,
    description: 'item perlu restock',
    icon: 'chart',
    iconBgClass: 'bg-orange-600',
    iconClass: 'text-white',
    bgClass: 'bg-orange-50',
    valueClass: 'text-orange-600'
  },
  {
    label: 'Nilai Inventori',
    value: props.inventory.total_inventory_value,
    format: 'currency',
    description: 'total nilai HPP',
    icon: 'money',
    iconBgClass: 'bg-emerald-600',
    iconClass: 'text-white',
    bgClass: 'bg-emerald-50',
    valueClass: 'text-emerald-600'
  }
]);

const trendsDatasets = computed(() => [
  {
    label: 'Pendapatan',
    data: props.trends.income,
    color: '#10b981',
    fill: true
  },
  {
    label: 'Pengeluaran',
    data: props.trends.expenses,
    color: '#ef4444',
    fill: false
  },
  {
    label: 'Penjualan',
    data: props.trends.sales,
    color: '#996600',
    fill: false
  }
]);

const formatCurrency = (amount) => {
  return `Rp ${Math.round(amount).toLocaleString('id-ID')}`;
};

const getExpenseRatio = () => {
  if (props.financial.current_month_income === 0) return 0;
  return ((props.financial.current_month_expenses / props.financial.current_month_income) * 100).toFixed(1);
};

const getCategoryColor = (index) => {
  const colors = [
    '#996600', '#6b4700', '#7a5100', '#895b00', '#5b3d00',
    '#4c3300', '#3d2800', '#2d1e00', '#b7934c', '#c1a366'
  ];
  return colors[index % colors.length];
};
</script>
