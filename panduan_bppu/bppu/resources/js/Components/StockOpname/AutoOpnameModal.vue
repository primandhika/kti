<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="closeModal"
  >
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

      <div class="relative inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-[#996600] to-[#7a5100]">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              <span>Auto Stock Opname</span>
            </h3>
            <button
              @click="closeModal"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Progress Steps -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div
              v-for="(step, index) in steps"
              :key="index"
              class="flex-1 flex items-center"
            >
              <div class="flex items-center space-x-2">
                <div
                  :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors',
                    currentStep > index
                      ? 'bg-green-600 text-white'
                      : currentStep === index
                      ? 'bg-[#996600] text-white'
                      : 'bg-gray-300 text-gray-600'
                  ]"
                >
                  <svg v-if="currentStep > index" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span v-else>{{ index + 1 }}</span>
                </div>
                <span
                  :class="[
                    'text-sm font-medium hidden md:block',
                    currentStep >= index ? 'text-gray-900' : 'text-gray-500'
                  ]"
                >
                  {{ step.title }}
                </span>
              </div>
              <div
                v-if="index < steps.length - 1"
                :class="[
                  'flex-1 h-0.5 mx-2',
                  currentStep > index ? 'bg-green-600' : 'bg-gray-300'
                ]"
              ></div>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-4 max-h-[calc(100vh-350px)] overflow-y-auto">
          <!-- Step 1: Pilih Barang -->
          <div v-if="currentStep === 0">
            <div class="space-y-4">
              <!-- Info -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div class="text-sm text-blue-800">
                    <p class="font-medium">Fitur Auto Opname membantu Anda:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs mt-1">
                      <li>Mengisi stock fisik secara otomatis menggunakan stock sistem</li>
                      <li>Memproses banyak barang sekaligus</li>
                      <li>Menghemat waktu untuk stock yang akurat</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Selection Mode -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mode:</label>
                <div class="grid grid-cols-2 gap-3">
                  <button
                    @click="selectionMode = 'all'"
                    :class="[
                      'p-4 border-2 rounded-lg transition-all text-left',
                      selectionMode === 'all'
                        ? 'border-[#996600] bg-amber-50'
                        : 'border-gray-300 hover:border-gray-400'
                    ]"
                  >
                    <div class="flex items-center space-x-2 mb-1">
                      <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="font-medium text-gray-900">Semua Barang</span>
                    </div>
                    <p class="text-xs text-gray-600">Proses semua {{ availableBarangs.length }} barang</p>
                  </button>

                  <button
                    @click="selectionMode = 'manual'"
                    :class="[
                      'p-4 border-2 rounded-lg transition-all text-left',
                      selectionMode === 'manual'
                        ? 'border-[#996600] bg-amber-50'
                        : 'border-gray-300 hover:border-gray-400'
                    ]"
                  >
                    <div class="flex items-center space-x-2 mb-1">
                      <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                      <span class="font-medium text-gray-900">Pilih Manual</span>
                    </div>
                    <p class="text-xs text-gray-600">Pilih barang tertentu saja</p>
                  </button>
                </div>
              </div>

              <!-- Manual Selection -->
              <div v-if="selectionMode === 'manual'" class="space-y-3">
                <!-- Search -->
                <div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari kode barang atau nama barang..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  />
                </div>

                <!-- Select All Filtered -->
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                  <span class="text-sm text-gray-700">{{ filteredBarangs.length }} barang ditemukan</span>
                  <button
                    @click="toggleAllFiltered"
                    class="text-sm text-[#996600] hover:text-[#7a5100] font-medium"
                  >
                    {{ isAllFilteredSelected ? 'Batalkan Semua' : 'Pilih Semua' }}
                  </button>
                </div>

                <!-- Barang List -->
                <div class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                  <div
                    v-for="barang in filteredBarangs"
                    :key="barang.id"
                    @click="toggleBarang(barang.id)"
                    class="p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 cursor-pointer transition-colors"
                  >
                    <div class="flex items-start space-x-3">
                      <input
                        type="checkbox"
                        :checked="selectedBarangIds.includes(barang.id)"
                        @click.stop="toggleBarang(barang.id)"
                        class="mt-1 w-4 h-4 text-[#996600] border-gray-300 rounded focus:ring-[#996600]"
                      />
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                          <p class="text-sm font-medium text-gray-900 truncate">{{ barang.nama_barang }}</p>
                          <span class="ml-2 text-sm font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                            {{ barang.kode_barang }}
                          </span>
                        </div>
                        <div class="flex items-center space-x-4 mt-1 text-xs text-gray-600">
                          <span v-if="barang.kategori" class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ barang.kategori }}
                          </span>
                          <span class="flex items-center font-medium text-[#996600]">
                            Stock: {{ barang.stok }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-if="filteredBarangs.length === 0" class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm">Tidak ada barang yang sesuai</p>
                  </div>
                </div>

                <!-- Selected Count -->
                <div v-if="selectedBarangIds.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-3">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-green-800 font-medium">
                      {{ selectedBarangIds.length }} barang dipilih
                    </span>
                    <button
                      @click="selectedBarangIds = []"
                      class="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                      Batalkan Semua
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Review Data -->
          <div v-if="currentStep === 1">
            <div class="space-y-4">
              <!-- Summary -->
              <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] rounded-lg p-4 text-white">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div>
                    <p class="text-xs opacity-90">Total Barang</p>
                    <p class="text-2xl font-bold">{{ previewData.length }}</p>
                  </div>
                  <div>
                    <p class="text-xs opacity-90">Total Stock</p>
                    <p class="text-2xl font-bold">{{ totalStock }}</p>
                  </div>
                  <div>
                    <p class="text-xs opacity-90">Tanggal Opname</p>
                    <p class="text-sm font-medium mt-1">{{ formatDateTime(opnameDate) }}</p>
                  </div>
                  <div>
                    <p class="text-xs opacity-90">Status</p>
                    <p class="text-sm font-medium mt-1">Auto-Fill Aktif</p>
                  </div>
                </div>
              </div>

              <!-- Opname Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Stock Opname:</label>
                <input
                  type="datetime-local"
                  v-model="opnameDate"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
              </div>

              <!-- Preview Table -->
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-sm font-medium text-gray-700">Data yang akan diproses:</label>
                  <span class="text-xs text-gray-600">{{ previewData.length }} item</span>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                  <div class="max-h-80 overflow-y-auto">
                    <table class="w-full text-sm">
                      <thead class="bg-gray-50 sticky top-0">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">No</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kode Barang</th>
                          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Nama Barang</th>
                          <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">Stock Sistem</th>
                          <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">Stock Fisik</th>
                          <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">Selisih</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100">
                        <tr v-for="(item, index) in previewData" :key="item.id" class="hover:bg-gray-50">
                          <td class="px-3 py-2 text-gray-900">{{ index + 1 }}</td>
                          <td class="px-3 py-2 font-mono text-gray-900">{{ item.kode_barang }}</td>
                          <td class="px-3 py-2 text-gray-900">{{ item.nama_barang }}</td>
                          <td class="px-3 py-2 text-center font-medium text-gray-900">{{ item.stok }}</td>
                          <td class="px-3 py-2 text-center">
                            <input
                              type="number"
                              v-model.number="item.stock_fisik"
                              min="0"
                              class="w-20 px-2 py-1 text-center border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-transparent"
                            />
                          </td>
                          <td class="px-3 py-2 text-center">
                            <span
                              :class="[
                                'font-bold text-xs',
                                (item.stock_fisik - item.stok) > 0 ? 'text-green-600' :
                                (item.stock_fisik - item.stok) < 0 ? 'text-red-600' : 'text-gray-600'
                              ]"
                            >
                              {{ (item.stock_fisik - item.stok) > 0 ? '+' : '' }}{{ item.stock_fisik - item.stok }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Note -->
              <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex items-start space-x-2">
                  <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <div class="text-xs text-yellow-800">
                    <p class="font-medium mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                      <li>Stock fisik telah diisi otomatis dengan nilai stock sistem</li>
                      <li>Anda dapat mengubah nilai stock fisik jika diperlukan</li>
                      <li>Data akan langsung tersimpan dan stock akan diupdate</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-200">
          <button
            v-if="currentStep > 0"
            @click="previousStep"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali</span>
          </button>
          <button
            v-else
            @click="closeModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Batal
          </button>

          <div class="flex items-center space-x-3">
            <span v-if="currentStep === 0 && selectionMode === 'manual'" class="text-sm text-gray-600">
              {{ selectedBarangIds.length }} dipilih
            </span>
            <button
              v-if="currentStep < steps.length - 1"
              @click="nextStep"
              :disabled="!canProceed"
              class="px-4 py-2 text-sm font-medium text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center space-x-1"
            >
              <span>Lanjutkan</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
            <button
              v-else
              @click="submitOpname"
              :disabled="processing || previewData.length === 0"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center space-x-2"
            >
              <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ processing ? 'Memproses...' : 'Simpan Stock Opname' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  barangs: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['close', 'submit']);

