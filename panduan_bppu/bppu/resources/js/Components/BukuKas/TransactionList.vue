<template>
    <div>
        <!-- Empty State -->
        <div v-if="transaksi.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
            <p class="mt-1 text-sm text-gray-500">
                {{ hasActiveFilters ? 'Tidak ada transaksi yang sesuai dengan filter' : 'Transaksi akan muncul di sini' }}
            </p>
        </div>

        <!-- Mobile Cards View -->
        <div v-else class="md:hidden">
            <div
                v-for="item in transaksi.data"
                :key="item.id"
                class="border-b-2 border-[#eae0cc] p-4 hover:bg-[#f4efe5] transition-colors duration-200"
            >
                <!-- Card Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center text-xs text-gray-500 mb-1">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ item.tanggal }}
                        </div>
                        <p class="text-sm font-semibold text-gray-900 line-clamp-2">{{ item.deskripsi }}</p>
                    </div>
                    <div class="flex space-x-1 ml-2">
                        <button
                            @click="$emit('edit', item)"
                            class="p-2 text-[#996600] hover:bg-[#eae0cc] rounded-lg transition-all duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            @click="$emit('delete', item)"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="space-y-2 text-xs">
                    <div class="mb-2">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-xs font-mono text-gray-500">{{ item.transaction_id }}</p>
                            <!-- Source Type Badge -->
                            <span v-if="item.source_type === 'unit-kerja'" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Import
                            </span>
                            <span v-else-if="item.source_type === 'kas-lain'" class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Transfer
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ item.kategori }}</p>
                        <p v-if="item.jenis_transaksi" class="text-xs text-[#996600] mt-1 inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ item.jenis_transaksi }}
                        </p>
                    </div>

                    <div v-if="item.unit_kerja_name" class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-[#996600] font-medium">{{ item.unit_kerja_name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <div class="bg-green-50 p-2 rounded border border-green-200">
                            <p class="text-gray-500 mb-1">Pemasukan</p>
                            <p v-if="item.pemasukan > 0" class="font-bold text-green-700">Rp {{ formatNumber(item.pemasukan) }}</p>
                            <p v-else class="text-gray-400">-</p>
                        </div>
                        <div class="bg-red-50 p-2 rounded border border-red-200">
                            <p class="text-gray-500 mb-1">Pengeluaran</p>
                            <p v-if="item.pengeluaran > 0" class="font-bold text-red-700">Rp {{ formatNumber(item.pengeluaran) }}</p>
                            <p v-else class="text-gray-400">-</p>
                        </div>
                    </div>

                    <div v-if="item.bukti_transaksi || item.bukti_transaksi_link || item.bukti_aktivitas || item.bukti_aktivitas_link" class="flex items-center space-x-2 pt-2">
                        <a v-if="item.bukti_transaksi_type === 'upload' && item.bukti_transaksi" :href="`/storage/${item.bukti_transaksi}`" target="_blank"
                           class="flex items-center text-blue-600 hover:text-blue-800 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Bukti Transaksi
                        </a>
                        <a v-if="item.bukti_transaksi_type === 'link' && item.bukti_transaksi_link" :href="item.bukti_transaksi_link" target="_blank"
                           class="flex items-center text-blue-600 hover:text-blue-800 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Bukti Transaksi
                        </a>
                        <a v-if="item.bukti_aktivitas_type === 'upload' && item.bukti_aktivitas" :href="`/storage/${item.bukti_aktivitas}`" target="_blank"
                           class="flex items-center text-blue-600 hover:text-blue-800 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Bukti Aktivitas
                        </a>
                        <a v-if="item.bukti_aktivitas_type === 'link' && item.bukti_aktivitas_link" :href="item.bukti_aktivitas_link" target="_blank"
                           class="flex items-center text-blue-600 hover:text-blue-800 text-xs">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Bukti Aktivitas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div v-if="transaksi.data.length > 0" class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#eae0cc]">
                    <tr>
                        <th
                            @click="$emit('sort', 'tanggal')"
                            class="px-4 py-3 text-left text-xs font-semibold text-[#6b4700] uppercase tracking-wider cursor-pointer hover:bg-[#d6c199] transition-colors"
                        >
                            <div class="flex items-center">
                                Tanggal
                                <svg v-if="sortField === 'tanggal'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                        <th
                            @click="$emit('sort', 'kategori')"
                            class="px-4 py-3 text-left text-xs font-semibold text-[#6b4700] uppercase tracking-wider cursor-pointer hover:bg-[#d6c199] transition-colors"
                        >
                            <div class="flex items-center">
                                Detail Transaksi
                                <svg v-if="sortField === 'kategori'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b4700] uppercase tracking-wider">Unit Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b4700] uppercase tracking-wider">Deskripsi</th>
                        <th
                            @click="$emit('sort', 'pemasukan')"
                            class="px-4 py-3 text-right text-xs font-semibold text-[#6b4700] uppercase tracking-wider cursor-pointer hover:bg-[#d6c199] transition-colors"
                        >
                            <div class="flex items-center justify-end">
                                Pemasukan
                                <svg v-if="sortField === 'pemasukan'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                        <th
                            @click="$emit('sort', 'pengeluaran')"
                            class="px-4 py-3 text-right text-xs font-semibold text-[#6b4700] uppercase tracking-wider cursor-pointer hover:bg-[#d6c199] transition-colors"
                        >
                            <div class="flex items-center justify-end">
                                Pengeluaran
                                <svg v-if="sortField === 'pengeluaran'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-[#6b4700] uppercase tracking-wider">Bukti</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-[#6b4700] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in transaksi.data" :key="item.id" class="hover:bg-[#f4efe5] transition-colors duration-150">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ item.tanggal }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-xs font-mono text-gray-500">{{ item.transaction_id }}</p>
                                    <!-- Source Type Badge -->
                                    <span v-if="item.source_type === 'unit-kerja'" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Import
                                    </span>
                                    <span v-else-if="item.source_type === 'kas-lain'" class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Transfer
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ item.kategori }}</p>
                                <p v-if="item.jenis_transaksi" class="text-xs text-[#996600] mt-0.5">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ item.jenis_transaksi }}
                                    </span>
                                </p>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span v-if="item.unit_kerja_name" class="text-[#996600] font-medium">{{ item.unit_kerja_name }}</span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ item.deskripsi }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                            <span v-if="item.pemasukan > 0" class="font-bold text-green-600">
                                Rp {{ formatNumber(item.pemasukan) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                            <span v-if="item.pengeluaran > 0" class="font-bold text-red-600">
                                Rp {{ formatNumber(item.pengeluaran) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            <div class="flex justify-center space-x-2">
                                <a v-if="item.bukti_transaksi_type === 'upload' && item.bukti_transaksi" :href="`/storage/${item.bukti_transaksi}`" target="_blank"
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center" title="Bukti Transaksi (File)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a v-if="item.bukti_transaksi_type === 'link' && item.bukti_transaksi_link" :href="item.bukti_transaksi_link" target="_blank"
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center" title="Bukti Transaksi (Link)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </a>
                                <a v-if="item.bukti_aktivitas_type === 'upload' && item.bukti_aktivitas" :href="`/storage/${item.bukti_aktivitas}`" target="_blank"
                                   class="text-green-600 hover:text-green-900 inline-flex items-center" title="Bukti Aktivitas (File)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>
                                <a v-if="item.bukti_aktivitas_type === 'link' && item.bukti_aktivitas_link" :href="item.bukti_aktivitas_link" target="_blank"
                                   class="text-green-600 hover:text-green-900 inline-flex items-center" title="Bukti Aktivitas (Link)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </a>
                                <span v-if="!item.bukti_transaksi && !item.bukti_transaksi_link && !item.bukti_aktivitas && !item.bukti_aktivitas_link" class="text-gray-400">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                            <div class="flex justify-center space-x-2">
                                <button
                                    @click="$emit('edit', item)"
                                    class="p-1 text-[#996600] hover:bg-[#eae0cc] rounded transition-all duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="$emit('delete', item)"
                                    class="p-1 text-red-600 hover:bg-red-50 rounded transition-all duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="transaksi.data.length > 0" class="px-4 md:px-6 py-4 border-t-2 border-[#eae0cc] bg-white">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Menampilkan <span class="font-semibold">{{ transaksi.from }}</span> sampai <span class="font-semibold">{{ transaksi.to }}</span> dari <span class="font-semibold">{{ transaksi.total }}</span> transaksi
                </div>
                <div class="flex items-center space-x-1">
                    <button
                        v-for="(link, index) in transaksi.links"
                        :key="index"
                        @click="$emit('page-change', link.url)"
                        :disabled="!link.url"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
                            link.active
                                ? 'bg-[#996600] text-white shadow-md'
                                : link.url
                                ? 'bg-white border border-[#d6c199] text-gray-700 hover:border-[#996600] hover:bg-[#f4efe5]'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        ]"
                    >
                        <template v-if="index === 0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </template>
                        <template v-else-if="index === transaksi.links.length - 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </template>
                        <template v-else>
                            {{ link.label }}
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    transaksi: {
        type: Object,
        required: true
    },
    hasActiveFilters: Boolean,
    sortField: String,
    sortDirection: String,
});

defineEmits(['edit', 'delete', 'sort', 'page-change']);

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number || 0);
};
</script>
