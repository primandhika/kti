<template>
  <div class="flex items-center justify-between">
    <span class="text-sm text-gray-600 w-40">{{ label }}</span>
    <div class="flex items-center space-x-1.5">
      <div
        v-for="n in 5"
        :key="n"
        :class="[
          'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors',
          n <= value
            ? 'bg-[#996600] text-white'
            : 'bg-gray-100 text-gray-400'
        ]"
      >
        {{ n }}
      </div>
      <span class="ml-2 text-sm font-semibold text-[#996600]">{{ value }}/5</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: Number,
});
</script>
