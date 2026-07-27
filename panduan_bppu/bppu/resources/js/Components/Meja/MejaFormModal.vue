<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="fixed inset-0 bg-gray-900/50" @click="$emit('close')"></div>
      <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ meja ? 'Edit Meja' : 'Tambah Meja' }}
          </h3>
          <button @click="$emit('close')" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="px-6 py-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
            <select
              v-model="form.lokasi_meja_id"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              required
            >
              <option value="">-- Pilih Lokasi --</option>
              <option v-for="lok in lokasis" :key="lok.id" :value="lok.id">{{ lok.nama }}</option>
            </select>
            <p v-if="errors.lokasi_meja_id" class="mt-1 text-xs text-red-600">{{ errors.lokasi_meja_id }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Meja <span class="text-red-500">*</span></label>
            <input
              v-model="form.kode_meja"
              type="text"
              maxlength="20"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="cth: 01"
              required
            />
            <p v-if="errors.kode_meja" class="mt-1 text-xs text-red-600">{{ errors.kode_meja }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Meja</label>
            <input
              v-model="form.nama"
              type="text"
              maxlength="100"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="cth: Meja 01"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor</label>
            <input
              v-model="form.nomor"
              type="text"
              maxlength="10"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="cth: 1"
            />
          </div>

          <div class="flex items-center gap-2">
            <input id="is_active_meja" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]" />
            <label for="is_active_meja" class="text-sm text-gray-700">Aktif</label>
          </div>

          <p v-if="errorMsg" class="text-xs text-red-600 bg-red-50 rounded px-3 py-2">{{ errorMsg }}</p>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 text-sm bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] disabled:opacity-50"
            >
              {{ loading ? 'Menyimpan...' : (meja ? 'Simpan Perubahan' : 'Tambah Meja') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  meja: { type: Object, default: null },
  lokasis: { type: Array, default: () => [] },
  defaultLokasiId: { type: Number, default: null },
})

const emit = defineEmits(['close', 'saved'])

const form = ref({ lokasi_meja_id: '', kode_meja: '', nama: '', nomor: '', is_active: true })
const errors = ref({})
const errorMsg = ref('')
const loading = ref(false)

watch(() => [props.meja, props.defaultLokasiId], ([val, defLok]) => {
  if (val) {
    form.value = {
      lokasi_meja_id: val.lokasi_meja_id,
      kode_meja: val.kode_meja,
      nama: val.nama ?? '',
      nomor: val.nomor ?? '',
      is_active: val.is_active,
    }
  } else {
    form.value = { lokasi_meja_id: defLok ?? '', kode_meja: '', nama: '', nomor: '', is_active: true }
  }
}, { immediate: true })

async function submit() {
  errors.value = {}
  errorMsg.value = ''
  loading.value = true

  try {
    let res
    if (props.meja) {
      res = await axios.put(`/pengelola/meja/${props.meja.id}`, form.value)
    } else {
      res = await axios.post('/pengelola/meja', form.value)
    }
    emit('saved', res.data, !!props.meja)
  } catch (e) {
    if (e.response?.status === 422) {
      const data = e.response.data
      if (data.errors) errors.value = Object.fromEntries(Object.entries(data.errors).map(([k, v]) => [k, v[0]]))
      else errorMsg.value = data.message || 'Validasi gagal'
    } else {
      errorMsg.value = 'Terjadi kesalahan, coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>
