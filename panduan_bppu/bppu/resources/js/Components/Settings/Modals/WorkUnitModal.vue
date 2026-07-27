<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-bold text-gray-900">
          {{ editingUnit ? 'Edit Unit Kerja' : 'Tambah Unit Kerja Baru' }}
        </h3>
      </div>

      <form @submit.prevent="$emit('save')" class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Unit <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              placeholder="Contoh: Kantin Gedung G"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tipe Unit <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.type"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            >
              <option value="">Pilih Tipe</option>
              <option value="Kantin">Kantin</option>
              <option value="Shop">Shop</option>
              <option value="Koperasi">Koperasi</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Deskripsi singkat tentang unit kerja"
          ></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
            <input
              v-model="form.location"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              placeholder="Contoh: Gedung G Lantai 1"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengelola</label>
            <input
              v-model="form.manager_name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              placeholder="Nama pengelola unit"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontak</label>
            <input
              v-model="form.contact_phone"
              type="tel"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              placeholder="081234567890"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Kontak</label>
            <input
              v-model="form.contact_email"
              type="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              placeholder="unit@ikipsiliwangi.ac.id"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jam Operasional</label>
          <input
            v-model="form.operating_hours"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Senin-Sabtu, 07:00-15:00"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Foto Unit Usaha</label>
          <div class="space-y-3">
            <div v-if="form.logoPreview || (editingUnit && editingUnit.logo)" class="relative w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
              <img
                :src="form.logoPreview || `/storage/${editingUnit.logo}`"
                alt="Preview"
                class="w-full h-full object-cover"
              />
              <button
                type="button"
                @click="$emit('remove-logo')"
                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="flex items-center justify-center w-full">
              <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                  <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="mb-1 text-sm text-gray-500">
                    <span class="font-semibold">Klik untuk upload</span>
                  </p>
                  <p class="text-xs text-gray-500">PNG, JPG atau JPEG (Max. 2MB)</p>
                </div>
                <input
                  type="file"
                  class="hidden"
                  accept="image/jpeg,image/png,image/jpg"
                  @change="$emit('upload-logo', $event)"
                />
              </label>
            </div>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="is_active"
            class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900"
          />
          <label for="is_active" class="text-sm font-medium text-gray-700">
            Unit Aktif
          </label>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
  editingUnit: Object,
  form: Object,
  loading: Boolean,
});

defineEmits(['close', 'save', 'upload-logo', 'remove-logo']);
</script>
