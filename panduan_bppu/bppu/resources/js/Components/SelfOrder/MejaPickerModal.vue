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
            <div v-if="show" class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />

                <!-- Panel -->
                <div class="relative bg-white w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl shadow-2xl max-h-[80vh] flex flex-col overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-100 flex-shrink-0">
                        <h3 class="text-sm font-bold text-gray-900">Pilih Meja Tujuan Antar</h3>
                        <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="px-4 py-2.5 border-b border-gray-100 flex-shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari meja atau lokasi..."
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                        />
                    </div>

                    <!-- List -->
                    <div class="overflow-y-auto flex-1 p-3 space-y-3">
                        <div v-if="filteredLokasis.length === 0" class="py-8 text-center text-sm text-gray-400">
                            Tidak ada meja ditemukan
                        </div>

                        <div
                            v-for="lokasi in filteredLokasis"
                            :key="lokasi.id"
                        >
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 px-1">
                                {{ lokasi.nama }}
                            </p>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    v-for="meja in lokasi.mejas"
                                    :key="meja.id"
                                    @click="selectMeja(meja, lokasi)"
                                    class="flex flex-col items-center justify-center p-2.5 rounded-xl border-2 transition-all text-center"
                                    :class="selectedId === meja.id
                                        ? 'border-[#996600] bg-[#f4efe5] text-[#996600]'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-[#ccb27f] hover:bg-[#f4efe5]'"
                                >
                                    <span class="text-xs font-bold leading-tight">{{ meja.nama || meja.kode_meja }}</span>
                                    <span v-if="meja.nomor" class="text-[10px] text-gray-500 mt-0.5">No. {{ meja.nomor }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-3 border-t border-gray-100 flex-shrink-0 flex gap-2">
                        <button
                            @click="$emit('close')"
                            class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            v-if="pendingMeja"
                            @click="confirm"
                            class="flex-1 py-2.5 text-sm font-bold text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors"
                        >
                            Pilih Meja Ini
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    lokasiMejas: { type: Array, default: () => [] },
    currentMeja: { type: Object, default: null },
});

const emit = defineEmits(['close', 'select']);

const search = ref('');
const pendingMeja = ref(null);

const selectedId = computed(() => pendingMeja.value?.id ?? props.currentMeja?.id ?? null);

const filteredLokasis = computed(() => {
    if (!search.value) return props.lokasiMejas;
    const q = search.value.toLowerCase();
    return props.lokasiMejas
        .map(l => ({
            ...l,
            mejas: l.mejas.filter(m =>
                (m.nama && m.nama.toLowerCase().includes(q)) ||
                m.kode_meja.toLowerCase().includes(q) ||
                (m.nomor && m.nomor.toLowerCase().includes(q)) ||
                l.nama.toLowerCase().includes(q)
            ),
        }))
        .filter(l => l.mejas.length > 0);
});

const selectMeja = (meja, lokasi) => {
    pendingMeja.value = { ...meja, lokasi: lokasi.nama };
};

const confirm = () => {
    if (pendingMeja.value) {
        emit('select', pendingMeja.value);
        pendingMeja.value = null;
        search.value = '';
    }
};

// Reset saat modal dibuka
import { watch } from 'vue';
watch(() => props.show, (v) => {
    if (v) {
        pendingMeja.value = null;
        search.value = '';
    }
});
</script>
