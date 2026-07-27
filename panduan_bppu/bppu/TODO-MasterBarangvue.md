# TODO: Refactor MasterBarang.vue

File saat ini: 1248 baris, melanggar rule max 1000 lines.
Masalah utama ada di script section (line 437-1248 = 811 baris) - semua logic inline.

## Composables yang perlu dibuat

### 1. `useBarangForm.js`
Logic CRUD barang: `submitForm` (line 827), `editBarang` (641), `duplicateBarang` (571), `confirmDelete` (701), `deleteBarang` (776), `closeModal` (799)
State: `form`, `showAddModal`, `showEditModal`, `showDeleteModal`, `barangToDelete`, `editingBarang`

### 2. `useImageModal.js`
Logic upload/delete gambar: `openImageModal` (707), `closeImageModal` (715), `handleImageFileChange` (722), `uploadBarangImage` (732), `deleteBarangImage` (754)
State: `showImageModal`, `imageModalBarang`, `imageModalSelectedFile`, `imageModalPreview`, `imageModalUploading`

### 3. `useBarangImport.js`
Logic import dari unit lain: `loadBarangsFromUnit` (985), `toggleSelectAll` (1008), `importBarangs` (1016), `handleImportBarang` (1196)
State: `availableBarangs`, `loadingBarangs`, `importingBarangs`, `showImportModal`, `importForm`

### 4. `usePriceTags.js`
Logic cetak price tag: `submitPrintForm` (1163), `printSinglePriceTag` (1108), `printPriceTags` (1118)
State: `selectedBarangsForPrint`, `printing`

### 5. `useStockOpname.js`
Logic opname: `showOpnameModal` (932), `closeOpnameModal` (943), `submitOpname` (954)
State: `showStockOpnameModal`, `opnameBarang`, `opnameForm`

### 6. `useBarangFilter.js`
Logic filter + watcher + pagination: watcher (556-569), `filteredBarangs`, `emptyMessage`, `paginationClass`, `paginationClassMobile`, `toggleSelectBarang` (1090), `toggleSelectAllBarangs` (1099)
State: `searchQuery`, `filterKategori`, `showActiveOnly`, `filterAjuan`, `filterMenuKantin`, `selectedBarangsForPrint`

## Lokasi file composables
`resources/js/Composables/MasterBarang/`

## Estimasi ukuran setelah refactor
- `MasterBarang.vue`: ~500 baris (template 435 + script tipis ~60 baris)
- Masing-masing composable: ~100-150 baris
