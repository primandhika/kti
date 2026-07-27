<template>
  <AdminLayout page-title="Beranda">
    <div class="space-y-6">
      <!-- Welcome Card -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Selamat Datang di Dashboard Admin</h1>
        <p class="text-[#d6c199]">BPPU | IKIP Siliwangi - Sistem Pengelolaan</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Laporan</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalPosts }}</p>
            </div>
            <div class="w-14 h-14 bg-[#eae0cc] rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-500">Total laporan dipublikasi</div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Arsip</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalArsip }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-500">Dokumen tersimpan</div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Unit Kerja</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeWorkUnits }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-500">Unit aktif</div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pengguna</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalUsers }}</p>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-500">Total pengguna terdaftar</div>
        </div>
      </div>

      <!-- Info Hari Ini + Laporan Terbaru -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ringkasan Hari Ini -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Hari Ini</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-[#f4efe5] rounded-lg">
              <div>
                <p class="text-sm text-gray-600">Transaksi</p>
                <p class="text-2xl font-bold text-[#996600]">{{ transaksHariIni }}</p>
              </div>
              <svg class="w-8 h-8 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
              <div>
                <p class="text-sm text-gray-600">Omzet</p>
                <p class="text-2xl font-bold text-green-700">{{ formatRupiah(omzetHariIni) }}</p>
              </div>
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Laporan Terbaru (real data) -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Laporan Terbaru</h3>
          <div v-if="recentPosts.length > 0" class="space-y-3">
            <div
              v-for="post in recentPosts"
              :key="post.id"
              class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
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
          <div v-else class="text-sm text-gray-400 text-center py-6">Belum ada laporan dipublikasi</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link href="/pengelola/laporan" class="p-4 bg-[#f4efe5] rounded-xl hover:bg-[#eae0cc] transition-colors group">
            <div class="w-12 h-12 bg-[#996600] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Buat Laporan</p>
          </Link>

          <Link href="/pengelola/arsip" class="p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Kelola Arsip</p>
          </Link>

          <Link href="/pengelola/unit-kerja/unit-1" class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Unit Kerja</p>
          </Link>

          <Link href="/pengelola/setting" class="p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors group">
            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-900">Pengaturan</p>
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
