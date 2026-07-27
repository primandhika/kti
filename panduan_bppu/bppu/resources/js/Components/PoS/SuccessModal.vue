<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <!-- Header - IKIP Colors -->
      <div class="bg-[#996600] text-white p-3 text-center">
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-1.5">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3 class="text-lg font-bold">Transaksi Berhasil!</h3>
      </div>

      <!-- Content - Compact -->
      <div class="p-3">

        <!-- MULTI-TOKO: Ringkasan per toko -->
        <template v-if="isMultiToko">
          <div class="space-y-2 mb-2.5">
            <div
              v-for="trx in multiData"
              :key="trx.nomor_transaksi"
              class="bg-[#f4efe5] rounded-lg p-2.5 border border-[#e0d1b2]"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-[#7a5100]">{{ trx.work_unit_name }}</span>
                <span class="font-mono text-[10px] text-[#996600]">{{ trx.nomor_transaksi }}</span>
              </div>
              <details class="text-xs">
                <summary class="cursor-pointer text-gray-500 hover:text-[#996600] select-none">
                  {{ trx.items?.length || 0 }} item
                </summary>
                <div class="mt-1 space-y-0.5 pl-2">
                  <div
                    v-for="(item, i) in trx.items"
                    :key="i"
                    class="flex justify-between text-gray-600"
                  >
                    <span class="truncate flex-1">{{ item.nama }} <span class="text-[#996600]">x{{ item.qty }}</span></span>
                    <span class="ml-2 flex-shrink-0">Rp {{ formatCurrency(item.subtotal) }}</span>
                  </div>
                </div>
              </details>
              <div class="flex justify-between text-xs font-bold text-[#996600] border-t border-[#d6c199] mt-1.5 pt-1.5">
                <span>Subtotal {{ trx.work_unit_name }}</span>
                <span>Rp {{ formatCurrency(trx.total) }}</span>
              </div>
            </div>
          </div>
          <div class="flex justify-between font-bold text-base text-[#996600] border-t border-[#d6c199] pt-2 mb-2.5">
            <span>Total Keseluruhan</span>
            <span>Rp {{ formatCurrency(grandTotal) }}</span>
          </div>
        </template>

        <!-- SINGLE TOKO: tampilan lama -->
        <template v-else>
          <!-- Transaction Info Compact -->
          <div class="bg-[#f4efe5] rounded-lg p-2.5 mb-2.5 border border-[#e0d1b2]">
            <div class="flex items-center justify-between mb-1.5">
              <div class="text-xs text-[#6b4700]">No. Transaksi</div>
              <div class="font-mono text-xs font-bold text-[#996600]">{{ singleData.nomor_transaksi }}</div>
            </div>

            <!-- Summary Compact -->
            <div class="space-y-0.5 text-sm border-t border-[#e0d1b2] pt-1.5">
              <div class="flex justify-between">
                <span class="text-[#6b4700]">Items ({{ singleData.items?.length || 0 }})</span>
                <span class="font-medium text-[#996600]">Rp {{ formatCurrency(singleData.subtotal) }}</span>
              </div>
              <div v-if="singleData.diskon > 0" class="flex justify-between text-xs text-orange-600">
                <span>Diskon</span>
                <span>- Rp {{ formatCurrency(singleData.diskon) }}</span>
              </div>
              <div v-if="singleData.potongan_poin > 0" class="flex justify-between text-xs text-green-600">
                <span>Potongan Poin</span>
                <span>- Rp {{ formatCurrency(singleData.potongan_poin) }}</span>
              </div>
              <div class="flex justify-between font-bold text-base border-t border-[#d6c199] pt-1 mt-1">
                <span class="text-[#6b4700]">Total</span>
                <span class="text-[#996600]">Rp {{ formatCurrency(singleData.total) }}</span>
              </div>
              <div v-if="singleData.kembalian > 0" class="flex justify-between text-xs text-green-700">
                <span>Kembalian</span>
                <span class="font-semibold">Rp {{ formatCurrency(singleData.kembalian) }}</span>
              </div>
            </div>
          </div>

          <!-- Items List - Collapsible/Compact -->
          <details class="mb-2.5 text-sm">
            <summary class="cursor-pointer text-xs font-semibold text-[#6b4700] hover:text-[#996600] select-none">
              Detail Item ({{ singleData.items?.length || 0 }})
            </summary>
            <div class="mt-1.5 space-y-1 pl-2">
              <div
                v-for="(item, index) in singleData.items"
                :key="index"
                class="flex justify-between items-start text-xs text-gray-600"
              >
                <div class="flex-1">
                  <span>{{ item.nama }} <span class="text-[#996600] font-medium">({{ item.qty }}x)</span></span>
                  <span v-if="item.diskon_per_item > 0" class="ml-1 text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">DISKON</span>
                  <div v-if="item.diskon_per_item > 0" class="text-[10px] text-gray-400">
                    Harga satuan: <span class="line-through">Rp {{ formatCurrency(item.harga_satuan) }}</span>
                    <span class="text-green-700 ml-1">Rp {{ formatCurrency(item.harga_satuan - item.diskon_per_item) }}</span>
                  </div>
                </div>
                <span class="font-medium text-gray-800 ml-2 flex-shrink-0">Rp {{ formatCurrency(item.subtotal) }}</span>
              </div>
            </div>
          </details>
        </template>

        <!-- Actions - Compact Single Row -->
        <div class="space-y-1.5">
          <!-- Primary Actions Row -->
          <div class="flex gap-1.5">
            <!-- Print Button -->
            <button
              @click="handlePrintReceipt"
              :disabled="isPrinting"
              :class="[
                'flex-1 py-2 rounded-lg font-semibold transition-colors flex items-center justify-center gap-1.5 text-xs',
                isPrinting
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  : 'bg-[#996600] hover:bg-[#7a5100] text-white shadow-sm'
              ]"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              <span class="hidden sm:inline">{{ isPrinting ? 'Printing...' : 'Cetak' }}</span>
            </button>

            <!-- Upload Photo Button (hanya untuk single-toko) -->
            <div v-if="!isMultiToko && !fotoBukti" class="flex-1">
              <input
                ref="photoInput"
                type="file"
                accept="image/*"
                capture="environment"
                class="hidden"
                @change="handlePhotoSelected"
              />
              <button
                @click="$refs.photoInput.click()"
                :disabled="isUploading"
                :class="[
                  'w-full py-2 rounded-lg font-semibold transition-colors flex items-center justify-center gap-1.5 text-xs border-2',
                  isUploading
                    ? 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                    : 'bg-white border-[#996600] text-[#996600] hover:bg-[#f4efe5]'
                ]"
                :title="isUploading ? uploadProgress : 'Ambil/Upload Foto Bukti'"
              >
                <svg v-if="!isUploading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="hidden sm:inline">{{ isUploading ? uploadProgress : 'Foto' }}</span>
                <span class="sm:hidden">{{ isUploading ? '...' : 'Foto' }}</span>
              </button>
            </div>

            <!-- Photo Uploaded Indicator -->
            <div v-else-if="!isMultiToko" class="flex-1 flex items-center justify-center bg-green-50 border-2 border-green-500 rounded-lg px-2 py-1.5">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span class="text-xs text-green-700 font-medium ml-1.5 hidden sm:inline">Foto OK</span>
            </div>

            <!-- OK Button -->
            <button
              @click="$emit('close')"
              class="flex-1 bg-[#d6c199] hover:bg-[#c1a366] text-[#3d2800] py-2 rounded-lg font-semibold text-xs transition-colors"
            >
              OK
            </button>
          </div>

          <!-- Secondary Action -->
          <Link
            href="/pengelola/rekap-penjualan"
            @click="$emit('close')"
            class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-1.5 rounded-lg font-medium text-xs text-center transition-colors"
          >
            Lihat Rekap
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useThermalPrinter } from '@/composables/useThermalPrinter'
import { compressImage } from '@/utils/imageCompressor'

