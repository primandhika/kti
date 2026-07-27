# Komponen Buku Kas

Dokumentasi untuk komponen-komponen yang digunakan dalam modul Buku Kas.

## Struktur Komponen

```
BukuKas/
├── BukuKasHeader.vue          # Header dengan ringkasan buku kas dan unit kerja
├── FilterSection.vue           # Section filter dan tombol tambah transaksi
├── TransactionList.vue         # List/table transaksi dengan pagination
├── TransactionModal.vue        # Modal untuk tambah/edit transaksi
├── TransactionFormManual.vue   # Form transaksi manual
├── TransactionFormFromKasLain.vue  # Form transaksi dari kas lain
└── TransactionFormFromUnitKerja.vue  # Form transaksi dari unit kerja
```

## Komponen-Komponen

### 1. BukuKasHeader.vue

Menampilkan header halaman dengan informasi buku kas dan ringkasan per unit kerja.

**Props:**
- `bukuKas` (Object): Data buku kas
- `summaryByUnit` (Array): Ringkasan transaksi per unit kerja
- `isSuperAdmin` (Boolean): Status super admin
- `isOwner` (Boolean): Status pemilik buku kas

**Events:**
- `back`: Event ketika tombol kembali diklik

### 2. FilterSection.vue

Menampilkan filter pencarian, filter dropdown, dan tombol export.

**Props:**
- `filters` (Object): Object berisi filter yang aktif
- `kategoriList` (Array): List kategori transaksi
- `jenisTransaksiList` (Array): List jenis transaksi
- `workUnits` (Array): List unit kerja
- `hasTransactions` (Boolean): Apakah ada transaksi

**Events:**
- `update:filters`: Event ketika filter diubah
- `reset-filters`: Event untuk reset semua filter
- `add-transaction`: Event ketika tombol tambah transaksi diklik
- `export`: Event untuk export data (CSV/XLSX)

### 3. TransactionList.vue

Menampilkan list transaksi dalam bentuk card (mobile) atau table (desktop).

**Props:**
- `transaksi` (Object): Data transaksi dengan pagination
- `hasActiveFilters` (Boolean): Apakah ada filter yang aktif
- `sortField` (String): Field yang sedang di-sort
- `sortDirection` (String): Arah sorting (asc/desc)

**Events:**
- `edit`: Event ketika tombol edit diklik (payload: transaksi object)
- `delete`: Event ketika tombol hapus diklik (payload: transaksi object)
- `sort`: Event ketika header table diklik untuk sorting (payload: field name)
- `page-change`: Event ketika pagination diklik (payload: URL)

### 4. TransactionModal.vue

Modal untuk menambah atau mengedit transaksi dengan tab untuk berbagai jenis input.

**Props:**
- `modelValue` (Boolean): Status modal (open/close)
- `form` (Object): Inertia form object
- `isEditing` (Boolean): Mode edit atau create
- `editingId` (Number): ID transaksi yang sedang diedit
- `bukuKasId` (Number): ID buku kas
- `workUnits` (Array): List unit kerja
- `allBukuKas` (Array): List semua buku kas
- `existingBuktiTransaksi` (String): Path bukti transaksi existing
- `existingBuktiTransaksiLink` (String): Link bukti transaksi existing
- `existingBuktiAktivitas` (String): Path bukti aktivitas existing
- `existingBuktiAktivitasLink` (String): Link bukti aktivitas existing

**Events:**
- `update:modelValue`: Event untuk update status modal
- `submit`: Event ketika form disubmit
- `bukti-transaksi-change`: Event ketika file bukti transaksi dipilih
- `bukti-aktivitas-change`: Event ketika file bukti aktivitas dipilih

### 5. TransactionFormManual.vue

Form untuk input transaksi manual.

**Props:**
- `form` (Object): Inertia form object
- `workUnits` (Array): List unit kerja
- `existingBuktiTransaksi` (String): Path bukti transaksi existing
- `existingBuktiTransaksiLink` (String): Link bukti transaksi existing
- `existingBuktiAktivitas` (String): Path bukti aktivitas existing
- `existingBuktiAktivitasLink` (String): Link bukti aktivitas existing

