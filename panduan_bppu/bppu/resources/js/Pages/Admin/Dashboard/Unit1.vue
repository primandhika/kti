<template>
  <AdminLayout page-title="Unit 1">
    <div class="space-y-6">
      <!-- Header -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-2xl shadow-xl p-8 text-white">
        <div class="flex items-center space-x-4">
          <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold">Unit Kerja 1</h1>
            <p class="text-[#d6c199] mt-1">Divisi Administrasi dan Keuangan</p>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Anggota</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
            </div>
            <div class="w-14 h-14 bg-[#eae0cc] rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Tugas Aktif</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">8</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Proyek Selesai</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">24</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Team Members -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Anggota Tim</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-[#996600] rounded-full flex items-center justify-center text-white font-bold">
                JD
              </div>
              <div>
                <p class="font-semibold text-gray-900">John Doe</p>
                <p class="text-sm text-gray-600">Kepala Unit</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-[#7a5100] rounded-full flex items-center justify-center text-white font-bold">
                JS
              </div>
              <div>
                <p class="font-semibold text-gray-900">Jane Smith</p>
                <p class="text-sm text-gray-600">Staff Administrasi</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">
                RJ
              </div>
              <div>
                <p class="font-semibold text-gray-900">Robert Johnson</p>
                <p class="text-sm text-gray-600">Staff Keuangan</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold">
                MW
              </div>
              <div>
                <p class="font-semibold text-gray-900">Mary Wilson</p>
                <p class="text-sm text-gray-600">Staff Administrasi</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                DB
              </div>
              <div>
                <p class="font-semibold text-gray-900">David Brown</p>
                <p class="text-sm text-gray-600">Staff Keuangan</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
              <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                SD
              </div>
              <div>
                <p class="font-semibold text-gray-900">Sarah Davis</p>
                <p class="text-sm text-gray-600">Staff Administrasi</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Current Projects -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Proyek Berjalan</h3>
        </div>
        <div class="p-6 space-y-4">
          <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-colors">
            <div class="flex items-start justify-between mb-3">
              <div>
                <h4 class="font-semibold text-gray-900">Audit Keuangan Semester 1</h4>
                <p class="text-sm text-gray-600 mt-1">Due: 15 Januari 2025</p>
              </div>
              <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                In Progress
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
              <div class="bg-[#996600] h-2 rounded-full" style="width: 65%"></div>
            </div>
            <p class="text-xs text-gray-600 mt-2">65% Selesai</p>
          </div>

          <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-colors">
            <div class="flex items-start justify-between mb-3">
              <div>
                <h4 class="font-semibold text-gray-900">Penyusunan Anggaran 2025</h4>
                <p class="text-sm text-gray-600 mt-1">Due: 20 Januari 2025</p>
              </div>
              <span class="px-3 py-1 bg-[#eae0cc] text-[#6b4700] text-xs font-semibold rounded-full">
                Planning
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
              <div class="bg-purple-600 h-2 rounded-full" style="width: 30%"></div>
            </div>
            <p class="text-xs text-gray-600 mt-2">30% Selesai</p>
          </div>

          <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-colors">
            <div class="flex items-start justify-between mb-3">
              <div>
                <h4 class="font-semibold text-gray-900">Rekonsiliasi Bank Desember</h4>
                <p class="text-sm text-gray-600 mt-1">Due: 12 Januari 2025</p>
              </div>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                Review
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
              <div class="bg-purple-600 h-2 rounded-full" style="width: 90%"></div>
            </div>
            <p class="text-xs text-gray-600 mt-2">90% Selesai</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
