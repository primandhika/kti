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
                    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden mb-0 sm:mb-0">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-5 py-4">
                            <h2 class="text-base font-bold text-white">Siapa nama Anda?</h2>
                            <p class="text-xs text-white/80 mt-0.5">Nama akan ditampilkan di antrian kantin</p>
                        </div>

                        <!-- Body -->
                        <div class="px-5 py-4 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pemesan</label>
                                <input
                                    ref="inputRef"
                                    v-model="nama"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Contoh: Budi"
                                    @keydown.enter="submit"
                                    class="w-full px-3 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent outline-none transition"
                                    :class="error ? 'border-red-400' : 'border-gray-300'"
                                />
                                <p v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</p>
                            </div>

                            <!-- Sugesti login -->
                            <button
                                type="button"
                                @click="$emit('open-login')"
                                class="w-full flex items-center gap-3 px-3 py-2.5 bg-[#f4efe5] border border-[#ccb27f] rounded-lg hover:bg-[#eae0cc] transition-colors text-left group"
                            >
                                <div class="w-8 h-8 rounded-full bg-[#996600] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#6b4700]">Punya akun? Login &amp; raih poin!</p>
                                    <p class="text-[11px] text-[#996600] mt-0.5">Setiap pembelian kamu dapat poin reward</p>
                                </div>
                                <svg class="w-4 h-4 text-[#996600] flex-shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
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
                                class="flex-1 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white font-bold text-sm rounded-lg transition-colors"
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
    initialNama: { type: String, default: '' },
});

const emit = defineEmits(['close', 'confirm', 'open-login']);

const nama = ref(props.initialNama);
const error = ref('');
const inputRef = ref(null);

watch(() => props.show, (v) => {
    if (v) {
        error.value = '';
        nama.value = props.initialNama || '';
        // Tidak auto-focus agar keyboard mobile tidak langsung menutupi modal
    }
});

const submit = () => {
    const trimmed = nama.value.trim();
    if (!trimmed) {
        error.value = 'Nama tidak boleh kosong';
        return;
    }
    emit('confirm', trimmed);
};
</script>