const steps = [
  { title: 'Pilih Barang' },
  { title: 'Review & Konfirmasi' }
];

const currentStep = ref(0);
const selectionMode = ref('all');
const searchQuery = ref('');
const selectedBarangIds = ref([]);
const opnameDate = ref(new Date().toISOString().slice(0, 16));
const processing = ref(false);
const previewData = ref([]);

const availableBarangs = computed(() => {
  return props.barangs.filter(b => b.stok !== undefined && b.stok !== null);
});

const filteredBarangs = computed(() => {
  if (!searchQuery.value) {
    return availableBarangs.value;
  }

  const query = searchQuery.value.toLowerCase();
  return availableBarangs.value.filter(b =>
    b.kode_barang.toLowerCase().includes(query) ||
    b.nama_barang.toLowerCase().includes(query) ||
    (b.kategori && b.kategori.toLowerCase().includes(query))
  );
});

const isAllFilteredSelected = computed(() => {
  if (filteredBarangs.value.length === 0) return false;
  return filteredBarangs.value.every(b => selectedBarangIds.value.includes(b.id));
});

const canProceed = computed(() => {
  if (currentStep.value === 0) {
    if (selectionMode.value === 'all') {
      return availableBarangs.value.length > 0;
    } else {
      return selectedBarangIds.value.length > 0;
    }
  }
  return true;
});

