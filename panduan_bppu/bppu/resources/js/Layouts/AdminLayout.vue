<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile menu backdrop -->
    <div
      v-show="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#6b4700] to-[#5b3d00] text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo/Brand -->
        <div class="flex items-center justify-between h-16 px-6 bg-[#4c3300]/50 border-b border-[#7a5100]/50">
          <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center">
              <img src="/logo-white.png" alt="BPPU Logo" class="h-10 w-auto" />
            </div>
            <div>
              <h1 class="text-sm font-bold">BPPU</h1>
              <p class="text-xs text-[#c1a366]">IKIP Siliwangi</p>
            </div>
          </div>
          <button
            @click="sidebarOpen = false"
            class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto sidebar-scroll">
          <!-- Dashboard Section - Hide for data_clerk and pure PoS users -->
          <Link
            v-if="!isDataClerk && !isPurePoSUser"
            href="/pengelola/dasbor"
            :class="[
              'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
              isActive('/pengelola/dasbor')
                ? 'bg-white/20 text-white shadow-lg'
                : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-medium">Beranda</span>
          </Link>

          <!-- Konten Website Section - Officer, Sysadmin & Data Clerk (Hide for pure PoS users) -->
          <div v-if="!isPurePoSUser && (canManageContent || canManagePages || canManageNavbar || canManageArchives)" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Konten Website
            </h3>
            <div class="space-y-1">
              <Link
                v-if="canManageContent"
                href="/pengelola/pos"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/pos')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <span class="font-medium">Pos Berita</span>
              </Link>

              <Link
                v-if="canManagePages"
                href="/pengelola/halaman"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/halaman')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">Halaman</span>
              </Link>

              <Link
                v-if="canManageNavbar"
                href="/pengelola/menu"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/menu')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="font-medium">Menu Navbar</span>
              </Link>

              <Link
                v-if="canManageArchives"
                href="/pengelola/arsip"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/arsip')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">Media Arsip</span>
              </Link>
            </div>
          </div>

          <!-- Produk & Layanan Section - Officer, Shop, Sysadmin & Data Clerk (Hide for pure PoS users) -->
          <div v-if="!isPurePoSUser && (canManageCanteenMenu || canManageShopping)" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Produk & Layanan
            </h3>
            <div class="space-y-1">
              <Link
                v-if="canManageCanteenMenu"
                href="/pengelola/menu-kantin"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/menu-kantin')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="font-medium">Menu Kantin</span>
              </Link>

              <Link
                v-if="canManageShopping"
                href="/pengelola/belanja"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/belanja')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="font-medium">Item Belanja</span>
              </Link>

              <Link
                v-if="canManageContent"
                href="/pengelola/meja"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/meja')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="font-medium">Meja</span>
              </Link>
            </div>
          </div>

          <!-- Keuangan & Inventori Section - Officer, Sysadmin, Head & Data Clerk (Hide for pure PoS users) -->
          <div v-if="!isPurePoSUser && (canManageContent || canViewBukuKas || canManageStock)" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Keuangan & Inventori
            </h3>
            <div class="space-y-1">
              <Link
                v-if="canViewBukuKas"
                href="/pengelola/buku-kas"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/buku-kas') || $page.url.startsWith('/pengelola/buku-kas/')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="font-medium">Buku Kas</span>
                <span v-if="isHead" class="text-xs bg-blue-500/20 text-blue-200 px-2 py-0.5 rounded">View</span>
              </Link>

              <Link
                v-if="canManageStock"
                href="/pengelola/opname-stock"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/opname-stock')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="font-medium">Opname Stock</span>
              </Link>

              <!-- Diskon & Voucher - Sysadmin & Officer only -->
              <Link
                v-if="canManageContent"
                href="/pengelola/diskon"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/diskon')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span class="font-medium">Diskon & Voucher</span>
              </Link>
            </div>
          </div>

          <!-- Transaksi Penjualan Section - Officer, Kantin & Sysadmin -->
          <div v-if="canAccessPoS" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Transaksi Penjualan
            </h3>
            <div class="space-y-1">
              <Link
                href="/pengelola/penjualan"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/penjualan')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="font-medium">Point of Sale</span>
              </Link>

              <Link
                v-if="canViewRekapPenjualan"
                href="/pengelola/rekap-penjualan"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/rekap-penjualan')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="font-medium flex-1">Rekap Penjualan</span>
                <span v-if="isHead" class="text-xs bg-blue-500/20 text-blue-200 px-2 py-0.5 rounded">View</span>
                <span
                  v-if="totalPencairanNotif > 0 && !isHead"
                  class="min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold bg-red-500 text-white rounded-full px-1"
                >{{ totalPencairanNotif }}</span>
              </Link>

              <Link
                v-if="canManageContent"
                href="/live-transaction"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/live-transaction')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="font-medium">Live Transaction</span>
              </Link>
            </div>
          </div>

          <!-- Laporan Section - Officer & Sysadmin (Hide for pure PoS users) -->
          <div v-if="!isPurePoSUser && (canManageContent || canManageUsers)" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Laporan
            </h3>
            <div class="space-y-1">
              <Link
                v-if="canManageContent"
                href="/pengelola/laporan-penjualan"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/laporan-penjualan')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-medium">Laporan Penjualan</span>
              </Link>

              <Link
                v-if="canManageUsers"
                href="/pengelola/laporan"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/laporan')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-medium">Laporan Umum</span>
              </Link>
            </div>
          </div>

          <!-- Master Data Section - Sysadmin, Officer & Data Clerk (Hide for pure PoS users) -->
          <div v-if="!isPurePoSUser && (canManageUsersBuyer || canManageSupplier)" class="pt-4">
            <h3 class="px-4 mb-2 text-xs font-semibold text-[#c1a366] uppercase tracking-wider">
              Master Data
            </h3>
            <div class="space-y-1">
              <Link
                v-if="canManageUsers"
                href="/pengelola/survey"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActiveParent('/pengelola/survey')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="font-medium">Hasil Survey</span>
              </Link>

              <Link
                v-if="canManageUsersBuyer"
                href="/pengelola/manajemen-pengguna"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/manajemen-pengguna')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-medium">Pengguna</span>
              </Link>

              <Link
                v-if="isSysadmin"
                href="/pengelola/activity-log"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/activity-log')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="font-medium">Log Aktivitas</span>
              </Link>

              <Link
                v-if="canManageSupplier"
                href="/pengelola/mitra-usaha"
                :class="[
                  'flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                  isActive('/pengelola/mitra-usaha')
                    ? 'bg-white/20 text-white shadow-lg'
                    : 'text-[#d6c199] hover:bg-white/10 hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="font-medium">Mitra Usaha</span>
                <span v-if="isHead" class="text-xs bg-blue-500/20 text-blue-200 px-2 py-0.5 rounded">View</span>
              </Link>

              <!-- Unit Kerja Submenu - Sysadmin only -->
              <div v-if="canManageUsers && workUnits.length > 0">
                <button
                  @click="unitKerjaOpen = !unitKerjaOpen"
                  :class="[
                    'w-full flex items-center justify-between space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200',
                    'text-[#d6c199] hover:bg-white/10 hover:text-white'
                  ]"
                >
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="font-medium">Unit Kerja</span>
                  </div>
                  <svg
                    :class="['w-4 h-4 transition-transform duration-200', unitKerjaOpen ? 'rotate-180' : '']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div
                  v-show="unitKerjaOpen"
                  class="mt-1 ml-4 space-y-1 border-l-2 border-[#7a5100]/50 pl-4"
                >
                  <Link
                    v-for="unit in workUnits"
                    :key="unit.id"
                    :href="`/pengelola/unit-kerja/${unit.unit_id}`"
                    :class="[
                      'flex items-center justify-between px-4 py-2 rounded-lg transition-all duration-200',
                      isActive(`/pengelola/unit-kerja/${unit.unit_id}`)
                        ? 'bg-white/10 text-white'
                        : 'text-[#c1a366] hover:bg-white/5 hover:text-white'
                    ]"
                  >
                    <span class="text-sm">{{ unit.name }}</span>
                    <span
                      v-if="!unit.is_active"
                      class="text-xs px-1.5 py-0.5 bg-red-500/20 text-red-200 rounded"
                    >
                      Nonaktif
                    </span>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </nav>
      </div>
    </aside>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top bar -->
      <header class="sticky top-0 z-30 bg-[#f4efe5]/95 border-b border-[#e0d1b2] shadow-sm backdrop-blur">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
          <button
            @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg text-[#6b4700] hover:bg-[#eae0cc] transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex flex-col">
            <h2 class="text-base md:text-xl font-bold text-[#5b3d00]">{{ pageTitle }}</h2>
            <p v-if="pageSubtitle" class="text-xs md:text-sm text-[#7a5100] mt-0.5">{{ pageSubtitle }}</p>
          </div>

          <div class="flex items-center space-x-4">
            <!-- User Dropdown -->
            <div class="relative">
              <button
                @click="profileDropdownOpen = !profileDropdownOpen"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-[#eae0cc] transition-colors"
              >
                <div class="hidden sm:block text-right">
                  <p class="text-sm font-medium text-[#5b3d00]">{{ page.props.auth.user?.name || 'Admin User' }}</p>
                  <p class="text-xs text-[#7a5100]">{{ page.props.auth.user?.username || 'Administrator' }}</p>
                </div>
                <div class="w-10 h-10 bg-[#996600] rounded-full flex items-center justify-center text-white font-semibold">
                  {{ (page.props.auth.user?.name || 'A').charAt(0).toUpperCase() }}
                </div>
                <svg class="w-4 h-4 text-[#7a5100]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div
                  v-show="profileDropdownOpen"
                  class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                >
                  <!-- User Info (Mobile) -->
                  <div class="sm:hidden px-4 py-3 border-b border-gray-200">
                    <p class="text-sm font-medium text-gray-700">{{ page.props.auth.user?.name || 'Admin User' }}</p>
                    <p class="text-xs text-gray-500">{{ page.props.auth.user?.username || 'Administrator' }}</p>
                  </div>

                  <!-- Edit Profil -->
                  <Link
                    href="/pengelola/profil"
                    class="flex items-center space-x-3 px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition-colors"
                    @click="profileDropdownOpen = false"
                  >
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-sm">Edit Profil</span>
                  </Link>

                  <!-- Ganti Password -->
                  <button
                    @click="openChangePassword"
                    class="w-full flex items-center space-x-3 px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="text-sm">Ganti Password</span>
                  </button>

                  <!-- Setting -->
                  <Link
                    href="/pengelola/setting"
                    class="flex items-center space-x-3 px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition-colors"
                    @click="profileDropdownOpen = false"
                  >
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm">Setting</span>
                  </Link>

                  <div class="border-t border-gray-200 my-1"></div>

                  <!-- Logout -->
                  <button
                    @click="logout"
                    class="w-full flex items-center space-x-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-sm font-medium">Logout</span>
                  </button>
                </div>
              </Transition>
            </div>
          </div>
        </div>
        <slot name="sub-navbar" />
      </header>

      <!-- Page content -->
      <main class="p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>

    <!-- Change Password Modal -->
    <ChangePasswordModal
      :show="page.props.auth.user?.must_change_password || showChangePasswordModal"
      :errors="page.props.errors || {}"
      @close="showChangePasswordModal = false"
    />

    <!-- Flash Message Notification -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div
        v-if="showFlash && (page.props.flash?.success || page.props.flash?.error)"
        class="fixed bottom-4 right-4 z-50 max-w-md"
      >
        <div
          :class="[
            'border-l-4 p-4 shadow-lg rounded-lg',
            page.props.flash?.success ? 'bg-green-50 border-green-400' : 'bg-red-50 border-red-400'
          ]"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg
                v-if="page.props.flash?.success"
                class="h-5 w-5 text-green-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <svg
                v-else
                class="h-5 w-5 text-red-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3">
              <p
                :class="[
                  'text-sm font-medium',
                  page.props.flash?.success ? 'text-green-800' : 'text-red-800'
                ]"
              >
                {{ page.props.flash?.success || page.props.flash?.error }}
              </p>
            </div>
            <div class="ml-auto pl-3">
              <button
                @click="showFlash = false"
                :class="[
                  'inline-flex focus:outline-none',
                  page.props.flash?.success ? 'text-green-400 hover:text-green-600' : 'text-red-400 hover:text-red-600'
                ]"
              >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ChangePasswordModal from '@/Components/ChangePasswordModal.vue';
