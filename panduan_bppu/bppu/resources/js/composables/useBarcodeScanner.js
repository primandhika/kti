import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useToast } from 'vue-toastification'

export function useBarcodeScanner(searchInputRef, filteredBarangs, addToCart) {
  const toast = useToast()
  const barcodeMode = ref(false)
  // 'usb' = hardware scanner (focus ke input), 'camera' = kamera perangkat
  const scannerType = ref('usb')
  const showModeSelectModal = ref(false)
  const showSuggestionModal = ref(false)
  const suggestedBarangs = ref([])

  const refocusSearch = () => {
    if (!barcodeMode.value) return

    nextTick(() => {
      setTimeout(() => {
        if (searchInputRef.value) {
          searchInputRef.value.focus()
        }
      }, 100)
    })
  }

  const calculateSimilarity = (str1, str2) => {
    const len1 = str1.length
    const len2 = str2.length
    const matrix = []

    for (let i = 0; i <= len1; i++) {
      matrix[i] = [i]
    }
    for (let j = 0; j <= len2; j++) {
      matrix[0][j] = j
    }

    for (let i = 1; i <= len1; i++) {
      for (let j = 1; j <= len2; j++) {
        if (str1[i - 1] === str2[j - 1]) {
          matrix[i][j] = matrix[i - 1][j - 1]
        } else {
          matrix[i][j] = Math.min(
            matrix[i - 1][j - 1] + 1,
            matrix[i][j - 1] + 1,
            matrix[i - 1][j] + 1
          )
        }
      }
    }

    const distance = matrix[len1][len2]
    const maxLen = Math.max(len1, len2)
    return maxLen === 0 ? 1 : 1 - distance / maxLen
  }

  const handleBarcodeSearch = (searchValue) => {
    if (!searchValue || !searchValue.trim()) return

    const trimmedValue = searchValue.trim().toLowerCase()

    // Cari barang berdasarkan kode_barang (exact match)
    const barang = filteredBarangs.value.find(b =>
      b.kode_barang.toLowerCase() === trimmedValue
    )

    if (barang) {
      // Tambahkan ke keranjang
      addToCart(barang)

      // Auto-focus kembali ke search input
      refocusSearch()
    } else {
      // Cari barang yang mirip
      const suggestions = filteredBarangs.value
        .map(b => ({
          ...b,
          similarity: calculateSimilarity(trimmedValue, b.kode_barang.toLowerCase())
        }))
        .filter(b => b.similarity > 0.6)
        .sort((a, b) => b.similarity - a.similarity)
        .slice(0, 3)

      if (suggestions.length > 0) {
        suggestedBarangs.value = suggestions
        showSuggestionModal.value = true

        toast.warning(`Barcode "${searchValue}" tidak ditemukan. Mungkin maksud Anda salah satu produk berikut?`, {
          timeout: 5000
        })
      } else {
        toast.error(`Barcode "${searchValue}" tidak ditemukan di inventaris`, {
          timeout: 3000
        })
      }

      refocusSearch()
    }
  }

  const selectSuggestion = (barang) => {
    addToCart(barang)
    showSuggestionModal.value = false
    suggestedBarangs.value = []
    refocusSearch()
  }

  const closeSuggestionModal = () => {
    showSuggestionModal.value = false
    suggestedBarangs.value = []

    // Delay refocus untuk menghindari modal terbuka lagi
    setTimeout(() => {
      refocusSearch()
    }, 150)
  }

  // Global click handler untuk refocus saat barcode mode aktif
  const handleGlobalClick = (e) => {
    if (!barcodeMode.value) return

    // Jangan refocus jika klik di modal atau area tertentu
    const isModalArea = e.target.closest('[data-checkout-area]') ||
                        e.target.closest('[role="dialog"]') ||
                        e.target.closest('.modal') ||
                        e.target.closest('.fixed.inset-0') // modal backdrop

    if (!isModalArea && !showSuggestionModal.value) {
      refocusSearch()
    }
  }

  // Fullscreen handler untuk mobile/tablet
  const toggleFullscreen = async (enable) => {
    try {
      if (enable) {
        // Request fullscreen untuk mobile/tablet
        if (document.documentElement.requestFullscreen) {
          await document.documentElement.requestFullscreen()
        } else if (document.documentElement.webkitRequestFullscreen) {
          // Safari
          await document.documentElement.webkitRequestFullscreen()
        } else if (document.documentElement.mozRequestFullScreen) {
          // Firefox
          await document.documentElement.mozRequestFullScreen()
        } else if (document.documentElement.msRequestFullscreen) {
          // IE/Edge
          await document.documentElement.msRequestFullscreen()
        }
      } else {
        // Exit fullscreen
        if (document.exitFullscreen) {
          await document.exitFullscreen()
        } else if (document.webkitExitFullscreen) {
          // Safari
          await document.webkitExitFullscreen()
        } else if (document.mozCancelFullScreen) {
          // Firefox
          await document.mozCancelFullScreen()
        } else if (document.msExitFullscreen) {
          // IE/Edge
          await document.msExitFullscreen()
        }
      }
    } catch (error) {
      console.log('Fullscreen error:', error)
    }
  }

  const enableBarcodeMode = (type) => {
    scannerType.value = type
    showModeSelectModal.value = false
    barcodeMode.value = true
  }

  const disableBarcodeMode = () => {
    barcodeMode.value = false
    scannerType.value = 'usb'
  }

  const requestBarcodeMode = () => {
    // Jika sudah aktif, matikan
    if (barcodeMode.value) {
      disableBarcodeMode()
      return
    }
    // Tampilkan modal pilih tipe scanner
    showModeSelectModal.value = true
  }

  const cancelModeSelect = () => {
    showModeSelectModal.value = false
  }

  // Auto-focus saat barcode mode aktif (hanya untuk USB mode)
  watch(barcodeMode, (newVal) => {
    if (newVal) {
      if (scannerType.value === 'usb') {
        refocusSearch()
        // Add global click listener
        document.addEventListener('click', handleGlobalClick)
      }
      // Enable fullscreen untuk mobile/tablet
      toggleFullscreen(true)
    } else {
      // Remove listener when disabled
      document.removeEventListener('click', handleGlobalClick)
      // Exit fullscreen
      toggleFullscreen(false)
    }
  })

  // Cleanup on unmount
  onUnmounted(() => {
    document.removeEventListener('click', handleGlobalClick)
  })

  return {
    barcodeMode,
    scannerType,
    showModeSelectModal,
    requestBarcodeMode,
    enableBarcodeMode,
    disableBarcodeMode,
    cancelModeSelect,
    handleBarcodeSearch,
    refocusSearch,
    showSuggestionModal,
    suggestedBarangs,
    selectSuggestion,
    closeSuggestionModal
  }
}
