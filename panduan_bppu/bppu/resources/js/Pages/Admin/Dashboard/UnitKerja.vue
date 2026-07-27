<template>
  <AdminLayout
    page-title="Detail Unit Kerja"
    :page-subtitle="`${workUnit.name}${workUnit.location ? ' - ' + workUnit.location : ''}`"
  >
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
            <div class="flex items-center space-x-2">
              <h1 class="text-3xl font-bold">{{ workUnit.name }}</h1>
              <span
                v-if="workUnit.is_active"
                class="px-3 py-1 bg-green-500/20 text-green-200 text-xs font-semibold rounded-full"
              >
                Aktif
              </span>
              <span
                v-else
                class="px-3 py-1 bg-red-500/20 text-red-200 text-xs font-semibold rounded-full"
              >
                Nonaktif
              </span>
            </div>
            <p class="text-[#d6c199] mt-1">{{ workUnit.type }}</p>
          </div>
        </div>
      </div>

      <!-- Unit Information -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Informasi Unit Kerja</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">ID Unit</label>
              <p class="text-base font-semibold text-gray-900">#{{ workUnit.unit_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Tipe</label>
              <p class="text-base font-semibold text-gray-900">{{ workUnit.type }}</p>
            </div>

            <div v-if="workUnit.location">
              <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
              <p class="text-base text-gray-900">{{ workUnit.location }}</p>
            </div>

            <div v-if="workUnit.manager_name">
              <label class="block text-sm font-medium text-gray-600 mb-1">Pengelola</label>
              <p class="text-base text-gray-900">{{ workUnit.manager_name }}</p>
            </div>

            <div v-if="workUnit.contact_phone">
              <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Kontak</label>
              <p class="text-base text-gray-900">{{ workUnit.contact_phone }}</p>
            </div>

            <div v-if="workUnit.contact_email">
              <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
              <p class="text-base text-gray-900">{{ workUnit.contact_email }}</p>
            </div>

            <div v-if="workUnit.operating_hours" class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-600 mb-1">Jam Operasional</label>
              <p class="text-base text-gray-900">{{ workUnit.operating_hours }}</p>
            </div>

            <div v-if="workUnit.description" class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi</label>
              <p class="text-base text-gray-900">{{ workUnit.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link
              :href="`/pengelola/setting?tab=workUnits`"
              class="flex items-center space-x-3 p-4 bg-gradient-to-r from-[#6b4700] to-[#996600] text-white rounded-xl hover:from-[#5b3d00] hover:to-[#7a5100] transition-all shadow-md"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span class="font-semibold">Edit Unit Kerja</span>
            </Link>

            <Link
              href="/pengelola/buku-kas"
              class="flex items-center space-x-3 p-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-semibold">Buku Kas</span>
            </Link>

            <Link
              href="/pengelola/laporan"
              class="flex items-center space-x-3 p-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-md"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span class="font-semibold">Laporan</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Info Note -->
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h4 class="text-sm font-semibold text-blue-800">Informasi</h4>
            <p class="text-sm text-blue-700 mt-1">
              Halaman ini menampilkan informasi detail unit kerja. Anda dapat mengelola unit kerja melalui menu "Kelola Unit Kerja" di sidebar atau tombol "Edit Unit Kerja" di atas.
            </p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  workUnit: {
    type: Object,
    required: true
  }
});
</script>
