<template>
  <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ title }}</h3>
    <div class="space-y-4">
      <div
        v-for="(stat, index) in stats"
        :key="index"
        class="flex items-center justify-between p-4 rounded-lg"
        :class="stat.bgClass || 'bg-gray-50'"
      >
        <div class="flex items-center space-x-3">
          <div
            class="w-10 h-10 rounded-lg flex items-center justify-center"
            :class="stat.iconBgClass || 'bg-gray-200'"
          >
            <svg
              v-if="stat.icon"
              class="w-5 h-5"
              :class="stat.iconClass || 'text-gray-600'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                :d="getIconPath(stat.icon)"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900">{{ stat.label }}</p>
            <p v-if="stat.description" class="text-xs text-gray-600">{{ stat.description }}</p>
          </div>
        </div>
        <div class="text-right">
          <p
            class="text-lg font-bold"
            :class="stat.valueClass || 'text-gray-900'"
          >
            {{ formatValue(stat.value, stat.format) }}
          </p>
          <p v-if="stat.change !== undefined" class="text-xs" :class="stat.change >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ stat.change >= 0 ? '+' : '' }}{{ stat.change }}%
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  stats: {
    type: Array,
    required: true
  }
});

const formatValue = (value, format) => {
  if (format === 'currency') {
    return `Rp ${Math.round(value).toLocaleString('id-ID')}`;
  } else if (format === 'percentage') {
    return `${value}%`;
  }
  return value;
};

const getIconPath = (iconName) => {
  const icons = {
    'trending-up': 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'trending-down': 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6',
    'chart': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'money': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'wallet': 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
  };
  return icons[iconName] || icons['chart'];
};
</script>
