<template>
  <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
    <div class="flex flex-wrap items-end gap-4">
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
        <select
          v-model="localPeriod"
          @change="handlePeriodChange"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option value="current_month">Bulan Ini</option>
          <option value="last_month">Bulan Lalu</option>
          <option value="current_year">Tahun Ini</option>
          <option value="custom">Custom</option>
        </select>
      </div>

      <div v-if="localPeriod === 'custom'" class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
        <input
          type="date"
          v-model="localStartDate"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>

      <div v-if="localPeriod === 'custom'" class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
        <input
          type="date"
          v-model="localEndDate"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>

      <div class="flex gap-2">
        <button
          @click="applyFilter"
          class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors font-medium"
        >
          Terapkan
        </button>
        <button
          @click="exportData"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export CSV
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  period: {
    type: String,
    default: 'current_month'
  },
  startDate: {
    type: String,
    default: null
  },
  endDate: {
    type: String,
    default: null
  }
});

const localPeriod = ref(props.period);
const localStartDate = ref(props.startDate);
const localEndDate = ref(props.endDate);

const handlePeriodChange = () => {
  if (localPeriod.value !== 'custom') {
    applyFilter();
  }
};

const applyFilter = () => {
  const params = {
    period: localPeriod.value
  };

  if (localPeriod.value === 'custom') {
    params.start_date = localStartDate.value;
    params.end_date = localEndDate.value;
  }

  router.get(route('admin.dashboard'), params, {
    preserveState: true,
    preserveScroll: true
  });
};

const exportData = () => {
  const params = new URLSearchParams({
    period: localPeriod.value
  });

  if (localPeriod.value === 'custom') {
    params.append('start_date', localStartDate.value);
    params.append('end_date', localEndDate.value);
  }

  window.location.href = route('admin.dashboard.export') + '?' + params.toString();
};
</script>
