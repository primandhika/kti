<template>
  <span :class="badgeClass" class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold">
    {{ value }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: Number,
});

const badgeClass = computed(() => {
  if (props.value >= 5) return 'bg-green-100 text-green-800';
  if (props.value >= 4) return 'bg-[#996600]/15 text-[#6b4700]';
  if (props.value >= 3) return 'bg-yellow-100 text-yellow-800';
  if (props.value >= 2) return 'bg-orange-100 text-orange-800';
  return 'bg-red-100 text-red-800';
});
</script>
