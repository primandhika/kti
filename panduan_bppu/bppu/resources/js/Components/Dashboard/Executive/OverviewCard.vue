<template>
  <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">
          <slot name="value">{{ formattedValue }}</slot>
        </p>
        <p v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</p>
      </div>
      <div
        class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
        :class="iconBgClass"
      >
        <component :is="iconComponent" class="w-7 h-7" :class="iconClass" />
      </div>
    </div>
    <div v-if="$slots.footer || footerText" class="mt-4 flex items-center text-sm">
      <slot name="footer">
        <span :class="footerClass">{{ footerText }}</span>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    default: 0
  },
  subtitle: {
    type: String,
    default: ''
  },
  footerText: {
    type: String,
    default: ''
  },
  footerClass: {
    type: String,
    default: 'text-gray-600'
  },
  icon: {
    type: String,
    default: 'chart'
  },
  iconBgClass: {
    type: String,
    default: 'bg-blue-100'
  },
  iconClass: {
    type: String,
    default: 'text-blue-600'
  },
  formatType: {
    type: String,
    default: 'number'
  }
});

const formattedValue = computed(() => {
  if (props.formatType === 'currency') {
    return formatCurrency(props.value);
  } else if (props.formatType === 'percentage') {
    return `${props.value}%`;
  }
  return props.value;
});

const formatCurrency = (amount) => {
  if (amount >= 1000000000) {
    return `Rp ${(amount / 1000000000).toFixed(1)}B`;
  } else if (amount >= 1000000) {
    return `Rp ${(amount / 1000000).toFixed(1)}M`;
  } else if (amount >= 1000) {
    return `Rp ${(amount / 1000).toFixed(0)}K`;
  }
  return `Rp ${amount.toLocaleString('id-ID')}`;
};

const iconComponent = computed(() => {
  const icons = {
    money: 'svg',
    wallet: 'svg',
    building: 'svg',
    chart: 'svg',
    users: 'svg',
    box: 'svg',
    trending: 'svg'
  };
  return icons[props.icon] || 'svg';
});
</script>
