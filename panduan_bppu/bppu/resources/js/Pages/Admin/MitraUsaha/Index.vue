<template>
  <AdminLayout
    page-title="Manajemen Mitra Usaha"
    page-subtitle="Kelola data mitra usaha dalam sistem"
  >
    <div class="space-y-6">
      <!-- Filters and Actions -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
          <div class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari kode, nama, perusahaan, email, telepon..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>

            <!-- Tipe Mitra Filter -->
            <div class="min-w-[200px]">
              <select
                v-model="tipeFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Semua Tipe</option>
                <option v-for="(label, value) in tipeMitraOptions" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Status Filter -->
            <div class="min-w-[200px]">
              <select
                v-model="statusFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>

            <!-- Reset Button -->
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Reset
            </button>

            <!-- Download PDF Button -->
            <button
              v-if="suppliers.data.length > 0"
              @click="downloadAllPdf"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Export PDF</span>
            </button>

            <!-- Tambah Mitra Button -->
            <button
              @click="openCreateModal"
              class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Mitra</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Suppliers Grid Cards -->
      <div v-if="suppliers.data.length === 0" class="bg-white rounded-xl shadow-md border border-gray-100 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">Belum ada data mitra usaha</p>
        <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Mitra" untuk memulai</p>
      </div>

      <!-- Grouped Suppliers -->
      <div v-else>
        <div v-for="group in groupedSuppliers" :key="group.key" class="mb-6">
          <!-- Group Header -->
          <div class="mb-3 pb-2 border-b-2 border-[#996600]">
            <h2 class="text-lg font-bold text-[#6b4700]">{{ group.label }}</h2>
            <p class="text-sm text-gray-600">{{ group.items.length }} mitra usaha</p>
          </div>

          <!-- Cards Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div
              v-for="supplier in group.items"
              :key="supplier.id"
              class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-all duration-200 cursor-pointer overflow-hidden"
              @click="openDetailModal(supplier)"
            >
          <div class="flex items-stretch">
            <!-- Logo Section - Square Left -->
            <div class="w-28 h-28 flex-shrink-0 bg-gradient-to-br from-[#eae0cc] to-[#d6c199] flex items-center justify-center relative">
              <img
                v-if="supplier.logo"
                :src="'/' + supplier.logo"
                :alt="supplier.nama"
                class="w-full h-full object-cover"
              />
              <div
                v-else
                class="w-16 h-16 flex items-center justify-center"
              >
                <svg class="w-12 h-12 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <!-- Status Badge on Logo -->
              <div class="absolute top-1 right-1">
                <span
                  :class="[
                    'px-1.5 py-0.5 text-[10px] font-semibold rounded-full',
                    supplier.is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                  ]"
                >
                  {{ supplier.is_active ? 'ON' : 'OFF' }}
                </span>
              </div>
            </div>

            <!-- Content Section - Flexible Right -->
            <div class="flex-1 p-3 flex flex-col">
              <!-- Header -->
              <div class="flex-1">
                <div class="flex items-start justify-between mb-1">
                  <div class="flex-1 min-w-0 pr-2">
                    <h3 class="text-sm font-bold text-gray-900 truncate leading-tight">{{ supplier.nama }}</h3>
                    <p v-if="supplier.perusahaan" class="text-[11px] text-gray-500 truncate">{{ supplier.perusahaan }}</p>
                  </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-1 mb-2">
                  <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-[#eae0cc] text-[#6b4700]">
                    {{ supplier.kode_supplier }}
                  </span>
                  <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800">
                    {{ supplier.tipe_mitra_label }}
                  </span>
                  <span
                    v-if="supplier.skema_bisnis_aktif"
                    class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-green-100 text-green-800"
                  >
                    {{ jenisSkemaOptions[supplier.skema_bisnis_aktif.jenis_skema] }}
                  </span>
                </div>

                <!-- Contact Info - Compact -->
                <div class="space-y-0.5">
                  <div v-if="supplier.telepon" class="flex items-center text-[11px] text-gray-600">
                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="truncate">{{ supplier.telepon }}</span>
                  </div>
                  <div v-if="supplier.kota" class="flex items-center text-[11px] text-gray-600">
                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate">{{ supplier.kota }}</span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-1.5 mt-2 pt-2 border-t border-gray-200">
                <button
                  @click.stop="openEditModal(supplier)"
                  class="flex-1 px-2 py-1 text-[11px] font-medium text-blue-700 bg-blue-50 rounded hover:bg-blue-100 transition-colors"
                >
                  Edit
                </button>
                <button
                  @click.stop="deleteSupplier(supplier)"
                  class="flex-1 px-2 py-1 text-[11px] font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-colors"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
        </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="suppliers.data.length > 0" class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">
            Menampilkan {{ suppliers.from }} - {{ suppliers.to }} dari {{ suppliers.total }} data
          </div>
          <div class="flex space-x-1">
            <button
              v-for="(link, index) in suppliers.links"
              :key="index"
              @click="changePage(link.url)"
              :disabled="!link.url"
              :class="[
                'px-3 py-1 border rounded',
                link.active
                  ? 'bg-[#996600] text-white border-[#996600]'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                !link.url ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              <span v-if="index === 0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </span>
              <span v-else-if="index === suppliers.links.length - 1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </span>
              <span v-else>{{ link.label }}</span>
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEditing ? 'Edit Mitra Usaha' : 'Tambah Mitra Usaha Baru' }}
          </h2>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <!-- Logo Upload -->
          <div class="flex justify-center">
            <div class="relative">
              <div class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 hover:border-[#996600] transition-colors flex items-center justify-center cursor-pointer overflow-hidden bg-gray-50"
                @click="$refs.logoInput.click()"
              >
                <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
                <div v-else class="text-center">
                  <svg class="w-10 h-10 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-500">Upload Logo</p>
                </div>
              </div>
              <input
                ref="logoInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleLogoUpload"
              />
              <button
                v-if="logoPreview"
                @click.prevent="removeLogo"
                type="button"
                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors flex items-center justify-center"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kode Supplier -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kode Mitra *</label>
              <input
                v-model="form.kode_supplier"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Contoh: MTR001"
              />
            </div>

            <!-- Tipe Mitra -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Mitra *</label>
              <select
                v-model="form.tipe_mitra"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Pilih Tipe</option>
                <option v-for="(label, value) in tipeMitraOptions" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Nama -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Mitra *</label>
              <input
                v-model="form.nama"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama mitra usaha"
              />
            </div>

            <!-- Perusahaan -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan</label>
              <input
                v-model="form.perusahaan"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama perusahaan"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="email@example.com"
              />
            </div>

            <!-- Telepon -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
              <input
                v-model="form.telepon"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="08xx-xxxx-xxxx"
              />
            </div>

            <!-- Kontak Person -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kontak Person</label>
              <input
                v-model="form.kontak_person"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Nama kontak person"
              />
            </div>

            <!-- Telepon Kontak -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Telepon Kontak</label>
              <input
                v-model="form.telepon_kontak"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="08xx-xxxx-xxxx"
              />
            </div>

            <!-- Kota -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
              <input
                v-model="form.kota"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Kota"
              />
            </div>

            <!-- Provinsi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
              <input
                v-model="form.provinsi"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Provinsi"
              />
            </div>

            <!-- Kode Pos -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
              <input
                v-model="form.kode_pos"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="40xxx"
              />
            </div>
          </div>

          <!-- Alamat -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea
              v-model="form.alamat"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Alamat lengkap mitra usaha"
            ></textarea>
          </div>

          <!-- Catatan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
            <textarea
              v-model="form.catatan"
              rows="2"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Catatan tambahan (opsional)"
            ></textarea>
          </div>

          <!-- Tenant Fields -->
          <div v-if="form.tipe_mitra === 'tenant'" class="border border-[#c1a366] rounded-lg p-4 bg-[#f4efe5] space-y-4">
            <h3 class="text-sm font-semibold text-[#6b4700]">Informasi Tenant</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Penempatan (Unit Kerja)</label>
                <select
                  v-model="form.penempatan_work_unit_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                >
                  <option :value="null">-- Pilih Unit Kerja --</option>
                  <option v-for="wu in workUnits" :key="wu.id" :value="wu.id">{{ wu.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Kontribusi (Rp/bulan)</label>
                <input
                  v-model="form.biaya_kontribusi"
                  type="number"
                  min="0"
                  step="1000"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                  placeholder="0"
                />
              </div>
            </div>
          </div>

          <!-- Status -->
          <div>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
              />
              <span class="text-sm font-medium text-gray-700">Mitra Usaha Aktif</span>
            </label>
          </div>

          <!-- Skema Bisnis Form -->
          <SkemaBisnisForm
            :form="form.skema_bisnis"
            :jenisSkemaOptions="jenisSkemaOptions"
          />

          <!-- Buttons -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors disabled:opacity-50"
            >
              {{ processing ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Detail Mitra Usaha</h2>
          <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="selectedSupplier" class="p-6 space-y-6">
          <!-- Logo & Info Section -->
          <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
              <img
                v-if="selectedSupplier.logo"
                :src="'/' + selectedSupplier.logo"
                :alt="selectedSupplier.nama"
                class="w-24 h-24 object-cover rounded-lg shadow-md border-2 border-[#996600]"
              />
              <div
                v-else
                class="w-24 h-24 bg-gradient-to-br from-[#eae0cc] to-[#d6c199] rounded-lg shadow-md border-2 border-[#996600] flex items-center justify-center"
              >
                <svg class="w-12 h-12 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 mb-1">{{ selectedSupplier.nama }}</h3>
              <p v-if="selectedSupplier.perusahaan" class="text-sm text-gray-600 mb-2">{{ selectedSupplier.perusahaan }}</p>
              <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-[#eae0cc] text-[#6b4700]">
                  {{ selectedSupplier.kode_supplier }}
                </span>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                  {{ selectedSupplier.tipe_mitra_label }}
                </span>
                <span
                  :class="[
                    'px-3 py-1 text-xs font-semibold rounded-full',
                    selectedSupplier.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ selectedSupplier.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Kontak</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
              <div v-if="selectedSupplier.telepon">
                <label class="text-xs text-gray-500 font-medium">Telepon</label>
                <p class="text-sm text-gray-900">{{ selectedSupplier.telepon }}</p>
              </div>
              <div v-if="selectedSupplier.email">
                <label class="text-xs text-gray-500 font-medium">Email</label>
                <p class="text-sm text-gray-900">{{ selectedSupplier.email }}</p>
              </div>
              <div v-if="selectedSupplier.kontak_person">
                <label class="text-xs text-gray-500 font-medium">Kontak Person</label>
                <p class="text-sm text-gray-900">{{ selectedSupplier.kontak_person }}</p>
              </div>
              <div v-if="selectedSupplier.telepon_kontak">
                <label class="text-xs text-gray-500 font-medium">Telepon Kontak</label>
                <p class="text-sm text-gray-900">{{ selectedSupplier.telepon_kontak }}</p>
              </div>
            </div>
          </div>

          <!-- Address Information -->
          <div v-if="selectedSupplier.alamat || selectedSupplier.kota" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Alamat</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <p v-if="selectedSupplier.alamat" class="text-sm text-gray-900 mb-2">{{ selectedSupplier.alamat }}</p>
              <p class="text-sm text-gray-600">
                {{ selectedSupplier.kota }}{{ selectedSupplier.provinsi ? ', ' + selectedSupplier.provinsi : '' }}
                {{ selectedSupplier.kode_pos ? ' ' + selectedSupplier.kode_pos : '' }}
              </p>
            </div>
          </div>

          <!-- Skema Bisnis -->
          <div v-if="selectedSupplier.skema_bisnis_aktif" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Skema Bisnis</h4>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Jenis Skema</span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                  {{ jenisSkemaOptions[selectedSupplier.skema_bisnis_aktif.jenis_skema] }}
                </span>
              </div>

              <!-- Bagi Hasil Detail -->
              <div v-if="selectedSupplier.skema_bisnis_aktif.jenis_skema === 'bagi_hasil'" class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded p-3">
                  <label class="text-xs text-gray-600 font-medium">Persentase Vendor</label>
                  <p class="text-lg font-bold text-green-700">{{ selectedSupplier.skema_bisnis_aktif.persentase_vendor }}%</p>
                </div>
                <div class="bg-white rounded p-3">
                  <label class="text-xs text-gray-600 font-medium">Persentase Badan Usaha</label>
                  <p class="text-lg font-bold text-[#996600]">{{ selectedSupplier.skema_bisnis_aktif.persentase_badan_usaha }}%</p>
                </div>
              </div>

              <!-- Flat Detail -->
              <div v-else-if="['flat_per_item', 'flat_per_transaksi'].includes(selectedSupplier.skema_bisnis_aktif.jenis_skema)" class="bg-white rounded p-3">
                <label class="text-xs text-gray-600 font-medium">Nominal Flat</label>
                <p class="text-lg font-bold text-[#996600]">Rp {{ formatNumber(selectedSupplier.skema_bisnis_aktif.nominal_flat) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ selectedSupplier.skema_bisnis_aktif.jenis_skema === 'flat_per_item' ? 'per item terjual' : 'per transaksi' }}
                </p>
              </div>

              <!-- Persentase Keuntungan Detail -->
              <div v-else-if="selectedSupplier.skema_bisnis_aktif.jenis_skema === 'persentase_keuntungan'" class="bg-white rounded p-3">
                <label class="text-xs text-gray-600 font-medium">Persentase dari Keuntungan</label>
                <p class="text-lg font-bold text-[#996600]">{{ selectedSupplier.skema_bisnis_aktif.persentase_dari_keuntungan }}%</p>
              </div>

              <div v-if="selectedSupplier.skema_bisnis_aktif.catatan" class="pt-2 border-t border-blue-200">
                <label class="text-xs text-gray-600 font-medium">Catatan</label>
                <p class="text-sm text-gray-700 mt-1">{{ selectedSupplier.skema_bisnis_aktif.catatan }}</p>
              </div>

              <div class="flex items-center justify-between pt-2 border-t border-blue-200">
                <span class="text-xs text-gray-600 font-medium">Status Skema</span>
                <span
                  :class="[
                    'px-2 py-1 text-xs font-semibold rounded-full',
                    selectedSupplier.skema_bisnis_aktif.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ selectedSupplier.skema_bisnis_aktif.is_active ? 'Berlakukan' : 'Tidak Berlakukan' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Catatan -->
          <div v-if="selectedSupplier.catatan" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Catatan</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-700">{{ selectedSupplier.catatan }}</p>
            </div>
          </div>

          <!-- Tenant Info -->
          <div v-if="selectedSupplier.tipe_mitra === 'tenant'" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Tenant</h4>
            <div class="bg-[#f4efe5] border border-[#c1a366] rounded-lg p-4 grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-500 font-medium">Penempatan</label>
                <p class="text-sm font-semibold text-gray-900">{{ selectedSupplier.penempatan_work_unit_nama || '-' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500 font-medium">Biaya Kontribusi</label>
                <p class="text-sm font-semibold text-[#6b4700]">
                  {{ selectedSupplier.biaya_kontribusi ? 'Rp ' + formatNumber(selectedSupplier.biaya_kontribusi) : '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Info Akun -->
          <div class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Akun Portal Mitra</h4>
            <div v-if="selectedSupplier.akun_user" class="bg-[#f4efe5] border border-[#c1a366] rounded-lg p-4 space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600 font-medium">Username</span>
                <span class="text-sm font-bold text-[#6b4700] font-mono">{{ selectedSupplier.akun_user.username }}</span>
              </div>
              <div v-if="selectedSupplier.akun_user.email" class="flex items-center justify-between">
                <span class="text-xs text-gray-600 font-medium">Email</span>
                <span class="text-sm text-gray-700">{{ selectedSupplier.akun_user.email }}</span>
              </div>
              <p class="text-xs text-gray-500 pt-1">Mitra dapat login di <span class="font-mono">/mitra/login</span></p>
            </div>
            <div v-else class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <p class="text-sm text-gray-500">Belum memiliki akun portal mitra.</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button
              @click="editFromDetail"
              class="flex-1 px-4 py-2 text-sm font-medium text-white bg-[#996600] rounded-lg hover:bg-[#6b4700] transition-colors"
            >
              Edit Mitra
            </button>
            <button
              @click="closeDetailModal"
              class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Info Akun Mitra Baru -->
    <div
      v-if="showAkunModal && newAkunInfo"
      class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-lg font-bold text-gray-900">Akun Mitra Berhasil Dibuat</h2>
          <p class="text-sm text-gray-500 mt-1">Simpan informasi berikut dan berikan ke mitra usaha.</p>
        </div>
        <div class="p-6 space-y-4">
          <div class="bg-[#f4efe5] border border-[#c1a366] rounded-lg p-4 space-y-3">
            <div>
              <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Nama</p>
              <p class="text-sm font-semibold text-gray-900">{{ newAkunInfo.nama }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Username</p>
              <p class="text-base font-bold text-[#6b4700] font-mono">{{ newAkunInfo.username }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Password Sementara</p>
              <p class="text-base font-bold text-[#6b4700] font-mono">{{ newAkunInfo.password }}</p>
            </div>
          </div>
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-xs text-yellow-800">Mitra wajib mengganti password saat login pertama kali. URL login: <span class="font-mono font-semibold">/mitra/login</span></p>
          </div>
        </div>
        <div class="p-6 border-t border-gray-200">
          <button
            @click="showAkunModal = false; newAkunInfo = null"
            class="w-full px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors font-medium"
          >
            Mengerti, Tutup
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SkemaBisnisForm from '@/Components/MitraUsaha/SkemaBisnisForm.vue';
import { router, usePage } from '@inertiajs/vue3';
import { compressImageToBase64 } from '@/utils/imageCompressor';

// Simple debounce function
const debounce = (fn, delay) => {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
};

const props = defineProps({
  suppliers: Object,
  tipeMitraOptions: Object,
  jenisSkemaOptions: Object,
  workUnits: Array,
  filters: Object,
});

const page = usePage();

const showModal = ref(false);
const showDetailModal = ref(false);
const showAkunModal = ref(false);
const isEditing = ref(false);
const processing = ref(false);
const editingSupplier = ref(null);
const selectedSupplier = ref(null);
const logoPreview = ref(null);
const newAkunInfo = ref(null);

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const tipeFilter = ref(props.filters.tipe || '');

const form = reactive({
  kode_supplier: '',
  tipe_mitra: '',
  nama: '',
  perusahaan: '',
  alamat: '',
  kota: '',
  provinsi: '',
  kode_pos: '',
  telepon: '',
  email: '',
  kontak_person: '',
  telepon_kontak: '',
  catatan: '',
  penempatan_work_unit_id: null,
  biaya_kontribusi: null,
  is_active: true,
  logo: null,
  skema_bisnis: {
    jenis_skema: '',
    persentase_vendor: null,
    persentase_badan_usaha: null,
    nominal_flat: null,
    minimum_harga_item: null,
    persentase_dari_keuntungan: null,
    is_active: true,
    catatan: '',
  },
});

// Grouped suppliers by tipe_mitra
const groupedSuppliers = computed(() => {
  const groups = {};

  props.suppliers.data.forEach(supplier => {
    const tipeLabel = supplier.tipe_mitra_label;
    if (!groups[tipeLabel]) {
      groups[tipeLabel] = [];
    }
    groups[tipeLabel].push(supplier);
  });

  // Convert to array and sort alphabetically
  return Object.keys(groups)
    .sort()
    .map(label => ({
      key: label,
      label: label,
      items: groups[label].sort((a, b) => a.nama.localeCompare(b.nama))
    }));
});

const openCreateModal = () => {
  isEditing.value = false;
  editingSupplier.value = null;
  logoPreview.value = null;
  form.kode_supplier = '';
  form.tipe_mitra = '';
  form.nama = '';
  form.perusahaan = '';
  form.alamat = '';
  form.kota = '';
  form.provinsi = '';
  form.kode_pos = '';
  form.telepon = '';
  form.email = '';
  form.kontak_person = '';
  form.telepon_kontak = '';
  form.catatan = '';
  form.penempatan_work_unit_id = null;
  form.biaya_kontribusi = null;
  form.is_active = true;
  form.logo = null;
  form.skema_bisnis = {
    jenis_skema: '',
    persentase_vendor: null,
    persentase_badan_usaha: null,
    nominal_flat: null,
    minimum_harga_item: null,
    persentase_dari_keuntungan: null,
    is_active: true,
    catatan: '',
  };
  showModal.value = true;
};

const openEditModal = (supplier) => {
  isEditing.value = true;
  editingSupplier.value = supplier;
  logoPreview.value = supplier.logo ? '/' + supplier.logo : null;
  form.kode_supplier = supplier.kode_supplier;
  form.tipe_mitra = supplier.tipe_mitra;
  form.nama = supplier.nama;
  form.perusahaan = supplier.perusahaan || '';
  form.alamat = supplier.alamat || '';
  form.kota = supplier.kota || '';
  form.provinsi = supplier.provinsi || '';
  form.kode_pos = supplier.kode_pos || '';
  form.telepon = supplier.telepon || '';
  form.email = supplier.email || '';
  form.kontak_person = supplier.kontak_person || '';
  form.telepon_kontak = supplier.telepon_kontak || '';
  form.catatan = supplier.catatan || '';
  form.penempatan_work_unit_id = supplier.penempatan_work_unit_id || null;
  form.biaya_kontribusi = supplier.biaya_kontribusi || null;
  form.is_active = supplier.is_active;
  form.logo = null;

  const skema = supplier.skema_bisnis_aktif;
  form.skema_bisnis = {
    jenis_skema: skema?.jenis_skema || '',
    persentase_vendor: skema?.persentase_vendor || null,
    persentase_badan_usaha: skema?.persentase_badan_usaha || null,
    nominal_flat: skema?.nominal_flat || null,
    minimum_harga_item: skema?.minimum_harga_item || null,
    persentase_dari_keuntungan: skema?.persentase_dari_keuntungan || null,
    is_active: skema?.is_active ?? true,
    catatan: skema?.catatan || '',
  };

  showModal.value = true;
};

const openDetailModal = (supplier) => {
  selectedSupplier.value = supplier;
  showDetailModal.value = true;
};

const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedSupplier.value = null;
};

const editFromDetail = () => {
  openEditModal(selectedSupplier.value);
  closeDetailModal();
};

const closeModal = () => {
  showModal.value = false;
  isEditing.value = false;
  editingSupplier.value = null;
};

const submitForm = () => {
  processing.value = true;

  if (isEditing.value) {
    router.put(`/pengelola/mitra-usaha/${editingSupplier.value.id}`, form, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      },
    });
  } else {
    router.post('/pengelola/mitra-usaha', form, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
        const akun = page.props.flash?.akun_mitra;
        if (akun) {
          newAkunInfo.value = akun;
          showAkunModal.value = true;
        }
      },
      onError: () => {
        processing.value = false;
      },
    });
  }
};

const deleteSupplier = (supplier) => {
  if (confirm(`Apakah Anda yakin ingin menghapus mitra usaha "${supplier.nama}" (${supplier.kode_supplier})?`)) {
    router.delete(`/pengelola/mitra-usaha/${supplier.id}`);
  }
};

const downloadAllPdf = () => {
  window.open('/pengelola/mitra-usaha/download-pdf', '_blank');
};

// Watch filters and apply debounce
watch(searchQuery, debounce(function (value) {
  applyFilters();
}, 500));

watch(statusFilter, () => {
  applyFilters();
});

watch(tipeFilter, () => {
  applyFilters();
});

const applyFilters = () => {
  router.get('/pengelola/mitra-usaha', {
    search: searchQuery.value,
    status: statusFilter.value,
    tipe: tipeFilter.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  tipeFilter.value = '';
  router.get('/pengelola/mitra-usaha');
};

const changePage = (url) => {
  if (!url) return;
  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const handleLogoUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (!file.type.startsWith('image/')) {
    alert('File harus berupa gambar!');
    return;
  }

  try {
    const fileSizeKB = file.size / 1024;

    if (fileSizeKB < 50) {
      const reader = new FileReader();
      reader.onload = (e) => {
        form.logo = e.target.result;
        logoPreview.value = e.target.result;
        console.log('Image loaded without compression:', {
          originalSize: `${fileSizeKB.toFixed(2)} KB`,
          reason: 'File already small enough'
        });
      };
      reader.readAsDataURL(file);
      return;
    }

    const base64 = await compressImageToBase64(file, {
      maxWidth: 400,
      maxHeight: 400,
      quality: 0.85,
      type: 'image/jpeg'
    });

    form.logo = base64;
    logoPreview.value = base64;
  } catch (error) {
    console.error('Error compressing image:', error);
    alert('Gagal memproses gambar. Silakan coba lagi.');
  }
};

const removeLogo = () => {
  form.logo = null;
  logoPreview.value = null;
};

const formatNumber = (value) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0);
};
</script>
