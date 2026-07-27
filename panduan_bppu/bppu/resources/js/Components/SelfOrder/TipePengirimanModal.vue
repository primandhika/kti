<template>
    <Teleport to="body">
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[80] overflow-y-auto bg-black/50">
                <div class="flex min-h-full items-end sm:items-center justify-center p-4 pb-[env(keyboard-inset-height,1rem)]">
                <transition
                    enter-active-class="transition-transform duration-200"
                    enter-from-class="translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="translate-y-0 sm:scale-100"
                >
                    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-5 py-4">
                            <h2 class="text-base font-bold text-white">Bagaimana cara terima pesanan?</h2>
                            <p class="text-xs text-white/80 mt-0.5">Pilih sesuai kebutuhan kamu</p>
                        </div>

                        <!-- Options -->
                        <div class="px-5 py-4 space-y-3">
                            <!-- Antar ke meja -->
                            <button
                                type="button"
                                @click="selected = 'antar'"
                                class="w-full flex items-start gap-3 p-3.5 rounded-xl border-2 transition-all text-left"
                                :class="selected === 'antar'
                                    ? 'border-[#996600] bg-[#f4efe5]'
                                    : 'border-gray-200 hover:border-gray-300 bg-white'"
                            >
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                    :class="selected === 'antar' ? 'bg-[#996600]' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="selected === 'antar' ? 'text-white' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold" :class="selected === 'antar' ? 'text-[#6b4700]' : 'text-gray-800'">
                                        Antar ke Meja/Ruangan
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                                        Pesanan akan diantar langsung ke meja atau ruangan kamu
                                    </p>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-1 flex items-center justify-center"
                                    :class="selected === 'antar' ? 'border-[#996600] bg-[#996600]' : 'border-gray-300'">
                                    <div v-if="selected === 'antar'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </button>

                            <!-- Pickup -->
                            <button
                                type="button"
                                @click="selected = 'pickup'"
                                class="w-full flex items-start gap-3 p-3.5 rounded-xl border-2 transition-all text-left"
                                :class="selected === 'pickup'
                                    ? 'border-[#996600] bg-[#f4efe5]'
                                    : 'border-gray-200 hover:border-gray-300 bg-white'"
                            >
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                    :class="selected === 'pickup' ? 'bg-[#996600]' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="selected === 'pickup' ? 'text-white' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold" :class="selected === 'pickup' ? 'text-[#6b4700]' : 'text-gray-800'">
                                        Ambil Sendiri (Pickup)
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                                        Pesanan diambil langsung di kantin/toko oleh pembeli
                                    </p>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-1 flex items-center justify-center"
                                    :class="selected === 'pickup' ? 'border-[#996600] bg-[#996600]' : 'border-gray-300'">
                                    <div v-if="selected === 'pickup'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </button>

                            <!-- Estimasi waktu ambil (hanya pickup) -->
                            <transition
                                enter-active-class="transition-all duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <div v-if="selected === 'pickup'" class="bg-[#f4efe5] border border-[#ccb27f] rounded-xl p-3.5 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#996600] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <label class="text-xs font-semibold text-[#6b4700]">Kapan kira-kira pesanan akan diambil?</label>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button
                                            v-for="opt in estimasiOptions"
                                            :key="opt"
                                            type="button"
                                            @click="estimasi = opt"
                                            class="px-2 py-1.5 text-xs rounded-lg border transition-all font-medium"
                                            :class="estimasi === opt
                                                ? 'border-[#996600] bg-[#996600] text-white'
                                                : 'border-[#ccb27f] bg-white text-[#6b4700] hover:bg-[#eae0cc]'"
                                        >
                                            {{ opt }}
                                        </button>
                                    </div>
                                    <input
                                        v-model="estimasiCustom"
                                        type="text"
                                        maxlength="100"
                                        placeholder="Atau ketik sendiri, mis. 'Setelah istirahat'"
                                        @focus="estimasi = ''"
                                        class="w-full px-3 py-2 text-xs border border-[#ccb27f] rounded-lg bg-white focus:ring-2 focus:ring-[#996600] focus:border-transparent outline-none"
                                    />
                                </div>
                            </transition>
                        </div>

                        <!-- Footer -->
                        <div class="px-5 pb-5 flex gap-2">
                            <button
                                @click="$emit('close')"
                                class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                @click="submit"
                                :disabled="!selected"
                                class="flex-1 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white font-bold text-sm rounded-lg transition-colors disabled:opacity-50"
                            >
                                Lanjut Pesan
                            </button>
                        </div>
                    </div>
                </transition>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'confirm']);

const selected = ref('antar');
const estimasi = ref('');
const estimasiCustom = ref('');

const estimasiOptions = ['Segera (~5 menit)', '10-15 menit', '15-30 menit', 'Nanti saat istirahat'];

watch(() => props.show, (v) => {
    if (v) {
        selected.value = 'antar';
        estimasi.value = '';
        estimasiCustom.value = '';
    }
});

const submit = () => {
    if (!selected.value) return;
    const finalEstimasi = selected.value === 'pickup'
        ? (estimasiCustom.value.trim() || estimasi.value || null)
        : null;
    emit('confirm', { tipe: selected.value, estimasi: finalEstimasi });
};
</script>
