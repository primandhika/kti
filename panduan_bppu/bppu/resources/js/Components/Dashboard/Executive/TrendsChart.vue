<template>
  <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
      <div class="flex space-x-2">
        <button
          v-for="period in periods"
          :key="period.value"
          @click="selectedPeriod = period.value"
          class="px-3 py-1 text-xs font-medium rounded-lg transition-colors"
          :class="selectedPeriod === period.value
            ? 'bg-[#996600] text-white'
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
        >
          {{ period.label }}
        </button>
      </div>
    </div>

    <div class="relative h-64">
      <canvas ref="chartCanvas"></canvas>
    </div>

    <div v-if="showLegend" class="mt-4 flex flex-wrap gap-4">
      <div v-for="(dataset, index) in datasets" :key="index" class="flex items-center">
        <div
          class="w-3 h-3 rounded-full mr-2"
          :style="{ backgroundColor: dataset.color }"
        ></div>
        <span class="text-sm text-gray-600">{{ dataset.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  title: {
    type: String,
    default: 'Trends'
  },
  labels: {
    type: Array,
    required: true
  },
  datasets: {
    type: Array,
    required: true
  },
  showLegend: {
    type: Boolean,
    default: true
  },
  chartType: {
    type: String,
    default: 'line'
  }
});

const chartCanvas = ref(null);
const selectedPeriod = ref('6m');
let chartInstance = null;

const periods = [
  { label: '3M', value: '3m' },
  { label: '6M', value: '6m' },
  { label: '1Y', value: '1y' }
];

const initChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
  }

  if (!chartCanvas.value) return;

  const ctx = chartCanvas.value.getContext('2d');

  const chartDatasets = props.datasets.map(dataset => ({
    label: dataset.label,
    data: dataset.data,
    borderColor: dataset.color,
    backgroundColor: dataset.fill ? `${dataset.color}20` : 'transparent',
    borderWidth: 2,
    tension: 0.4,
    fill: dataset.fill || false,
    pointRadius: 3,
    pointHoverRadius: 5
  }));

  chartInstance = new Chart(ctx, {
    type: props.chartType,
    data: {
      labels: props.labels,
      datasets: chartDatasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              if (context.parsed.y !== null) {
                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
              }
              return label;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              if (value >= 1000000) {
                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
              } else if (value >= 1000) {
                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
              }
              return 'Rp ' + value;
            }
          },
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });
};

onMounted(() => {
  initChart();
});

watch(() => [props.labels, props.datasets], () => {
  initChart();
}, { deep: true });
</script>
