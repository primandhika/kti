<template>
  <div class="space-y-1">
    <label class="text-sm font-medium text-gray-700">{{ label }}</label>
    <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-800 leading-relaxed border border-gray-200 min-h-[48px] whitespace-pre-wrap">
      {{ value || '-' }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: String,
});
</script>