import axios from 'axios';

const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard'
  },
  pageSubtitle: {
    type: String,
    default: ''
  }
});

const sidebarOpen = ref(false);
const unitKerjaOpen = ref(true);
const showFlash = ref(false);
const workUnits = ref([]);
const profileDropdownOpen = ref(false);
const showChangePasswordModal = ref(false);
const previousMenungguCount = ref(null);

const page = usePage();
let orderNotifInterval = null;

// Watch for flash messages
watch(() => page.props.flash, (newFlash) => {
  if (newFlash?.success || newFlash?.error) {
    showFlash.value = true;
    setTimeout(() => {
      showFlash.value = false;
    }, 5000);
  }
}, { deep: true, immediate: true });

const isActive = (path) => {
  return page.url === path;
};

const isActiveParent = (path) => {
  return page.url.startsWith(path);
};

// Role checking helpers
const hasRole = (roleName) => {
  return page.props.auth.user?.roles?.includes(roleName) || false;
};

const hasAnyRole = (...roleNames) => {
  return roleNames.some(role => hasRole(role));
};

const pembagianMengajukanCount = computed(() => page.props.pembagianMengajukanCount || 0)
const pengajuanPencairanCount = computed(() => page.props.pengajuanPencairanCount || 0)
const totalPencairanNotif = computed(() => pembagianMengajukanCount.value + pengajuanPencairanCount.value)

