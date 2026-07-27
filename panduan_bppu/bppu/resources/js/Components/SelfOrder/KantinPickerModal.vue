<template>
    <teleport to="body">
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[300] flex items-end sm:items-center justify-center">
                <div class="absolute inset-0 bg-black/60" @click="!required && $emit('select', null)" />

                <div class="relative bg-white w-full sm:max-w-sm sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden">
                    <!-- Handle bar (mobile) -->
                    <div class="sm:hidden flex justify-center pt-3 pb-1">
                        <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
                    </div>

                    <div class="px-5 pt-4 pb-2 text-center">
                        <div class="w-12 h-12 rounded-full bg-[#f4efe5] flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Pilih kantin terlebih dahulu</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            <template v-if="required">Kamu harus memilih kantin sebelum melanjutkan</template>
                            <template v-else>Pilih kantin untuk melihat menu yang tersedia</template>
                        </p>
                    </div>

                    <div class="px-4 pb-2 space-y-2 max-h-72 overflow-y-auto">
                        <button
                            v-for="workUnit in workUnits"
                            :key="workUnit.id"
                            @click="$emit('select', workUnit.id)"
                            class="w-full flex items-center gap-3 p-3 rounded-xl border-2 border-gray-100 hover:border-[#996600] hover:bg-[#f4efe5] transition-all text-left group"
                        >
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-[#eae0cc] flex items-center justify-center flex-shrink-0 ring-2 ring-[#ccb27f] group-hover:ring-[#996600] transition-all">
                                <img v-if="workUnit.logo" :src="`/storage/${workUnit.logo}`" :alt="workUnit.name" class="w-full h-full object-cover" />
                                <svg v-else class="w-5 h-5 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ workUnit.name }}</p>
                                <p v-if="workUnit.location" class="text-xs text-gray-400 truncate">{{ workUnit.location }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-[#996600] flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div v-if="!required" class="px-4 py-3 border-t border-gray-100">
                        <button
                            @click="$emit('select', null)"
                            class="w-full py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            Lihat semua kantin
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
defineProps({
    show:      { type: Boolean, default: false },
    workUnits: { type: Array,   default: () => [] },
    required:  { type: Boolean, default: false },
});

defineEmits(['select']);
</script>
