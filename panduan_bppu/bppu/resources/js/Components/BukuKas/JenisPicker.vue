<template>
    <Teleport to="body">
        <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('update:modelValue', false)"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Pilih Jenis Buku Kas</h3>
                        <button @click="$emit('update:modelValue', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2 mb-6 max-h-96 overflow-y-auto">
                        <!-- Option: Tidak ada jenis -->
                        <button
                            @click="selectJenis(null)"
                            :class="[
                                'w-full text-left p-3 rounded-lg border-2 transition-all duration-200',
                                !selectedJenis
                                    ? 'border-gray-400 bg-gray-50'
                                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Tanpa Label</p>
                                    <p class="text-xs text-gray-500">Buku kas umum tanpa kategori khusus</p>
                                </div>
                                <svg v-if="!selectedJenis" class="w-5 h-5 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>

                        <!-- Jenis Buku Kas Options -->
                        <button
                            v-for="jenis in jenisList"
                            :key="jenis.id"
                            @click="selectJenis(jenis.id)"
                            :class="[
                                'w-full text-left p-3 rounded-lg border-2 transition-all duration-200',
                                selectedJenis === jenis.id
                                    ? 'bg-opacity-10'
                                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                            ]"
                            :style="selectedJenis === jenis.id ? {
                                borderColor: jenis.warna,
                                backgroundColor: jenis.warna + '20'
                            } : {}"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span
                                        :style="{ backgroundColor: jenis.warna }"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white font-bold text-sm"
                                    >
                                        {{ jenis.kode }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ jenis.nama }}</p>
                                        <p class="text-xs text-gray-500">{{ jenis.deskripsi }}</p>
                                    </div>
                                </div>
                                <svg v-if="selectedJenis === jenis.id" class="w-5 h-5" :style="{ color: jenis.warna }" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            @click="$emit('update:modelValue', false)"
                            class="px-5 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all"
                        >
                            Batal
                        </button>
                        <button
                            @click="confirm"
                            class="px-5 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-all"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: Boolean,
    jenisList: Array,
    currentJenisId: [Number, String],
});

const emit = defineEmits(['update:modelValue', 'select']);

const selectedJenis = ref(props.currentJenisId || null);

watch(() => props.currentJenisId, (newVal) => {
    selectedJenis.value = newVal || null;
});

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        selectedJenis.value = props.currentJenisId || null;
    }
});

const selectJenis = (jenisId) => {
    selectedJenis.value = jenisId;
};

const confirm = () => {
    emit('select', selectedJenis.value);
    emit('update:modelValue', false);
};
</script>
