<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="fixed inset-0 bg-gray-900/50" @click="$emit('close')"></div>
      <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ lokasi ? 'Edit Lokasi' : 'Tambah Lokasi' }}
          </h3>
          <button @click="$emit('close')" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="px-6 py-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi <span class="text-red-500">*</span></label>
            <input
              v-model="form.nama"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="cth: Kantin G"
              required
            />
            <p v-if="errors.nama" class="mt-1 text-xs text-red-600">{{ errors.nama }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Lokasi</label>
            <input
              v-model="form.kode"
              type="text"
              maxlength="20"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="cth: KG"
            />
            <p v-if="errors.kode" class="mt-1 text-xs text-red-600">{{ errors.kode }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea
              v-model="form.keterangan"
              rows="2"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              placeholder="Deskripsi lokasi (opsional)"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kantin Default</label>
            <select
              v-model="form.default_work_unit_id"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
            >
              <option :value="null">-- Tidak ada (tampilkan semua) --</option>
              <option v-for="wu in workUnits" :key="wu.id" :value="wu.id">{{ wu.name }}</option>
            </select>
            <p class="mt-1 text-xs text-gray-400">Saat scan QR meja di lokasi ini, langsung filter ke kantin tersebut.</p>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex items-center gap-2">
              <input id="is_active_lokasi" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]" />
              <label for="is_active_lokasi" class="text-sm text-gray-700">Aktif</label>
            </div>
            <div class="flex items-center gap-2">
              <input id="is_public_lokasi" v-model="form.is_public" type="checkbox" class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]" />
              <label for="is_public_lokasi" class="text-sm text-gray-700">Area Publik</label>
            </div>
          </div>

          <!-- Koordinat (hanya relevan jika area publik) -->
          <div v-if="form.is_public" class="space-y-3 border border-[#ccb27f] rounded-lg p-3 bg-[#f4efe5]">
            <p class="text-xs font-semibold text-[#6b4700]">Koordinat Lokasi <span class="font-normal text-[#996600]">(untuk validasi jarak saat checkout)</span></p>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-600 mb-1">Latitude</label>
                <input
                  v-model="form.latitude"
                  type="number"
                  step="0.0000001"
                  placeholder="-6.8000000"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-600 mb-1">Longitude</label>
                <input
                  v-model="form.longitude"
                  type="number"
                  step="0.0000001"
                  placeholder="107.5000000"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                />
              </div>
            </div>
            <div class="flex items-end gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-600 mb-1">Radius (meter)</label>
                <input
                  v-model.number="form.radius_meter"
                  type="number"
                  min="10"
                  max="5000"
                  placeholder="200"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                />
              </div>
              <button
                type="button"
                @click="ambilLokasi"
                :disabled="gettingLocation"
                class="px-3 py-2 text-xs bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] disabled:opacity-50 whitespace-nowrap flex items-center gap-1.5"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ gettingLocation ? 'Mengambil...' : 'Lokasi Saya' }}
              </button>
            </div>
            <p v-if="locationError" class="text-xs text-red-600">{{ locationError }}</p>
            <p v-if="form.latitude && form.longitude" class="text-xs text-green-700">
              Koordinat tersimpan: {{ Number(form.latitude).toFixed(6) }}, {{ Number(form.longitude).toFixed(6) }}
            </p>
            <p class="text-xs text-gray-500">Jika koordinat diisi, pemesanan dari meja di lokasi ini akan divalidasi jaraknya. Kosongkan jika tidak perlu validasi jarak.</p>
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
              {{ loading ? 'Menyimpan...' : (lokasi ? 'Simpan Perubahan' : 'Tambah Lokasi') }}
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
  lokasi: { type: Object, default: null },
  workUnits: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'saved'])

const form = ref({ nama: '', kode: '', keterangan: '', is_active: true, is_public: false, default_work_unit_id: null, latitude: null, longitude: null, radius_meter: 200 })
const errors = ref({})
const errorMsg = ref('')
const loading = ref(false)
const gettingLocation = ref(false)
const locationError = ref('')

watch(() => props.lokasi, (val) => {
  if (val) {
    form.value = {
      nama: val.nama, kode: val.kode ?? '', keterangan: val.keterangan ?? '',
      is_active: val.is_active, is_public: val.is_public ?? false,
      default_work_unit_id: val.default_work_unit_id ?? null,
      latitude: val.latitude ?? null, longitude: val.longitude ?? null,
      radius_meter: val.radius_meter ?? 200,
    }
  } else {
    form.value = { nama: '', kode: '', keterangan: '', is_active: true, is_public: false, default_work_unit_id: null, latitude: null, longitude: null, radius_meter: 200 }
  }
}, { immediate: true })

function ambilLokasi() {
  if (!navigator.geolocation) {
    locationError.value = 'Browser tidak mendukung geolokasi.'
    return
  }
  gettingLocation.value = true
  locationError.value = ''
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      form.value.latitude = parseFloat(pos.coords.latitude.toFixed(7))
      form.value.longitude = parseFloat(pos.coords.longitude.toFixed(7))
      gettingLocation.value = false
    },
    (err) => {
      locationError.value = 'Gagal mendapatkan lokasi: ' + err.message
      gettingLocation.value = false
    },
    { enableHighAccuracy: true, timeout: 10000 }
  )
}

async function submit() {
  errors.value = {}
  errorMsg.value = ''
  loading.value = true

  try {
    let res
    if (props.lokasi) {
      res = await axios.put(`/pengelola/meja/lokasi/${props.lokasi.id}`, form.value)
    } else {
      res = await axios.post('/pengelola/meja/lokasi', form.value)
    }
    emit('saved', res.data, !!props.lokasi)
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
