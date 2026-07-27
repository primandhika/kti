<template>
  <div class="flex flex-col h-full max-h-[calc(100vh-120px)] bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Products Grid -->
    <div class="flex-1 overflow-y-auto p-2 md:pb-2 pb-64">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
        <div
          v-for="barang in paginatedBarangs"
          :key="barang.id"
          @click="handleCardClick(barang)"
          :class="[
            'bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 overflow-hidden',
            isKantinUser && barang.stok === 0 ? 'cursor-pointer' : ''
          ]"
        >
          <!-- Main Product -->
          <div class="flex items-center p-1.5 gap-2 relative">
            <!-- Overlay Stok Habis -->
            <div
              v-if="barang.stok === 0"
              class="absolute inset-0 bg-gray-900/40 flex items-center justify-center rounded-lg z-10 pointer-events-none"
            >
              <div class="flex items-center gap-1">
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs font-semibold text-white">Stok Habis</span>
              </div>
            </div>

            <!-- Image - Clickable untuk upload/view -->
            <div
              @click.stop="handleImageClick(barang)"
              class="w-14 h-14 flex-shrink-0 bg-gray-200 rounded overflow-hidden relative group cursor-pointer hover:ring-2 hover:ring-[#996600]"
            >
              <img
                v-if="barang.image"
                :src="barang.image"
                :alt="barang.nama_barang"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
              </div>

              <!-- Warning Badge untuk Kadaluarsa -->
              <div
                v-if="getExpiryStatus(barang.tanggal_kadaluarsa) === 'expired'"
                class="absolute top-0 right-0 bg-red-500 text-white p-0.5 rounded-bl"
                title="Produk sudah kadaluarsa"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div
                v-else-if="getExpiryStatus(barang.tanggal_kadaluarsa) === 'expiring-soon'"
                class="absolute top-0 right-0 bg-yellow-500 text-white p-0.5 rounded-bl"
                title="Produk akan kadaluarsa dalam 1-3 hari"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>

              <!-- Overlay untuk upload image -->
              <div v-if="barang.stok > 0" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-xs truncate" :class="barang.stok === 0 ? 'text-gray-500' : 'text-gray-800'">
                {{ barang.nama_barang }}
              </h3>
              <div class="flex items-center gap-1">
                <p class="text-[10px] text-gray-500 truncate">{{ barang.kode_barang }}</p>
                <span
                  v-if="barang.work_unit_name"
                  class="flex-shrink-0 text-[9px] px-1 py-0.5 rounded bg-[#f4efe5] text-[#7a5100] font-medium border border-[#d6c199]"
                >{{ barang.work_unit_name }}</span>
              </div>
              <div class="flex items-center justify-between mt-0.5">
                <div class="flex flex-col">
                  <span
                    class="text-sm font-bold"
                    :class="[
                      barang.stok === 0 ? 'text-gray-400' : 'text-[#996600]',
                      barang.diskon_aktif ? 'line-through text-xs text-gray-400 font-normal' : ''
                    ]"
                  >
                    Rp {{ formatCurrency(barang.harga_jual) }}
                  </span>
                  <span v-if="barang.diskon_aktif" class="text-sm font-bold text-green-700">
                    Rp {{ formatCurrency(barang.harga_jual - barang.nominal_diskon) }}
                  </span>
                </div>
                <button
                  v-if="isKantinUser"
                  type="button"
                  @click.stop="$emit('open-pengajuan', barang)"
                  class="text-[10px] px-1.5 py-0.5 rounded-full hover:opacity-75 transition-opacity cursor-pointer"
                  :class="barang.stok === 0 ? 'text-red-600 bg-red-100 font-semibold' : 'text-gray-600 bg-gray-100'"
                  title="Klik untuk mengajukan perubahan stok"
                >
                  Stok: {{ barang.stok }}
                </button>
                <span
                  v-else
                  class="text-[10px] px-1.5 py-0.5 rounded-full"
                  :class="barang.stok === 0 ? 'text-red-600 bg-red-100 font-semibold' : 'text-gray-600 bg-gray-100'"
                >
                  Stok: {{ barang.stok }}
                </span>
              </div>
              <div v-if="barang.diskon_aktif" class="mt-0.5">
                <span class="text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">
                  DISKON
                  <template v-if="barang.diskon_tipe === 'persen'">{{ barang.diskon_nilai }}%</template>
                  <template v-else-if="barang.diskon_tipe === 'nominal'">- Rp {{ formatCurrency(barang.diskon_nilai) }}</template>
                  <template v-else-if="barang.diskon_tipe === 'harga_menjadi'">harga promo</template>
                </span>
              </div>
            </div>

            <!-- Controls -->
            <div class="flex-shrink-0">
              <template v-if="getCartItemQty(barang.id) > 0 && barang.stok > 0">
                <!-- Item sudah di cart: tampilkan - qty + -->
                <div class="flex items-center gap-1.5 bg-gray-100 rounded-full px-2 py-1">
                  <button
                    @click.stop="handleDecrement(barang.id)"
                    :class="[
                      'w-6 h-6 rounded-full flex items-center justify-center transition-colors',
                      getCartItemQty(barang.id) === 1
                        ? 'bg-red-100 hover:bg-red-200 text-red-600'
                        : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                    ]"
                  >
                    <!-- Trash icon jika qty = 1 -->
                    <svg v-if="getCartItemQty(barang.id) === 1" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <!-- Minus icon jika qty > 1 -->
                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>

                  <span class="w-6 text-center font-semibold text-sm text-gray-800">
                    {{ getCartItemQty(barang.id) }}
                  </span>

                  <button
                    @click.stop="handleIncrement(barang.id)"
                    class="w-6 h-6 rounded-full bg-[#996600] hover:bg-[#7a5100] text-white flex items-center justify-center transition-colors"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
              </template>

              <!-- Item belum di cart: tampilkan tombol + atau disabled -->
              <button
                v-else
                @click.stop="barang.stok > 0 && handleAddToCart(barang)"
                :disabled="barang.stok === 0"
                :class="[
                  'w-8 h-8 rounded-full flex items-center justify-center transition-colors',
                  barang.stok === 0
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-[#996600] hover:bg-[#7a5100] text-white cursor-pointer'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Varians (jika ada) - Accordion -->
          <div v-if="barang.varians && barang.varians.length > 0" class="border-t border-gray-100">
            <button
              @click.stop="toggleVarianAccordion(barang.id)"
              class="w-full px-2 py-1.5 bg-gray-50 hover:bg-gray-100 transition-colors flex items-center justify-between"
            >
              <p class="text-xs font-medium text-gray-600">Varian ({{ barang.varians.length }})</p>
              <svg
                :class="['w-4 h-4 text-gray-600 transition-transform', openVarianAccordions.includes(barang.id) ? 'rotate-180' : '']"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <transition name="accordion">
              <div v-show="openVarianAccordions.includes(barang.id)" class="space-y-1 px-2 pb-2 pt-1">
                <div
                  v-for="varian in barang.varians"
                  :key="varian.id"
                  class="flex items-center gap-2 py-1.5 px-2 bg-white rounded border border-gray-100 hover:border-gray-200 transition-colors"
                >
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 truncate">{{ varian.nama_varian }}</p>
                    <p v-if="varian.deskripsi" class="text-xs text-gray-500 truncate">{{ varian.deskripsi }}</p>
                    <p class="text-sm font-bold text-[#996600] mt-0.5">Rp {{ formatCurrency(varian.harga_jual) }}</p>
                  </div>
                  <div class="flex-shrink-0">
                    <template v-if="getCartVarianQty(barang.id, varian.id) > 0">
                      <!-- Varian sudah di cart -->
                      <div class="flex items-center gap-1 bg-gray-100 rounded-full px-1.5 py-0.5">
                        <button
                          @click.stop="handleDecrementVarian(barang.id, varian.id)"
                          :class="[
                            'w-5 h-5 rounded-full flex items-center justify-center transition-colors',
                            getCartVarianQty(barang.id, varian.id) === 1
                              ? 'bg-red-100 hover:bg-red-200 text-red-600'
                              : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                          ]"
                        >
                          <svg v-if="getCartVarianQty(barang.id, varian.id) === 1" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                          <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                          </svg>
                        </button>
                        <span class="w-5 text-center font-semibold text-xs text-gray-800">
                          {{ getCartVarianQty(barang.id, varian.id) }}
                        </span>
                        <button
                          @click.stop="handleIncrementVarian(barang.id, varian.id)"
                          class="w-5 h-5 rounded-full bg-[#996600] hover:bg-[#7a5100] text-white flex items-center justify-center transition-colors"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                          </svg>
                        </button>
                      </div>
                    </template>
                    <!-- Varian belum di cart -->
                    <button
                      v-else
                      @click.stop="handleAddVarianToCart(barang, varian)"
                      class="w-6 h-6 bg-[#996600] hover:bg-[#7a5100] rounded-full flex items-center justify-center text-white transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="barangs.length === 0" class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-gray-500">Tidak ada barang ditemukan</p>
        </div>
      </div>

      <!-- Footer bar: Ajukan + Pagination -->
      <div class="flex items-center justify-between px-1 py-2 min-h-[36px]">
        <!-- Kiri: link ajukan tambah barang (kantin only) -->
        <button
          v-if="isKantinUser"
          type="button"
          @click="$emit('open-pengajuan-tambah')"
          class="text-xs text-[#996600] hover:text-[#7a5100] font-medium flex items-center gap-1 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Ajukan tambah barang
        </button>
        <div v-else></div>

        <!-- Kanan: pagination -->
        <div v-if="totalPages > 1" class="flex items-center gap-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Prev
          </button>
          <span class="text-sm text-gray-600">{{ currentPage }} / {{ totalPages }}</span>
          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  barangs: Array,
  cart: Array,
  isKantinUser: Boolean,
})

