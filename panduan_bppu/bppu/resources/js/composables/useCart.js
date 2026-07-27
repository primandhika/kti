import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useSound } from './useSound'

export function useCart() {
  const toast = useToast()
  const { playSFX } = useSound()

  const cart = ref([])
  const diskon = ref(0)

  const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => {
      const hargaEfektif = Math.max(0, item.harga_satuan - (item.diskon_per_item || 0))
      return sum + (item.qty * hargaEfektif)
    }, 0)
  })

  const total = computed(() => {
    return Math.max(0, subtotal.value - diskon.value)
  })

  const showCartToast = (namaBarang) => {
    toast.success(`${namaBarang} ditambahkan!`, {
      hideProgressBar: true,
      timeout: 2000
    })
  }

  const addToCart = (barang) => {
    const existingIndex = cart.value.findIndex(item => item.barang_id === barang.id && !item.varian_id)

    if (existingIndex >= 0) {
      if (cart.value[existingIndex].qty < barang.stok) {
        cart.value[existingIndex].qty++
        showCartToast(barang.nama_barang)
        playSFX('add')
      } else {
        toast.warning(`Stok tidak cukup!`)
      }
    } else {
      cart.value.push({
        barang_id: barang.id,
        nama_barang: barang.nama_barang,
        qty: 1,
        satuan: barang.satuan,
        harga_satuan: barang.harga_jual,
        diskon_per_item: barang.diskon_aktif ? (barang.nominal_diskon || 0) : 0,
        stok_tersedia: barang.stok,
        varian_id: null,
        work_unit_id: barang.work_unit_id || null,
        work_unit_name: barang.work_unit_name || null,
      })
      showCartToast(barang.nama_barang)
      playSFX('add')
    }
  }

  const addVarianToCart = (barang, varian) => {
    const existingIndex = cart.value.findIndex(item => item.barang_id === barang.id && item.varian_id === varian.id)

    if (existingIndex >= 0) {
      if (cart.value[existingIndex].qty < barang.stok) {
        cart.value[existingIndex].qty++
        showCartToast(varian.nama_varian)
        playSFX('add')
      } else {
        toast.warning(`Stok tidak cukup!`)
      }
    } else {
      cart.value.push({
        barang_id: barang.id,
        nama_barang: `${barang.nama_barang} - ${varian.nama_varian}`,
        qty: 1,
        satuan: barang.satuan,
        harga_satuan: varian.harga_jual,
        diskon_per_item: barang.diskon_aktif ? (barang.nominal_diskon || 0) : 0,
        stok_tersedia: barang.stok,
        varian_id: varian.id,
        work_unit_id: barang.work_unit_id || null,
        work_unit_name: barang.work_unit_name || null,
      })
      showCartToast(`${barang.nama_barang} - ${varian.nama_varian}`)
      playSFX('add')
    }
  }

  const handleIncrement = (barangId) => {
    const item = cart.value.find(item => item.barang_id === barangId && !item.varian_id)
    if (item && item.qty < item.stok_tersedia) {
      item.qty++
      playSFX('increment')
    }
  }

  const handleDecrement = (barangId) => {
    const item = cart.value.find(item => item.barang_id === barangId && !item.varian_id)
    if (!item) return

    if (item.qty === 1) {
      const index = cart.value.findIndex(i => i.barang_id === barangId && !i.varian_id)
      if (index >= 0) {
        cart.value.splice(index, 1)
        playSFX('remove')
      }
    } else {
      item.qty--
      playSFX('decrement')
    }
  }

  const handleIncrementVarian = (barangId, varianId) => {
    const item = cart.value.find(item => item.barang_id === barangId && item.varian_id === varianId)
    if (item && item.qty < item.stok_tersedia) {
      item.qty++
      playSFX('increment')
    }
  }

  const handleDecrementVarian = (barangId, varianId) => {
    const item = cart.value.find(item => item.barang_id === barangId && item.varian_id === varianId)
    if (!item) return

    if (item.qty === 1) {
      const index = cart.value.findIndex(i => i.barang_id === barangId && i.varian_id === varianId)
      if (index >= 0) {
        cart.value.splice(index, 1)
        playSFX('remove')
      }
    } else {
      item.qty--
      playSFX('decrement')
    }
  }

  const incrementQty = (index) => {
    if (cart.value[index].qty < cart.value[index].stok_tersedia) {
      cart.value[index].qty++
      playSFX('increment')
    }
  }

  const decrementQty = (index) => {
    if (cart.value[index].qty > 1) {
      cart.value[index].qty--
      playSFX('decrement')
    }
  }

  const removeFromCart = (index) => {
    cart.value.splice(index, 1)
    playSFX('remove')
  }

  const clearCart = () => {
    cart.value = []
    diskon.value = 0
    playSFX('clear')
  }

  const updateCartStocks = (newBarangs) => {
    cart.value.forEach(cartItem => {
      const updatedBarang = newBarangs.find(b => b.id === cartItem.barang_id)
      if (updatedBarang) {
        cartItem.stok_tersedia = updatedBarang.stok

        // Update diskon per item jika diskon barang berubah
        if (!cartItem.varian_id) {
          cartItem.diskon_per_item = updatedBarang.diskon_aktif ? (updatedBarang.nominal_diskon || 0) : 0
        }

        // Jika qty di cart lebih besar dari stok yang tersedia, adjust
        if (cartItem.qty > updatedBarang.stok) {
          cartItem.qty = updatedBarang.stok
          if (updatedBarang.stok === 0) {
            // Hapus dari keranjang jika stok habis
            const indexToRemove = cart.value.findIndex(item =>
              item.barang_id === cartItem.barang_id && item.varian_id === cartItem.varian_id
            )
            if (indexToRemove >= 0) {
              cart.value.splice(indexToRemove, 1)
            }
          }
        }
      }
    })
  }

  return {
    cart,
    diskon,
    subtotal,
    total,
    addToCart,
    addVarianToCart,
    handleIncrement,
    handleDecrement,
    handleIncrementVarian,
    handleDecrementVarian,
    incrementQty,
    decrementQty,
    removeFromCart,
    clearCart,
    updateCartStocks
  }
}