const props = defineProps({
  show: Boolean,
  successMessage: {
    type: [Object, Array],
    default: () => ({})
  }
})

// Deteksi multi-toko (array dari transactions)
const isMultiToko = computed(() => Array.isArray(props.successMessage))

// Untuk single-toko, gunakan langsung; untuk multi normalize jadi summary
const singleData = computed(() => isMultiToko.value ? null : props.successMessage)
const multiData = computed(() => isMultiToko.value ? props.successMessage : null)
const grandTotal = computed(() => isMultiToko.value
  ? props.successMessage.reduce((s, t) => s + t.total, 0)
  : props.successMessage.total
)

defineEmits(['close'])

const isPrinting = ref(false)
const isUploading = ref(false)
const uploadProgress = ref('')
const fotoBukti = ref(null)
const photoInput = ref(null)
const { printReceipt } = useThermalPrinter()

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const handlePrintReceipt = async () => {
  try {
    isPrinting.value = true
    if (isMultiToko.value) {
      for (const trx of multiData.value) {
        await printReceipt(trx)
      }
    } else {
      await printReceipt(props.successMessage)
    }
  } catch (error) {
    console.error('Print error:', error)
    alert('Gagal mencetak struk. Pastikan printer terhubung.')
  } finally {
    isPrinting.value = false
  }
}

const handlePhotoSelected = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    alert('File harus berupa gambar')
    return
  }

  if (file.size > 10 * 1024 * 1024) {
    alert('Ukuran file maksimal 10MB')
    return
  }

  try {
    isUploading.value = true
    uploadProgress.value = 'Memproses foto...'

    // Client-side compression untuk foto dari ponsel (target 250KB)
    let fileToUpload = file
    const fileSizeKB = file.size / 1024

    if (fileSizeKB > 300) {
      uploadProgress.value = 'Mengompresi foto...'
      try {
        fileToUpload = await compressImage(file, {
          maxWidth: 1200,
          maxHeight: 1200,
          quality: 0.85,
          targetSizeKB: 250,
          type: 'image/jpeg'
        })
        console.log('Client compression:', {
          original: `${fileSizeKB.toFixed(2)} KB`,
          compressed: `${(fileToUpload.size / 1024).toFixed(2)} KB`
        })
      } catch (compressionError) {
        console.warn('Client compression failed, uploading original:', compressionError)
        // Jika kompresi gagal, tetap upload file asli
      }
    }

    uploadProgress.value = 'Mengupload...'
    const formData = new FormData()
    formData.append('foto_bukti', fileToUpload)

    router.post(
      `/pengelola/penjualan/${props.successMessage.id}/upload-foto-bukti`,
      formData,
      {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
          fotoBukti.value = true
          uploadProgress.value = 'Berhasil!'
        },
        onError: (errors) => {
          console.error('Upload error:', errors)
          alert('Gagal upload foto bukti: ' + (errors.foto_bukti || errors.error || 'Unknown error'))
          uploadProgress.value = ''
        },
        onFinish: () => {
          isUploading.value = false
          uploadProgress.value = ''
          if (photoInput.value) {
            photoInput.value.value = ''
          }
        }
      }
    )
  } catch (error) {
    console.error('Photo upload error:', error)
    alert('Gagal upload foto bukti')
    isUploading.value = false
    uploadProgress.value = ''
  }
}
</script>
