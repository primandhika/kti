import { onMounted, onUnmounted } from 'vue'
import { useToast } from 'vue-toastification'

export function useKeyboardShortcuts({
  barcodeMode,
  cart,
  proceedToCheckout,
  clearCart,
  removeLastItem,
  incrementLastItem,
  decrementLastItem,
  toggleBarcodeMode,
  printLastReceipt
}) {
  const toast = useToast()

  const handleKeyPress = (event) => {
    // Ignore shortcuts when typing in input/textarea
    const activeElement = document.activeElement
    const isTyping = activeElement.tagName === 'INPUT' ||
                     activeElement.tagName === 'TEXTAREA' ||
                     activeElement.isContentEditable

    // Allow shortcuts in barcode mode even when typing (in search input)
    const isBarcodeSearch = barcodeMode.value && activeElement.type === 'text'

    // Special keys that work even when typing
    const specialKeys = ['F1', 'F2', 'F3', 'F4', 'F5', 'Escape']

    if (isTyping && !isBarcodeSearch && !specialKeys.includes(event.key)) {
      return
    }

    // Prevent default for function keys
    if (event.key.startsWith('F')) {
      event.preventDefault()
    }

    const key = event.key
    const ctrl = event.ctrlKey
    const alt = event.altKey

    // F1: Quick Checkout
    if (key === 'F1') {
      event.preventDefault()
      if (cart.value.length > 0) {
        proceedToCheckout()
        toast.info('Checkout (F1)', { timeout: 1000 })
      } else {
        toast.warning('Keranjang kosong!')
      }
      return
    }

    // F2: Clear Cart
    if (key === 'F2') {
      event.preventDefault()
      if (cart.value.length > 0) {
        if (confirm('Kosongkan keranjang?')) {
          clearCart()
          toast.success('Keranjang dikosongkan (F2)', { timeout: 1000 })
        }
      }
      return
    }

    // F3: Toggle Barcode Mode
    if (key === 'F3') {
      event.preventDefault()
      toggleBarcodeMode()
      const status = !barcodeMode.value ? 'ON' : 'OFF'
      toast.info(`Barcode Mode ${status} (F3)`, { timeout: 1000 })
      return
    }

    // F4: Print Last Receipt
    if (key === 'F4') {
      event.preventDefault()
      if (printLastReceipt) {
        printLastReceipt()
        toast.info('Print struk terakhir (F4)', { timeout: 1000 })
      }
      return
    }

    // F5: Refresh (allow browser default but show toast)
    if (key === 'F5') {
      toast.info('Refresh halaman (F5)', { timeout: 1000 })
      return
    }

    // ESC: Cancel/Back
    if (key === 'Escape') {
      // If barcode mode is on, turn it off
      if (barcodeMode.value) {
        toggleBarcodeMode()
        toast.info('Barcode Mode OFF (ESC)', { timeout: 1000 })
        return
      }

      // Otherwise let it bubble (close modals, etc)
      return
    }

    // Don't process other shortcuts when typing (except in barcode mode)
    if (isTyping && !isBarcodeSearch) {
      return
    }

    // Delete: Remove last item
    if (key === 'Delete' && cart.value.length > 0) {
      event.preventDefault()
      const lastItem = cart.value[cart.value.length - 1]
      removeLastItem()
      toast.success(`${lastItem.nama_barang} dihapus (Del)`, { timeout: 1000 })
      return
    }

    // + or =: Increment last item quantity
    if ((key === '+' || key === '=') && cart.value.length > 0) {
      event.preventDefault()
      incrementLastItem()
      toast.success('Qty +1 ('+' key)', { timeout: 1000 })
      return
    }

    // - or _: Decrement last item quantity
    if ((key === '-' || key === '_') && cart.value.length > 0) {
      event.preventDefault()
      decrementLastItem()
      toast.success('Qty -1 ('-' key)', { timeout: 1000 })
      return
    }

    // Ctrl+D: Duplicate last item
    if (ctrl && key === 'd' && cart.value.length > 0) {
      event.preventDefault()
      incrementLastItem()
      toast.success('Item duplikat (Ctrl+D)', { timeout: 1000 })
      return
    }

    // Ctrl+Z: Remove last item (undo)
    if (ctrl && key === 'z' && cart.value.length > 0) {
      event.preventDefault()
      const lastItem = cart.value[cart.value.length - 1]
      removeLastItem()
      toast.info(`Undo: ${lastItem.nama_barang} (Ctrl+Z)`, { timeout: 1000 })
      return
    }

    // Alt+C: Clear Cart (alternative to F2)
    if (alt && key === 'c' && cart.value.length > 0) {
      event.preventDefault()
      if (confirm('Kosongkan keranjang?')) {
        clearCart()
        toast.success('Keranjang dikosongkan (Alt+C)', { timeout: 1000 })
      }
      return
    }

    // Alt+B: Toggle Barcode Mode (alternative to F3)
    if (alt && key === 'b') {
      event.preventDefault()
      toggleBarcodeMode()
      const status = !barcodeMode.value ? 'ON' : 'OFF'
      toast.info(`Barcode Mode ${status} (Alt+B)`, { timeout: 1000 })
      return
    }

    // Alt+P: Proceed to checkout (alternative to F1)
    if (alt && key === 'p' && cart.value.length > 0) {
      event.preventDefault()
      proceedToCheckout()
      toast.info('Checkout (Alt+P)', { timeout: 1000 })
      return
    }
  }

  onMounted(() => {
    document.addEventListener('keydown', handleKeyPress)
  })

  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyPress)
  })

  return {
    // Return shortcuts info for help dialog
    shortcuts: [
      { key: 'F1', description: 'Quick Checkout', category: 'Transaksi' },
      { key: 'F2', description: 'Kosongkan Keranjang', category: 'Transaksi' },
      { key: 'F3', description: 'Toggle Barcode Mode', category: 'Mode' },
      { key: 'F4', description: 'Print Struk Terakhir', category: 'Print' },
      { key: 'ESC', description: 'Cancel/Keluar', category: 'Navigasi' },
      { key: 'Del', description: 'Hapus Item Terakhir', category: 'Cart' },
      { key: '+', description: 'Tambah Qty Item Terakhir', category: 'Cart' },
      { key: '-', description: 'Kurangi Qty Item Terakhir', category: 'Cart' },
      { key: 'Ctrl+Z', description: 'Undo (Hapus Item Terakhir)', category: 'Cart' },
      { key: 'Ctrl+D', description: 'Duplikat Item Terakhir', category: 'Cart' },
      { key: 'Alt+C', description: 'Clear Cart', category: 'Transaksi' },
      { key: 'Alt+B', description: 'Toggle Barcode Mode', category: 'Mode' },
      { key: 'Alt+P', description: 'Proceed Checkout', category: 'Transaksi' },
    ]
  }
}
