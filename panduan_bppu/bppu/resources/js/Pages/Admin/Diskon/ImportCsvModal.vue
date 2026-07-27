<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-800">Impor Potongan dari CSV</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <!-- Format info -->
          <div class="bg-[#f4efe5] rounded-lg p-4 text-sm text-[#6b4700] space-y-2">
            <p class="font-semibold">Format kolom CSV:</p>
            <div class="font-mono text-xs bg-white border border-[#e0d1b2] rounded p-2 overflow-x-auto">
              nama, tipe, nilai, deskripsi, minimum_transaksi, maksimum_potongan, berlaku_mulai, berlaku_sampai, kuota_voucher, member_codes
            </div>
            <ul class="text-xs space-y-1 list-disc pl-4 text-[#5b3d00]">
              <li><strong>nama, tipe, nilai</strong> - wajib diisi</li>
              <li><strong>tipe</strong> - isi dengan "persen" atau "nominal"</li>
              <li><strong>berlaku_mulai/sampai</strong> - format YYYY-MM-DD</li>
              <li><strong>member_codes</strong> - kode member dipisah titik koma (cth: MC001;MC002)</li>
              <li>Voucher akan di-generate otomatis sesuai kuota_voucher</li>
            </ul>
            <a
              href="#"
              @click.prevent="downloadContoh"
              class="inline-flex items-center gap-1 text-xs text-[#996600] underline"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download contoh CSV
            </a>
          </div>

          <!-- File upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">File CSV <span class="text-red-500">*</span></label>
            <input
              ref="fileInput"
              type="file"
              accept=".csv,.txt"
              @change="onFileChange"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
              :class="{ 'border-red-400': form.errors.file }"
            />
            <p v-if="form.errors.file" class="text-xs text-red-500 mt-1">{{ form.errors.file }}</p>
          </div>

          <!-- Error list from import -->
          <div v-if="importErrors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <p class="text-sm font-medium text-red-700 mb-2">Beberapa baris dilewati:</p>
            <ul class="text-xs text-red-600 space-y-1 list-disc pl-4 max-h-28 overflow-y-auto">
              <li v-for="(err, i) in importErrors" :key="i">{{ err }}</li>
            </ul>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              @click="submit"
              :disabled="!selectedFile || form.processing"
              class="px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] transition-colors disabled:opacity-50"
            >
              {{ form.processing ? 'Mengimpor...' : 'Impor' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const emit = defineEmits(['close'])

const fileInput = ref(null)
const selectedFile = ref(null)

const form = useForm({ file: null })

const page = usePage()
const importErrors = computed(() => page.props.flash?.import_errors || [])

function onFileChange(e) {
  selectedFile.value = e.target.files[0] || null
  form.file = selectedFile.value
}

function submit() {
  if (!selectedFile.value) return
  form.file = selectedFile.value
  form.post('/pengelola/diskon/import-csv', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      if (!importErrors.value.length) emit('close')
    },
  })
}

function downloadContoh() {
  const header = 'nama,tipe,nilai,deskripsi,minimum_transaksi,maksimum_potongan,berlaku_mulai,berlaku_sampai,kuota_voucher,member_codes'
  const row = 'Diskon Spesial,persen,10,Diskon 10% untuk pelanggan setia,50000,,2026-05-01,2026-05-31,5,MC001;MC002'
  const csv = header + '\n' + row
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'contoh-import-potongan.csv'
  a.click()
  URL.revokeObjectURL(url)
}
</script>
