export function useFormatters() {
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value || 0)
  }

  const formatDateTime = (dateString) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(date)
  }

  const formatDateIndo = (dateString) => {
    if (!dateString) return '-'

    const date = new Date(dateString)
    return new Intl.DateTimeFormat('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(date)
  }

  const getPaymentMethodClass = (method) => {
    const classes = {
      tunai: 'bg-green-100 text-green-800',
      transfer: 'bg-blue-100 text-blue-800',
      qris: 'bg-purple-100 text-purple-800',
      debit: 'bg-yellow-100 text-yellow-800',
      kredit: 'bg-red-100 text-red-800',
    }
    return classes[method] || 'bg-gray-100 text-gray-800'
  }

  const getStatusClass = (penjualan) => {
    if (penjualan.is_recorded) return 'bg-purple-100 text-purple-800'
    if (penjualan.is_approved) return 'bg-blue-100 text-blue-800'
    if (penjualan.is_verified) return 'bg-green-100 text-green-800'
    return 'bg-yellow-100 text-yellow-800'
  }

  const getStatusText = (penjualan) => {
    if (penjualan.is_recorded) return 'Tercatat'
    if (penjualan.is_approved) return 'Approved'
    if (penjualan.is_verified) return 'Verified'
    return 'Entry'
  }

  const getItemsText = (items) => {
    if (!items || items.length === 0) return '-'

    return items.map(item => {
      const name = item.nama_barang || item.barang?.nama_barang || 'Item'
      const qty = item.qty
      return `${name} (${qty}x)`
    }).join(', ')
  }

  return {
    formatCurrency,
    formatDateTime,
    formatDateIndo,
    getPaymentMethodClass,
    getStatusClass,
    getStatusText,
    getItemsText,
  }
}
