<template>
  <AdminLayout pageTitle="Opname Stock">
    <div class="space-y-6">

      <div v-if="tokos.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <div
          v-for="toko in tokos"
          :key="toko.id"
          class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden"
        >
          <div class="p-3 md:p-4">
            <div class="flex items-start gap-3 md:gap-4">
              <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                <img
                  v-if="toko.logo"
                  :src="getLogoUrl(toko.logo)"
                  :alt="toko.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                  <h3 class="text-sm md:text-base font-bold text-gray-800 truncate">{{ toko.name }}</h3>
                  <span class="text-xs text-gray-500 font-mono whitespace-nowrap">#{{ toko.unit_id || '-' }}</span>
                </div>
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    toko.type.toLowerCase() === 'toko' ? 'bg-blue-100 text-blue-800' :
                    toko.type.toLowerCase() === 'shop' ? 'bg-purple-100 text-purple-800' :
                    'bg-orange-100 text-orange-800'
                  ]"
                >
                  {{ toko.type.toLowerCase() === 'toko' ? 'Toko' :
                     toko.type.toLowerCase() === 'shop' ? 'Shop' : 'Kantin' }}
                </span>
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-600">
                  <div class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="font-semibold text-gray-700">{{ toko.barangs_count }} barang</span>
                  </div>
                  <div v-if="toko.location" class="inline-flex items-center max-w-full">
                    <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate">{{ toko.location }}</span>
                  </div>
                  <div v-if="toko.contact_phone" class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>{{ toko.contact_phone }}</span>
                  </div>
                  <div v-if="toko.manager_name" class="inline-flex items-center max-w-full">
                    <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="truncate">PJ: {{ toko.manager_name }}</span>
                  </div>
                </div>

                <div class="mt-2 grid grid-cols-2 gap-1.5">
                  <Link
                    :href="`/pengelola/opname-stock/${toko.id}/barang`"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-md transition-colors text-[11px] md:text-xs font-medium text-center flex items-center justify-center space-x-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Master Barang</span>
                  </Link>
                  <Link
                    :href="`/pengelola/opname-stock/${toko.id}`"
                    class="bg-[#996600] hover:bg-[#7a5100] text-white px-2.5 py-1.5 rounded-md transition-colors text-[11px] md:text-xs font-medium text-center flex items-center justify-center space-x-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan SO</span>
                  </Link>
                </div>
              </div>
            </div>

            <!-- Tenant sub-section -->
            <div v-if="tenantsByUnit[toko.id]?.length" class="mt-3 pt-3 border-t border-gray-100">
              <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Tenant</p>
              <div class="space-y-1.5">
                <div
                  v-for="tenant in tenantsByUnit[toko.id]"
                  :key="tenant.id"
                  class="flex items-center justify-between gap-2 bg-[#f4efe5] rounded-lg px-3 py-2"
                >
                  <div class="min-w-0 flex-1">
                    <span class="text-xs font-semibold text-gray-800">{{ tenant.nama }}</span>
                    <span v-if="tenant.biaya_kontribusi" class="ml-2 text-[11px] text-[#6b4700] font-medium">
                      Rp {{ formatNumber(tenant.biaya_kontribusi) }}/bln
                    </span>
                  </div>
                  <button
                    v-if="tenant.biaya_kontribusi"
                    @click="openPrintModal(tenant)"
                    class="flex-shrink-0 p-1.5 rounded-md bg-[#996600] hover:bg-[#7a5100] text-white transition-colors"
                    title="Cetak Kuitansi"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Unit Kerja Toko/Kantin</h3>
        <p class="text-gray-600">Silakan buat unit kerja dengan tipe "toko" atau "kantin" di menu Unit Kerja terlebih dahulu</p>
      </div>

    </div>

    <!-- Modal Pilih Bulan Kuitansi -->
    <div
      v-if="printModal.show"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="printModal.show = false"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm">
        <div class="p-5 border-b border-gray-200">
          <h2 class="text-base font-bold text-gray-900">Cetak Kuitansi</h2>
          <p class="text-xs text-gray-500 mt-0.5">{{ printModal.tenant?.nama }}</p>
        </div>
        <div class="p-5 space-y-3">
          <label class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
          <input
            v-model="printModal.bulan"
            type="month"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
          />
          <div class="bg-[#f4efe5] rounded-lg px-4 py-3 text-sm">
            <span class="text-gray-500">Biaya Kontribusi:</span>
            <span class="font-bold text-[#6b4700] ml-2">
              Rp {{ formatNumber(printModal.tenant?.biaya_kontribusi) }}/bln
            </span>
          </div>
        </div>
        <div class="p-5 border-t border-gray-200 flex gap-2">
          <button
            @click="printModal.show = false"
            class="flex-1 px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Batal
          </button>
          <a
            :href="printKuitansiUrl"
            target="_blank"
            @click="printModal.show = false"
            class="flex-1 px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] transition-colors text-center flex items-center justify-center gap-1.5"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak
          </a>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  tokos: Array,
  tenants: { type: Array, default: () => [] },
});

const tenantsByUnit = computed(() => {
  const map = {};
  props.tenants.forEach(t => {
    const uid = t.penempatan_work_unit_id;
    if (!uid) return;
    if (!map[uid]) map[uid] = [];
    map[uid].push(t);
  });
  return map;
});

const now = new Date();
const defaultBulan = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
const printModal = ref({ show: false, tenant: null, bulan: defaultBulan });

const printKuitansiUrl = computed(() => {
  if (!printModal.value.tenant || !printModal.value.bulan) return '#';
  return `/pengelola/opname-stock/tenant/${printModal.value.tenant.id}/kuitansi?bulan=${printModal.value.bulan}`;
});

const openPrintModal = (tenant) => {
  printModal.value = { show: true, tenant, bulan: defaultBulan };
};

const getLogoUrl = (logo) => {
  if (!logo) return '';
  if (logo.startsWith('http://') || logo.startsWith('https://') || logo.startsWith('/')) return logo;
  return `/storage/${logo}`;
};

const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value || 0);
</script>
