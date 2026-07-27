<template>
  <AdminLayout
    pageTitle="Laporan Stock Opname"
    :pageSubtitle="`${toko.name}${toko.location ? ' - ' + toko.location : ''}`"
  >
    <div class="space-y-3 md:space-y-4 pb-24 md:pb-6">
      <!-- Breadcrumbs -->
      <nav class="flex text-xs md:text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="inline-flex items-center">
            <Link href="/pengelola/opname-stock" class="inline-flex items-center hover:text-[#996600]">
              <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              Daftar Toko
            </Link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 font-medium text-gray-800">{{ toko.name }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Toolbar Compact -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm p-3 space-y-2">
        <!-- Row 1: Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
          <button
            @click="showAutoOpnameModal = true"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium"
            title="Auto Opname"
          >
            Auto Opname
          </button>
          <button
            @click="openAddModal"
            class="bg-[#996600] hover:bg-[#7a5100] text-white px-3 py-1.5 rounded text-xs font-medium"
            title="Tambah Stock Opname"
          >
            + Tambah SO
          </button>
          <button
            @click="showImportModal = true"
            class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-xs font-medium"
            title="Import CSV"
          >
            Impor CSV
          </button>
          <Link
            :href="`/pengelola/opname-stock/${toko.id}/barang`"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-medium"
            title="Master Barang"
          >
            Master ({{ toko.barangs_count || 0 }})
          </Link>
          <button
            @click="exportToCSV"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium"
            title="Export CSV"
          >
            Export CSV
          </button>
          <button
            @click="downloadPdf"
            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium"
            title="Download PDF"
          >
            PDF
          </button>
        </div>

        <!-- Row 2: Search & Filters -->
        <div class="flex flex-wrap items-center gap-2">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari PLU, barang..."
            class="px-3 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent flex-1 min-w-40"
          />
          <select
            v-model="filterPeriod"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option value="this_month">Bulan Ini</option>
            <option value="today">Hari Ini</option>
            <option value="this_week">Minggu Ini</option>
            <option value="last_month">Bulan Lalu</option>
            <option value="">Semua Periode</option>
          </select>
          <select
            v-model="sortBy"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option value="waktu">Waktu</option>
            <option value="alfabetis">Alfabetis</option>
          </select>
          <select
            v-model="sortOrder"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option value="desc">{{ sortBy === 'alfabetis' ? 'Z-A' : 'Baru-Lama' }}</option>
            <option value="asc">{{ sortBy === 'alfabetis' ? 'A-Z' : 'Lama-Baru' }}</option>
          </select>
        </div>
      </div>

      <!-- Info Box untuk alur kerja jika belum ada data -->
      <div v-if="!stockOpnames?.data || stockOpnames.data.length === 0" class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-lg font-medium text-blue-900 mb-2">Belum Ada Data Stock Opname</h3>
            <div class="text-sm text-blue-700 space-y-2">
              <p class="font-semibold">Untuk memulai stock opname, ikuti langkah berikut:</p>
              <ol class="list-decimal list-inside space-y-1 ml-2">
                <li>Klik tombol <span class="font-bold">"Master Barang ({{ toko.barangs_count || 0 }})"</span> di atas untuk kelola data barang</li>
                <li>Pastikan sudah ada barang di master data ({{ toko.barangs_count || 0 }} barang saat ini)</li>
                <li>Lakukan stock opname dari halaman Master Barang dengan klik tombol "Stock Opname" pada setiap barang</li>
                <li>Hasil stock opname akan muncul di halaman ini sebagai laporan</li>
              </ol>
              <div class="mt-4">
                <Link
                  :href="`/pengelola/opname-stock/${toko.id}/barang`"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  Buka Master Barang
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Compact -->
      <div v-if="displayedOpnames.length > 0" class="bg-white rounded-lg shadow-sm p-2.5">
        <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm">
          <div class="flex items-center gap-1.5">
            <span class="text-gray-600">Total (halaman ini):</span>
            <span class="font-bold text-gray-900">{{ displayedOpnames.length }}</span>
          </div>
          <div class="w-px h-4 bg-gray-300"></div>
          <div class="flex items-center gap-1.5">
            <span class="text-gray-600">Selisih (+):</span>
            <span class="font-bold text-green-600">{{ totalSelisihPlus }}</span>
          </div>
          <div class="w-px h-4 bg-gray-300"></div>
          <div class="flex items-center gap-1.5">
            <span class="text-gray-600">Selisih (-):</span>
            <span class="font-bold text-red-600">{{ totalSelisihMinus }}</span>
          </div>
          <div class="w-px h-4 bg-gray-300"></div>
          <div class="flex items-center gap-1.5">
            <span class="text-gray-600">Selisih Rp:</span>
            <span class="font-bold" :class="totalSelisihRupiah >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(totalSelisihRupiah) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Stock Opname Table (Desktop) -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal SO</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PLU</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi Barang</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">COGS</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Awal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Penyesuaian</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Selisih Qty</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Pokok</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Selisih Rupiah</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Expired Date</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(opname, index) in displayedOpnames" :key="opname.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-center text-sm text-gray-900">{{ paginationOffset + index + 1 }}</td>
                <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(opname.tanggal_so) }}</td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ opname.plu }}</td>
                <td class="px-4 py-3">
                  <div class="text-sm font-medium text-gray-900">{{ opname.deskripsi_barang }}</div>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ formatCurrency(opname.cogs) }}</td>
                <td class="px-4 py-3 text-sm text-center font-medium text-gray-900">{{ opname.stock_awal }}</td>
                <td class="px-4 py-3 text-sm text-center font-medium text-blue-600">{{ opname.penyesuaian }}</td>
                <td class="px-4 py-3 text-sm text-center">
                  <span
                    :class="[
                      'font-bold',
                      opname.selisih_quantity > 0 ? 'text-green-600' :
                      opname.selisih_quantity < 0 ? 'text-red-600' : 'text-gray-600'
                    ]"
                  >
                    {{ opname.selisih_quantity > 0 ? '+' : '' }}{{ opname.selisih_quantity }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ formatCurrency(opname.harga_pokok) }}</td>
                <td class="px-4 py-3 text-sm text-right">
                  <span
                    :class="[
                      'font-bold',
                      opname.selisih_rupiah > 0 ? 'text-green-600' :
                      opname.selisih_rupiah < 0 ? 'text-red-600' : 'text-gray-600'
                    ]"
                  >
                    {{ formatCurrency(opname.selisih_rupiah) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-center">
                  <span v-if="opname.expired_date && opname.kategori && opname.kategori.toLowerCase().includes('makanan')" :class="[
                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                    isExpiringSoon(opname.expired_date) ? 'bg-red-100 text-red-800' :
                    isExpiringThisMonth(opname.expired_date) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
                  ]">
                    {{ formatDate(opname.expired_date) }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button
                      @click="openEditModal(opname)"
                      class="text-blue-600 hover:text-blue-800 transition-colors"
                      title="Edit"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(opname)"
                      class="text-red-600 hover:text-red-800 transition-colors"
                      title="Hapus"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="displayedOpnames.length === 0">
                <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                  <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-sm">
                    {{ searchQuery || filterPeriod ? 'Tidak ada data stock opname yang sesuai dengan pencarian' : 'Belum ada data stock opname di toko ini' }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Card Layout (Mobile) -->
      <div class="md:hidden space-y-2">
        <div
          v-for="(opname, index) in displayedOpnames"
          :key="opname.id"
          class="bg-white rounded-lg shadow-sm p-2.5"
        >
          <div class="flex justify-between items-start mb-2">
            <div class="flex-1">
              <div class="flex items-center space-x-2 mb-1">
                <span class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ opname.plu }}</span>
                <span class="text-xs text-gray-500">{{ formatDate(opname.tanggal_so) }}</span>
              </div>
              <h3 class="font-semibold text-gray-900 text-sm">{{ opname.deskripsi_barang }}</h3>
            </div>
            <span
              :class="[
                'inline-flex items-center px-2 py-1 rounded-full text-xs font-bold ml-2',
                opname.selisih_quantity > 0 ? 'bg-green-100 text-green-800' :
                opname.selisih_quantity < 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ opname.selisih_quantity > 0 ? '+' : '' }}{{ opname.selisih_quantity }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs mb-2 bg-gray-50 p-2 rounded">
            <div>
              <span class="text-gray-600">Stock Awal:</span>
              <span class="font-medium text-gray-900 ml-1">{{ opname.stock_awal }}</span>
            </div>
            <div>
              <span class="text-gray-600">Penyesuaian:</span>
              <span class="font-medium text-blue-600 ml-1">{{ opname.penyesuaian }}</span>
            </div>
            <div>
              <span class="text-gray-600">COGS:</span>
              <span class="font-medium text-gray-900 ml-1">{{ formatCurrency(opname.cogs) }}</span>
            </div>
            <div>
              <span class="text-gray-600">Harga Pokok:</span>
              <span class="font-medium text-gray-900 ml-1">{{ formatCurrency(opname.harga_pokok) }}</span>
            </div>
          </div>

          <div class="flex items-center justify-between bg-blue-50 p-2 rounded mb-2">
            <span class="text-xs text-gray-600">Selisih Rupiah:</span>
            <span
              :class="[
                'font-bold text-sm',
                opname.selisih_rupiah > 0 ? 'text-green-600' :
                opname.selisih_rupiah < 0 ? 'text-red-600' : 'text-gray-600'
              ]"
            >
              {{ formatCurrency(opname.selisih_rupiah) }}
            </span>
          </div>

          <div v-if="opname.expired_date && opname.kategori && opname.kategori.toLowerCase().includes('makanan')" class="flex items-center justify-between bg-orange-50 p-2 rounded mb-2">
            <span class="text-xs text-gray-600">Expired Date:</span>
            <span :class="[
              'text-xs font-medium px-2 py-1 rounded-full',
              isExpiringSoon(opname.expired_date) ? 'bg-red-100 text-red-800' :
              isExpiringThisMonth(opname.expired_date) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
            ]">
              {{ formatDate(opname.expired_date) }}
            </span>
          </div>

          <div class="flex space-x-2">
            <button
              @click="openEditModal(opname)"
              class="flex-1 flex items-center justify-center space-x-1 px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Edit</span>
            </button>
            <button
              @click="confirmDelete(opname)"
              class="flex items-center justify-center px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty State (Mobile) -->
        <div v-if="displayedOpnames.length === 0" class="bg-white rounded-lg shadow-sm p-6 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-sm text-gray-500">
            {{ searchQuery || filterPeriod ? 'Tidak ada data stock opname yang sesuai dengan pencarian' : 'Belum ada data stock opname di toko ini' }}
          </p>
        </div>
      </div>

      <!-- Pagination Controls -->
      <PaginationControls :pagination="stockOpnames" />
    </div>

    <!-- Sticky Bottom Actions (Mobile Only) -->
    <div class="block md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
      <div class="grid grid-cols-5 gap-1.5 p-2">
        <button
          @click="showAutoOpnameModal = true"
          class="bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 rounded-lg transition-colors flex flex-col items-center justify-center text-xs font-medium"
        >
          <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
          <span>Auto</span>
        </button>
        <button
          @click="openAddModal"
          class="bg-[#996600] hover:bg-[#7a5100] text-white px-2 py-1.5 rounded-lg transition-colors flex flex-col items-center justify-center text-xs font-medium"
        >
          <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah</span>
        </button>
        <button
          @click="showImportModal = true"
          class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-1.5 rounded-lg transition-colors flex flex-col items-center justify-center text-xs font-medium"
        >
          <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
          <span>Impor</span>
        </button>
        <Link
          :href="`/pengelola/opname-stock/${toko.id}/barang`"
          class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1.5 rounded-lg transition-colors flex items-center justify-center space-x-1 text-xs font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <span>Master</span>
        </Link>
        <button
          @click="exportToCSV"
          class="bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 rounded-lg transition-colors flex items-center justify-center space-x-1 text-xs font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Export</span>
        </button>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
          <h2 class="text-2xl font-bold text-gray-800">
            {{ isEditMode ? 'Edit Stock Opname' : 'Tambah Stock Opname' }}
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div v-if="!isEditMode">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang *</label>
            <select
              v-model="form.barang_id"
              @change="onBarangChange"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">-- Pilih Barang (Ada Stok) --</option>
              <option v-for="barang in filteredBarangs" :key="barang.id" :value="barang.id">
                {{ barang.kode_barang }} - {{ barang.nama_barang }} (Stok: {{ barang.stok }})
              </option>
            </select>
            <p v-if="filteredBarangs.length === 0" class="text-xs text-orange-600 mt-1">
              Semua barang di unit ini memiliki stok 0.
            </p>
            <p v-else class="text-xs text-gray-500 mt-1">
              Menampilkan {{ filteredBarangs.length }} barang dengan stok tersedia.
            </p>
          </div>

          <div v-if="isEditMode">
            <label class="block text-sm font-medium text-gray-700 mb-2">Barang</label>
            <input
              type="text"
              :value="form.barang_name"
              disabled
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Stock Opname *</label>
            <input
              type="datetime-local"
              v-model="form.opname_date"
              :disabled="isEditMode"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pengentri</label>
            <input
              type="text"
              :value="currentUserName"
              disabled
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
            />
          </div>

          <div v-if="selectedBarang">
            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Awal (Sistem)</label>
            <input
              type="number"
              :value="selectedBarang.stok"
              disabled
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Fisik (Hasil Hitung) *</label>
            <input
              type="number"
              v-model.number="form.stock_fisik"
              min="0"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div v-if="selectedBarang && form.stock_fisik !== null">
            <div class="bg-gray-50 p-4 rounded-lg space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Selisih Quantity:</span>
                <span :class="[
                  'font-bold',
                  (form.stock_fisik - selectedBarang.stok) > 0 ? 'text-green-600' :
                  (form.stock_fisik - selectedBarang.stok) < 0 ? 'text-red-600' : 'text-gray-600'
                ]">
                  {{ form.stock_fisik - selectedBarang.stok > 0 ? '+' : '' }}{{ form.stock_fisik - selectedBarang.stok }}
                </span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Harga Pokok:</span>
                <span class="font-medium">{{ formatCurrency(selectedBarang.harga_beli) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Selisih Rupiah:</span>
                <span :class="[
                  'font-bold',
                  ((form.stock_fisik - selectedBarang.stok) * selectedBarang.harga_beli) > 0 ? 'text-green-600' :
                  ((form.stock_fisik - selectedBarang.stok) * selectedBarang.harga_beli) < 0 ? 'text-red-600' : 'text-gray-600'
                ]">
                  {{ formatCurrency((form.stock_fisik - selectedBarang.stok) * selectedBarang.harga_beli) }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="selectedBarang && selectedBarang.kategori && selectedBarang.kategori.toLowerCase().includes('makanan')">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tanggal Kedaluwarsa (Expired Date)
              <span class="text-red-500">*</span>
            </label>
            <input
              type="date"
              v-model="form.expired_date"
              :min="getTomorrow()"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <p class="text-xs text-gray-500 mt-1">Wajib diisi untuk kategori makanan</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
            <textarea
              v-model="form.keterangan"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Catatan tambahan (opsional)"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-6 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6">
          <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-center text-gray-900 mb-2">Hapus Stock Opname?</h3>
          <p class="text-sm text-gray-600 text-center mb-6">
            Apakah Anda yakin ingin menghapus data stock opname ini? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex space-x-3">
            <button
              @click="closeDeleteModal"
              class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              @click="deleteStockOpname"
              :disabled="processing"
              class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Menghapus...' : 'Hapus' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Import Modal -->
    <ImportStockOpnameModal
      :show="showImportModal"
      :barangs="barangs"
      @close="showImportModal = false"
      @import="handleImport"
    />

    <!-- Auto Opname Modal -->
    <AutoOpnameModal
      ref="autoOpnameModalRef"
      :show="showAutoOpnameModal"
      :barangs="barangs"
      @close="showAutoOpnameModal = false"
      @submit="handleAutoOpname"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImportStockOpnameModal from '@/Components/StockOpname/ImportStockOpnameModal.vue';
import AutoOpnameModal from '@/Components/StockOpname/AutoOpnameModal.vue';
import PaginationControls from '@/Components/StockOpname/PaginationControls.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const autoOpnameModalRef = ref(null);
const page = usePage();

const props = defineProps({
  toko: Object,
  stockOpnames: Object,
  barangs: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const currentUserName = computed(() => page.props.auth?.user?.name || 'sysadmin');

// Barang dengan stok > 0 untuk dropdown di modal
const filteredBarangs = computed(() => props.barangs.filter(b => b.stok > 0));

const searchQuery = ref(props.filters.search || '');
const filterPeriod = ref(props.filters.period || 'this_month');
const sortBy = ref(props.filters.sort_by || 'waktu');
const sortOrder = ref(props.filters.sort_order || 'desc');
const showModal = ref(false);
const showImportModal = ref(false);
const showAutoOpnameModal = ref(false);
const showDeleteModal = ref(false);
const isEditMode = ref(false);
const processing = ref(false);
const selectedBarang = ref(null);
const itemToDelete = ref(null);

const form = ref({
  barang_id: '',
  barang_name: '',
  opname_date: new Date().toISOString().slice(0, 16),
  stock_fisik: null,
  keterangan: '',
  expired_date: ''
});

// Direct access to data (no client-side filtering)
const displayedOpnames = computed(() => props.stockOpnames?.data || []);

// Summary from displayed data
const totalSelisihPlus = computed(() => {
  return displayedOpnames.value
    .filter(o => o.selisih_quantity > 0)
    .reduce((sum, o) => sum + o.selisih_quantity, 0);
});

const totalSelisihMinus = computed(() => {
  return displayedOpnames.value
    .filter(o => o.selisih_quantity < 0)
    .reduce((sum, o) => sum + Math.abs(o.selisih_quantity), 0);
});

const totalSelisihRupiah = computed(() => {
  return displayedOpnames.value.reduce((sum, o) => sum + parseFloat(o.selisih_rupiah), 0);
});

// Watch for filter changes and reload data from server
const applyFilters = useDebounceFn(() => {
  router.get(
    `/pengelola/opname-stock/${props.toko.id}`,
    {
      search: searchQuery.value || undefined,
      period: filterPeriod.value || undefined,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['stockOpnames', 'filters'],
    }
  );
}, 500);

watch([searchQuery, filterPeriod, sortBy, sortOrder], () => {
  applyFilters();
});

const paginationOffset = computed(() => {
  return (props.stockOpnames?.current_page - 1) * (props.stockOpnames?.per_page || 50);
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  // Check if date includes time
  if (dateString.includes(':')) {
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
  // Date only
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
};

const getTomorrow = () => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  return tomorrow.toISOString().split('T')[0];
};

const isExpiringSoon = (expiredDate) => {
  if (!expiredDate) return false;
  const today = new Date();
  const expired = new Date(expiredDate);
  const diffTime = expired - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 7 && diffDays > 0; // Expired dalam 7 hari
};

const isExpiringThisMonth = (expiredDate) => {
  if (!expiredDate) return false;
  const today = new Date();
  const expired = new Date(expiredDate);
  const diffTime = expired - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays > 7 && diffDays <= 30; // Expired dalam 30 hari
};

const openAddModal = () => {
  isEditMode.value = false;
  selectedBarang.value = null;
  form.value = {
    barang_id: '',
    barang_name: '',
    opname_date: new Date().toISOString().slice(0, 16),
    stock_fisik: null,
    keterangan: '',
    expired_date: ''
  };
  showModal.value = true;
};

const openEditModal = (opname) => {
  isEditMode.value = true;
  const barang = props.barangs?.find(b => b.kode_barang === opname.plu);
  selectedBarang.value = barang || {
    stok: opname.stock_awal,
    harga_beli: opname.harga_pokok
  };

  form.value = {
    id: opname.id,
    barang_id: barang?.id || '',
    barang_name: opname.deskripsi_barang,
    opname_date: opname.tanggal_so,
    stock_fisik: opname.penyesuaian,
    keterangan: opname.keterangan || '',
    expired_date: opname.expired_date || ''
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedBarang.value = null;
};

const onBarangChange = () => {
  const barang = props.barangs?.find(b => b.id === parseInt(form.value.barang_id));
  selectedBarang.value = barang || null;
};

const submitForm = () => {
  processing.value = true;

  const data = {
    barang_id: form.value.barang_id,
    opname_date: form.value.opname_date,
    stock_fisik: form.value.stock_fisik,
    keterangan: form.value.keterangan,
    expired_date: form.value.expired_date || null
  };

  if (isEditMode.value) {
    router.put(`/pengelola/opname-stock/${props.toko.id}/stock-opname/${form.value.id}`, data, {
      onSuccess: () => {
        toast.success('Stock opname berhasil diupdate');
        closeModal();
        processing.value = false;
      },
      onError: (errors) => {
        toast.error('Gagal update stock opname');
        console.error(errors);
        processing.value = false;
      }
    });
  } else {
    router.post(`/pengelola/opname-stock/${props.toko.id}/stock-opname`, data, {
      onSuccess: () => {
        toast.success('Stock opname berhasil ditambahkan');
        closeModal();
        processing.value = false;
      },
      onError: (errors) => {
        toast.error('Gagal menambahkan stock opname');
        console.error(errors);
        processing.value = false;
      }
    });
  }
};

const confirmDelete = (opname) => {
  itemToDelete.value = opname;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  itemToDelete.value = null;
};

const deleteStockOpname = () => {
  processing.value = true;

  router.delete(`/pengelola/opname-stock/${props.toko.id}/stock-opname/${itemToDelete.value.id}`, {
    onSuccess: () => {
      toast.success('Stock opname berhasil dihapus');
      closeDeleteModal();
      processing.value = false;
    },
    onError: (errors) => {
      toast.error('Gagal menghapus stock opname');
      console.error(errors);
      processing.value = false;
    }
  });
};

const handleImport = (data) => {
  processing.value = true;

  router.post(`/pengelola/opname-stock/${props.toko.id}/stock-opname/import`, {
    data: data
  }, {
    onSuccess: (page) => {
      processing.value = false;
      showImportModal.value = false;

      if (page.props.flash?.import_warnings && page.props.flash.import_warnings.length > 0) {
        const errorCount = page.props.flash.import_warnings.length;
        toast.warning(`Impor selesai dengan ${errorCount} error. Lihat console untuk detail.`, {
          timeout: 5000
        });
        console.warn('Import errors:', page.props.flash.import_warnings);
      }

      if (page.props.flash?.success) {
        toast.success(page.props.flash.success, {
          timeout: 3000
        });
      }
    },
    onError: (errors) => {
      console.error('Import errors:', errors);
      processing.value = false;

      let errorMessage = 'Gagal mengimpor data stock opname';

      if (errors.import) {
        errorMessage = errors.import;
      } else if (errors && typeof errors === 'object') {
        const errorList = Object.entries(errors)
          .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
          .join('; ');
        errorMessage = errorList || errorMessage;
      }

      toast.error(errorMessage, {
        timeout: 7000
      });
    }
  });
};

const handleAutoOpname = (payload) => {
  processing.value = true;

  router.post(`/pengelola/opname-stock/${props.toko.id}/stock-opname/auto-import`, {
    data: payload.data,
    opname_date: payload.opname_date
  }, {
    onSuccess: (page) => {
      processing.value = false;
      showAutoOpnameModal.value = false;

      if (autoOpnameModalRef.value) {
        autoOpnameModalRef.value.resetProcessing();
      }

      if (page.props.flash?.import_warnings && page.props.flash.import_warnings.length > 0) {
        const errorCount = page.props.flash.import_warnings.length;
        toast.warning(`Auto opname selesai dengan ${errorCount} error. Lihat console untuk detail.`, {
          timeout: 5000
        });
        console.warn('Auto opname errors:', page.props.flash.import_warnings);
      }

      if (page.props.flash?.success) {
        toast.success(page.props.flash.success, {
          timeout: 3000
        });
      } else {
        toast.success(`Berhasil memproses ${payload.data.length} stock opname`, {
          timeout: 3000
        });
      }
    },
    onError: (errors) => {
      console.error('Auto opname errors:', errors);
      processing.value = false;

      if (autoOpnameModalRef.value) {
        autoOpnameModalRef.value.resetProcessing();
      }

      let errorMessage = 'Gagal memproses auto opname';

      if (errors.import) {
        errorMessage = errors.import;
      } else if (errors && typeof errors === 'object') {
        const errorList = Object.entries(errors)
          .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
          .join('; ');
        errorMessage = errorList || errorMessage;
      }

      toast.error(errorMessage, {
        timeout: 7000
      });
    }
  });
};

const exportToCSV = () => {
  const totalData = props.stockOpnames?.total || 0;

  // Build URL with current filters
  const params = new URLSearchParams({
    search: searchQuery.value || '',
    period: filterPeriod.value || '',
    sort_by: sortBy.value,
    sort_order: sortOrder.value,
  });

  const url = `/pengelola/opname-stock/${props.toko.id}/stock-opname/export-csv?${params.toString()}`;

  // Show info
  const periodLabels = {
    'today': 'Hari Ini',
    'this_week': 'Minggu Ini',
    'this_month': 'Bulan Ini',
    'last_month': 'Bulan Lalu',
    '': 'Semua Data'
  };
  const periodLabel = periodLabels[filterPeriod.value] || 'Semua Data';

  toast.info(`Mengunduh CSV ${totalData} record (${periodLabel})...`, {
    timeout: 3000
  });

  // Download CSV
  window.open(url, '_blank');
};

const downloadPdf = () => {
  const totalData = props.stockOpnames?.total || 0;

  // Warn if data is large
  if (totalData > 500) {
    const proceed = confirm(
      `Data yang akan di-PDF ada ${totalData} record.\n\n` +
      `⚠️ PDF dibatasi maksimal 500 record untuk menghindari error memory.\n\n` +
      `Untuk data lengkap, gunakan "Export CSV" atau filter periode lebih spesifik.\n\n` +
      `Lanjutkan download PDF (500 record pertama)?`
    );

    if (!proceed) return;
  }

  const now = new Date();
  let startDate, endDate;

  // Convert period to date range for PDF
  if (filterPeriod.value) {
    switch (filterPeriod.value) {
      case 'today':
        startDate = endDate = now.toISOString().split('T')[0];
        break;
      case 'this_week':
        const weekStart = new Date(now);
        weekStart.setDate(now.getDate() - now.getDay());
        startDate = weekStart.toISOString().split('T')[0];
        endDate = now.toISOString().split('T')[0];
        break;
      case 'this_month':
        startDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
        break;
      case 'last_month':
        const lastMonthDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        startDate = lastMonthDate.toISOString().split('T')[0];
        endDate = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().split('T')[0];
        break;
    }
  }

  const params = new URLSearchParams({
    sort_by: sortBy.value,
    sort_order: sortOrder.value,
  });

  if (startDate && endDate) {
    params.append('start_date', startDate);
    params.append('end_date', endDate);
  }

  toast.info('Generating PDF...', { timeout: 2000 });

  const url = `/pengelola/opname-stock/${props.toko.id}/stock-opname/download-pdf?${params.toString()}`;
  window.open(url, '_blank');
};
</script>