const canManageContent = computed(() => hasAnyRole('officer', 'sysadmin'));
const canManageCanteenMenu = computed(() => hasAnyRole('officer', 'sysadmin', 'data_clerk'));
const canManageUsers = computed(() => hasAnyRole('sysadmin', 'officer'));
const canManageUsersBuyer = computed(() => hasAnyRole('sysadmin', 'officer', 'data_clerk'));
const canManagePages = computed(() => hasAnyRole('officer', 'sysadmin'));
const canManageNavbar = computed(() => hasAnyRole('officer', 'sysadmin'));
const canManageShopping = computed(() => hasAnyRole('officer', 'shop', 'sysadmin', 'data_clerk'));
const canAccessPoS = computed(() => hasAnyRole('officer', 'canteen', 'shop', 'sysadmin'));
const canViewRekapPenjualan = computed(() => hasAnyRole('canteen', 'shop', 'officer', 'sysadmin', 'head'));
const canManageSupplier = computed(() => hasAnyRole('officer', 'sysadmin', 'head'));
const canViewBukuKas = computed(() => hasAnyRole('officer', 'sysadmin', 'head'));
const canManageStock = computed(() => hasAnyRole('officer', 'sysadmin', 'data_clerk'));
const isHead = computed(() => hasRole('head'));
const canManageArchives = computed(() => hasAnyRole('officer', 'sysadmin', 'data_clerk'));
const isDataClerk = computed(() => hasRole('data_clerk'));
const isSysadmin = computed(() => hasRole('sysadmin'));
const isCanteen = computed(() => hasRole('canteen') && !hasAnyRole('officer', 'sysadmin'));
const isShop = computed(() => hasRole('shop') && !hasAnyRole('officer', 'sysadmin'));
const isPurePoSUser = computed(() => isCanteen.value || isShop.value);
const shouldEnableOrderNotifPolling = computed(() => {
  if (!hasRole('canteen')) return false;

  return (
    page.url.startsWith('/pengelola/penjualan') ||
    page.url.startsWith('/pengelola/rekap-penjualan')
  );
});

