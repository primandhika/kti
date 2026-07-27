<template>
    <div
        @click="$emit('click')"
        class="group relative bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 hover:border-[#996600] cursor-pointer overflow-hidden transform hover:-translate-y-0.5"
    >
        <div class="p-2.5">
            <!-- Induk/Anak indicator -->
            <div v-if="buku.is_induk || buku.is_anak" class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                <span v-if="buku.is_induk" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Buku Induk
                </span>
                <span v-if="buku.is_anak" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Sudah Diimport
                </span>
            </div>

            <!-- Header -->
            <div class="flex items-start justify-between mb-1.5">
                <div class="flex-1 pr-2 min-w-0">
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-[#996600] transition-colors duration-300 truncate">
                        {{ buku.nama }}
                    </h3>
                    <p v-if="buku.keterangan" class="text-xs text-gray-600 line-clamp-1 mt-0.5">{{ buku.keterangan }}</p>
                </div>
                <div class="flex items-center gap-1" @click.stop>
                    <!-- Assign Jenis Button / Badge -->
                    <button
                        v-if="!isReadOnly"
                        @click="showJenisPicker = true"
                        :class="[
                            'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium transition-all',
                            buku.jenis_buku_kas
                                ? 'text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-300'
                        ]"
                        :style="buku.jenis_buku_kas ? { backgroundColor: buku.jenis_buku_kas.warna } : {}"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>{{ buku.jenis_buku_kas ? buku.jenis_buku_kas.kode : 'Label' }}</span>
                    </button>
                    <span
                        v-else-if="buku.jenis_buku_kas"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium text-white"
                        :style="{ backgroundColor: buku.jenis_buku_kas.warna }"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>{{ buku.jenis_buku_kas.kode }}</span>
                    </span>
                    <button
                        v-if="!isReadOnly"
                        @click="$emit('edit', buku)"
                        class="p-1 text-[#996600] hover:bg-[#996600] hover:text-white rounded transition-all duration-300"
                        title="Edit"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button
                        v-if="!isReadOnly"
                        @click="$emit('delete', buku)"
                        class="p-1 text-red-600 hover:bg-red-600 hover:text-white rounded transition-all duration-300"
                        title="Hapus"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Info -->
            <div class="flex items-center gap-2 mb-1.5 text-xs text-gray-600">
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ buku.created_at }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ buku.user_name }}</span>
                    <span v-if="isSuperAdmin && buku.user_username" class="text-[#996600] font-mono">({{ buku.user_username }})</span>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-1.5 pt-1.5 border-t border-gray-200">
                <div class="bg-green-50 p-1 rounded border border-green-200">
                    <p class="text-xs font-medium text-green-700">Pemasukan</p>
                    <p class="text-xs font-bold text-green-700 truncate">{{ formatNumber(buku.total_pemasukan) }}</p>
                </div>
                <div class="bg-red-50 p-1 rounded border border-red-200">
                    <p class="text-xs font-medium text-red-700">Pengeluaran</p>
                    <p class="text-xs font-bold text-red-700 truncate">{{ formatNumber(buku.total_pengeluaran) }}</p>
                </div>
                <div :class="[
                    'p-1 rounded border',
                    buku.saldo >= 0
                        ? 'bg-[#eae0cc] border-[#996600]'
                        : 'bg-red-50 border-red-300'
                ]">
                    <p :class="['text-xs font-medium', buku.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700']">Saldo</p>
                    <p class="text-xs font-bold truncate" :class="buku.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700'">
                        {{ formatNumber(buku.saldo) }}
                    </p>
                </div>
            </div>

            <!-- Sumber kas (hanya tampil untuk buku induk) -->
            <div v-if="buku.is_induk && buku.sumber_kas && buku.sumber_kas.length" class="mt-1.5 pt-1.5 border-t border-amber-200">
                <p class="text-xs text-amber-700 font-medium mb-1">Merekap dari:</p>
                <div class="flex flex-wrap gap-1">
                    <span
                        v-for="(nama, i) in buku.sumber_kas"
                        :key="i"
                        class="text-xs px-1.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 rounded"
                    >
                        {{ nama }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Jenis Picker Modal -->
        <JenisPicker
            v-model="showJenisPicker"
            :jenis-list="jenisBukuKasList"
            :current-jenis-id="buku.jenis_buku_kas?.id"
            @select="handleJenisSelect"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import JenisPicker from './JenisPicker.vue';

const toast = useToast();
const showJenisPicker = ref(false);

const props = defineProps({
    buku: {
        type: Object,
        required: true,
    },
    jenisBukuKasList: {
        type: Array,
        default: () => [],
    },
    isSuperAdmin: {
        type: Boolean,
        default: false,
    },
    isReadOnly: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['click', 'edit', 'delete']);

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
};

const handleJenisSelect = (jenisId) => {
    router.put(`/pengelola/buku-kas/${props.buku.id}/assign-jenis`, {
        jenis_buku_kas_id: jenisId || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showJenisPicker.value = false;
            toast.success('Label berhasil diupdate');
        },
        onError: () => {
            toast.error('Gagal mengupdate label');
        }
    });
};
</script>
