<template>
  <MitraLayout>
    <!-- Tidak ada supplier terdaftar -->
    <div v-if="!supplier" class="text-center py-16">
      <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <p class="text-sm font-medium text-gray-500 mb-1">Akun belum terhubung ke data mitra</p>
      <p class="text-xs text-gray-400">Hubungi pengelola BPPU untuk mengaitkan akun ini.</p>
    </div>

    <!-- Dashboard content -->
    <div v-else class="space-y-4">
      <!-- Identitas Mitra -->
      <IdentitasCard
        :supplier="supplier"
        @edit-profil="showEditProfil = true"
        @ganti-password="showGantiPassword = true"
      />

      <!-- Pendapatan -->
      <PendapatanCard
        :pendapatan="pendapatan"
        :current-filter="currentFilter"
        :current-dari="dari"
        :current-sampai="sampai"
        @filter-change="onFilterChange"
        @tarik-dana="showTarikDana = true"
      />

      <!-- Daftar Produk -->
      <ProdukList :produk="produk" />
    </div>

    <!-- Modal Ajukan Pencairan -->
    <TarikDanaModal
      :show="showTarikDana"
      @close="showTarikDana = false"
      @success="onModalSuccess"
    />

    <!-- Modal Edit Profil -->
    <EditProfilModal
      v-if="supplier"
      :show="showEditProfil"
      :supplier="supplier"
      @close="showEditProfil = false"
      @success="onModalSuccess"
    />

    <!-- Modal Ganti Password -->
    <GantiPasswordModal
      :show="showGantiPassword"
      @close="showGantiPassword = false"
      @success="onModalSuccess"
    />

    <!-- Toast notifikasi -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div v-if="toastMessage" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-sm z-50">
        <div class="bg-green-50 border-l-4 border-green-400 p-4 shadow-lg rounded-lg flex items-center space-x-3">
          <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm font-medium text-green-800">{{ toastMessage }}</p>
        </div>
      </div>
    </Transition>
  </MitraLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import IdentitasCard from '@/Components/Mitra/IdentitasCard.vue';
import PendapatanCard from '@/Components/Mitra/PendapatanCard.vue';
import ProdukList from '@/Components/Mitra/ProdukList.vue';
import TarikDanaModal from '@/Components/Mitra/TarikDanaModal.vue';
import EditProfilModal from '@/Components/Mitra/EditProfilModal.vue';
import GantiPasswordModal from '@/Components/Mitra/GantiPasswordModal.vue';

const props = defineProps({
  supplier:   { type: Object, default: null },
  pendapatan: { type: Object, default: () => ({}) },
  produk:     { type: Array, default: () => [] },
  filter:     { type: String, default: 'hari_ini' },
  dari:       { type: String, default: '' },
  sampai:     { type: String, default: '' },
});

const currentFilter     = ref(props.filter);
const showTarikDana     = ref(false);
const showEditProfil    = ref(false);
const showGantiPassword = ref(false);
const toastMessage      = ref('');

// Cek flash message dari redirect setelah form submit
const page = usePage();
if (page.props.flash?.success) {
  toastMessage.value = page.props.flash.success;
  setTimeout(() => { toastMessage.value = ''; }, 5000);
}

const onFilterChange = (payload) => {
  const { filter, dari, sampai, skipRequest } = payload;
  currentFilter.value = filter;

  if (skipRequest) return;

  const params = { filter };
  if (filter === 'custom' && dari && sampai) {
    params.dari    = dari;
    params.sampai  = sampai;
  }

  router.get('/mitra/dasbor', params, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

const onModalSuccess = (message) => {
  showToast(message);
};

const showToast = (message) => {
  toastMessage.value = message;
  setTimeout(() => { toastMessage.value = ''; }, 5000);
};
</script>
