<template>
  <BarcodeCameraScanner
    :show="showBarcodeScanner"
    @close="showBarcodeScanner = false"
    @barcode-scanned="onBarcodeScanned"
  />

  <form @submit.prevent="$emit('submit')" class="p-4 md:p-6 space-y-3">
    <!-- Alert pengajuan - hanya tampil di mode edit untuk officer/sysadmin -->
    <PengajuanBarangAlert
      v-if="isEditMode && editingBarang && workUnitId"
      :barang="editingBarang"
      :work-unit-id="workUnitId"
      @updated="onPengajuanApproved"
      @close-modal="$emit('close-modal')"
    />

    <!-- Kode Barang & Nama Barang -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <!-- Kode Barang -->
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Kode PLU *</label>
        <div class="flex-1 flex space-x-2">
          <input
            ref="kodeBarangInput"
            v-model="form.kode_barang"
            type="text"
            required
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            placeholder="BRG001"
          />
          <button
            type="button"
            @click="showBarcodeScanner = true"
            class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors"
            title="Scan Barcode via Kamera"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </button>
          <button
            v-if="!isEditMode"
            type="button"
            @click="$emit('generate-plu')"
            :disabled="generatingPlu"
            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50"
            title="Generate PLU Otomatis"
          >
            <svg v-if="!generatingPlu" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Nama Barang -->
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Nama Barang *</label>
        <input
          v-model="form.nama_barang"
          type="text"
          required
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          placeholder="Nama barang"
        />
      </div>
    </div>

    <!-- Kategori & Satuan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Kategori</label>
        <select
          v-model="form.kategori_id"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option value="">-- Pilih --</option>
          <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
            {{ kategori.nama }}
          </option>
        </select>
      </div>

      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Satuan *</label>
        <select
          v-model="form.satuan"
          required
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option value="pcs">Pcs</option>
          <option value="porsi">Porsi</option>
          <option value="kg">Kg</option>
          <option value="gram">Gram</option>
          <option value="liter">Liter</option>
          <option value="ml">Ml</option>
          <option value="box">Box</option>
          <option value="pack">Pack</option>
          <option value="lusin">Lusin</option>
          <option value="karung">Karung</option>
          <option value="botol">Botol</option>
          <option value="kaleng">Kaleng</option>
        </select>
      </div>
    </div>

    <!-- Stok & Minimum Stok -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Stok *</label>
        <input
          :value="form.stok ?? 0"
          @input="form.stok = parseInt($event.target.value) || 0"
          type="number"
          required
          min="0"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>

      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Min. Stok *</label>
        <input
          :value="form.minimum_stok ?? 0"
          @input="form.minimum_stok = parseInt($event.target.value) || 0"
          type="number"
          required
          min="0"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>
    </div>

    <!-- Mitra & Jenis Barang (dipindah ke atas biar logic conditional bisa jalan) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Mitra</label>
        <select
          v-model.number="form.supplier_id"
          @change="onSupplierChange"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option :value="null">-- Pilih --</option>
          <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
            {{ supplier.nama }}
          </option>
        </select>
      </div>

      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Jenis Barang *</label>
        <select
          v-model="form.jenis_barang"
          required
          :disabled="supplierHasSchema"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
        >
          <option value="jual_langsung">Jual Langsung</option>
          <option value="konsinyasi">Konsinyasi</option>
          <option value="preorder">Preorder</option>
          <option value="bundling">Bundling</option>
          <option value="langganan">Langganan</option>
          <option value="dropship_internal">Dropship Internal</option>
        </select>
      </div>
    </div>

    <!-- Info Auto-filled -->
    <div v-if="supplierHasSchema" class="text-xs text-blue-600 bg-blue-50 p-2 rounded border border-blue-200">
      Jenis barang otomatis mengikuti skema mitra yang dipilih
    </div>

    <!-- Harga Produksi - Hanya untuk Jual Langsung -->
    <div v-if="form.jenis_barang === 'jual_langsung'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Harga Produksi *</label>
        <input
          :value="form.harga_beli ?? 0"
          @input="updateHargaBeli($event.target.value)"
          type="number"
          required
          min="0"
          step="1"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>

      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Harga Jual *</label>
        <input
          :value="form.harga_jual ?? 0"
          @input="updateHargaJual($event.target.value)"
          type="number"
          required
          min="0"
          step="1"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>
    </div>

    <!-- Harga Konsinyasi - Hanya untuk Konsinyasi -->
    <div v-if="form.jenis_barang === 'konsinyasi'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Harga Konsinyasi *</label>
        <input
          :value="form.harga_konsinyasi ?? 0"
          @input="form.harga_konsinyasi = parseInt($event.target.value) || 0"
          type="number"
          required
          min="0"
          step="1"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          placeholder="Harga yang dibayar ke vendor"
        />
      </div>

      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Harga Jual *</label>
        <input
          :value="form.harga_jual ?? 0"
          @input="form.harga_jual = parseInt($event.target.value) || 0"
          type="number"
          required
          min="0"
          step="1"
          class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        />
      </div>
    </div>

    <!-- Harga untuk Jenis Lainnya -->
    <div v-if="!['jual_langsung', 'konsinyasi'].includes(form.jenis_barang)" class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
      <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Harga Jual *</label>
      <input
        :value="form.harga_jual ?? 0"
        @input="form.harga_jual = parseInt($event.target.value) || 0"
        type="number"
        required
        min="0"
        step="1"
        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
      />
    </div>


    <!-- Margin Calculator - Hanya untuk Jual Langsung -->
    <div v-if="form.jenis_barang === 'jual_langsung' && form.harga_beli && form.harga_beli > 0" class="bg-blue-50 p-3 rounded-lg border border-blue-200">
      <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-700">Margin:</span>
            <span class="text-sm font-bold" :class="getMarginColor(form.harga_beli || 0, form.harga_jual || 0)">
              {{ formatCurrency((form.harga_jual || 0) - (form.harga_beli || 0)) }} ({{ currentMarginPercent }}%)
            </span>
          </div>
          <button
            type="button"
            @click="showMarginTools = !showMarginTools"
            class="text-xs text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ showMarginTools ? 'Tutup' : 'Kalkulator' }}
          </button>
        </div>
        <div v-if="showMarginTools" class="flex flex-wrap items-center gap-1">
          <button
            v-for="margin in marginPresets"
            :key="margin"
            type="button"
            @click="applyMargin(margin)"
            class="px-2 py-1 bg-white hover:bg-blue-600 hover:text-white border border-blue-300 text-blue-700 rounded text-xs font-semibold transition-colors"
          >
            +{{ margin }}%
          </button>
          <input
            v-model.number="customMarginInput"
            type="number"
            min="0"
            step="0.1"
            class="w-14 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600]"
            placeholder="15"
          />
          <button
            type="button"
            @click="applyCustomMargin"
            class="px-2 py-1 bg-[#996600] hover:bg-[#7a5100] text-white rounded text-xs font-semibold"
          >
            OK
          </button>
        </div>
      </div>
    </div>

    <!-- Margin Info - Hanya untuk Konsinyasi -->
    <div v-if="form.jenis_barang === 'konsinyasi' && form.harga_konsinyasi && form.harga_konsinyasi > 0" class="bg-green-50 p-3 rounded-lg border border-green-200">
      <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-gray-700">Margin Konsinyasi:</span>
        <span class="text-sm font-bold text-green-700">
          {{ formatCurrency((form.harga_jual || 0) - (form.harga_konsinyasi || 0)) }}
        </span>
        <span class="text-xs text-gray-600">(Mitra: {{ formatCurrency((form.harga_jual || 0) - (form.harga_konsinyasi || 0)) }}, Badan Usaha: {{ formatCurrency(form.harga_konsinyasi || 0) }})</span>
      </div>
    </div>

    <!-- Tanggal Kadaluarsa -->
    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
      <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Kadaluarsa</label>
      <input
        v-model="form.tanggal_kadaluarsa"
        type="date"
        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
      />
    </div>

    <!-- Diskon Produk -->
    <DiskonBarangForm :form="form" />

    <!-- Pengentri (Edit Mode) -->
    <div v-if="isEditMode && users.length > 0" class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
      <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Pengentri</label>
      <select
        v-model.number="form.created_by"
        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
      >
        <option :value="null">-- Pilih --</option>
        <option v-for="user in users" :key="user.id" :value="user.id">
          {{ user.name }}
        </option>
      </select>
    </div>

    <!-- Deskripsi -->
    <div class="flex flex-col md:flex-row gap-1 md:gap-0">
      <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0 md:pt-2">Deskripsi</label>
      <textarea
        v-model="form.deskripsi"
        rows="2"
        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        placeholder="Deskripsi barang (opsional)"
      ></textarea>
    </div>

    <!-- Status Aktif & Visibility Toggles -->
    <div class="border border-gray-200 rounded-lg p-3">
      <div :class="isEditMode ? 'grid grid-cols-1 md:grid-cols-3 gap-3' : 'grid grid-cols-1 md:grid-cols-2 gap-3'">
        <!-- Barang Aktif (Only in Edit Mode) -->
        <div v-if="isEditMode" class="flex items-center gap-2">
          <div class="text-xs font-semibold text-gray-800">Barang Aktif</div>
          <button
            type="button"
            @click="form.is_active = !form.is_active"
            :class="[
              'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#996600] focus:ring-offset-2',
              form.is_active ? 'bg-[#996600]' : 'bg-gray-200'
            ]"
          >
            <span
              :class="[
                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                form.is_active ? 'translate-x-4' : 'translate-x-0'
              ]"
            ></span>
          </button>
        </div>

        <!-- Tampilkan di Menu Kantin -->
        <div class="flex items-center gap-2">
          <div
            class="text-xs font-semibold"
            :class="!form.is_active && isEditMode ? 'text-gray-400' : 'text-gray-800'"
          >Menu Kantin</div>
          <button
            type="button"
            :disabled="!form.is_active && isEditMode"
            @click="!(!form.is_active && isEditMode) && (form.is_menu_item = !form.is_menu_item)"
            :title="!form.is_active && isEditMode ? 'Aktifkan barang terlebih dahulu' : ''"
            :class="[
              'relative inline-flex h-5 w-9 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#996600] focus:ring-offset-2',
              (!form.is_active && isEditMode) ? 'cursor-not-allowed opacity-40 bg-gray-200' : 'cursor-pointer',
              (form.is_active || !isEditMode) && form.is_menu_item ? 'bg-[#996600]' : (!form.is_active && isEditMode ? '' : 'bg-gray-200')
            ]"
          >
            <span
              :class="[
                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                form.is_menu_item ? 'translate-x-4' : 'translate-x-0'
              ]"
            ></span>
          </button>
        </div>

        <!-- Unggulan / Featured -->
        <div class="flex items-center gap-2">
          <div
            class="text-xs font-semibold"
            :class="(!form.is_active && isEditMode) || (!form.is_menu_item && !form.show_in_shop) ? 'text-gray-400' : 'text-gray-800'"
          >Unggulan</div>
          <button
            type="button"
            :disabled="(!form.is_active && isEditMode) || (!form.is_menu_item && !form.show_in_shop)"
            @click="(form.is_menu_item || form.show_in_shop) && (form.is_featured = !form.is_featured)"
            :title="(!form.is_menu_item && !form.show_in_shop) ? 'Aktifkan Menu Kantin atau Menu Toko terlebih dahulu' : ''"
            :class="[
              'relative inline-flex h-5 w-9 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#996600] focus:ring-offset-2',
              ((!form.is_active && isEditMode) || (!form.is_menu_item && !form.show_in_shop)) ? 'cursor-not-allowed opacity-40 bg-gray-200' : 'cursor-pointer',
              (form.is_menu_item || form.show_in_shop) && form.is_featured ? 'bg-[#996600]' : 'bg-gray-200'
            ]"
          >
            <span
              :class="[
                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                form.is_featured ? 'translate-x-4' : 'translate-x-0'
              ]"
            ></span>
          </button>
        </div>

        <!-- Tampilkan di Menu Toko -->
        <div class="flex items-center gap-2">
          <div
            class="text-xs font-semibold"
            :class="!form.is_active && isEditMode ? 'text-gray-400' : 'text-gray-800'"
          >Menu Toko</div>
          <button
            type="button"
            :disabled="!form.is_active && isEditMode"
            @click="!(!form.is_active && isEditMode) && (form.show_in_shop = !form.show_in_shop)"
            :title="!form.is_active && isEditMode ? 'Aktifkan barang terlebih dahulu' : ''"
            :class="[
              'relative inline-flex h-5 w-9 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#996600] focus:ring-offset-2',
              (!form.is_active && isEditMode) ? 'cursor-not-allowed opacity-40 bg-gray-200' : 'cursor-pointer',
              (form.is_active || !isEditMode) && form.show_in_shop ? 'bg-[#996600]' : (!form.is_active && isEditMode ? '' : 'bg-gray-200')
            ]"
          >
            <span
              :class="[
                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                form.show_in_shop ? 'translate-x-4' : 'translate-x-0'
              ]"
            ></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Varian Barang Section -->
    <div class="border-t border-gray-200 pt-3 pb-20">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-gray-800">Varian Barang</h3>
        <p class="text-xs text-gray-500">Seret untuk mengatur urutan</p>
      </div>

      <draggable
        v-if="varians.length > 0"
        v-model="varians"
        item-key="id"
        :animation="200"
        handle=".drag-handle"
        @end="updateVarianOrder"
        class="space-y-2"
      >
        <template #item="{ element: varian, index }">
          <div :class="['border rounded overflow-hidden', varian.is_active === false ? 'border-gray-300 bg-gray-50 opacity-70' : 'border-gray-200 bg-white']">
            <!-- Header Accordion -->
            <div class="w-full px-2.5 py-2 bg-gray-50 flex items-center justify-between">
              <!-- Drag Handle -->
              <button
                type="button"
                class="drag-handle p-1 hover:bg-gray-200 rounded cursor-move mr-2 flex-shrink-0"
                title="Seret untuk mengatur urutan"
              >
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                </svg>
              </button>

              <!-- Toggle Accordion -->
              <button
                type="button"
                @click="toggleVarianEdit(index)"
                class="flex items-center gap-2 flex-1 min-w-0 hover:bg-gray-100 transition-colors px-2 py-1 rounded"
              >
                <svg
                  :class="['w-3.5 h-3.5 text-gray-600 transition-transform flex-shrink-0', openVarianIndex === index ? 'rotate-90' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <div class="flex-1 text-left min-w-0">
                  <p class="font-medium text-sm text-gray-800 truncate flex items-center gap-1.5">
                    {{ varian.nama_varian || `Varian ${index + 1}` }}
                    <span v-if="varian.is_active === false" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600 flex-shrink-0">Nonaktif</span>
                  </p>
                  <p class="text-xs text-gray-600">
                    {{ formatCurrency(varian.harga_jual || 0) }}
                    <span v-if="varian.harga_jual" :class="calculateMargin(varian) >= 0 ? 'text-green-600' : 'text-red-600'" class="ml-1">
                      ({{ calculateMargin(varian) >= 0 ? '+' : '' }}{{ formatCurrency(calculateMargin(varian)) }})
                    </span>
                  </p>
                </div>
              </button>

              <!-- Delete Button -->
              <button
                type="button"
                @click.stop="removeVarian(index)"
                class="ml-2 p-1 text-red-600 hover:bg-red-50 rounded transition-colors flex-shrink-0"
                title="Hapus varian"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>

            <!-- Content Accordion -->
            <transition name="accordion">
              <div v-show="openVarianIndex === index" class="p-2.5 space-y-2 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Varian *</label>
                    <input
                      v-model="varian.nama_varian"
                      type="text"
                      required
                      class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#996600]"
                      placeholder="Nama varian"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Jual *</label>
                    <input
                      :value="varian.harga_jual || 0"
                      @input="varian.harga_jual = parseInt($event.target.value) || 0"
                      type="number"
                      required
                      min="0"
                      step="100"
                      class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#996600]"
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">HPP Tambahan</label>
                    <input
                      :value="varian.hpp_tambahan || 0"
                      @input="varian.hpp_tambahan = parseInt($event.target.value) || 0"
                      type="number"
                      min="0"
                      step="100"
                      class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#996600]"
                    />
                  </div>
                </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea
                  v-model="varian.deskripsi"
                  rows="2"
                  class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#996600]"
                  placeholder="Deskripsi varian (opsional)"
                ></textarea>
              </div>

              <!-- Info Margin - Compact -->
              <div class="bg-blue-50 border border-blue-200 rounded p-2">
                <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs">
                  <div class="flex justify-between">
                    <span class="text-gray-600">HPP Base:</span>
                    <span class="font-semibold">{{ formatCurrency(form.harga_beli || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">HPP +:</span>
                    <span class="font-semibold">{{ formatCurrency(varian.hpp_tambahan || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">HPP Final:</span>
                    <span class="font-semibold text-blue-700">{{ formatCurrency(calculateHppFinal(varian)) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Margin:</span>
                    <span
                      :class="[
                        'font-semibold',
                        calculateMargin(varian) >= 0 ? 'text-green-700' : 'text-red-700'
                      ]"
                    >
                      {{ formatCurrency(calculateMargin(varian)) }}
                    </span>
                  </div>
                </div>
              </div>

                <!-- Komponen bahan baku -->
                <VarianKomponenForm
                  :model-value="varian.komponens"
                  :work-unit-id="workUnitId"
                  :varian-id="varian.id || null"
                  :hpp-base="form.harga_beli || 0"
                  :hpp-tambahan="varian.hpp_tambahan || 0"
                  @update:model-value="(val) => { form.varians[index].komponens = val }"
                  @apply-rekomendasi="(harga) => { form.varians[index].harga_jual = harga }"
                />

                <div v-if="isEditMode" class="flex items-center gap-2 pt-1">
                  <input
                    v-model="varian.is_active"
                    type="checkbox"
                    :id="`varian_active_${index}`"
                    class="w-3.5 h-3.5 text-[#996600] border-gray-300 rounded focus:ring-[#996600]"
                  />
                  <label :for="`varian_active_${index}`" class="text-xs font-medium text-gray-700 cursor-pointer">
                    Varian Aktif
                  </label>
                </div>
              </div>
            </transition>
          </div>
        </template>
      </draggable>

      <div v-else class="text-center py-4 text-gray-500 bg-gray-50 rounded border border-dashed border-gray-300">
        <p class="text-xs">Belum ada varian.</p>
      </div>
    </div>

    <!-- Sticky Footer Buttons -->
    <div class="sticky bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 -mx-4 md:-mx-6 -mb-4 md:-mb-6 mt-3">
      <!-- Action Buttons Row -->
      <div class="flex items-center gap-1.5 md:gap-2 mb-3">
        <button
          type="button"
          @click="addVarian"
          class="px-2 md:px-3 py-1.5 md:py-2 bg-green-600 hover:bg-green-700 text-white rounded transition-colors flex items-center gap-1 text-xs font-medium"
        >
          <svg class="w-3 h-3 md:w-3.5 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Varian</span>
        </button>

        <button
          type="button"
          @click="triggerFileInput"
          class="px-2 md:px-3 py-1.5 md:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors flex items-center gap-1 text-xs font-medium"
        >
          <svg class="w-3 h-3 md:w-3.5 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>Unggah</span>
        </button>

        <button
          type="button"
          @click="triggerCameraInput"
          class="px-2 md:px-3 py-1.5 md:py-2 bg-purple-600 hover:bg-purple-700 text-white rounded transition-colors flex items-center gap-1 text-xs font-medium"
        >
          <svg class="w-3 h-3 md:w-3.5 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>Foto</span>
        </button>

        <!-- Hidden file inputs -->
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          @change="handleFileSelect"
          class="hidden"
        />
        <input
          ref="cameraInput"
          type="file"
          accept="image/*"
          capture="environment"
          @change="handleFileSelect"
          class="hidden"
        />

        <div class="flex-1"></div>

        <button
          type="button"
          @click="$emit('close')"
          class="px-3 md:px-4 py-1.5 md:py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors text-xs font-medium"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-3 md:px-5 py-1.5 md:py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded transition-colors text-xs font-medium"
        >
          {{ isEditMode ? 'Update' : 'Simpan' }}
        </button>
      </div>

      <!-- Image Preview -->
      <div v-if="selectedImagePreview" class="flex items-center gap-2 p-2 bg-gray-50 rounded border border-gray-200">
        <img :src="selectedImagePreview" alt="Preview" class="w-16 h-16 object-cover rounded" />
        <div class="flex-1 min-w-0">
          <p v-if="selectedImage" class="text-xs font-medium text-gray-700 truncate">{{ selectedImage.name }}</p>
          <p v-else class="text-xs font-medium text-gray-700">Foto produk saat ini</p>
          <p v-if="selectedImage" class="text-xs text-gray-500">{{ formatFileSize(selectedImage.size) }}</p>
        </div>
        <button
          type="button"
          @click="removeImage"
          class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
          title="Hapus foto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { computed, ref, watch, nextTick, onMounted } from 'vue';
import draggable from 'vuedraggable';
import DiskonBarangForm from './DiskonBarangForm.vue';
import PengajuanBarangAlert from './PengajuanBarangAlert.vue';
import BarcodeCameraScanner from '@/Components/PoS/BarcodeCameraScanner.vue';
import VarianKomponenForm from './VarianKomponenForm.vue';

const props = defineProps({
  form: Object,
  kategoris: Array,
  suppliers: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] },
  isEditMode: Boolean,
  generatingPlu: Boolean,
  editingBarang: { type: Object, default: null },
  workUnitId: { type: [String, Number], default: null },
});

const emit = defineEmits(['submit', 'generate-plu', 'close', 'close-modal']);

const onPengajuanApproved = (payload) => {
  if (!payload) return;
  if (payload.jenis === 'nama_barang') {
    props.form.nama_barang = payload.nilai_baru;
  } else if (payload.jenis === 'kode_barang') {
    props.form.kode_barang = payload.nilai_baru;
  } else if (payload.jenis === 'stok') {
    props.form.stok = parseInt(payload.nilai_baru) || 0;
  }
};

// Ref for kode barang input
const kodeBarangInput = ref(null);
const showBarcodeScanner = ref(false);

const onBarcodeScanned = (code) => {
  props.form.kode_barang = code;
  showBarcodeScanner.value = false;
};

// Refs for file inputs
const fileInput = ref(null);
const cameraInput = ref(null);
const selectedImage = ref(null);
const selectedImagePreview = ref(null);

// Margin calculator
const marginPresets = [5, 10, 20, 40];
const customMarginInput = ref(null);
const showMarginTools = ref(false);

const currentMarginPercent = computed(() => {
  if (!props.form.harga_beli || props.form.harga_beli <= 0) return '0.0';
  const margin = ((props.form.harga_jual - props.form.harga_beli) / props.form.harga_beli * 100);
  return margin.toFixed(1);
});

// Auto-reset is_featured jika menu kantin & menu toko keduanya dinonaktifkan
watch(() => [props.form.is_menu_item, props.form.show_in_shop], ([isMenu, isShop]) => {
  if (!isMenu && !isShop) {
    props.form.is_featured = false;
  }
});

watch(() => props.form.harga_jual, () => {
  if (customMarginInput.value === null && props.form.harga_beli > 0 && props.form.harga_jual > 0) {
    customMarginInput.value = parseFloat(currentMarginPercent.value);
  }
});

// Watch supplier change untuk auto-fill jenis_barang
const supplierHasSchema = ref(false);

const onSupplierChange = () => {
  if (props.form.supplier_id) {
    const selectedSupplier = props.suppliers.find(s => s.id === props.form.supplier_id);
    if (selectedSupplier && selectedSupplier.skema_bisnis_aktif) {
      const skema = selectedSupplier.skema_bisnis_aktif.jenis_skema;

      // Map skema bisnis ke jenis barang
      if (skema === 'konsinyasi') {
        props.form.jenis_barang = 'konsinyasi';
        supplierHasSchema.value = true;
      } else if (skema === 'dropshipper') {
        props.form.jenis_barang = 'dropship_internal';
        supplierHasSchema.value = true;
      } else {
        // bagi_hasil atau supplier = jual_langsung
        props.form.jenis_barang = 'jual_langsung';
        supplierHasSchema.value = true;
      }
    } else {
      supplierHasSchema.value = false;
    }
  } else {
    supplierHasSchema.value = false;
  }
};

const updateHargaBeli = (value) => {
  props.form.harga_beli = parseInt(value) || 0;
  // Auto-apply 20% margin when harga beli changes and harga jual is 0
  if (props.form.harga_beli > 0 && (!props.form.harga_jual || props.form.harga_jual === 0)) {
    applyMargin(20);
  }
};

const updateHargaJual = (value) => {
  props.form.harga_jual = parseInt(value) || 0;
  // Update custom margin input when harga jual manually changed
  if (props.form.harga_beli > 0 && props.form.harga_jual > 0) {
    customMarginInput.value = parseFloat(currentMarginPercent.value);
  }
};

const applyMargin = async (marginPercent) => {
  if (!props.form.harga_beli || props.form.harga_beli <= 0) return;
  if (marginPercent === null || marginPercent === undefined || marginPercent < 0) return;

  const hargaBeli = parseFloat(props.form.harga_beli);
  const margin = hargaBeli * (parseFloat(marginPercent) / 100);
  const hargaJualBaru = Math.round(hargaBeli + margin);

  props.form.harga_jual = hargaJualBaru;
  await nextTick();
  customMarginInput.value = parseFloat(marginPercent);
};

const applyCustomMargin = () => {
  if (customMarginInput.value !== null && customMarginInput.value !== undefined && customMarginInput.value >= 0) {
    applyMargin(customMarginInput.value);
  }
};

const toInteger = (value, fallback = 0) => {
  if (value === null || value === undefined || value === '') return fallback;
  const parsed = Number.parseFloat(value);
  return Number.isFinite(parsed) ? Math.trunc(parsed) : fallback;
};

const normalizeNumericFields = () => {
  if (!props.form) return;

  props.form.stok = toInteger(props.form.stok, 0);
  props.form.minimum_stok = toInteger(props.form.minimum_stok, 0);
  props.form.harga_beli = toInteger(props.form.harga_beli, 0);
  props.form.harga_jual = toInteger(props.form.harga_jual, 0);
  props.form.harga_grosir = props.form.harga_grosir === null ? null : toInteger(props.form.harga_grosir, 0);
  props.form.harga_konsinyasi = props.form.harga_konsinyasi === null ? null : toInteger(props.form.harga_konsinyasi, 0);

  if (Array.isArray(props.form.varians)) {
    props.form.varians.forEach((varian) => {
      varian.harga_jual = toInteger(varian.harga_jual, 0);
      varian.harga_grosir = varian.harga_grosir === null ? null : toInteger(varian.harga_grosir, 0);
      varian.hpp_tambahan = toInteger(varian.hpp_tambahan, 0);
      if (!Array.isArray(varian.komponens)) {
        varian.komponens = [];
      }
    });
  }
};

// Varian management
const varians = computed({
  get() {
    return props.form?.varians || [];
  },
  set(value) {
    if (props.form) {
      props.form.varians = value;
    }
  }
});

const openVarianIndex = ref(null);

const toggleVarianEdit = (index) => {
  openVarianIndex.value = openVarianIndex.value === index ? null : index;
};

const addVarian = () => {
  if (!props.form.varians) {
    props.form.varians = [];
  }
  props.form.varians.push({
    id: null,
    nama_varian: '',
    deskripsi: '',
    harga_jual: 0,
    harga_grosir: null,
    hpp_tambahan: 0,
    display_order: props.form.varians.length,
    is_active: true,
    komponens: [],
  });
  // Auto-open varian yang baru ditambahkan
  nextTick(() => {
    openVarianIndex.value = props.form.varians.length - 1;
  });
};

const removeVarian = (index) => {
  if (props.form.varians) {
    props.form.varians.splice(index, 1);
    // Update display order
    updateVarianOrder();
  }
};

const updateVarianOrder = () => {
  // Update display_order after drag and drop
  if (props.form.varians) {
    props.form.varians.forEach((v, i) => {
      v.display_order = i;
    });
  }
};

const formatCurrency = (value) => {
  if (value === null || value === undefined || isNaN(value)) {
    return 'Rp 0';
  }
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

const getMarginColor = (hargaBeli, hargaJual) => {
  const margin = hargaJual - hargaBeli;
  if (margin > 0) return 'text-green-600';
  if (margin < 0) return 'text-red-600';
  return 'text-gray-600';
};

const calculateHppFinal = (varian) => {
  const hppBase = parseFloat(props.form.harga_beli) || 0;
  const hppTambahan = parseFloat(varian.hpp_tambahan) || 0;
  return hppBase + hppTambahan;
};

const calculateMargin = (varian) => {
  const hargaJual = parseFloat(varian.harga_jual) || 0;
  const hppFinal = calculateHppFinal(varian);
  return hargaJual - hppFinal;
};

// Image upload functions
const triggerFileInput = () => {
  fileInput.value?.click();
};

const triggerCameraInput = () => {
  cameraInput.value?.click();
};

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Harap pilih file gambar');
      return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 5MB');
      return;
    }

    selectedImage.value = file;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      selectedImagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    // Add to form
    if (!props.form.gambar) {
      props.form.gambar = file;
    } else {
      props.form.gambar = file;
    }
  }
};

const removeImage = () => {
  selectedImage.value = null;
  selectedImagePreview.value = null;
  props.form.gambar = null;

  // Reset file inputs
  if (fileInput.value) fileInput.value.value = '';
  if (cameraInput.value) cameraInput.value.value = '';
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Watch for form changes when editing (to load existing image)
watch(() => props.form, (newForm) => {
  if (newForm && newForm.gambar_url && !selectedImage.value) {
    selectedImagePreview.value = newForm.gambar_url;
  }
}, { immediate: true, deep: true });

// Auto-focus kode barang input on mount
onMounted(() => {
  normalizeNumericFields();
  nextTick(() => {
    if (kodeBarangInput.value) {
      kodeBarangInput.value.focus();
      kodeBarangInput.value.select(); // Select all text for easy barcode scanning
    }
  });
});
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
