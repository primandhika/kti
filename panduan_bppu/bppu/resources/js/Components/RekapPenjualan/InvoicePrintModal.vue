<template>
  <div
    v-if="show && penjualan"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-4 py-3 border-b bg-gray-50 rounded-t-lg flex-shrink-0">
        <h3 class="font-semibold text-gray-800 text-sm">Pratinjau Invoice</h3>
        <div class="flex items-center gap-2">
          <!-- Toggle hitam putih -->
          <button
            @click="bwMode = !bwMode"
            class="px-3 py-1.5 text-xs font-semibold rounded flex items-center gap-1.5 border transition-colors"
            :class="bwMode ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13l-.87.5M4.21 17.5l-.87.5M20.66 17.5l-.87-.5M4.21 6.5l-.87-.5M21 12h-1M4 12H3" />
              <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2" />
            </svg>
            {{ bwMode ? 'Berwarna' : 'Hitam Putih' }}
          </button>
          <button
            @click="doPrint"
            class="px-3 py-1.5 text-xs font-semibold text-white rounded flex items-center gap-1.5 transition-colors"
            style="background-color: #996600;"
            @mouseenter="$event.currentTarget.style.backgroundColor = '#7a5100'"
            @mouseleave="$event.currentTarget.style.backgroundColor = '#996600'"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak
          </button>
          <button @click="$emit('close')" class="p-1.5 hover:bg-gray-200 rounded transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Invoice Preview (scrollable) -->
      <div class="flex-1 overflow-y-auto p-4 bg-gray-100">
        <div
          id="invoice-print-area"
          class="bg-white shadow-sm mx-auto"
          style="width: 100%; max-width: 520px; font-family: Arial, sans-serif; font-size: 12px;"
          :style="bwMode ? 'color: #000;' : 'color: #1a1a1a;'"
        >
          <!-- Invoice Header -->
          <div :style="bwMode
            ? 'padding: 20px 24px 16px; border-bottom: 2px solid #000; display: flex; align-items: center; gap: 14px;'
            : 'padding: 20px 24px 16px; border-bottom: 2px solid #996600; display: flex; align-items: center; gap: 14px;'"
          >
            <img
              src="/storage/logo-round_ijokuning.png"
              alt="BPPU Logo"
              :style="bwMode
                ? 'width: 56px; height: 56px; object-fit: contain; flex-shrink: 0; filter: grayscale(100%);'
                : 'width: 56px; height: 56px; object-fit: contain; flex-shrink: 0;'"
            />
            <div style="flex: 1;">
              <div :style="bwMode
                ? 'font-size: 15px; font-weight: 700; color: #000; line-height: 1.2;'
                : 'font-size: 15px; font-weight: 700; color: #996600; line-height: 1.2;'"
              >BPPU IKIP Siliwangi</div>
              <div :style="bwMode ? 'font-size: 10px; color: #333; margin-top: 2px;' : 'font-size: 10px; color: #6b4700; margin-top: 2px;'">Badan Pengelola dan Pengembangan Usaha</div>
              <div :style="bwMode ? 'font-size: 10px; color: #333;' : 'font-size: 10px; color: #6b4700;'">Jl. Terusan Jenderal Sudirman, Cimahi, Jawa Barat 40526</div>
              <div :style="bwMode ? 'font-size: 10px; color: #333;' : 'font-size: 10px; color: #6b4700;'">bppu@ikipsiliwangi.ac.id</div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
              <div :style="bwMode
                ? 'font-size: 16px; font-weight: 700; color: #000; letter-spacing: 0.03em;'
                : 'font-size: 16px; font-weight: 700; color: #996600; letter-spacing: 0.03em;'"
              >INVOICE</div>
              <div style="font-size: 10px; color: #555; margin-top: 3px; word-break: break-all;">{{ penjualan.nomor_transaksi }}</div>
            </div>
          </div>

          <!-- Invoice Meta -->
          <div :style="bwMode
            ? 'padding: 14px 24px; display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px solid #000;'
            : 'padding: 14px 24px; display: flex; justify-content: space-between; gap: 16px; background: #faf6f0; border-bottom: 1px solid #e0d1b2;'"
          >
            <div style="flex: 1;">
              <div style="font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Kepada</div>
              <div style="font-weight: 600; font-size: 12px;">
                {{ penjualan.buyer ? penjualan.buyer.name : (penjualan.nama_pelanggan || 'Umum') }}
              </div>
              <div v-if="penjualan.buyer" :style="bwMode ? 'font-size: 10px; color: #333;' : 'font-size: 10px; color: #6b4700;'">Member</div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
              <table style="font-size: 10px; border-collapse: collapse; margin-left: auto;">
                <tr>
                  <td style="padding: 1px 8px 1px 0; color: #888; white-space: nowrap;">Tanggal</td>
                  <td style="padding: 1px 0; font-weight: 600; white-space: nowrap;">{{ formatDate(penjualan.tanggal_transaksi) }}</td>
                </tr>
                <tr>
                  <td style="padding: 1px 8px 1px 0; color: #888; white-space: nowrap;">Unit</td>
                  <td style="padding: 1px 0; font-weight: 600; white-space: nowrap;">{{ penjualan.work_unit?.name || '-' }}</td>
                </tr>
              </table>
            </div>
          </div>

          <!-- Items Table -->
          <div style="padding: 16px 24px 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
              <thead>
                <tr :style="bwMode
                  ? 'background-color: #000; color: #fff;'
                  : 'background-color: #996600; color: #fff;'"
                >
                  <th style="padding: 6px 8px; text-align: left; font-weight: 600; width: 20px;">No</th>
                  <th style="padding: 6px 8px; text-align: left; font-weight: 600;">Nama Barang</th>
                  <th style="padding: 6px 8px; text-align: center; font-weight: 600; white-space: nowrap;">Qty</th>
                  <th style="padding: 6px 8px; text-align: right; font-weight: 600; white-space: nowrap;">Harga</th>
                  <th style="padding: 6px 8px; text-align: right; font-weight: 600; white-space: nowrap;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in penjualan.items"
                  :key="index"
                  :style="bwMode
                    ? (index % 2 === 0 ? 'background: #fff;' : 'background: #f5f5f5;')
                    : (index % 2 === 0 ? 'background: #fff;' : 'background: #faf6f0;')"
                >
                  <td style="padding: 5px 8px; color: #888; text-align: center;">{{ index + 1 }}</td>
                  <td style="padding: 5px 8px;">
                    <div style="font-weight: 500;">{{ item.nama_barang }}</div>
                    <div v-if="item.diskon_per_item > 0" :style="bwMode ? 'font-size: 10px; color: #333;' : 'font-size: 10px; color: #dc2626;'">
                      Diskon: -Rp {{ formatNum(item.diskon_per_item) }}
                    </div>
                  </td>
                  <td style="padding: 5px 8px; text-align: center; color: #555; white-space: nowrap;">{{ item.qty }} {{ item.satuan }}</td>
                  <td style="padding: 5px 8px; text-align: right; color: #555; white-space: nowrap;">Rp {{ formatNum(item.harga_satuan) }}</td>
                  <td style="padding: 5px 8px; text-align: right; font-weight: 600; white-space: nowrap;">Rp {{ formatNum(item.subtotal) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Summary -->
          <div style="padding: 0 24px 16px; display: flex; justify-content: flex-end;">
            <table style="font-size: 11px; border-collapse: collapse; min-width: 220px;">
              <tr>
                <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Subtotal</td>
                <td style="padding: 3px 0; text-align: right; white-space: nowrap;">Rp {{ formatNum(penjualan.subtotal) }}</td>
              </tr>
              <tr v-if="penjualan.diskon > 0">
                <td :style="bwMode ? 'padding: 3px 12px 3px 0; color: #000; white-space: nowrap;' : 'padding: 3px 12px 3px 0; color: #dc2626; white-space: nowrap;'">Diskon</td>
                <td :style="bwMode ? 'padding: 3px 0; text-align: right; white-space: nowrap;' : 'padding: 3px 0; text-align: right; color: #dc2626; white-space: nowrap;'">-Rp {{ formatNum(penjualan.diskon) }}</td>
              </tr>
              <tr>
                <td colspan="2" style="padding: 3px 0;">
                  <div :style="bwMode ? 'border-top: 1px solid #000; margin: 2px 0;' : 'border-top: 1px solid #e0d1b2; margin: 2px 0;'"></div>
                </td>
              </tr>
              <tr :style="bwMode ? 'background: #f0f0f0;' : 'background: #faf6f0;'">
                <td :style="bwMode
                  ? 'padding: 6px 12px 6px 8px; font-weight: 700; font-size: 13px; white-space: nowrap;'
                  : 'padding: 6px 12px 6px 8px; font-weight: 700; font-size: 13px; color: #996600; white-space: nowrap;'"
                >TOTAL</td>
                <td :style="bwMode
                  ? 'padding: 6px 8px 6px 0; text-align: right; font-weight: 700; font-size: 13px; white-space: nowrap;'
                  : 'padding: 6px 8px 6px 0; text-align: right; font-weight: 700; font-size: 13px; color: #996600; white-space: nowrap;'"
                >Rp {{ formatNum(penjualan.total) }}</td>
              </tr>
              <tr>
                <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Metode Bayar</td>
                <td style="padding: 3px 0; text-align: right; white-space: nowrap;">
                  <span :style="bwMode
                    ? 'border: 1px solid #000; padding: 1px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; text-transform: capitalize;'
                    : 'background: #eae0cc; color: #6b4700; padding: 1px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; text-transform: capitalize;'"
                  >{{ penjualan.metode_pembayaran }}</span>
                </td>
              </tr>
              <tr v-if="penjualan.bayar > 0">
                <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Dibayar</td>
                <td style="padding: 3px 0; text-align: right; white-space: nowrap;">Rp {{ formatNum(penjualan.bayar) }}</td>
              </tr>
              <tr v-if="penjualan.kembalian > 0">
                <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Kembalian</td>
                <td :style="bwMode
                  ? 'padding: 3px 0; text-align: right; font-weight: 600; white-space: nowrap;'
                  : 'padding: 3px 0; text-align: right; color: #16a34a; font-weight: 600; white-space: nowrap;'"
                >Rp {{ formatNum(penjualan.kembalian) }}</td>
              </tr>
            </table>
          </div>

          <!-- Catatan -->
          <div v-if="penjualan.catatan" style="padding: 0 24px 16px;">
            <div :style="bwMode
              ? 'border: 1px solid #000; padding: 8px 12px; border-radius: 4px;'
              : 'background: #faf6f0; border-left: 3px solid #996600; padding: 8px 12px; border-radius: 0 4px 4px 0;'"
            >
              <div style="font-size: 10px; color: #888; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.06em;">Catatan</div>
              <div style="font-size: 11px; color: #555;">{{ penjualan.catatan }}</div>
            </div>
          </div>

          <!-- Tanda Tangan -->
          <div style="padding: 0 24px 20px;">
            <div style="display: flex; justify-content: flex-end;">
              <div style="text-align: center; min-width: 160px;">
                <div style="font-size: 10px; color: #555; margin-bottom: 4px;">Hormat kami,</div>
                <div style="height: 52px;"></div>
                <div style="border-top: 1px solid #1a1a1a; padding-top: 4px;">
                  <div style="font-size: 11px; font-weight: 700;">{{ penjualan.user?.name || '-' }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div :style="bwMode
            ? 'padding: 12px 24px; border-top: 1px solid #000; text-align: center;'
            : 'padding: 12px 24px; border-top: 1px solid #e0d1b2; background: #faf6f0; text-align: center;'"
          >
            <div style="font-size: 10px; color: #888;">Dokumen ini dicetak secara elektronik oleh sistem BPPU</div>
            <div style="font-size: 9px; color: #bbb; margin-top: 2px;">Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi &bull; bppu.ikipsiliwangi.ac.id</div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  show: Boolean,
  penjualan: Object,
})

defineEmits(['close'])

const bwMode = ref(false)

const formatNum = (value) => {
  return new Intl.NumberFormat('id-ID').format(value || 0)
}

const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  }).format(new Date(dateString))
}

const getBwStyles = () => `
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; background: white; color: #000; }
  @media print {
    body { margin: 0; }
    @page { margin: 12mm; size: A5; }
  }
`

const getColorStyles = () => `
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; background: white; color: #1a1a1a; }
  @media print {
    body { margin: 0; }
    @page { margin: 12mm; size: A5; }
  }
`

const doPrint = () => {
  const el = document.getElementById('invoice-print-area')
  if (!el) return

  const html = el.innerHTML
  const win = window.open('', '_blank', 'width=620,height=800')
  win.document.write(`<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Invoice - ${props.penjualan?.nomor_transaksi || ''}</title>
  <style>${bwMode.value ? getBwStyles() : getColorStyles()}</style>
</head>
<body>
  <div style="font-family: Arial, sans-serif; font-size: 12px; max-width: 520px; margin: 0 auto;">
    ${html}
  </div>
  <script>
    window.onload = function() {
      setTimeout(function() { window.print(); window.close(); }, 300);
    };
  <\/script>
</body>
</html>`)
  win.document.close()
}
</script>
