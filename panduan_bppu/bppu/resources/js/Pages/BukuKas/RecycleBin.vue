<template>
    <AdminLayout page-title="Tempat Sampah Buku Kas" page-subtitle="Kelola buku kas yang dihapus">
        <div class="p-6">
            <!-- Breadcrumbs -->
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a :href="route('admin.bukukas.index')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#996600]">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Buku Kas
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tempat Sampah</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Empty State -->
            <div v-if="!bukuKas.data || bukuKas.data.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tempat sampah kosong</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada buku kas yang dihapus</p>
            </div>

            <!-- Cards Grid -->
            <div v-else>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                    <div
                        v-for="buku in bukuKas.data"
                        :key="buku.id"
                        class="relative bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden opacity-75"
                    >
                        <div class="p-3">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 pr-2 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate mb-1">
                                        {{ buku.nama }}
                                    </h3>
                                    <p v-if="buku.keterangan" class="text-xs text-gray-600 line-clamp-1 mb-2">{{ buku.keterangan }}</p>

                                    <!-- Jenis Badge -->
                                    <div v-if="buku.jenis_buku_kas" class="mb-1">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium text-white"
                                            :style="{ backgroundColor: buku.jenis_buku_kas.warna }"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span>{{ buku.jenis_buku_kas.kode }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex items-center gap-3 mb-2 text-xs text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ buku.created_at }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ buku.user_name }}</span>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-1.5 pt-2 border-t border-gray-200 mb-3">
                                <div class="bg-green-50 p-1.5 rounded border border-green-200">
                                    <p class="text-xs font-medium text-green-700 mb-0.5">Pemasukan</p>
                                    <p class="text-xs font-bold text-green-700 truncate">{{ formatNumber(buku.total_pemasukan) }}</p>
                                </div>
                                <div class="bg-red-50 p-1.5 rounded border border-red-200">
                                    <p class="text-xs font-medium text-red-700 mb-0.5">Pengeluaran</p>
                                    <p class="text-xs font-bold text-red-700 truncate">{{ formatNumber(buku.total_pengeluaran) }}</p>
                                </div>
                                <div :class="[
                                    'p-1.5 rounded border',
                                    buku.saldo >= 0
                                        ? 'bg-[#eae0cc] border-[#996600]'
                                        : 'bg-red-50 border-red-300'
                                ]">
                                    <p :class="['text-xs font-medium mb-0.5', buku.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700']">Saldo</p>
                                    <p class="text-xs font-bold truncate" :class="buku.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700'">
                                        {{ formatNumber(buku.saldo) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <button
                                    @click="confirmRestore(buku)"
                                    class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-all flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Pulihkan
                                </button>
                                <button
                                    @click="confirmPermanentDelete(buku)"
                                    class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Permanen
                                </button>
                            </div>
                        </div>

                        <!-- Deleted Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Terhapus
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="bukuKas.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 rounded-lg shadow-sm">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <a
                            v-if="bukuKas.prev_page_url"
                            :href="bukuKas.prev_page_url"
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Previous
                        </a>
                        <a
                            v-if="bukuKas.next_page_url"
                            :href="bukuKas.next_page_url"
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ bukuKas.from }}</span>
                                to
                                <span class="font-medium">{{ bukuKas.to }}</span>
                                of
                                <span class="font-medium">{{ bukuKas.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <a
                                    v-if="bukuKas.prev_page_url"
                                    :href="bukuKas.prev_page_url"
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a
                                    v-for="page in bukuKas.links.slice(1, -1)"
                                    :key="page.label"
                                    :href="page.url"
                                    :class="[
                                        page.active
                                            ? 'z-10 bg-[#996600] text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#996600]'
                                            : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                                        'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                                    ]"
                                >
                                    {{ page.label }}
                                </a>
                                <a
                                    v-if="bukuKas.next_page_url"
                                    :href="bukuKas.next_page_url"
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    bukuKas: Object,
});

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
};

const confirmRestore = (buku) => {
    if (confirm(`Pulihkan buku kas "${buku.nama}"?`)) {
        router.put(route('admin.bukukas.restore', buku.id), {}, {
            onSuccess: () => {
                toast.success('Buku kas berhasil dipulihkan');
            },
            onError: () => {
                toast.error('Gagal memulihkan buku kas');
            }
        });
    }
};

const confirmPermanentDelete = (buku) => {
    if (confirm(`PERINGATAN: Hapus permanen buku kas "${buku.nama}"?\n\nSemua data transaksi akan ikut terhapus PERMANEN dan TIDAK BISA dikembalikan!\n\nKetik nama buku kas untuk konfirmasi.`)) {
        const userInput = prompt(`Ketik nama buku kas untuk konfirmasi: "${buku.nama}"`);
        if (userInput === buku.nama) {
            router.delete(route('admin.bukukas.permanent-delete', buku.id), {
                onSuccess: () => {
                    toast.success('Buku kas berhasil dihapus permanen');
                },
                onError: () => {
                    toast.error('Gagal menghapus buku kas');
                }
            });
        } else {
            toast.error('Nama buku kas tidak cocok. Penghapusan dibatalkan.');
        }
    }
};
</script>