**Events:**
- `bukti-transaksi-change`: Event ketika file bukti transaksi dipilih
- `bukti-aktivitas-change`: Event ketika file bukti aktivitas dipilih

### 6. TransactionFormFromKasLain.vue

Form untuk mencatat transaksi dari buku kas lain.

**Props:**
- `form` (Object): Inertia form object
- `allBukuKas` (Array): List semua buku kas
- `currentBukuKasId` (Number): ID buku kas saat ini

**Fitur:**
- Pilih buku kas sumber
- Auto-fill data transaksi dari buku kas sumber
- Tombol quick-fill untuk pemasukan, pengeluaran, atau saldo

### 7. TransactionFormFromUnitKerja.vue

Placeholder untuk form transaksi dari penghasilan unit kerja (dalam pengembangan).

## Perubahan dari Versi Sebelumnya

### Masalah yang Diperbaiki:
1. ✅ **Form lama yang di-hide dengan `v-show="false"`** - Menyebabkan error "invalid form control"
2. ✅ **Kode monolithic** - File Show.vue dari 1126 baris menjadi 306 baris (pengurangan 73%)
3. ✅ **Tumpang tindih tab** - Tab sekarang bekerja dengan baik tanpa konflik
4. ✅ **Modal tidak bekerja** - Modal sekarang clean dan modular
5. ✅ **Tombol Simpan tidak bekerja** - Form submit sekarang bekerja dengan baik

### Improvement:
- Kode lebih modular dan mudah di-maintain
- Setiap komponen memiliki tanggung jawab yang jelas (Single Responsibility Principle)
- Mudah untuk menambah fitur baru
- Lebih mudah untuk testing
- Tidak ada duplicate code

## Penggunaan di Show.vue

```vue
<template>
    <AdminLayout>
        <div class="p-4 md:p-6">
            <!-- Header Section -->
            <BukuKasHeader
                :buku-kas="bukuKas"
                :summary-by-unit="summaryByUnit"
                :is-super-admin="isSuperAdmin"
                :is-owner="isOwner"
                @back="goBack"
            />

            <!-- Transactions Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-[#d6c199]">
                <FilterSection
                    :filters="localFilters"
                    :kategori-list="kategoriList"
                    :jenis-transaksi-list="jenisTransaksiList"
                    :work-units="workUnits"
                    :has-transactions="transaksi.data.length > 0"
                    @update:filters="handleFilterUpdate"
                    @reset-filters="resetFilters"
                    @add-transaction="openCreateModal"
                    @export="exportTransaksi"
                />

                <TransactionList
                    :transaksi="transaksi"
                    :has-active-filters="hasActiveFilters"
                    :sort-field="localFilters.sort_field"
                    :sort-direction="localFilters.sort_direction"
                    @edit="openEditModal"
                    @delete="confirmDelete"
                    @sort="sortBy"
                    @page-change="changePage"
                />
            </div>
        </div>

        <!-- Transaction Modal -->
        <TransactionModal
            v-model="showModal"
            :form="form"
            :is-editing="isEditing"
            :editing-id="editingId"
            :buku-kas-id="bukuKas.id"
            :work-units="workUnits"
            :all-buku-kas="allBukuKas"
            @submit="submitForm"
            @bukti-transaksi-change="handleBuktiTransaksi"
            @bukti-aktivitas-change="handleBuktiAktivitas"
        />
    </AdminLayout>
</template>
```

## Tips Development

1. **Testing**: Test setiap komponen secara terpisah untuk memastikan tidak ada regression
2. **Error Handling**: Pastikan semua null/undefined values di-handle dengan baik
3. **Props Validation**: Tambahkan validation di props jika diperlukan
4. **Performance**: Gunakan `v-if` untuk komponen yang tidak perlu dirender
5. **Accessibility**: Pastikan semua interactive elements accessible untuk keyboard navigation

## Future Improvements

- [ ] Implementasi TransactionFormFromUnitKerja
- [ ] Tambah unit tests
- [ ] Tambah loading states
- [ ] Tambah error boundary
- [ ] Implementasi optimistic UI updates
