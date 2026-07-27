<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-800">
            {{ editing ? 'Edit Potongan' : 'Tambah Potongan Baru' }}
          </h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="px-6 py-5 space-y-4">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Potongan <span class="text-red-500">*</span></label>
            <input
              v-model="form.nama"
              type="text"
              placeholder="cth. Diskon Hari Kemerdekaan"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
              :class="{ 'border-red-400': errors.nama }"
            />
            <p v-if="errors.nama" class="text-xs text-red-500 mt-1">{{ errors.nama }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea
              v-model="form.deskripsi"
              rows="2"
              placeholder="Keterangan singkat program potongan..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600] resize-none"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Potongan <span class="text-red-500">*</span></label>
              <select
                v-model="form.tipe"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                :class="{ 'border-red-400': errors.tipe }"
              >
                <option value="persen">Persen (%)</option>
                <option value="nominal">Nominal (Rp)</option>
              </select>
              <p v-if="errors.tipe" class="text-xs text-red-500 mt-1">{{ errors.tipe }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">{{ form.tipe === 'persen' ? '%' : 'Rp' }}</span>
                <input
                  v-model="form.nilai"
                  type="number"
                  min="0.01"
                  step="0.01"
                  :max="form.tipe === 'persen' ? 100 : undefined"
                  class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                  :class="{ 'border-red-400': errors.nilai }"
                />
              </div>
              <p v-if="errors.nilai" class="text-xs text-red-500 mt-1">{{ errors.nilai }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Transaksi</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                <input
                  v-model="form.minimum_transaksi"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                />
              </div>
            </div>

            <div v-if="form.tipe === 'persen'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Maksimum Potongan</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                <input
                  v-model="form.maksimum_potongan"
                  type="number"
                  min="0"
                  placeholder="Tidak dibatasi"
                  class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
              <input
                v-model="form.berlaku_mulai"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
              <input
                v-model="form.berlaku_sampai"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                :class="{ 'border-red-400': errors.berlaku_sampai }"
              />
              <p v-if="errors.berlaku_sampai" class="text-xs text-red-500 mt-1">{{ errors.berlaku_sampai }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Voucher <span class="text-red-500">*</span></label>
              <input
                v-model="form.kuota_voucher"
                type="number"
                min="1"
                max="1000"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
                :class="{ 'border-red-400': errors.kuota_voucher }"
              />
              <p v-if="errors.kuota_voucher" class="text-xs text-red-500 mt-1">{{ errors.kuota_voucher }}</p>
              <p class="text-xs text-gray-400 mt-1">Maksimal voucher yang bisa digenerate</p>
            </div>

            <div class="flex items-center gap-3 pt-6">
              <input
                v-model="form.is_active"
                type="checkbox"
                id="is_active"
                class="w-4 h-4 accent-[#996600]"
              />
              <label for="is_active" class="text-sm text-gray-700">Program Aktif</label>
            </div>
          </div>

          <!-- Khusus Barang Tertentu -->
          <div>
            <div class="flex items-center gap-2 mb-2">
              <input
                v-model="khususBarang"
                type="checkbox"
                id="khusus_barang"
                class="w-4 h-4 accent-[#996600]"
              />
              <label for="khusus_barang" class="text-sm font-medium text-gray-700">Potongan khusus barang tertentu</label>
            </div>
            <div v-if="khususBarang" class="border border-gray-200 rounded-lg p-3 space-y-2">
              <p class="text-xs text-gray-500">Pilih barang yang mendapat potongan ini (kosong = berlaku untuk semua barang)</p>
              <div class="max-h-40 overflow-y-auto space-y-1">
                <label
                  v-for="b in barangOptions"
                  :key="b.id"
                  class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded"
                >
                  <input
                    type="checkbox"
                    :value="b.id"
                    v-model="form.barang_ids"
                    class="w-3.5 h-3.5 accent-[#996600]"
                  />
                  <span class="text-sm text-gray-700">{{ b.nama_barang }}</span>
                  <span class="text-xs text-gray-400 font-mono">{{ b.kode_barang }}</span>
                </label>
              </div>
              <p v-if="form.barang_ids.length > 0" class="text-xs text-[#996600] font-medium">
                {{ form.barang_ids.length }} barang dipilih
              </p>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] transition-colors disabled:opacity-50"
            >
              {{ form.processing ? 'Menyimpan...' : (editing ? 'Simpan Perubahan' : 'Buat Potongan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  editing: { type: Object, default: null },
  barangOptions: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'saved'])

const khususBarang = ref(!!(props.editing?.barang_ids?.length))

function toDateInput(val) {
  if (!val) return ''
  return val.substring(0, 10)
}

const form = useForm({
  nama: props.editing?.nama || '',
  deskripsi: props.editing?.deskripsi || '',
  tipe: props.editing?.tipe || 'persen',
  nilai: props.editing?.nilai || '',
  minimum_transaksi: props.editing?.minimum_transaksi || 0,
  maksimum_potongan: props.editing?.maksimum_potongan || '',
  berlaku_mulai: toDateInput(props.editing?.berlaku_mulai),
  berlaku_sampai: toDateInput(props.editing?.berlaku_sampai),
  kuota_voucher: props.editing?.kuota_voucher || 1,
  is_active: props.editing?.is_active ?? true,
  barang_ids: props.editing?.barang_ids || [],
})

const errors = form.errors

watch(khususBarang, (val) => {
  if (!val) form.barang_ids = []
})

function submit() {
  if (!khususBarang.value) form.barang_ids = []

  if (props.editing) {
    form.put(`/pengelola/diskon/${props.editing.id}`, {
      preserveScroll: true,
      onSuccess: () => emit('saved'),
    })
  } else {
    form.post('/pengelola/diskon', {
      preserveScroll: true,
      onSuccess: () => emit('saved'),
    })
  }
}
</script>
