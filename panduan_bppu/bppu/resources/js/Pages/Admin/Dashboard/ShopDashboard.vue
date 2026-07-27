<template>
  <ShopLayout page-title="Dashboard Toko">
    <div class="space-y-6">
      <!-- Welcome Card -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Dashboard Toko</h1>
        <p class="text-[#d6c199]">Selamat datang, {{ $page.props.auth.user.name }}</p>
        <p class="text-sm text-[#d6c199] mt-1">Kelola pesanan dan stock opname toko</p>
      </div>

      <!-- Shop Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pesanan Hari Ini</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">23</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">+5</span>
            <span class="text-gray-600 ml-2">dari kemarin</span>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pesanan Pending</p>
              <p class="text-3xl font-bold text-orange-600 mt-2">5</p>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-orange-600 font-medium">Perlu diproses</span>
          </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Produk Tersedia</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">284</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">268 in stock</span>
          </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
              <p class="text-3xl font-bold text-red-600 mt-2">7</p>
            </div>
            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-red-600 font-medium">Perlu restock</span>
          </div>
        </div>
      </div>

      <!-- Orders & Stock Opname -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Pesanan Masuk Terbaru</h3>
            <span class="text-xs text-gray-500">Update real-time</span>
          </div>
          <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
              <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Order #3421 - Pending</p>
                <p class="text-xs text-gray-600 mt-1">Buku Tulis (10), Pensil (20), Penghapus (15)</p>
                <p class="text-xs text-orange-600 font-medium mt-1">Rp 175.000 • 3 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Order #3420 - Dikemas</p>
                <p class="text-xs text-gray-600 mt-1">Spidol Whiteboard (5), Penggaris (3)</p>
                <p class="text-xs text-blue-600 font-medium mt-1">Rp 95.000 • 15 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
              <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Order #3419 - Selesai</p>
                <p class="text-xs text-gray-600 mt-1">Map Plastik (50), Stapler (2)</p>
                <p class="text-xs text-green-600 font-medium mt-1">Rp 285.000 • 28 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
              <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Order #3418 - Selesai</p>
                <p class="text-xs text-gray-600 mt-1">Amplop Coklat (100), Lakban (5)</p>
                <p class="text-xs text-gray-600 font-medium mt-1">Rp 425.000 • 1 jam yang lalu</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Opname Status -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Opname & Peringatan</h3>
          <div class="space-y-4">
            <!-- Low Stock Items -->
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2">Produk Stok Menipis</p>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Kertas HVS A4</span>
                  </div>
                  <span class="text-sm font-bold text-red-600">12 rim</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Tinta Printer HP</span>
                  </div>
                  <span class="text-sm font-bold text-red-600">3 pcs</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Ballpoint Hitam</span>
                  </div>
                  <span class="text-sm font-bold text-yellow-600">25 pcs</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Lem Fox Putih</span>
                  </div>
                  <span class="text-sm font-bold text-yellow-600">18 pcs</span>
                </div>
              </div>
            </div>

            <!-- Upcoming Stock Opname -->
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2">Stock Opname Terjadwal</p>
              <div class="space-y-2">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900">Opname Bulanan</span>
                    <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full">Mendatang</span>
                  </div>
                  <p class="text-xs text-gray-600">Tanggal 1 Februari 2025 • Semua kategori</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900">Opname Kategori ATK</span>
                    <span class="text-xs bg-gray-600 text-white px-2 py-1 rounded-full">Selesai</span>
                  </div>
                  <p class="text-xs text-gray-600">15 Januari 2025 • Status: Lengkap</p>
                </div>
              </div>
            </div>

            <!-- Quick Stock Info -->
            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
              <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-green-900">Info Stok</p>
                  <p class="text-xs text-green-700 mt-1">Akses penuh ke stock opname dan dapat melihat riwayat perubahan stok untuk semua produk</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link
            href="/pengelola/opname-stock"
            class="p-4 bg-[#f4efe5] rounded-xl hover:bg-[#eae0cc] transition-colors group"
          >
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Stock Opname</p>
          </Link>

          <button class="p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Lihat Pesanan</p>
          </button>

          <button class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Kelola Stok</p>
          </button>

          <button class="p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors group">
            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Laporan</p>
          </button>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>
