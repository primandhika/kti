<template>
  <AdminLayout page-title="Dashboard Kantin">
    <div class="space-y-6">
      <!-- Welcome Card -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Dashboard Kantin</h1>
        <p class="text-[#d6c199]">Selamat datang, {{ $page.props.auth.user.name }}</p>
        <p class="text-sm text-[#d6c199] mt-1">Kelola pesanan dan stok kantin</p>
      </div>

      <!-- Canteen Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pesanan Hari Ini</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">47</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">+12</span>
            <span class="text-gray-600 ml-2">dari kemarin</span>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pesanan Pending</p>
              <p class="text-3xl font-bold text-orange-600 mt-2">8</p>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-orange-600 font-medium">Segera proses</span>
          </div>
        </div>

        <!-- Stock Items -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Item Stok</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">156</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">152 tersedia</span>
          </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
              <p class="text-3xl font-bold text-red-600 mt-2">4</p>
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

      <!-- Orders & Stock Management -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
            <span class="text-xs text-gray-500">Update otomatis</span>
          </div>
          <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
              <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Pesanan #1247 - Pending</p>
                <p class="text-xs text-gray-600 mt-1">Nasi Goreng (2), Teh Manis (2)</p>
                <p class="text-xs text-orange-600 font-medium mt-1">Rp 45.000 • 5 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Pesanan #1246 - Diproses</p>
                <p class="text-xs text-gray-600 mt-1">Mie Ayam (1), Kopi (1)</p>
                <p class="text-xs text-blue-600 font-medium mt-1">Rp 28.000 • 12 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
              <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Pesanan #1245 - Selesai</p>
                <p class="text-xs text-gray-600 mt-1">Soto Ayam (1), Es Teh (1)</p>
                <p class="text-xs text-green-600 font-medium mt-1">Rp 32.000 • 18 menit yang lalu</p>
              </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
              <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">Pesanan #1244 - Selesai</p>
                <p class="text-xs text-gray-600 mt-1">Ayam Bakar (2), Jus Jeruk (2)</p>
                <p class="text-xs text-gray-600 font-medium mt-1">Rp 85.000 • 25 menit yang lalu</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Alerts & Incoming -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Stok & Peringatan</h3>
          <div class="space-y-4">
            <!-- Low Stock Items -->
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2">Item Stok Menipis</p>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Beras Premium</span>
                  </div>
                  <span class="text-sm font-bold text-red-600">5 kg</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Minyak Goreng</span>
                  </div>
                  <span class="text-sm font-bold text-red-600">3 liter</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Gula Pasir</span>
                  </div>
                  <span class="text-sm font-bold text-yellow-600">8 kg</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                    <span class="text-sm text-gray-900">Teh Celup</span>
                  </div>
                  <span class="text-sm font-bold text-yellow-600">25 pcs</span>
                </div>
              </div>
            </div>

            <!-- Incoming Stock -->
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-2">Stok Masuk (Dijadwalkan)</p>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                  <div>
                    <span class="text-sm text-gray-900 font-medium">Beras Premium - 50 kg</span>
                    <p class="text-xs text-gray-600">Besok, 10:00</p>
                  </div>
                </div>
                <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                  <div>
                    <span class="text-sm text-gray-900 font-medium">Sayur & Bumbu</span>
                    <p class="text-xs text-gray-600">Besok, 07:00</p>
                  </div>
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
          <button class="p-4 bg-[#f4efe5] rounded-xl hover:bg-[#eae0cc] transition-colors group">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Lihat Pesanan</p>
          </button>

          <button class="p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Kelola Stok</p>
          </button>

          <button class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Tambah Stok</p>
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
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>
