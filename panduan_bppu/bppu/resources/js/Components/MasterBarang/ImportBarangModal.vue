<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="$emit('close')"
  >
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('close')"></div>

      <div class="relative inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-[#996600] to-[#7a5100]">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <span>Impor Data Barang</span>
            </h3>
            <button
              @click="$emit('close')"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
          <div class="flex">
            <button
              @click="activeTab = 'paste'"
              :class="[
                'flex-1 px-6 py-3 text-sm font-medium transition-colors',
                activeTab === 'paste'
                  ? 'text-[#996600] border-b-2 border-[#996600] bg-amber-50'
                  : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
              ]"
            >
              <div class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Paste CSV</span>
              </div>
            </button>
            <button
              @click="activeTab = 'upload'"
              :class="[
                'flex-1 px-6 py-3 text-sm font-medium transition-colors',
                activeTab === 'upload'
                  ? 'text-[#996600] border-b-2 border-[#996600] bg-amber-50'
                  : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'
              ]"
            >
              <div class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span>Upload File CSV</span>
              </div>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-4 max-h-[calc(100vh-300px)] overflow-y-auto">
          <!-- Paste CSV Tab -->
          <div v-if="activeTab === 'paste'">
            <div class="space-y-4">
              <!-- Textarea -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Paste Data CSV:
                </label>
                <textarea
                  v-model="csvText"
                  rows="10"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent font-mono text-sm"
                  placeholder="Paste data CSV di sini..."
                ></textarea>
              </div>

              <!-- Accordion Info -->
              <div class="border border-gray-200 rounded-lg">
                <button
                  @click="showInfo = !showInfo"
                  class="w-full px-4 py-3 flex items-center justify-between text-left bg-blue-50 hover:bg-blue-100 transition-colors rounded-t-lg"
                >
                  <span class="text-sm font-semibold text-blue-900">Format CSV & Contoh</span>
                  <svg
                    :class="['w-5 h-5 text-blue-600 transition-transform', showInfo ? 'rotate-180' : '']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div v-show="showInfo" class="p-4 space-y-3 border-t border-gray-200">
                  <div class="text-xs text-gray-700 space-y-2">
                    <p class="font-semibold">Kolom CSV:</p>
                    <p>kode_barang, nama_barang, kategori_id, satuan, stok, harga_beli, harga_jual, minimum_stok, deskripsi, supplier_id, jenis_barang, tanggal_kadaluarsa</p>
                  </div>

                  <div class="bg-gray-50 border border-gray-200 rounded p-2 font-mono text-xs overflow-x-auto">
                    <div class="text-gray-600">kode_barang,nama_barang,kategori_id,satuan,stok,harga_beli,harga_jual,minimum_stok,deskripsi,supplier_id,jenis_barang,tanggal_kadaluarsa</div>
                    <div class="text-gray-800">ABC24-0001,Aqua Gelas,1,pcs,100,2500,3000,10,Air mineral gelas,1,jual_langsung,</div>
                    <div class="text-gray-800">ABC24-0002,Indomie Goreng,2,pcs,200,2800,3500,20,Mie instan goreng,,jual_langsung,2025-12-31</div>
                  </div>
                </div>
              </div>

              <!-- Error Display -->
              <div v-if="errors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div class="text-sm text-red-800">
                    <p class="font-medium mb-2">Terdapat {{ errors.length }} error:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs max-h-32 overflow-y-auto">
                      <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload File Tab -->
          <div v-if="activeTab === 'upload'">
            <div class="space-y-4">
              <!-- Info -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Persyaratan File:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                      <li>Format file: CSV (.csv)</li>
                      <li>Ukuran maksimal: 5 MB</li>
                      <li>Format kolom sama dengan contoh di bawah</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Download Template -->
              <div>
                <button
                  @click="downloadTemplate"
                  class="inline-flex items-center space-x-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm font-medium"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span>Download Template CSV</span>
                </button>
              </div>

              <!-- File Upload -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Upload File CSV:
                </label>
                <div
                  @dragover.prevent="isDragging = true"
                  @dragleave.prevent="isDragging = false"
                  @drop.prevent="handleDrop"
                  :class="[
                    'border-2 border-dashed rounded-lg p-8 text-center transition-colors',
                    isDragging ? 'border-[#996600] bg-amber-50' : 'border-gray-300 bg-gray-50'
                  ]"
                >
                  <input
                    ref="fileInput"
                    type="file"
                    accept=".csv"
                    @change="handleFileSelect"
                    class="hidden"
                  />

                  <div v-if="!selectedFile" class="space-y-2">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="text-sm text-gray-600">
                      <button
                        @click="$refs.fileInput.click()"
                        class="text-[#996600] hover:text-[#7a5100] font-medium"
                      >
                        Klik untuk upload
                      </button>
                      atau drag and drop
                    </div>
                    <p class="text-xs text-gray-500">CSV up to 5MB</p>
                  </div>

                  <div v-else class="space-y-3">
                    <div class="flex items-center justify-center space-x-3">
                      <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <div class="text-left">
                        <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
                        <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                      </div>
                    </div>
                    <button
                      @click="removeFile"
                      class="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                      Hapus File
                    </button>
                  </div>
                </div>
              </div>

              <!-- Error Display -->
              <div v-if="errors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div class="text-sm text-red-800">
                    <p class="font-medium mb-2">Terdapat {{ errors.length }} error:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs max-h-32 overflow-y-auto">
                      <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Batal
          </button>
          <button
            @click="processImport"
            :disabled="importing || !canImport"
            class="px-4 py-2 text-sm font-medium text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <svg v-if="importing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span>{{ importing ? 'Mengimpor...' : 'Impor Data' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  show: Boolean,
  kategoris: Array,
});

