import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useSound } from './useSound'

export function useCheckout(cart, subtotal, total, diskon, refreshBarangs) {
  const toast = useToast()
  const { playSFX } = useSound()

  const showCheckoutModal = ref(false)
  const showSuccessModal = ref(false)
  const successMessage = ref({})

  // Checkout form data
  const metodePembayaran = ref('tunai')
  const bayar = ref(0)
  const kembalian = ref(0)
  const namaPelanggan = ref('')
  const selectedBuyerId = ref(null)
  const redeemPoints = ref(0)
  const catatan = ref('')
  const appliedVoucher = ref(null) // { voucher_id, kode_voucher, nama_potongan, nilai_potongan }
  const tanggalTransaksi = ref(new Date().toISOString().split('T')[0])
  const today = new Date().toISOString().split('T')[0]

  const calculateChange = () => {
    kembalian.value = Math.max(0, bayar.value - total.value)
  }

  const resetCheckoutForm = () => {
    metodePembayaran.value = 'tunai'
    bayar.value = 0
    kembalian.value = 0
    namaPelanggan.value = ''
    selectedBuyerId.value = null
    redeemPoints.value = 0
    catatan.value = ''
    tanggalTransaksi.value = new Date().toISOString().split('T')[0]
    appliedVoucher.value = null
  }

  const proceedToCheckout = () => {
    showCheckoutModal.value = true
  }

  const processPayment = (selectedWorkUnitId, selectedWorkUnitIds) => {
    const paymentAmount = metodePembayaran.value === 'tunai' ? bayar.value : total.value

    // Deteksi multi-toko dari work_unit_id unik di cart
    const uniqueTokoIds = new Set(cart.value.map(i => i.work_unit_id).filter(Boolean))
    const isMultiToko = uniqueTokoIds.size > 1

    router.post('/pengelola/penjualan', {
      work_unit_id: selectedWorkUnitId,
      tanggal_transaksi: tanggalTransaksi.value,
      items: cart.value.map(item => ({
        barang_id: item.barang_id,
        varian_id: item.varian_id || null,
        qty: item.qty,
        harga_satuan: item.harga_satuan,
        diskon_per_item: item.diskon_per_item || 0,
        work_unit_id: item.work_unit_id || null,
      })),
      subtotal: subtotal.value,
      diskon: isMultiToko ? 0 : diskon.value,
      voucher_id: isMultiToko ? null : (appliedVoucher.value?.voucher_id || null),
      total: isMultiToko ? subtotal.value : total.value,
      bayar: paymentAmount,
      metode_pembayaran: metodePembayaran.value,
      buyer_id: isMultiToko ? null : (selectedBuyerId.value || null),
      nama_pelanggan: namaPelanggan.value || null,
      catatan: catatan.value || null,
      redeem_points: isMultiToko ? 0 : (redeemPoints.value || 0),
    }, {
      onSuccess: (page) => {
        showCheckoutModal.value = false
        playSFX('success')

        const transactionData = page.props.flash?.transaction

        if (transactionData) {
          successMessage.value = transactionData
          showSuccessModal.value = true
        } else {
          toast.success('Transaksi berhasil!')
        }

        cart.value = []
        diskon.value = 0
        appliedVoucher.value = null
        resetCheckoutForm()

        refreshBarangs(selectedWorkUnitIds || [selectedWorkUnitId], { silent: true })
      },
      onError: (errors) => {
        showCheckoutModal.value = false
        console.error('Payment error:', errors)
        const errMsg = errors?.error || errors?.message || 'Gagal memproses pembayaran'
        toast.error(errMsg)
      }
    })
  }

  const closeSuccessModal = () => {
    showSuccessModal.value = false
    successMessage.value = {}
    cart.value = []
    diskon.value = 0
  }

  // Watchers
  watch(total, (newTotal) => {
    if (metodePembayaran.value === 'tunai') {
      bayar.value = Math.ceil(newTotal / 1000) * 1000
      calculateChange()
    }
  })

  watch(metodePembayaran, (newMethod) => {
    if (newMethod === 'tunai') {
      bayar.value = Math.ceil(total.value / 1000) * 1000
      calculateChange()
    } else {
      bayar.value = total.value
      kembalian.value = 0
    }
  })

  watch(bayar, () => {
    if (metodePembayaran.value === 'tunai') {
      calculateChange()
    }
  })

  return {
    showCheckoutModal,
    showSuccessModal,
    successMessage,
    metodePembayaran,
    bayar,
    kembalian,
    namaPelanggan,
    selectedBuyerId,
    redeemPoints,
    catatan,
    tanggalTransaksi,
    today,
    appliedVoucher,
    calculateChange,
    resetCheckoutForm,
    proceedToCheckout,
    processPayment,
    closeSuccessModal
  }
}
