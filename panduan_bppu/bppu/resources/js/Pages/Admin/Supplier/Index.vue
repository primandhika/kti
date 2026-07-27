<template>
  <AdminLayout page-title="Manajemen Supplier">
    <div class="space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Supplier</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola data supplier dalam sistem</p>
          </div>
          <button
            @click="openCreateModal"
            class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Supplier</span>
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
          <div class="flex flex-wrap gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari kode, nama, perusahaan, email, telepon..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>

            <!-- Status Filter -->
            <div class="min-w-[200px]">
              <select
                v-model="statusFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>

            <!-- Reset Button -->
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Suppliers Table -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono font-semibold text-[#996600]">{{ supplier.kode_supplier }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ supplier.nama }}</div>
                  <div v-if="supplier.kontak_person" class="text-xs text-gray-500">CP: {{ supplier.kontak_person }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-600">{{ supplier.perusahaan || '-' }}</div>
                </td>
                <td class="px-6 py-4">
                  <div v-if="supplier.email" class="text-xs text-gray-600">{{ supplier.email }}</div>
                  <div v-if="supplier.telepon" class="text-xs text-gray-500">{{ supplier.telepon }}</div>
                  <div v-if="!supplier.email && !supplier.telepon" class="text-xs text-gray-400">-</div>
                </td>
                <td class="px-6 py-4">
                  <div v-if="supplier.kota" class="text-sm text-gray-600">{{ supplier.kota }}</div>
                  <div v-if="supplier.provinsi" class="text-xs text-gray-500">{{ supplier.provinsi }}</div>
                  <div v-if="!supplier.kota && !supplier.provinsi" class="text-xs text-gray-400">-</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    :class="[
                      'px-2 py-1 text-xs font-semibold rounded-full',
                      supplier.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ supplier.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="openEditModal(supplier)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteSupplier(supplier)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="suppliers.data.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  Belum ada data supplier
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="suppliers.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              Menampilkan {{ suppliers.from }} - {{ suppliers.to }} dari {{ suppliers.total }} data
            </div>
            <div class="flex space-x-1">
              <button
                v-for="(link, index) in suppliers.links"
                :key="index"
                @click="changePage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 border rounded',
                  link.active
                    ? 'bg-[#996600] text-white border-[#996600]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <span v-if="index === 0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </span>
                <span v-else-if="index === suppliers.links.length - 1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
                <span v-else>{{ link.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEditing ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
          </h2>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kode Supplier -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kode Supplier *</label>
              <input
                v-model="form.kode_supplier"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Contoh: SUP001"
              />
            </div>

            <!-- Nama -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Supplier *</label>
              <input
                v-model="form.nama"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama supplier"
              />
            </div>

            <!-- Perusahaan -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan</label>
              <input
                v-model="form.perusahaan"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama perusahaan"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="email@example.com"
              />
            </div>

            <!-- Telepon -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
              <input
                v-model="form.telepon"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="08xx-xxxx-xxxx"
              />
            </div>

            <!-- Kontak Person -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kontak Person</label>
              <input
                v-model="form.kontak_person"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama kontak person"
              />
            </div>

            <!-- Telepon Kontak -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Telepon Kontak</label>
              <input
                v-model="form.telepon_kontak"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="08xx-xxxx-xxxx"
              />
            </div>

            <!-- Kota -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
              <input
                v-model="form.kota"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Kota"
              />
            </div>

            <!-- Provinsi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
              <input
                v-model="form.provinsi"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Provinsi"
              />
            </div>

            <!-- Kode Pos -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
              <input
                v-model="form.kode_pos"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="40xxx"
              />
            </div>
          </div>

          <!-- Alamat -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea
              v-model="form.alamat"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Alamat lengkap supplier"
            ></textarea>
          </div>

          <!-- Catatan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
            <textarea
              v-model="form.catatan"
              rows="2"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Catatan tambahan (opsional)"
            ></textarea>
          </div>

          <!-- Status -->
          <div>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
              />
              <span class="text-sm font-medium text-gray-700">Supplier Aktif</span>
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors disabled:opacity-50"
            >
              {{ processing ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

// Simple debounce function
const debounce = (fn, delay) => {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
};

const props = defineProps({
  suppliers: Object,
  filters: Object,
});

const showModal = ref(false);
const isEditing = ref(false);
const processing = ref(false);
const editingSupplier = ref(null);

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const form = reactive({
  kode_supplier: '',
  nama: '',
  perusahaan: '',
  alamat: '',
  kota: '',
  provinsi: '',
  kode_pos: '',
  telepon: '',
  email: '',
  kontak_person: '',
  telepon_kontak: '',
  catatan: '',
  is_active: true,
});

const openCreateModal = () => {
  isEditing.value = false;
  editingSupplier.value = null;
  form.kode_supplier = '';
  form.nama = '';
  form.perusahaan = '';
  form.alamat = '';
  form.kota = '';
  form.provinsi = '';
  form.kode_pos = '';
  form.telepon = '';
  form.email = '';
  form.kontak_person = '';
  form.telepon_kontak = '';
  form.catatan = '';
  form.is_active = true;
  showModal.value = true;
};

const openEditModal = (supplier) => {
  isEditing.value = true;
  editingSupplier.value = supplier;
  form.kode_supplier = supplier.kode_supplier;
  form.nama = supplier.nama;
  form.perusahaan = supplier.perusahaan || '';
  form.alamat = supplier.alamat || '';
  form.kota = supplier.kota || '';
  form.provinsi = supplier.provinsi || '';
  form.kode_pos = supplier.kode_pos || '';
  form.telepon = supplier.telepon || '';
  form.email = supplier.email || '';
  form.kontak_person = supplier.kontak_person || '';
  form.telepon_kontak = supplier.telepon_kontak || '';
  form.catatan = supplier.catatan || '';
  form.is_active = supplier.is_active;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  isEditing.value = false;
  editingSupplier.value = null;
};

const submitForm = () => {
  processing.value = true;

  if (isEditing.value) {
    router.put(`/pengelola/supplier/${editingSupplier.value.id}`, form, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      },
    });
  } else {
    router.post('/pengelola/supplier', form, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      },
    });
  }
};

const deleteSupplier = (supplier) => {
  if (confirm(`Apakah Anda yakin ingin menghapus supplier "${supplier.nama}" (${supplier.kode_supplier})?`)) {
    router.delete(`/pengelola/supplier/${supplier.id}`);
  }
};

// Watch filters and apply debounce
watch(searchQuery, debounce(function (value) {
  applyFilters();
}, 500));

watch(statusFilter, () => {
  applyFilters();
});

const applyFilters = () => {
  router.get('/pengelola/supplier', {
    search: searchQuery.value,
    status: statusFilter.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  router.get('/pengelola/supplier');
};

const changePage = (url) => {
  if (!url) return;
  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>