const totalStock = computed(() => {
  return previewData.value.reduce((sum, item) => sum + (item.stock_fisik || 0), 0);
});

watch(() => props.show, (newVal) => {
  if (newVal) {
    resetModal();
  }
});

const resetModal = () => {
  currentStep.value = 0;
  selectionMode.value = 'all';
  searchQuery.value = '';
  selectedBarangIds.value = [];
  opnameDate.value = new Date().toISOString().slice(0, 16);
  previewData.value = [];
  processing.value = false;
};

const toggleBarang = (barangId) => {
  const index = selectedBarangIds.value.indexOf(barangId);
  if (index > -1) {
    selectedBarangIds.value.splice(index, 1);
  } else {
    selectedBarangIds.value.push(barangId);
  }
};

const toggleAllFiltered = () => {
  if (isAllFilteredSelected.value) {
    const filteredIds = filteredBarangs.value.map(b => b.id);
    selectedBarangIds.value = selectedBarangIds.value.filter(id => !filteredIds.includes(id));
  } else {
    const filteredIds = filteredBarangs.value.map(b => b.id);
    const newIds = filteredIds.filter(id => !selectedBarangIds.value.includes(id));
    selectedBarangIds.value.push(...newIds);
  }
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const nextStep = () => {
  if (currentStep.value === 0) {
    preparePreviewData();
  }
  currentStep.value++;
};

const previousStep = () => {
  currentStep.value--;
};

const preparePreviewData = () => {
  let selectedBarangs = [];

  if (selectionMode.value === 'all') {
    selectedBarangs = [...availableBarangs.value];
  } else {
    selectedBarangs = availableBarangs.value.filter(b => selectedBarangIds.value.includes(b.id));
  }

  previewData.value = selectedBarangs.map(barang => ({
    id: barang.id,
    kode_barang: barang.kode_barang,
    nama_barang: barang.nama_barang,
    kategori: barang.kategori,
    stok: barang.stok,
    stock_fisik: barang.stok,
    harga_beli: barang.harga_beli
  }));
};

const submitOpname = () => {
  processing.value = true;

  const data = previewData.value.map(item => ({
    barang_id: item.id,
    stock_fisik: item.stock_fisik,
    keterangan: 'Auto Opname'
  }));

  emit('submit', {
    data: data,
    opname_date: opnameDate.value
  });
};

const closeModal = () => {
  if (!processing.value) {
    emit('close');
  }
};

defineExpose({
  resetProcessing: () => {
    processing.value = false;
  }
});
</script>
