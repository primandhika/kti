import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function useRekapActions(toast) {
  const selectedTransactions = ref([])
  const selectedForApproval = ref([])
  const verifyingId = ref(null)

  // Selection functions for Verification
  const toggleSelection = (id) => {
    const index = selectedTransactions.value.indexOf(id)
    if (index >= 0) {
      selectedTransactions.value.splice(index, 1)
    } else {
      selectedTransactions.value.push(id)
    }
  }

  const clearSelection = () => {
    selectedTransactions.value = []
  }

  // Selection functions for Approval
  const toggleApprovalSelection = (id) => {
    const index = selectedForApproval.value.indexOf(id)
    if (index >= 0) {
      selectedForApproval.value.splice(index, 1)
    } else {
      selectedForApproval.value.push(id)
    }
  }

  const clearApprovalSelection = () => {
    selectedForApproval.value = []
  }

  // Verification functions
  const verifySingle = (id, data, onSuccess) => {
    router.post(`/pengelola/penjualan/${id}/verify`, {
      verify_date: data.date,
      metode_pembayaran: data.payment_method
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        onSuccess && onSuccess()
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil diverifikasi')
      },
      onError: (errors) => {
        onSuccess && onSuccess()
        toast.error(errors.error || 'Gagal verifikasi transaksi')
      }
    })
  }

  const verifySelected = () => {
    if (selectedTransactions.value.length === 0) return

    router.post('/pengelola/penjualan/verify-bulk', {
      transaction_ids: selectedTransactions.value
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        clearSelection()
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil diverifikasi')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal verifikasi transaksi')
      }
    })
  }

  const verifyAll = (filters, onClose) => {
    router.post('/pengelola/penjualan/verify-all', {
      work_unit_id: filters.value.work_unit_id,
      start_date: filters.value.start_date,
      end_date: filters.value.end_date,
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        onClose && onClose()
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Semua transaksi berhasil diverifikasi')
      },
      onError: (errors) => {
        onClose && onClose()
        toast.error(errors.error || 'Gagal verifikasi transaksi')
      }
    })
  }

  const unverifyTransaction = (id) => {
    if (!confirm('Yakin ingin unverify transaksi ini? Transaksi akan dikembalikan ke status entry untuk koreksi.')) {
      return
    }

    router.post(`/pengelola/penjualan/${id}/unverify`, {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil di-unverify')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal unverify transaksi')
      }
    })
  }

  // Approval functions
  const approveSingle = (id) => {
    router.post(`/pengelola/penjualan/${id}/approve`, {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil diapprove')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal approve transaksi')
      }
    })
  }

  const approveSelected = () => {
    if (selectedForApproval.value.length === 0) return

    router.post('/pengelola/penjualan/approve-bulk', {
      transaction_ids: selectedForApproval.value
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        clearApprovalSelection()
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil diapprove')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal approve transaksi')
      }
    })
  }

  const unapproveTransaction = (id) => {
    if (!confirm('Yakin ingin unapprove transaksi ini? Poin member (jika ada) akan di-reverse dan transaksi dikembalikan ke status verified untuk koreksi.')) {
      return
    }

    router.post(`/pengelola/penjualan/${id}/unapprove`, {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil di-unapprove dan poin telah di-reverse')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal unapprove transaksi')
      }
    })
  }

  // Record function
  const recordSingle = (id, data, onClose) => {
    router.post(`/pengelola/penjualan/${id}/record`, data, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        onClose && onClose()
        router.reload({ only: ['penjualans', 'summary'] })
        toast.success('Transaksi berhasil dicatat ke buku kas')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal mencatat transaksi')
      }
    })
  }

  // Cancel function
  const cancelTransaction = (penjualanId, onClose) => {
    router.post(`/pengelola/penjualan/${penjualanId}/cancel`, {}, {
      preserveState: false,
      preserveScroll: false,
      onBefore: () => {
        // Close modal immediately
        if (onClose) onClose()
      },
      onSuccess: (page) => {
        // Check for flash messages
        const flash = page.props?.flash
        if (flash?.success) {
          toast.success(flash.success)
        } else if (flash?.error) {
          toast.error(flash.error)
        } else {
          toast.success('Transaksi berhasil dibatalkan')
        }

        // Force full reload to refresh data
        setTimeout(() => {
          router.reload({ only: ['penjualans', 'summary'] })
        }, 100)
      },
      onError: (errors) => {
        // Extract error message
        let errorMessage = 'Gagal membatalkan transaksi'

        if (typeof errors === 'string') {
          errorMessage = errors
        } else if (errors?.error) {
          errorMessage = errors.error
        } else if (errors?.message) {
          errorMessage = errors.message
        }

        toast.error(errorMessage)
        console.error('Cancel transaction error:', errors)
      },
      onFinish: () => {
        // Ensure modal is closed even if there's an error
        if (onClose) onClose()
      }
    })
  }

  // Assign Member function
  const assignMember = (penjualanId, buyerId, onClose) => {
    router.post(`/pengelola/penjualan/${penjualanId}/assign-member`, {
      buyer_id: buyerId
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        onClose && onClose()
        router.reload({ only: ['penjualans'] })
        toast.success('Member berhasil di-assign ke transaksi')
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal assign member')
      }
    })
  }

  return {
    selectedTransactions,
    selectedForApproval,
    verifyingId,
    toggleSelection,
    clearSelection,
    toggleApprovalSelection,
    clearApprovalSelection,
    verifySingle,
    verifySelected,
    verifyAll,
    unverifyTransaction,
    approveSingle,
    approveSelected,
    unapproveTransaction,
    recordSingle,
    cancelTransaction,
    assignMember,
  }
}
