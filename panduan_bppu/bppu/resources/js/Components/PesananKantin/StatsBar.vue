<template>
    <div class="grid grid-cols-5 gap-2">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <p class="text-xl font-black text-yellow-700">{{ stats.menunggu }}</p>
            <p class="text-xs text-yellow-600 mt-0.5">Menunggu</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
            <p class="text-xl font-black text-blue-700">{{ stats.diproses }}</p>
            <p class="text-xs text-blue-600 mt-0.5">Diproses</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            <p class="text-xl font-black text-green-700">{{ stats.siap }}</p>
            <p class="text-xs text-green-600 mt-0.5">Siap</p>
        </div>
        <div class="bg-[#f4efe5] border border-[#c1a366] rounded-lg p-3 text-center">
            <p class="text-xl font-black text-[#996600]">{{ stats.selesai }}</p>
            <p class="text-xs text-[#7a5100] mt-0.5">Selesai</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
            <p class="text-xl font-black text-red-600">{{ stats.dibatalkan }}</p>
            <p class="text-xs text-red-500 mt-0.5">Batal</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: { type: Object, required: true },
})
</script>