const emit = defineEmits(['close', 'import']);

const activeTab = ref('paste');
const csvText = ref('');
const selectedFile = ref(null);
const isDragging = ref(false);
const importing = ref(false);
const errors = ref([]);
const fileInput = ref(null);
const showInfo = ref(false);

const canImport = computed(() => {
  if (activeTab.value === 'paste') {
    return csvText.value.trim().length > 0;
  } else {
    return selectedFile.value !== null;
  }
});

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    validateAndSetFile(file);
  }
};

const handleDrop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file) {
    validateAndSetFile(file);
  }
};

const validateAndSetFile = (file) => {
  errors.value = [];

  if (!file.name.endsWith('.csv')) {
    errors.value.push('File harus berformat CSV');
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    errors.value.push('Ukuran file maksimal 5 MB');
    return;
  }

  selectedFile.value = file;
};

const removeFile = () => {
  selectedFile.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const formatFileSize = (bytes) => {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
};

const downloadTemplate = () => {
  const csv = 'kode_barang,nama_barang,kategori_id,satuan,stok,harga_beli,harga_jual,minimum_stok,deskripsi,supplier_id,jenis_barang,tanggal_kadaluarsa\nABC24-0001,Aqua Gelas,1,pcs,100,2500,3000,10,Air mineral gelas,1,jual_langsung,\nABC24-0002,Indomie Goreng,2,pcs,200,2800,3500,20,Mie instan goreng,,jual_langsung,2025-12-31\nABC24-0003,Buku Tulis,3,pcs,50,3000,5000,5,Buku tulis 38 lembar,2,jual_langsung,';
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'template_import_barang.csv';
  a.click();
  window.URL.revokeObjectURL(url);
};

const parseCsvText = (text) => {
  const lines = text.trim().split('\n').filter(line => line.trim());
  if (lines.length < 2) {
    errors.value.push('File CSV harus memiliki minimal 1 baris header dan 1 baris data');
    return [];
  }

  const header = lines[0].split(',').map(h => h ? h.trim() : '');
  const data = [];

  for (let i = 1; i < lines.length; i++) {
    const values = lines[i].split(',').map(v => v ? v.trim() : '');
    const row = {};
    header.forEach((h, index) => {
      row[h] = values[index] !== undefined ? values[index] : '';
    });
    data.push(row);
  }

  return data;
};

const validateData = (data) => {
  errors.value = [];
  const validData = [];

  const requiredFields = ['kode_barang', 'nama_barang', 'kategori_id', 'satuan', 'stok', 'harga_beli', 'harga_jual', 'minimum_stok'];

  data.forEach((row, index) => {
    const lineNum = index + 2;

    // Check required fields
    for (const field of requiredFields) {
      if (!row[field]) {
        errors.value.push(`Baris ${lineNum}: ${field} wajib diisi`);
        return;
      }
    }

    // Validate numeric fields
    const numericFields = ['kategori_id', 'stok', 'harga_beli', 'harga_jual', 'minimum_stok'];
    for (const field of numericFields) {
      const value = parseInt(row[field]);
      if (isNaN(value) || value < 0) {
        errors.value.push(`Baris ${lineNum}: ${field} harus berupa angka positif`);
        return;
      }
    }

    // Validate optional numeric fields
    if (row.supplier_id) {
      const supplierId = parseInt(row.supplier_id);
      if (isNaN(supplierId) || supplierId < 0) {
        errors.value.push(`Baris ${lineNum}: supplier_id harus berupa angka positif`);
        return;
      }
    }

    // Validate kategori_id exists
    const kategoriId = parseInt(row.kategori_id);
    if (props.kategoris && !props.kategoris.find(k => k.id === kategoriId)) {
      errors.value.push(`Baris ${lineNum}: kategori_id ${kategoriId} tidak ditemukan`);
      return;
    }

    validData.push({
      kode_barang: row.kode_barang,
      nama_barang: row.nama_barang,
      kategori_id: parseInt(row.kategori_id),
      satuan: row.satuan,
      stok: parseInt(row.stok),
      harga_beli: parseInt(row.harga_beli),
      harga_jual: parseInt(row.harga_jual),
      minimum_stok: parseInt(row.minimum_stok),
      deskripsi: row.deskripsi || '',
      supplier_id: row.supplier_id ? parseInt(row.supplier_id) : null,
      jenis_barang: row.jenis_barang || 'jual_langsung',
      tanggal_kadaluarsa: row.tanggal_kadaluarsa || null,
      is_active: true,
    });
  });

  return validData;
};

const processImport = async () => {
  errors.value = [];
  importing.value = true;

  try {
    let csvData = '';

    if (activeTab.value === 'paste') {
      csvData = csvText.value;
    } else {
      csvData = await readFileAsText(selectedFile.value);
    }

    const parsedData = parseCsvText(csvData);
    if (parsedData.length === 0) {
      importing.value = false;
      return;
    }

    const validData = validateData(parsedData);
    if (errors.value.length > 0) {
      importing.value = false;
      return;
    }

    emit('import', validData);

  } catch (error) {
    errors.value.push('Terjadi kesalahan saat memproses file: ' + error.message);
  } finally {
    importing.value = false;
  }
};

const readFileAsText = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.onerror = (e) => reject(e);
    reader.readAsText(file);
  });
};
</script>