// Load work units from API
const loadWorkUnits = async () => {
  if (!canManageUsers.value) return;

  try {
    const response = await axios.get('/pengelola/unit-kerja');
    workUnits.value = response.data.filter(unit => unit.is_active);
  } catch (error) {
    console.error('Error loading work units:', error);
  }
};

const logout = () => {
  profileDropdownOpen.value = false;
  router.post('/pengelola/logout');
};

const openChangePassword = () => {
  profileDropdownOpen.value = false;
  showChangePasswordModal.value = true;
};

const pollKantinOrderNotifications = async () => {
  if (!shouldEnableOrderNotifPolling.value) return;

  try {
    const response = await fetch('/api/self-order/pesanan-kantin', {
      headers: { Accept: 'application/json' },
    });
    if (!response.ok) return;

    const data = await response.json();
    const currentMenunggu = data.stats?.menunggu ?? 0;

    if (
      previousMenungguCount.value !== null &&
      currentMenunggu > previousMenungguCount.value &&
      'Notification' in window &&
      Notification.permission === 'granted'
    ) {
      new Notification('Pesanan Baru Masuk!', {
        body: `Ada ${currentMenunggu} antrian menunggu konfirmasi`,
        icon: '/logo-BPPU-flat.png',
      });
    }

    previousMenungguCount.value = currentMenunggu;
  } catch {
    // silent
  }
};

