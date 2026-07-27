<template>
    <div class="space-y-2">
        <!-- Label + badge terkunci -->
        <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-gray-700">Antar ke Meja</label>
            <span v-if="isLocked" class="text-[10px] bg-[#f4efe5] text-[#996600] font-semibold px-2 py-0.5 rounded-full border border-[#ccb27f]">
                Terkunci
            </span>
        </div>

        <!-- Meja sudah dipilih -->
        <div
            v-if="selectedMeja"
            class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg border"
            :class="isLocked ? 'bg-[#f4efe5] border-[#ccb27f]' : 'bg-green-50 border-green-300'"
        >
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 flex-shrink-0" :class="isLocked ? 'text-[#996600]' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-xs font-bold truncate" :class="isLocked ? 'text-[#6b4700]' : 'text-green-800'">
                        {{ selectedMeja.nama || selectedMeja.kode_meja }}
                    </p>
                    <p v-if="selectedMeja.lokasi" class="text-[10px] truncate" :class="isLocked ? 'text-[#996600]' : 'text-green-600'">
                        {{ selectedMeja.lokasi }}
                    </p>
                </div>
            </div>
            <button
                v-if="!isLocked"
                @click="$emit('clear')"
                class="text-gray-400 hover:text-red-500 transition-colors flex-shrink-0 p-0.5"
                title="Ganti meja"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Belum pilih meja -->
        <button
            v-else
            @click="$emit('open')"
            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-dashed border-[#ccb27f] text-[#996600] hover:bg-[#f4efe5] transition-colors text-xs font-semibold"
        >
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pilih meja tujuan antar...
        </button>
    </div>
</template>

<script setup>
defineProps({
    selectedMeja: { type: Object, default: null },
    isLocked: { type: Boolean, default: false },
});

defineEmits(['open', 'clear']);
</script>
