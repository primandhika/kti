import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

export function useBarang(initialBarangs, updateCartStocks, options = {}) {
  const toast = useToast()
  const page = usePage()

  const barangs = ref(initialBarangs)
  const searchQuery = ref('')
  const selectedCategory = ref(null)
  const filterDiskon = ref(false)
  const filterStokAda = ref(false)
  const isRefreshing = ref(false)
  const showRefreshSummary = ref(false)
  const refreshSummary = ref({
    newItems: [],
    stockUpdated: [],
    outOfStock: [],
    timestamp: '',
  })

  // Image upload
  const showImageModal = ref(false)
  const selectedBarang = ref(null)
  const selectedFile = ref(null)
  const imagePreview = ref(null)
  const isUploading = ref(false)

  const countDiskon = computed(() => barangs.value.filter(b => b.diskon_aktif).length)

  const categories = computed(() => {
    const cats = new Set()
    barangs.value.forEach(b => {
      if (b.kategori) cats.add(b.kategori)
    })
    return Array.from(cats)
  })

  const categoryCounts = computed(() => {
    const counts = {}
    barangs.value.forEach(b => {
      if (b.kategori) counts[b.kategori] = (counts[b.kategori] || 0) + 1
    })
    return counts
  })

  const filteredBarangs = computed(() => {
    let filtered = barangs.value

    if (filterStokAda.value) {
      filtered = filtered.filter(b => {
        if (b.varians && b.varians.length > 0) {
          return b.varians.some(v => v.stok > 0)
        }
        return b.stok > 0
      })
    }

    if (filterDiskon.value) {
      filtered = filtered.filter(b => b.diskon_aktif)
    }

    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(b => {
        const matchBarang = b.nama_barang.toLowerCase().includes(query) ||
                           b.kode_barang.toLowerCase().includes(query) ||
                           (b.deskripsi && b.deskripsi.toLowerCase().includes(query))
        const matchVarian = b.varians && b.varians.some(v =>
          v.nama_varian.toLowerCase().includes(query) ||
          (v.deskripsi && v.deskripsi.toLowerCase().includes(query))
        )
        return matchBarang || matchVarian
      })
    }

    if (selectedCategory.value) {
      filtered = filtered.filter(b => b.kategori === selectedCategory.value)
    }

    return filtered
  })

  const refreshBarangs = async (selectedWorkUnitIdOrIds, options = {}) => {
    const { silent = false } = options // Add silent option to suppress modal

    if (isRefreshing.value) return

    isRefreshing.value = true

    try {
      // Support single ID atau array IDs
      const ids = Array.isArray(selectedWorkUnitIdOrIds) ? selectedWorkUnitIdOrIds : [selectedWorkUnitIdOrIds]
      const params = ids.map(id => `work_unit_ids[]=${id}`).join('&')
      const response = await fetch(`/pengelola/penjualan/refresh-barangs?${params}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        }
      })

      if (!response.ok) {
        throw new Error('Gagal memperbarui data')
      }

      const data = await response.json()

      // Prepare summary data
      const oldBarangs = barangs.value
      const newBarangs = data.barangs

      // Track changes
      const newItems = []
      const stockUpdated = []
      const outOfStock = []

      // Create map of old barangs for quick lookup
      const oldBarangsMap = new Map(oldBarangs.map(b => [b.id, b]))

      // Check each new barang
      newBarangs.forEach(newBarang => {
        const oldBarang = oldBarangsMap.get(newBarang.id)

        if (!oldBarang) {
          // Barang baru
          newItems.push(newBarang)
        } else if (oldBarang.stok !== newBarang.stok) {
          // Stok berubah
          if (newBarang.stok === 0) {
            outOfStock.push({
              ...newBarang,
              oldStok: oldBarang.stok
            })
          } else {
            stockUpdated.push({
              ...newBarang,
              oldStok: oldBarang.stok
            })
          }
        }
      })

      // Check for items that became out of stock (no longer in new list)
      oldBarangs.forEach(oldBarang => {
        const stillExists = newBarangs.find(b => b.id === oldBarang.id)
        if (!stillExists && oldBarang.stok > 0) {
          outOfStock.push({
            ...oldBarang,
            oldStok: oldBarang.stok,
            stok: 0
          })
        }
      })

      // Update barangs dengan data baru
      barangs.value = newBarangs

      // Update stok di keranjang
      updateCartStocks(newBarangs)

      // Set summary
      refreshSummary.value = {
        newItems: newItems.slice(0, 10), // Limit to 10 items
        stockUpdated: stockUpdated.slice(0, 10),
        outOfStock: outOfStock.slice(0, 10),
        timestamp: new Date(data.timestamp).toLocaleString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        })
      }

      // Show summary modal only if not silent mode
      if (!silent) {
        showRefreshSummary.value = true
      }
    } catch (error) {
      console.error('Error refreshing barangs:', error)
      if (!silent) {
        toast.error('Gagal memperbarui data barang')
      }
    } finally {
      isRefreshing.value = false
    }
  }

  const openImageModal = (barang) => {
    selectedBarang.value = barang
    showImageModal.value = true
    selectedFile.value = null
    imagePreview.value = null
  }

  const closeImageModal = () => {
    showImageModal.value = false
    selectedBarang.value = null
    selectedFile.value = null
    imagePreview.value = null
  }

  const handleFileChange = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    if (!file.type.startsWith('image/')) {
      toast.error('File harus berupa gambar')
      event.target.value = ''
      return
    }

    // Check original size
    const originalSizeKB = file.size / 1024
    if (originalSizeKB > 5 * 1024) { // 5MB
      toast.error('Ukuran file maksimal 5MB')
      event.target.value = ''
      return
    }

    // Skip compression if file is already small (<150KB)
    if (originalSizeKB < 150) {
      selectedFile.value = file

      // Show preview
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreview.value = e.target.result
      }
      reader.readAsDataURL(file)

      toast.success(`Gambar siap diupload (${originalSizeKB.toFixed(0)}KB)`)
      return
    }

    try {
      // Import compression utility
      const { compressImage } = await import('@/utils/imageCompressor.js')

      // Show loading state
      toast.info('Mengompres gambar...', { timeout: 2000 })

      // Compress image (target 100KB, max 800px)
      const compressedFile = await compressImage(file, {
        maxWidth: 800,
        maxHeight: 800,
        quality: 0.85,
        targetSizeKB: 100,
        type: 'image/jpeg'
      })

      selectedFile.value = compressedFile

      // Show preview
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreview.value = e.target.result
      }
      reader.readAsDataURL(compressedFile)

      // Show success message with size info - only if actually reduced
      const compressedSizeKB = compressedFile.size / 1024
      if (compressedSizeKB < originalSizeKB) {
        const reduction = ((originalSizeKB - compressedSizeKB) / originalSizeKB * 100).toFixed(0)
        toast.success(`Gambar dikompres ${reduction}%: ${originalSizeKB.toFixed(0)}KB → ${compressedSizeKB.toFixed(0)}KB`)
      } else {
        toast.success(`Gambar siap diupload (${originalSizeKB.toFixed(0)}KB)`)
      }

    } catch (error) {
      console.error('Compression error:', error)
      toast.error('Gagal mengompres gambar, mencoba tanpa kompresi...')

      // Fallback: use original file
      selectedFile.value = file
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreview.value = e.target.result
      }
      reader.readAsDataURL(file)
    }
  }

  const uploadImage = (selectedWorkUnitId) => {
    if (!selectedFile.value || !selectedBarang.value) return

    isUploading.value = true

    const formData = new FormData()
    formData.append('image', selectedFile.value)
    formData.append('barang_id', selectedBarang.value.id)

    const uploadUrl = options.uploadImageRoute
      ? options.uploadImageRoute
      : `/pengelola/opname-stock/${selectedWorkUnitId}/barang/upload-image`

    router.post(uploadUrl, formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Foto berhasil diupload')

        // Update barang di list dengan data terbaru dari server
        const updatedBarang = page.props.flash?.updatedBarang || page.props.updatedBarang
        if (updatedBarang) {
          const index = barangs.value.findIndex(b => b.id === updatedBarang.id)
          if (index !== -1) {
            barangs.value[index] = { ...barangs.value[index], ...updatedBarang }
          }
        }

        closeImageModal()

        // Refresh untuk pastikan semua data terbaru
        if (selectedWorkUnitId) {
          setTimeout(() => {
            refreshBarangs(selectedWorkUnitId, { silent: true })
          }, 300)
        }
      },
      onError: (errors) => {
        console.error('Upload error:', errors)

        let errorMessage = 'Gagal mengupload foto'
        if (errors.image) {
          errorMessage = Array.isArray(errors.image) ? errors.image[0] : errors.image
        } else if (errors.error) {
          errorMessage = errors.error
        } else if (typeof errors === 'object') {
          errorMessage = Object.values(errors).flat().join(', ')
        }

        toast.error(errorMessage, { timeout: 5000 })
        isUploading.value = false
      },
      onFinish: () => {
        isUploading.value = false
      }
    })
  }

  const deleteImage = (selectedWorkUnitId) => {
    if (!selectedBarang.value) return

    if (!confirm('Yakin ingin menghapus foto produk ini?')) return

    isUploading.value = true

    const deleteUrl = options.deleteImageRoute
      ? options.deleteImageRoute
      : `/pengelola/opname-stock/${selectedWorkUnitId}/barang/delete-image`

    router.post(deleteUrl, {
      barang_id: selectedBarang.value.id
    }, {
      onSuccess: () => {
        toast.success('Foto berhasil dihapus')
        closeImageModal()
        if (selectedWorkUnitId) {
          refreshBarangs(selectedWorkUnitId, { silent: true })
        }
      },
      onError: (errors) => {
        console.error('Delete error:', errors)

        let errorMessage = 'Gagal menghapus foto'
        if (errors.error) {
          errorMessage = errors.error
        } else if (typeof errors === 'object') {
          errorMessage = Object.values(errors).flat().join(', ')
        }

        toast.error(errorMessage, { timeout: 5000 })
        isUploading.value = false
      },
      onFinish: () => {
        isUploading.value = false
      }
    })
  }

  return {
    barangs,
    searchQuery,
    selectedCategory,
    filterDiskon,
    filterStokAda,
    categories,
    categoryCounts,
    countDiskon,
    filteredBarangs,
    isRefreshing,
    showRefreshSummary,
    refreshSummary,
    showImageModal,
    selectedBarang,
    selectedFile,
    imagePreview,
    isUploading,
    refreshBarangs,
    openImageModal,
    closeImageModal,
    handleFileChange,
    uploadImage,
    deleteImage
  }
}
