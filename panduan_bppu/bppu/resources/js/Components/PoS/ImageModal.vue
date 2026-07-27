<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <div class="p-4 border-b">
        <h3 class="text-lg font-bold text-gray-800">Foto Produk</h3>
        <p class="text-sm text-gray-600">{{ selectedBarang?.nama_barang }}</p>
      </div>

      <div class="p-4">
        <!-- Tab/Pills Mode Toggle - PINDAH KE ATAS -->
        <div class="mb-4">
          <div class="flex gap-1 p-1 bg-gray-100 rounded-lg">
            <button
              @click="setMode('upload')"
              :class="[
                'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all',
                mode === 'upload'
                  ? 'bg-white text-[#996600] shadow-sm'
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              Upload File
            </button>
            <button
              @click="setMode('camera')"
              :class="[
                'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all',
                mode === 'camera'
                  ? 'bg-white text-[#996600] shadow-sm'
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Kamera
            </button>
          </div>
        </div>

        <!-- Current Image / Camera Preview / Drop Zone -->
        <div class="mb-4">
          <div
            class="w-full h-64 rounded-lg overflow-hidden relative"
            :class="[
              isDragging ? 'ring-2 ring-[#996600] ring-offset-1' : '',
              !isCameraActive && !displayPreview ? 'border-2 border-dashed border-gray-300 bg-gray-50 hover:border-[#996600] hover:bg-[#f4efe5] cursor-pointer transition-colors' : 'bg-gray-100'
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDrop"
            @click="!isCameraActive && !displayPreview ? fileInputRef?.click() : null"
          >
            <!-- Camera Stream -->
            <video
              v-show="isCameraActive"
              ref="videoRef"
              autoplay
              playsinline
              class="w-full h-full object-cover"
            ></video>

            <!-- Preview or Current Image -->
            <img
              v-if="!isCameraActive && displayPreview"
              :src="displayPreview"
              :alt="selectedBarang?.nama_barang"
              class="w-full h-full object-cover"
            />

            <!-- Drop Zone Placeholder -->
            <div v-if="!isCameraActive && !displayPreview" class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-6">
              <div :class="['p-4 rounded-full transition-colors', isDragging ? 'bg-[#e0d1b2]' : 'bg-gray-100']">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="text-center">
                <p class="text-sm font-medium text-gray-700">
                  {{ isDragging ? 'Lepaskan untuk upload' : 'Seret foto ke sini' }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">atau klik untuk pilih file</p>
              </div>
            </div>

            <!-- Drag overlay saat ada foto -->
            <div
              v-if="isDragging && displayPreview && !isCameraActive"
              class="absolute inset-0 bg-black/50 flex items-center justify-center"
            >
              <p class="text-white font-semibold text-sm">Lepaskan untuk ganti foto</p>
            </div>
          </div>
        </div>

        <!-- Upload Mode -->
        <div v-if="mode === 'upload'" class="space-y-3">
          <input
            type="file"
            ref="fileInputRef"
            @change="handleFileChange"
            accept="image/*"
            class="hidden"
          />

          <!-- Action Buttons for Upload Mode -->
          <div class="flex gap-2">
            <button
              v-if="selectedBarang?.image"
              @click="$emit('delete-image')"
              :disabled="isUploading"
              class="px-3 py-2 border border-red-300 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50"
            >
              Hapus
            </button>
            <button
              @click="$emit('upload-image')"
              :disabled="!selectedFile || isUploading"
              class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :class="selectedFile && !isUploading ? 'bg-[#996600] hover:bg-[#7a5100] text-white' : 'bg-gray-300 text-gray-500'"
            >
              {{ isUploading ? 'Mengupload...' : 'Upload' }}
            </button>
            <button
              @click="closeModal"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Tutup
            </button>
          </div>
        </div>

        <!-- Camera Mode -->
        <div v-else-if="mode === 'camera'" class="space-y-3">
          <div v-if="cameraError" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ cameraError }}
          </div>

          <!-- Camera Active: Ambil Foto -->
          <div v-if="isCameraActive" class="flex gap-2">
            <button
              @click="capturePhoto"
              class="flex-1 px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg text-sm font-semibold transition-colors"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Ambil Foto
            </button>
            <button
              @click="closeModal"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Tutup
            </button>
          </div>

          <!-- Preview Captured Photo: Upload atau Ambil Ulang -->
          <div v-else-if="capturedPreview && !isCameraActive" class="flex gap-2">
            <button
              @click="retakePhoto"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Ambil Ulang
            </button>
            <button
              @click="uploadCapturedPhoto"
              :disabled="isUploading"
              class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50"
              :class="!isUploading ? 'bg-[#996600] hover:bg-[#7a5100] text-white' : 'bg-gray-300 text-gray-500'"
            >
              {{ isUploading ? 'Mengupload...' : 'Upload Foto Ini' }}
            </button>
            <button
              @click="closeModal"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Tutup
            </button>
          </div>

          <!-- No Camera, No Preview: Buka Kamera atau Hapus -->
          <div v-else class="flex gap-2">
            <button
              v-if="selectedBarang?.image"
              @click="$emit('delete-image')"
              :disabled="isUploading"
              class="px-3 py-2 border border-red-300 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Hapus Foto
            </button>
            <button
              @click="startCamera"
              class="flex-1 px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg text-sm font-semibold transition-colors"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              Buka Kamera
            </button>
            <button
              @click="closeModal"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount, computed } from 'vue'