const emit = defineEmits(['add-to-cart', 'increment', 'decrement', 'open-image-modal', 'add-varian-to-cart', 'increment-varian', 'decrement-varian', 'open-pengajuan', 'open-pengajuan-tambah'])

const currentPage = ref(1)
const itemsPerPage = 12
const openVarianAccordions = ref([])

watch(() => props.barangs, () => {
  currentPage.value = 1
})

const totalPages = computed(() => {
  return Math.ceil(props.barangs.length / itemsPerPage)
})

const paginatedBarangs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return props.barangs.slice(start, end)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const getCartItemQty = (barangId) => {
  const item = props.cart.find(item => item.barang_id === barangId && !item.varian_id)
  return item ? item.qty : 0
}

const getCartVarianQty = (barangId, varianId) => {
  const item = props.cart.find(item => item.barang_id === barangId && item.varian_id === varianId)
  return item ? item.qty : 0
}

const handleAddToCart = (barang) => {
  emit('add-to-cart', barang)
}

const handleIncrement = (barangId) => {
  emit('increment', barangId)
}

const handleDecrement = (barangId) => {
  emit('decrement', barangId)
}

const handleAddVarianToCart = (barang, varian) => {
  emit('add-varian-to-cart', barang, varian)
}

const handleIncrementVarian = (barangId, varianId) => {
  emit('increment-varian', barangId, varianId)
}

const handleDecrementVarian = (barangId, varianId) => {
  emit('decrement-varian', barangId, varianId)
}

const handleImageClick = (barang) => {
  emit('open-image-modal', barang)
}

const handleCardClick = (barang) => {
  if (props.isKantinUser && barang.stok === 0) {
    emit('open-pengajuan', barang)
  }
}

const toggleVarianAccordion = (barangId) => {
  const index = openVarianAccordions.value.indexOf(barangId)
  if (index > -1) {
    openVarianAccordions.value.splice(index, 1)
  } else {
    openVarianAccordions.value.push(barangId)
  }
}

const getExpiryStatus = (tanggalKadaluarsa) => {
  if (!tanggalKadaluarsa) return null

  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const expiryDate = new Date(tanggalKadaluarsa)
  expiryDate.setHours(0, 0, 0, 0)

  const diffTime = expiryDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) {
    return 'expired' // Sudah kadaluarsa
  } else if (diffDays >= 0 && diffDays <= 3) {
    return 'expiring-soon' // 0-3 hari lagi kadaluarsa
  }

  return null
}
</script>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
}

.accordion-enter-to,
.accordion-leave-from {
  max-height: 500px;
  opacity: 1;
}
</style>
