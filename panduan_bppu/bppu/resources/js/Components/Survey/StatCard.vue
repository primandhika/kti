<template>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <p class="text-xs text-gray-500">{{ label }}</p>
    <p class="text-2xl font-bold text-[#996600] mt-1">{{ value }}</p>
    <p class="text-xs text-gray-400 mt-0.5">dari 5</p>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: Number,
});
</script>
