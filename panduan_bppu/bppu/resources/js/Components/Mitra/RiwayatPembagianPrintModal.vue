<template>
  <div
    v-if="show && item"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-4 py-3 border-b bg-gray-50 rounded-t-lg flex-shrink-0">
        <h3 class="font-semibold text-gray-800 text-sm">Rincian Pembagian Mitra</h3>
        <div class="flex items-center gap-2">
          <button
            @click="bwMode = !bwMode"
            class="px-3 py-1.5 text-xs font-semibold rounded flex items-center gap-1.5 border transition-colors"
            :class="bwMode ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
          >
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

      <!-- Print Preview (scrollable) -->
      <div class="flex-1 overflow-y-auto p-4 bg-gray-100">
        <div
          id="riwayat-pembagian-print-area"
          class="bg-white shadow-sm mx-auto"
          style="width: 100%; max-width: 640px; font-family: Arial, sans-serif; font-size: 12px;"
        >
          <!-- Header Dokumen -->
          <div
            :style="bwMode
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
              <div :style="bwMode ? 'font-size: 10px; color: #333;' : 'font-size: 10px; color: #6b4700;'">bppu.ikipsiliwangi.ac.id</div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
              <div :style="bwMode
                ? 'font-size: 14px; font-weight: 700; color: #000; letter-spacing: 0.03em;'
                : 'font-size: 14px; font-weight: 700; color: #996600; letter-spacing: 0.03em;'"
              >RINCIAN PEMBAGIAN</div>
              <div style="font-size: 10px; color: #555; margin-top: 3px;">Mitra Usaha</div>
            </div>
          </div>

          <!-- Info Mitra & Periode -->
          <div :style="bwMode
            ? 'padding: 14px 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-bottom: 1px solid #000;'
            : 'padding: 14px 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; background: #faf6f0; border-bottom: 1px solid #e0d1b2;'"
          >
            <div>
              <div style="font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Mitra</div>
              <div style="font-weight: 700; font-size: 13px;">{{ item.supplier?.nama || '-' }}</div>
              <div style="font-size: 10px; color: #555; margin-top: 2px;">{{ item.work_unit?.name || 'Semua Unit' }}</div>
            </div>
            <div style="text-align: right;">
              <div style="font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Periode</div>
              <div style="font-weight: 600; font-size: 12px;">{{ formatDate(item.periode_mulai) }}</div>
              <div style="font-size: 10px; color: #555;">s/d {{ formatDate(item.periode_selesai) }}</div>
              <div style="margin-top: 4px;">
                <span :style="bwMode
                  ? 'border: 1px solid #000; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;'
                  : 'background: #d6c199; color: #6b4700; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;'"
                >{{ item.jenis_skema }}</span>
              </div>
            </div>
          </div>

          <!-- Ringkasan Pembagian -->
          <div style="padding: 16px 24px 8px;">
            <div :style="bwMode
              ? 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 4px;'
              : 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #e0d1b2; padding-bottom: 4px; color: #6b4700;'"
            >Ringkasan</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
              <tr>
                <td style="padding: 3px 0; color: #555; width: 55%;">Total Penjualan</td>
                <td style="padding: 3px 0; text-align: right; font-weight: 600;">{{ formatCurrency(item.total_penjualan) }}</td>
              </tr>
              <tr>
                <td style="padding: 3px 0; color: #555;">Bagian Badan Usaha</td>
                <td :style="bwMode
                  ? 'padding: 3px 0; text-align: right; font-weight: 600;'
                  : 'padding: 3px 0; text-align: right; font-weight: 600; color: #996600;'"
                >{{ formatCurrency(item.bagian_badan_usaha) }}</td>
              </tr>
              <tr>
                <td colspan="2" style="padding: 3px 0;">
                  <div :style="bwMode ? 'border-top: 1px solid #000; margin: 4px 0;' : 'border-top: 1px solid #e0d1b2; margin: 4px 0;'"></div>
                </td>
              </tr>
              <tr :style="bwMode ? 'background: #f0f0f0;' : 'background: #faf6f0;'">
                <td :style="bwMode
                  ? 'padding: 6px 8px; font-weight: 700; font-size: 13px;'
                  : 'padding: 6px 8px; font-weight: 700; font-size: 13px; color: #16a34a;'"
                >Bagian Mitra (Vendor)</td>
                <td :style="bwMode
                  ? 'padding: 6px 8px; text-align: right; font-weight: 700; font-size: 13px;'
                  : 'padding: 6px 8px; text-align: right; font-weight: 700; font-size: 13px; color: #16a34a;'"
                >{{ formatCurrency(item.bagian_vendor) }}</td>
              </tr>
            </table>
          </div>

          <!-- Rincian Per Item -->
          <div v-if="item.detail_items && item.detail_items.length > 0" style="padding: 8px 24px 16px;">
            <div :style="bwMode
              ? 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 4px;'
              : 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #e0d1b2; padding-bottom: 4px; color: #6b4700;'"
            >Rincian Per Item</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
              <thead>
                <tr :style="bwMode
                  ? 'background-color: #000; color: #fff;'
                  : 'background-color: #996600; color: #fff;'"
                >
                  <th style="padding: 6px 8px; text-align: left; font-weight: 600; width: 20px;">No</th>
                  <th style="padding: 6px 8px; text-align: left; font-weight: 600;">Nama Barang</th>
                  <th style="padding: 6px 8px; text-align: center; font-weight: 600; white-space: nowrap;">Qty</th>
                  <th style="padding: 6px 8px; text-align: right; font-weight: 600; white-space: nowrap;">Subtotal</th>
                  <th style="padding: 6px 8px; text-align: right; font-weight: 600; white-space: nowrap;">Bagian Vendor</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(detail, idx) in item.detail_items"
                  :key="idx"
                  :style="bwMode
                    ? (idx % 2 === 0 ? 'background: #fff;' : 'background: #f5f5f5;')
                    : (idx % 2 === 0 ? 'background: #fff;' : 'background: #faf6f0;')"
                >
                  <td style="padding: 5px 8px; color: #888; text-align: center;">{{ idx + 1 }}</td>
                  <td style="padding: 5px 8px;">
                    <div style="font-weight: 500;">{{ detail.nama_barang }}</div>
                    <div v-if="detail.work_unit?.name" style="font-size: 10px; color: #888;">{{ detail.work_unit.name }}</div>
                  </td>
                  <td style="padding: 5px 8px; text-align: center; color: #555; white-space: nowrap;">{{ detail.qty }} {{ detail.satuan }}</td>
                  <td style="padding: 5px 8px; text-align: right; color: #555; white-space: nowrap;">{{ formatCurrency(detail.subtotal) }}</td>
                  <td :style="bwMode
                    ? 'padding: 5px 8px; text-align: right; font-weight: 600; white-space: nowrap;'
                    : 'padding: 5px 8px; text-align: right; font-weight: 600; white-space: nowrap; color: #16a34a;'"
                  >{{ formatCurrency(detail.bagian_vendor || 0) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Status Pencairan -->
          <div style="padding: 0 24px 16px;">
            <div :style="bwMode
              ? 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 4px;'
              : 'font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #e0d1b2; padding-bottom: 4px; color: #6b4700;'"
            >Status Pencairan</div>
            <table style="font-size: 11px; border-collapse: collapse;">
              <tr>
                <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Status</td>
                <td style="padding: 3px 0; font-weight: 600;">
                  {{ statusLabel(item.status_pencairan) }}
                </td>
              </tr>
              <template v-if="item.status_pencairan === 'dicairkan'">
                <tr>
                  <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Metode</td>
                  <td style="padding: 3px 0;">{{ metodeLabel(item.metode_pencairan) }}</td>
                </tr>
                <tr v-if="item.detail_transfer">
                  <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Detail Transfer</td>
                  <td style="padding: 3px 0;">{{ item.detail_transfer }}</td>
                </tr>
                <tr>
                  <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Dicairkan Pada</td>
                  <td style="padding: 3px 0;">{{ formatDate(item.dicairkan_at) }}</td>
                </tr>
                <tr>
                  <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Dicairkan Oleh</td>
                  <td style="padding: 3px 0;">{{ item.dicairkan_oleh?.name || '-' }}</td>
                </tr>
                <tr v-if="item.catatan_pencairan">
                  <td style="padding: 3px 12px 3px 0; color: #555; white-space: nowrap;">Catatan</td>
                  <td style="padding: 3px 0;">{{ item.catatan_pencairan }}</td>
                </tr>
              </template>
            </table>
            <div v-if="item.catatan" style="margin-top: 8px; font-size: 11px; color: #555;">
              Catatan: {{ item.catatan }}
            </div>
          </div>

          <!-- Tanda Tangan -->
          <div style="padding: 8px 24px 24px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
              <tr>
                <td style="width: 50%; padding: 8px 16px 0 0; text-align: center; vertical-align: top;">
                  <div style="color: #555; margin-bottom: 4px;">Pengelola BPPU,</div>
                  <div style="height: 52px;"></div>
                  <div style="border-top: 1px solid #1a1a1a; padding-top: 4px; display: inline-block; min-width: 160px;">
                    <div style="font-weight: 700;">(................................)</div>
                  </div>
                </td>
                <td style="width: 50%; padding: 8px 0 0 16px; text-align: center; vertical-align: top;">
                  <div style="color: #555; margin-bottom: 4px;">Mitra Usaha,</div>
                  <div style="height: 52px;"></div>
                  <div style="border-top: 1px solid #1a1a1a; padding-top: 4px; display: inline-block; min-width: 160px;">
                    <div style="font-weight: 700;">{{ item.supplier?.nama || '(................................)' }}</div>
                  </div>
                </td>
              </tr>
            </table>
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
  item: Object,
})

defineEmits(['close'])

const bwMode = ref(false)

const formatCurrency = (value) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0)

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
}

const statusLabel = (status) => {
  if (status === 'dicairkan') return 'Sudah Dicairkan'
  if (status === 'mengajukan') return 'Mengajukan Pencairan'
  return 'Belum Dicairkan'
}

const metodeLabel = (metode) => metode === 'transfer_bank' ? 'Transfer Bank' : 'Tunai'

const getPrintStyles = (bw) => `
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; background: white; color: ${bw ? '#000' : '#1a1a1a'}; }
  @media print {
    body { margin: 0; }
    @page { margin: 12mm; size: A4; }
  }
`

const doPrint = () => {
  const el = document.getElementById('riwayat-pembagian-print-area')
  if (!el) return

  const html = el.innerHTML
  const win = window.open('', '_blank', 'width=700,height=900')
  win.document.write(`<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Rincian Pembagian - ${props.item?.supplier?.nama || ''}</title>
  <style>${getPrintStyles(bwMode.value)}</style>
</head>
<body>
  <div style="font-family: Arial, sans-serif; font-size: 12px; max-width: 640px; margin: 0 auto;">
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