const props = defineProps({
  show: Boolean,
  selectedBarang: Object,
  imagePreview: String,
  selectedFile: File,
  isUploading: Boolean,
})

const emit = defineEmits(['close', 'upload-image', 'delete-image', 'file-change'])

const mode = ref('upload')
const fileInputRef = ref(null)
const isDragging = ref(false)
const videoRef = ref(null)
const isCameraActive = ref(false)
const cameraError = ref('')
const mediaStream = ref(null)

// Local state untuk captured photo (tidak langsung ke parent)
const capturedPhoto = ref(null)
const capturedPreview = ref(null)

// Computed untuk preview yang ditampilkan
const displayPreview = computed(() => {
  // Prioritas: capturedPreview > imagePreview dari props > image barang
  if (capturedPreview.value) return capturedPreview.value
  if (props.imagePreview) return props.imagePreview
  return props.selectedBarang?.image
})

const handleFileChange = (event) => {
  emit('file-change', event)
}

const onDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer?.files?.[0]
  if (!file || !file.type.startsWith('image/')) return
  mode.value = 'upload'
  emit('file-change', { target: { files: [file] } })
}

const setMode = (newMode) => {
  if (mode.value === 'camera' && isCameraActive.value) {
    stopCamera()
  }
  mode.value = newMode
  cameraError.value = ''

  // Clear captured photo saat ganti mode
  if (newMode === 'upload') {
    capturedPhoto.value = null
    capturedPreview.value = null
  }
}

const startCamera = async () => {
  cameraError.value = ''

  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment',
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    })

    mediaStream.value = stream

    if (videoRef.value) {
      videoRef.value.srcObject = stream
      isCameraActive.value = true
    }
  } catch (error) {
    console.error('Camera error:', error)

    if (error.name === 'NotAllowedError') {
      cameraError.value = 'Akses kamera ditolak. Mohon izinkan akses kamera di pengaturan browser.'
    } else if (error.name === 'NotFoundError') {
      cameraError.value = 'Kamera tidak ditemukan pada perangkat ini.'
    } else {
      cameraError.value = 'Gagal membuka kamera. Pastikan kamera tersedia dan tidak digunakan aplikasi lain.'
    }
  }
}

const stopCamera = () => {
  if (mediaStream.value) {
    mediaStream.value.getTracks().forEach(track => track.stop())
    mediaStream.value = null
  }
  isCameraActive.value = false
}

const capturePhoto = () => {
  if (!videoRef.value) return

  const canvas = document.createElement('canvas')
  canvas.width = videoRef.value.videoWidth
  canvas.height = videoRef.value.videoHeight

  const ctx = canvas.getContext('2d')
  ctx.drawImage(videoRef.value, 0, 0)

  // Stop kamera SEBELUM create blob untuk avoid race condition
  stopCamera()

  canvas.toBlob((blob) => {
    if (!blob) {
      console.error('Failed to create blob from canvas')
      return
    }

    const file = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' })

    // Simpan lokal dulu, JANGAN langsung emit ke parent
    capturedPhoto.value = file
    capturedPreview.value = URL.createObjectURL(blob)

    console.log('Photo captured:', {
      hasFile: !!capturedPhoto.value,
      hasPreview: !!capturedPreview.value,
      isCameraActive: isCameraActive.value
    })
  }, 'image/jpeg', 0.9)
}

const retakePhoto = () => {
  console.log('Retaking photo - clearing preview and restarting camera')

  // Clear preview lokal
  if (capturedPreview.value) {
    URL.revokeObjectURL(capturedPreview.value)
  }
  capturedPhoto.value = null
  capturedPreview.value = null

  console.log('Preview cleared:', {
    hasFile: !!capturedPhoto.value,
    hasPreview: !!capturedPreview.value
  })

  // Restart camera
  startCamera()
}

const uploadCapturedPhoto = async () => {
  if (!capturedPhoto.value) return

  // Baru kirim ke parent saat user klik "Upload Foto Ini"
  const event = {
    target: {
      files: [capturedPhoto.value]
    }
  }

  // TUNGGU handleFileChange selesai (kompresi dll) BARU emit upload
  await handleFileChange(event)

  // Trigger upload setelah file ready
  emit('upload-image')
}

const closeModal = () => {
  stopCamera()

  // Cleanup captured preview URL
  if (capturedPreview.value) {
    URL.revokeObjectURL(capturedPreview.value)
  }

  // Reset state
  capturedPhoto.value = null
  capturedPreview.value = null
  mode.value = 'upload'
  cameraError.value = ''

  emit('close')
}

watch(() => mode.value, () => {
  if (mode.value !== 'camera') {
    stopCamera()
  }
})

watch(() => props.show, (newVal) => {
  // Reset captured photo saat modal dibuka/ditutup
  if (!newVal) {
    if (capturedPreview.value) {
      URL.revokeObjectURL(capturedPreview.value)
    }
    capturedPhoto.value = null
    capturedPreview.value = null
  }
})

onBeforeUnmount(() => {
  stopCamera()
  if (capturedPreview.value) {
    URL.revokeObjectURL(capturedPreview.value)
  }
})
</script>