const startOrderNotifPolling = () => {
  if (orderNotifInterval) return;

  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

  pollKantinOrderNotifications();
  orderNotifInterval = setInterval(pollKantinOrderNotifications, 10000);
};

const stopOrderNotifPolling = () => {
  if (orderNotifInterval) {
    clearInterval(orderNotifInterval);
    orderNotifInterval = null;
  }
  previousMenungguCount.value = null;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = document.querySelector('.absolute.right-0.mt-2.w-56');
  const button = event.target.closest('button');

  if (profileDropdownOpen.value && dropdown && !dropdown.contains(event.target) && !button?.closest('.flex.items-center.space-x-3.px-3.py-2')) {
    profileDropdownOpen.value = false;
  }
};

// Load work units on mount
onMounted(() => {
  loadWorkUnits();
  document.addEventListener('click', handleClickOutside);

  if (shouldEnableOrderNotifPolling.value) {
    startOrderNotifPolling();
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  stopOrderNotifPolling();
});

watch(shouldEnableOrderNotifPolling, (enabled) => {
  if (enabled) {
    startOrderNotifPolling();
    return;
  }

  stopOrderNotifPolling();
});
</script>

<style scoped>
/* Custom scrollbar untuk sidebar - elegant dan hanya muncul saat hover/scroll */
.sidebar-scroll {
  scrollbar-width: thin;
  scrollbar-color: transparent transparent;
  transition: scrollbar-color 0.3s ease;
}

.sidebar-scroll:hover,
.sidebar-scroll:active {
  scrollbar-color: rgba(193, 163, 102, 0.5) transparent;
}

/* Webkit browsers (Chrome, Safari, Edge) */
.sidebar-scroll::-webkit-scrollbar {
  width: 6px;
}

.sidebar-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
  background: transparent;
  border-radius: 3px;
  transition: background 0.3s ease;
}

.sidebar-scroll:hover::-webkit-scrollbar-thumb {
  background: rgba(193, 163, 102, 0.3);
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(193, 163, 102, 0.6);
}

.sidebar-scroll::-webkit-scrollbar-thumb:active {
  background: rgba(193, 163, 102, 0.8);
}
</style>
