<template>
  <AdminLayout page-title="Dashboard Sysadmin">
    <div class="space-y-6">
      <!-- Welcome Card -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Dashboard System Administrator</h1>
        <p class="text-[#d6c199]">Selamat datang, {{ $page.props.auth.user.name }}</p>
        <p class="text-sm text-[#d6c199] mt-1">Kelola pengguna dan konfigurasi sistem</p>
      </div>

      <!-- Admin Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalUsers }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">+{{ Math.floor(totalUsers * 0.1) }}</span>
            <span class="text-gray-600 ml-2">pengguna baru</span>
          </div>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Sesi Aktif</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeSessions }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-600">Online sekarang</span>
          </div>
        </div>

        <!-- Total Cash Books -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Buku Kas</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalBukuKas }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-600">Dapat diakses semua</span>
          </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Transaksi Hari Ini</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ transaksHariIni }}</p>
            </div>
            <div class="w-14 h-14 bg-[#eae0cc] rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-500">{{ formatRupiah(omzetHariIni) }}</div>
        </div>
      </div>

      <!-- User Management & Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Role Distribution -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Distribusi Role Pengguna</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Officer</p>
                  <p class="text-xs text-gray-600">Administrative officers</p>
                </div>
              </div>
              <span class="text-lg font-bold text-blue-600">{{ roleDistribution.officer }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Shop</p>
                  <p class="text-xs text-gray-600">Shop staff members</p>
                </div>
              </div>
              <span class="text-lg font-bold text-purple-600">{{ roleDistribution.shop }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Canteen</p>
                  <p class="text-xs text-gray-600">Canteen staff members</p>
                </div>
              </div>
              <span class="text-lg font-bold text-orange-600">{{ roleDistribution.canteen }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Head & Sysadmin</p>
                  <p class="text-xs text-gray-600">Leadership & administrators</p>
                </div>
              </div>
              <span class="text-lg font-bold text-gray-600">{{ roleDistribution.head + roleDistribution.sysadmin }}</span>
            </div>
          </div>
        </div>

        <!-- Laporan Terbaru -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Laporan Terbaru</h3>
          <div v-if="recentPosts && recentPosts.length > 0" class="space-y-3">
            <div
              v-for="post in recentPosts"
              :key="post.id"
              class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg"
            >
              <div class="w-10 h-10 bg-[#eae0cc] rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ post.title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ formatDate(post.created_at) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-400 text-center py-6">Belum ada laporan</div>
        </div>
      </div>

      <!-- Quick Actions for Sysadmin -->
      <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link
            href="/pengelola/manajemen-pengguna"
            class="p-4 bg-[#f4efe5] rounded-xl hover:bg-[#eae0cc] transition-colors group"
          >
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Tambah Pengguna</p>
          </Link>

          <Link
            href="/pengelola/manajemen-pengguna"
            class="p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group"
          >
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Kelola Pengguna</p>
          </Link>

          <Link
            href="/pengelola/buku-kas"
            class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group"
          >
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Lihat Buku Kas</p>
          </Link>

          <Link
            href="/pengelola/setting"
            class="p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors group"
          >
            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Konfigurasi</p>
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  totalUsers: Number,
  totalWorkUnits: Number,
  activeWorkUnits: Number,
  totalBukuKas: Number,
  totalPosts: Number,
  totalIncome: Number,
  totalExpenses: Number,
  monthlyIncome: Array,
  roleDistribution: Object,
  activeSessions: Number,
  recentPosts: Array,
  transaksHariIni: Number,
  omzetHariIni: Number,
  totalArsip: Number,
});

function formatRupiah(val) {
  return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
}

function formatDate(val) {
  if (!val) return '-';
  return new Date(val).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>
