<template>
  <div class="bg-gradient-to-b from-[#f4efe5] to-white rounded-t-xl shadow-sm p-3 -mb-2">
    <div class="flex flex-wrap gap-2">
      <button
        @click="$emit('showAllKategori')"
        :class="[
          'px-3 py-1.5 rounded-full text-xs font-medium transition-all',
          showAllKategori
            ? 'bg-[#996600] text-white shadow-md scale-105'
            : 'bg-white/80 text-gray-700 hover:bg-white hover:shadow-sm'
        ]"
      >
        Semua <span class="ml-1 opacity-75">({{ totalMenus }})</span>
      </button>
      <button
        v-for="sub in subKategoriList"
        :key="sub.label"
        @click="$emit('selectKategori', sub.label)"
        :class="[
          'px-3 py-1.5 rounded-full text-xs font-medium transition-all',
          !showAllKategori && activeSubKategori === sub.label
            ? 'bg-[#996600] text-white shadow-md scale-105'
            : 'bg-white/80 text-gray-700 hover:bg-white hover:shadow-sm'
        ]"
      >
        {{ sub.label }} <span class="ml-1 opacity-75">({{ sub.count }})</span>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  showAllKategori: { type: Boolean, required: true },
  subKategoriList: { type: Array, required: true },
  activeSubKategori: { type: String, required: true },
  totalMenus: { type: Number, required: true },
});

defineEmits(['selectKategori', 'showAllKategori']);
</script>
