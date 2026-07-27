<template>
  <transition name="modal">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75"
      @click.self="closeScanner"
    >
      <div class="relative w-full max-w-2xl mx-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
          <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#996600] to-[#7a5100]">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Scan Barcode
            </h3>
            <button
              @click="closeScanner"
              class="p-1 hover:bg-white/20 rounded-full transition-colors"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="relative bg-gray-900">
            <video
              ref="videoElement"
              class="w-full h-96 object-cover"
              autoplay
              playsinline
            ></video>

            <div class="absolute inset-0 pointer-events-none">
              <div class="absolute inset-0 border-2 border-[#996600] m-8 rounded-lg">
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-lg"></div>
              </div>
              <div class="absolute inset-x-0 top-1/2 h-0.5 bg-red-500 opacity-75"></div>
            </div>

            <canvas ref="canvasElement" class="hidden"></canvas>
          </div>

          <div class="p-4 bg-gray-50 space-y-3">
            <div v-if="lastScannedCode" class="p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-center gap-2 text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                  <p class="text-xs font-medium">Terakhir discan:</p>
                  <p class="font-bold">{{ lastScannedCode }}</p>
                </div>
              </div>
            </div>

            <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-sm text-red-800">{{ errorMessage }}</p>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Arahkan kamera ke barcode produk</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue'
import { BrowserMultiFormatReader } from '@zxing/library'

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'barcode-scanned'])

const videoElement = ref(null)
const canvasElement = ref(null)
const codeReader = ref(null)
const lastScannedCode = ref('')
const errorMessage = ref('')
const isScanning = ref(false)
const isProcessing = ref(false)
const beepSound = ref(null)

const startScanner = async () => {
  try {
    errorMessage.value = ''

    beepSound.value = new Audio('/sfx/bip_pindai.mp3')
    beepSound.value.load()

    codeReader.value = new BrowserMultiFormatReader()

    const videoInputDevices = await codeReader.value.listVideoInputDevices()

    if (videoInputDevices.length === 0) {
      errorMessage.value = 'Tidak ada kamera yang ditemukan'
      return
    }

    const selectedDeviceId = videoInputDevices[videoInputDevices.length - 1]?.deviceId

    isScanning.value = true

    codeReader.value.decodeFromVideoDevice(
      selectedDeviceId,
      videoElement.value,
      (result, error) => {
        if (result && !isProcessing.value) {
          const code = result.getText()

          if (code === lastScannedCode.value) {
            return
          }

          isProcessing.value = true
          lastScannedCode.value = code

          if (beepSound.value) {
            beepSound.value.play().catch(e => console.log('Audio play failed:', e))
          }

          navigator.vibrate?.(200)

          emit('barcode-scanned', code)

          setTimeout(() => {
            closeScanner()
          }, 1000)
        }

        if (error && error.name !== 'NotFoundException') {
          console.error('Scanner error:', error)
        }
      }
    )
  } catch (error) {
    console.error('Failed to start scanner:', error)
    errorMessage.value = 'Gagal mengakses kamera. Pastikan izin kamera sudah diberikan.'
  }
}

const stopScanner = () => {
  if (codeReader.value) {
    codeReader.value.reset()
    codeReader.value = null
  }
  if (beepSound.value) {
    beepSound.value = null
  }
  isScanning.value = false
  isProcessing.value = false
  lastScannedCode.value = ''
  errorMessage.value = ''
}

const closeScanner = () => {
  stopScanner()
  emit('close')
}

watch(() => props.show, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      startScanner()
    }, 100)
  } else {
    stopScanner()
  }
})

onUnmounted(() => {
  stopScanner()
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.9);
}
</style>
