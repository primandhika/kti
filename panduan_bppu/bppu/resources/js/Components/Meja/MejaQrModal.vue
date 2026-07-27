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
            <div v-if="show && meja" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xs p-5 flex flex-col items-center gap-4">
                    <!-- Header -->
                    <div class="w-full flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">QR Meja</h3>
                        <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Info meja -->
                    <div class="text-center">
                        <p class="text-lg font-black text-gray-900">{{ meja.nama || meja.kode_meja }}</p>
                        <p v-if="meja.nomor" class="text-xs text-gray-500">No. {{ meja.nomor }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ meja.kode_meja }}</p>
                    </div>

                    <!-- Toggle kantin / toko -->
                    <div class="flex w-full rounded-lg overflow-hidden border border-gray-200 text-xs font-semibold">
                        <button
                            @click="setType('kantin')"
                            :class="activeType === 'kantin' ? 'bg-[#996600] text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="flex-1 py-1.5 transition-colors"
                        >
                            Kantin
                        </button>
                        <button
                            @click="setType('belanja')"
                            :class="activeType === 'belanja' ? 'bg-[#996600] text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="flex-1 py-1.5 transition-colors border-l border-gray-200"
                        >
                            Belanja
                        </button>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white border-2 border-[#996600] rounded-xl p-3">
                        <canvas ref="qrCanvas" class="block" />
                    </div>

                    <!-- URL hint -->
                    <p class="text-[10px] text-gray-400 text-center break-all px-2">{{ activeUrl }}</p>

                    <!-- Actions -->
                    <div class="flex gap-2 w-full">
                        <button
                            @click="downloadQr"
                            class="flex-1 py-2 text-xs font-semibold text-[#996600] border border-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors"
                        >
                            Unduh
                        </button>
                        <button
                            @click="$emit('close')"
                            class="flex-1 py-2 text-xs font-semibold text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
    show: { type: Boolean, default: false },
    meja: { type: Object, default: null },
});

defineEmits(['close']);

const qrCanvas  = ref(null);
const activeType = ref('kantin');

const activeUrl = computed(() => {
    if (!props.meja) return '';
    return activeType.value === 'belanja'
        ? props.meja.belanja_order_url
        : props.meja.self_order_url;
});

const QR_OPTS = {
    width: 200,
    margin: 1,
    color: { dark: '#2d1e00', light: '#ffffff' },
};

const renderQr = async () => {
    await nextTick();
    if (!qrCanvas.value || !activeUrl.value) return;
    await QRCode.toCanvas(qrCanvas.value, activeUrl.value, QR_OPTS);
};

function setType(type) {
    activeType.value = type;
    renderQr();
}

const downloadQr = () => {
    if (!qrCanvas.value) return;
    const link = document.createElement('a');
    link.download = `qr-${activeType.value}-${props.meja.kode_meja}.png`;
    link.href = qrCanvas.value.toDataURL('image/png');
    link.click();
};

watch(() => [props.show, props.meja], ([show]) => {
    if (show) {
        activeType.value = 'kantin';
        nextTick(renderQr);
    }
});
</script>
