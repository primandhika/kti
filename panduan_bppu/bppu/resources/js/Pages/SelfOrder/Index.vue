<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Toast :message="toastMessage" :type="toastType" @close="clearToast" />
        <LoginModal
            :show="showLoginModal"
            :cancelable="loginFromCheckout"
            @close="showLoginModal = false; loginFromCheckout = false"
        />

        <!-- Header fixed -->
        <header class="fixed top-0 left-0 right-0 z-50" id="self-order-header">
            <div class="bg-white border-b-4 border-[#996600] shadow-md">
                <div class="h-1 bg-gradient-to-r from-[#996600] via-[#c1a366] to-[#996600]"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <Link href="/" class="flex items-center gap-1 hover:opacity-75 transition-opacity">
                                <svg class="w-4 h-4 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </Link>
                            <img src="/logo-BPPU-flat.png" alt="Logo BPPU" class="w-10 h-10 object-contain" />
                            <div>
                                <h1 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Kantin BPPU</h1>
                                <p class="text-xs text-[#996600] font-medium">IKIP Siliwangi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="showSearch = !showSearch"
                                class="p-2 hover:bg-[#f4efe5] rounded-lg transition-colors"
                                :class="showSearch ? 'bg-[#f4efe5] text-[#996600]' : 'text-gray-600'"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <button
                                v-if="lockedMeja"
                                @click="toggleFullscreen"
                                class="p-2 hover:bg-[#f4efe5] rounded-lg transition-colors"
                                :class="isFullscreen ? 'bg-[#f4efe5] text-[#996600]' : 'text-gray-600'"
                                :title="isFullscreen ? 'Keluar fullscreen' : 'Fullscreen'"
                            >
                                <svg v-if="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-7 11H4m0 0v4m0-4l5 5m7-5h4m-4 0v4m0-4l-5 5" />
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4m0 5H4m0 0l5-5M9 15v5m0-5H4m5 5l-5-5m11-5h5m-5 0V4m5 5l-5-5m5 11h-5m5 0v5m-5-5l5 5" />
                                </svg>
                            </button>
                            <div v-if="isAuthenticated && user" class="flex-shrink-0">
                                <UserDropdown :user="user" />
                            </div>
                        </div>
                    </div>


                    <transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-32"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 max-h-32"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-show="showSearch" class="overflow-hidden mt-2 space-y-1.5">
                            <input
                                ref="searchInput"
                                type="text"
                                v-model="quickSearch"
                                @input="handleQuickSearch"
                                placeholder="Cari menu atau varian..."
                                class="w-full px-3 py-2 text-sm border border-[#ccb27f] rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent bg-white"
                            />
                            <div v-if="searchSuggestions.length > 0 && quickSearch" class="text-xs text-gray-600 px-1">
                                <span>Mungkin: </span>
                                <button
                                    v-for="(s, idx) in searchSuggestions"
                                    :key="idx"
                                    @click="applySuggestion(s.text)"
                                    class="text-[#996600] font-semibold underline"
                                >"{{ s.text }}"{{ idx < searchSuggestions.length - 1 ? ', ' : '' }}</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <FilterSubNavbar
                :filters="filters"
                :categories="categories"
                :sub-kategories="subKategories"
                :show-out-of-stock="filters.show_habis"
                @update:showOutOfStock="handleToggleShowHabis"
            />
        </header>

        <!-- Content -->
        <div :class="{'filter blur-sm pointer-events-none': !isAuthenticated && !lockedMeja}" :style="{ paddingTop: headerHeight + 'px' }">

            <!-- Kantin Picker -->
            <div v-if="workUnits && workUnits.length > 0 && !filters.work_unit" class="bg-gradient-to-r from-[#f4efe5] via-white to-[#f4efe5] border-b border-[#ccb27f]/30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <p class="text-xs font-semibold text-[#996600] uppercase tracking-wide mb-3">Pilih Kantin</p>
                    <div
                        ref="kantinScroller"
                        class="flex gap-3 overflow-x-auto scrollbar-hide pb-1 cursor-grab active:cursor-grabbing select-none"
                        @mousedown="startDrag"
                        @mousemove="onDrag"
                        @mouseup="endDrag"
                        @mouseleave="endDrag"
                    >
                        <button
                            v-for="workUnit in workUnits"
                            :key="workUnit.id"
                            @click="handleKantinClick($event, workUnit.id)"
                            class="flex-shrink-0 flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-gray-200 bg-white transition-all duration-200 min-w-[90px] max-w-[110px] group hover:border-[#996600] hover:bg-[#f4efe5]"
                        >
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-[#eae0cc] flex items-center justify-center flex-shrink-0 ring-2 ring-[#ccb27f] group-hover:ring-[#996600] transition-all">
                                <img v-if="workUnit.logo" :src="`/storage/${workUnit.logo}`" :alt="workUnit.name" class="w-full h-full object-cover" draggable="false" />
                                <svg v-else class="w-6 h-6 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-800 text-center leading-tight line-clamp-2 pointer-events-none">{{ workUnit.name }}</span>
                            <span v-if="workUnit.location" class="text-[10px] text-gray-500 text-center truncate w-full pointer-events-none">{{ workUnit.location }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Kantin Banner (+ chip meja jika locked) -->
            <div v-if="(filters.work_unit && activeWorkUnit) || lockedMeja" class="bg-[#f4efe5] border-b border-[#ccb27f]/40">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div v-if="activeWorkUnit" class="w-9 h-9 rounded-full overflow-hidden bg-[#eae0cc] flex items-center justify-center ring-2 ring-[#996600] flex-shrink-0">
                                <img v-if="activeWorkUnit.logo" :src="`/storage/${activeWorkUnit.logo}`" :alt="activeWorkUnit.name" class="w-full h-full object-cover" />
                                <svg v-else class="w-5 h-5 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p v-if="activeWorkUnit" class="text-xs text-gray-500">Menampilkan menu dari</p>
                                <p v-if="activeWorkUnit" class="text-sm font-bold text-[#996600] truncate">{{ activeWorkUnit.name }}</p>
                            </div>
                            <!-- Chip meja -->
                            <div v-if="lockedMeja" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-[#ccb27f] rounded-full text-xs font-medium text-[#7a5100] flex-shrink-0">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span v-if="lockedMeja.lokasi">{{ lockedMeja.lokasi }} &mdash;</span>
                                <span class="font-bold">{{ lockedMeja.nama || lockedMeja.kode_meja }}</span>
                                <span v-if="lockedMeja.nomor" class="text-[#996600]">No. {{ lockedMeja.nomor }}</span>
                            </div>
                        </div>
                        <button v-if="!lockedMeja" @click="filterByWorkUnit(null)" class="text-xs text-gray-500 hover:text-gray-700 underline flex-shrink-0">
                            Semua kantin
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main layout: content + cart sidebar -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex gap-6 items-start">

                    <!-- Left: Menu Content -->
                    <div class="flex-1 min-w-0 pb-24 lg:pb-0" :class="(isAuthenticated || lockedMeja) ? 'lg:pr-4' : ''">

                        <!-- Featured Carousel -->
                        <div v-if="featuredMenus && featuredMenus.length > 0 && !filters.search && !filters.sub_kategori && !lockedMeja" class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Menu Spesial
                                </h2>
                                <div class="hidden md:flex items-center gap-1.5">
                                    <button @click="scrollFeatured('left')" class="p-1.5 rounded-full bg-white hover:bg-gray-100 shadow-md">
                                        <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button @click="scrollFeatured('right')" class="p-1.5 rounded-full bg-white hover:bg-gray-100 shadow-md">
                                        <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div ref="carouselContainer" class="flex gap-4 overflow-x-auto scroll-smooth scrollbar-hide pb-3">
                                <div
                                    v-for="menu in featuredMenus"
                                    :key="menu.id"
                                    class="flex-shrink-0 w-full md:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)]"
                                >
                                    <FeaturedMenuCard :menu="menu" @order="handleOrder" />
                                </div>
                            </div>
                            <div class="flex justify-center gap-1.5 mt-2">
                                <div
                                    v-for="(_, index) in featuredMenus" :key="index"
                                    class="h-1.5 rounded-full transition-all cursor-pointer"
                                    :class="currentCarouselPage === index ? 'w-6 bg-[#996600]' : 'w-1.5 bg-gray-300'"
                                    @click="scrollToCard(index)"
                                ></div>
                            </div>
                        </div>

                        <!-- Results count -->
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ menus?.data?.length || 0 }}</span> dari
                                <span class="font-semibold text-gray-900">{{ menus?.total || 0 }}</span> menu
                                <span v-if="filters.search"> untuk "<span class="font-semibold">{{ filters.search }}</span>"</span>
                            </p>
                        </div>

                        <!-- Search suggestions -->
                        <div v-if="filters.search && menus?.total < 3 && searchSuggestions.length > 0" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-gray-700">
                            Mungkin maksud kamu:
                            <button v-for="(s, idx) in searchSuggestions.slice(0, 3)" :key="idx" @click="applySuggestion(s.text)" class="ml-2 text-[#996600] font-semibold underline">
                                "{{ s.text }}"{{ idx < Math.min(searchSuggestions.length, 3) - 1 ? ',' : '' }}
                            </button>
                        </div>

                        <!-- Empty State -->
                        <div v-if="!menus?.data || menus.data.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
                            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Menu Tidak Ditemukan</h3>
                            <p class="text-gray-500 text-sm">Coba ubah filter atau kata kunci pencarian</p>
                        </div>

                        <!-- Menu Grid -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
                            <MenuCard v-for="menu in menus.data" :key="menu.id" :menu="menu" @order="handleOrder" />
                        </div>

                        <!-- Pagination -->
                        <div v-if="menus?.data && menus.data.length > 0 && menus.last_page > 1" class="mt-6">
                            <div class="bg-white rounded-xl shadow-sm p-4 md:p-5">
                                <div class="hidden md:flex items-center justify-between">
                                    <Link :href="addMejaToUrl(menus.prev_page_url)" :class="{'pointer-events-none opacity-50': !menus.prev_page_url}" class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        Sebelumnya
                                    </Link>
                                    <div class="flex items-center gap-2">
                                        <template v-for="page in paginationPages" :key="page">
                                            <span v-if="page === '...'" class="px-2 text-gray-400">...</span>
                                            <Link v-else :href="`/kantin/self-order?page=${page}&${queryString}`" :class="page === menus.current_page ? 'bg-[#996600] text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'" class="px-3 py-1.5 rounded-lg font-semibold text-sm transition-colors">{{ page }}</Link>
                                        </template>
                                    </div>
                                    <Link :href="addMejaToUrl(menus.next_page_url)" :class="{'pointer-events-none opacity-50': !menus.next_page_url}" class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors flex items-center gap-2 text-sm">
                                        Selanjutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </Link>
                                </div>
                                <div class="md:hidden flex items-center justify-between gap-2">
                                    <Link :href="addMejaToUrl(menus.prev_page_url)" :class="{'pointer-events-none opacity-50': !menus.prev_page_url}" class="px-3 py-2 bg-[#996600] text-white rounded-lg text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </Link>
                                    <span class="text-sm text-gray-600">{{ menus.current_page }} / {{ menus.last_page }}</span>
                                    <Link :href="addMejaToUrl(menus.next_page_url)" :class="{'pointer-events-none opacity-50': !menus.next_page_url}" class="px-3 py-2 bg-[#996600] text-white rounded-lg text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Cart Sidebar placeholder (desktop only) — actual sidebar is fixed -->
                    <div v-if="isAuthenticated || lockedMeja" class="hidden lg:block w-72 flex-shrink-0"></div>
                </div>
            </div>

            <footer class="bg-white border-t border-gray-200 mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
                    <p class="text-gray-600 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-[#996600]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        Buka setiap hari Senin - Sabtu, 07:00 - 15:00 WIB
                    </p>
                </div>
            </footer>
        </div>

        <!-- Desktop Cart Sidebar (fixed, ngikut scroll) -->
        <div
            v-if="isAuthenticated || lockedMeja"
            class="hidden lg:flex flex-col fixed z-40"
            :style="{
                top: (headerHeight + 16) + 'px',
                right: 'max(16px, calc((100vw - 1280px) / 2 + 16px))',
                width: '288px',
                maxHeight: `calc(100vh - ${headerHeight + 32}px)`
            }"
        >
            <CartSidebar
                :cart-items="cartItems"
                :total-items="totalItems"
                :total-price="totalPrice"
                :is-empty="isEmpty"
                :is-checking-out="isCheckingOut"
                :catatan="catatan"
                :selected-meja="selectedMeja"
                :is-meja-locked="isMejaLocked"
                :minimal-order="selfOrderConfig?.minimal_order ?? 0"
                :biaya-layanan-aktif="selfOrderConfig?.biaya_layanan_aktif ?? false"
                :biaya-layanan="selfOrderConfig?.biaya_layanan ?? 0"
                :metode-bayar="metodeBayar"
                @increase="handleIncreaseItem"
                @decrease="handleDecreaseItem"
                @clear="handleClearCart"
                @checkout="handleCheckout"
                @update:catatan="catatan = $event"
                @update:metodeBayar="metodeBayar = $event"
                @open-meja-picker="showMejaPickerModal = true"
                @clear-meja="clearMeja"
            />
        </div>

        <!-- Mobile Cart Bottom Bar -->
        <CartBottomBar
            v-if="(isAuthenticated || lockedMeja) && !showVarianModal"
            :cart-items="cartItems"
            :total-items="totalItems"
            :total-price="totalPrice"
            :is-empty="isEmpty"
            :is-checking-out="isCheckingOut"
            :catatan="catatan"
            :selected-meja="selectedMeja"
            :is-meja-locked="isMejaLocked"
            :minimal-order="selfOrderConfig?.minimal_order ?? 0"
            :biaya-layanan-aktif="selfOrderConfig?.biaya_layanan_aktif ?? false"
            :biaya-layanan="selfOrderConfig?.biaya_layanan ?? 0"
            :metode-bayar="metodeBayar"
            @increase="handleIncreaseItem"
            @decrease="handleDecreaseItem"
            @clear="handleClearCart"
            @checkout="handleCheckout"
            @update:catatan="catatan = $event"
            @update:metodeBayar="metodeBayar = $event"
            @open-meja-picker="showMejaPickerModal = true"
            @clear-meja="clearMeja"
        />

        <!-- Varian Modal -->
        <VarianModal
            :show="showVarianModal"
            :menu="selectedMenuForVarian"
            @close="showVarianModal = false"
            @add="handleAddToCartFromVarian"
        />

        <!-- Meja Picker Modal -->
        <MejaPickerModal
            :show="showMejaPickerModal"
            :lokasi-mejas="lokasiMejas"
            :current-meja="selectedMeja"
            @close="showMejaPickerModal = false"
            @select="(meja) => { setMeja(meja); showMejaPickerModal = false; }"
        />

        <!-- Kantin Picker Modal (muncul otomatis saat pertama buka) -->
        <KantinPickerModal
            :show="showKantinPicker"
            :work-units="workUnits"
            :required="isAuthenticated && !lockedMeja"
            @select="handleKantinPickerSelect"
        />

        <!-- Modal nama pemesan untuk guest via meja token -->
        <NamaPemesanModal
            :show="showNamaPemesanModal"
            :initial-nama="namaPemesan"
            @close="showNamaPemesanModal = false"
            @confirm="handleNamaPemesanConfirm"
            @open-login="showNamaPemesanModal = false; loginFromCheckout = true; showLoginModal = true"
        />

        <!-- Modal konfirmasi tipe pengiriman -->
        <TipePengirimanModal
            :show="showTipePengirimanModal"
            @close="showTipePengirimanModal = false"
            @confirm="handleTipePengirimanConfirm"
        />
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import MenuCard from '@/Components/SelfOrder/MenuCard.vue';
import FeaturedMenuCard from '@/Components/SelfOrder/FeaturedMenuCard.vue';
import FilterSubNavbar from '@/Components/SelfOrder/FilterSubNavbar.vue';
import CartSidebar from '@/Components/SelfOrder/CartSidebar.vue';
import CartBottomBar from '@/Components/SelfOrder/CartBottomBar.vue';
import LoginModal from '@/Components/SelfOrder/LoginModal.vue';
import UserDropdown from '@/Components/SelfOrder/UserDropdown.vue';
import Toast from '@/Components/SelfOrder/Toast.vue';
import VarianModal from '@/Components/SelfOrder/VarianModal.vue';
import MejaPickerModal from '@/Components/SelfOrder/MejaPickerModal.vue';
import { useCartSelfOrder } from '@/composables/useCartSelfOrder';
import { useMeja } from '@/composables/useMeja';
import { useSound } from '@/composables/useSound';
import { findBestMatches, extractSearchableTerms } from '@/utils/fuzzySearch';
import KantinPickerModal from '@/Components/SelfOrder/KantinPickerModal.vue';
import NamaPemesanModal from '@/Components/SelfOrder/NamaPemesanModal.vue';
import TipePengirimanModal from '@/Components/SelfOrder/TipePengirimanModal.vue';

const props = defineProps({
    menus: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    subKategories: { type: Array, default: () => [] },
    workUnits: { type: Array, default: () => [] },
    featuredMenus: { type: Array, default: () => [] },
    lokasiMejas: { type: Array, default: () => [] },
    lockedMeja: { type: Object, default: null },
    defaultMejaUser: { type: Object, default: null },
    filters: { type: Object, default: () => ({}) },
    isAuthenticated: { type: Boolean, default: false },
    user: { type: Object, default: null },
    selfOrderConfig: {
        type: Object,
        default: () => ({ minimal_order: 0, biaya_layanan_aktif: false, biaya_layanan: 0 })
    },
});

// Header height measurement (for dynamic sticky positioning)
const headerHeight = ref(112); // default estimate
const measureHeader = () => {
    const el = document.getElementById('self-order-header');
    if (el) headerHeight.value = el.offsetHeight;
};

const showSearch = ref(false);
const searchInput = ref(null);

// Fullscreen
const isFullscreen = ref(false);
const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen().catch(() => {});
    }
};
const onFullscreenChange = () => { isFullscreen.value = !!document.fullscreenElement; };
const quickSearch = ref(props.filters.search || '');
const carouselContainer = ref(null);
const kantinScroller = ref(null);
const currentCarouselPage = ref(0);
const searchSuggestions = ref([]);
const allSearchableTerms = ref([]);
let searchTimeout = null;

const {
    cartItems, addToCart, removeFromCart, updateQuantity, clearCart,
    totalItems, totalPrice, isEmpty,
} = useCartSelfOrder('bppu-kantin-cart');

const { playSFX } = useSound();

const { selectedMeja, isLocked: isMejaLocked, setMeja, lockMeja, clearMeja } = useMeja();
const showMejaPickerModal = ref(false);

const showVarianModal = ref(false);
const selectedMenuForVarian = ref(null);

// Login modal: selalu tampil jika belum auth dan tidak ada lockedMeja, atau manual trigger
const showLoginModal = ref(!props.isAuthenticated && !props.lockedMeja);

// Tutup modal login saat berhasil login (false -> true), reset flag dan paksa pilih kantin
watch(() => props.isAuthenticated, (val, oldVal) => {
    if (val && !oldVal) {
        showLoginModal.value = false;
        loginFromCheckout.value = false;
        sessionStorage.removeItem(KANTIN_PICKED_KEY);
        if (props.workUnits.length > 1 && !props.lockedMeja) {
            showKantinPicker.value = true;
        }
    }
});

// Logged user tanpa lockedMeja: paksa pilih kantin, tapi hanya sekali per sesi (pakai sessionStorage)
// lockedMeja / guest: tampilkan hanya jika belum ada work_unit
const KANTIN_PICKED_KEY = 'bppu-kantin-picked';
const kantinSudahDipilih = () => !!sessionStorage.getItem(KANTIN_PICKED_KEY);

const showKantinPicker = ref(
    props.workUnits.length > 1 &&
    !kantinSudahDipilih() &&
    !props.filters.work_unit &&
    (props.isAuthenticated || !!props.lockedMeja)
);

const page = usePage();
const toastMessage = ref('');
const toastType = ref('success');

watch(() => page.props.flash?.success, (v) => { if (v) { toastMessage.value = v; toastType.value = 'success'; } });
watch(() => page.props.flash?.error, (v) => { if (v) { toastMessage.value = v; toastType.value = 'error'; } });
const clearToast = () => { toastMessage.value = ''; };

watch(showSearch, (v) => { if (v) nextTick(() => searchInput.value?.focus()); });

const activeWorkUnit = computed(() => {
    if (!props.filters.work_unit) return null;
    return props.workUnits.find(w => w.id == props.filters.work_unit) || null;
});

onMounted(() => {
    measureHeader();
    window.addEventListener('resize', measureHeader);
    document.addEventListener('fullscreenchange', onFullscreenChange);

    // Inisialisasi meja: locked dari URL param atau default dari user
    if (props.lockedMeja) {
        lockMeja(props.lockedMeja);
        // Auto fullscreen saat akses via token meja
        document.documentElement.requestFullscreen().catch(() => {});
    } else if (props.defaultMejaUser && !selectedMeja.value) {
        setMeja(props.defaultMejaUser);
    }

    const allMenus = [...(props.menus?.data || []), ...(props.featuredMenus || [])];
    allSearchableTerms.value = extractSearchableTerms(allMenus);
    if (props.subKategories) allSearchableTerms.value.push(...props.subKategories);

    if (carouselContainer.value) {
        carouselContainer.value.addEventListener('scroll', () => {
            const c = carouselContainer.value;
            if (!c || !c.children.length) return;
            const index = Math.round(c.scrollLeft / (c.children[0].offsetWidth + 16));
            currentCarouselPage.value = Math.max(0, Math.min(index, props.featuredMenus.length - 1));
        });
    }
    if (props.filters.search) updateSearchSuggestions(props.filters.search);

    const pingInterval = setInterval(sessionPing, 5 * 60 * 1000);
    onUnmounted(() => {
        clearInterval(pingInterval);
        window.removeEventListener('resize', measureHeader);
        document.removeEventListener('fullscreenchange', onFullscreenChange);
    });
});

const updateSearchSuggestions = (term) => {
    if (!term || term.length < 3) { searchSuggestions.value = []; return; }
    searchSuggestions.value = findBestMatches(term, allSearchableTerms.value, 0.5, 5);
};

// Hanya kirim param aktif ke URL
const buildCleanParams = (overrides = {}) => {
    const f = { ...props.filters, ...overrides };
    const params = {};
    if (f.search)                               params.search      = f.search;
    if (f.kategori)                             params.kategori    = f.kategori;
    if (f.sub_kategori)                         params.sub_kategori = f.sub_kategori;
    if (f.work_unit)                            params.work_unit   = f.work_unit;
    if (f.min_price)                            params.min_price   = f.min_price;
    if (f.max_price)                            params.max_price   = f.max_price;
    if (f.sort_by && f.sort_by !== 'display_order') params.sort_by = f.sort_by;
    if (f.per_page && f.per_page !== 12)        params.per_page    = f.per_page;
    if (f.show_habis)                           params.show_habis  = f.show_habis;
    if (f.meja)                                 params.meja        = f.meja;
    if (f.page && f.page > 1)                   params.page        = f.page;
    return params;
};

const handleQuickSearch = () => {
    clearTimeout(searchTimeout);
    updateSearchSuggestions(quickSearch.value);
    searchTimeout = setTimeout(() => {
        router.get('/kantin/self-order', buildCleanParams({ search: quickSearch.value, page: 1 }), { preserveState: true, preserveScroll: true });
    }, 500);
};

const applySuggestion = (text) => {
    quickSearch.value = text;
    searchSuggestions.value = [];
    router.get('/kantin/self-order', buildCleanParams({ search: text, page: 1 }), { preserveState: true, preserveScroll: true });
};

const handleOrder = (menu) => {
    if (!props.isAuthenticated && !props.lockedMeja) {
        showLoginModal.value = true;
        return;
    }
    if (menu.varians && menu.varians.length > 0) {
        selectedMenuForVarian.value = menu;
        showVarianModal.value = true;
    } else {
        addToCart(menu, 1, null);
        playSFX('add');
        toastMessage.value = `${menu.name} ditambahkan ke keranjang`;
        toastType.value = 'success';
    }
};

const handleAddToCartFromVarian = (menu, quantity, varian) => {
    addToCart(menu, quantity, varian);
    playSFX('add');
    toastMessage.value = `${menu.name} - ${varian.nama_varian} ditambahkan`;
    toastType.value = 'success';
};

const handleIncreaseItem = (index) => { playSFX('increment'); updateQuantity(index, cartItems.value[index].quantity + 1); };
const handleDecreaseItem = (index) => {
    const item = cartItems.value[index];
    playSFX(item.quantity === 1 ? 'remove' : 'decrement');
    updateQuantity(index, item.quantity - 1);
};

const handleClearCart = () => {
    if (confirm('Yakin ingin mengosongkan keranjang?')) {
        clearCart();
        playSFX('clear');
        catatan.value = '';
        toastMessage.value = 'Keranjang dikosongkan';
        toastType.value = 'success';
    }
};

const isCheckingOut = ref(false);
const catatan = ref('');
const metodeBayar = ref(null);
const currentCsrfToken = ref(document.querySelector('meta[name="csrf-token"]')?.content || '');

// Guest checkout via meja token
const showNamaPemesanModal = ref(false);
const namaPemesan = ref('');

// Flag: login dibuka dari flow checkout (bukan modal awal)
const loginFromCheckout = ref(false);

// Tipe pengiriman
const showTipePengirimanModal = ref(false);
const tipePengiriman = ref('antar');
const estimasiAmbil = ref(null);

// Koordinat untuk area publik
const guestCoords = ref(null); // { latitude, longitude }

const getGuestLocation = () => new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
        reject(new Error('Browser tidak mendukung geolokasi.'));
        return;
    }
    navigator.geolocation.getCurrentPosition(
        (pos) => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude }),
        (err) => reject(new Error(err.code === 1 ? 'Izin lokasi ditolak. Aktifkan izin lokasi di browser untuk memesan.' : 'Gagal mendapatkan lokasi, coba lagi.')),
        { enableHighAccuracy: true, timeout: 10000 }
    );
});

const sessionPing = async () => {
    try {
        const res = await fetch('/api/self-order/ping', { method: 'GET', headers: { 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            if (data.csrf_token) {
                currentCsrfToken.value = data.csrf_token;
                document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', data.csrf_token);
            }
        }
    } catch (e) { console.warn('Session ping failed:', e); }
};

const handleCheckout = async () => {
    if (isCheckingOut.value || cartItems.value.length === 0) return;
    // Area publik + fitur verif lokasi aktif: minta izin lokasi dulu
    if (!props.isAuthenticated && props.lockedMeja) {
        if (props.selfOrderConfig?.verif_lokasi_aktif && props.lockedMeja.is_public && !guestCoords.value) {
            try {
                guestCoords.value = await getGuestLocation();
            } catch (err) {
                toastMessage.value = err.message;
                toastType.value = 'error';
                return;
            }
        }
    }
    // Selalu tanya tipe pengiriman (untuk semua user)
    showTipePengirimanModal.value = true;
};

const handleTipePengirimanConfirm = ({ tipe, estimasi }) => {
    tipePengiriman.value = tipe;
    estimasiAmbil.value = estimasi;
    showTipePengirimanModal.value = false;
    // Guest via meja token: tanya nama setelah tipe pengiriman
    if (!props.isAuthenticated && props.lockedMeja) {
        showNamaPemesanModal.value = true;
        return;
    }
    doCheckout();
};

const handleNamaPemesanConfirm = async (nama) => {
    showNamaPemesanModal.value = false;
    namaPemesan.value = nama;
    await doCheckout();
};

const doCheckout = async () => {
    if (isCheckingOut.value || cartItems.value.length === 0) return;

    // Validasi minimal order di frontend
    const minOrder = props.selfOrderConfig?.minimal_order ?? 0;
    if (minOrder > 0 && totalPrice.value < minOrder) {
        toastMessage.value = `Minimal order Rp ${minOrder.toLocaleString('id-ID')}`;
        toastType.value = 'error';
        return;
    }

    isCheckingOut.value = true;
    try {
        await sessionPing();
        const body = {
            items: cartItems.value.map(item => ({ id: item.id, name: item.name, price: item.price, quantity: item.quantity, varian: item.varian ?? null })),
            catatan: catatan.value,
            meja_id: selectedMeja.value?.id ?? null,
            metode_bayar: metodeBayar.value,
            tipe_pengiriman: tipePengiriman.value,
            estimasi_ambil: estimasiAmbil.value,
        };
        if (!props.isAuthenticated && props.lockedMeja) {
            body.nama_pemesan = namaPemesan.value;
            body.meja_qr_token = props.filters.meja;
            if (guestCoords.value) {
                body.latitude = guestCoords.value.latitude;
                body.longitude = guestCoords.value.longitude;
            }
        }
        const res = await fetch('/kantin/self-order/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken.value, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            if (data.location_required) {
                // Coba ambil lokasi lagi
                guestCoords.value = null;
                toastMessage.value = data.message;
                toastType.value = 'error';
            } else if (data.location_too_far) {
                guestCoords.value = null;
                toastMessage.value = data.message;
                toastType.value = 'error';
            } else {
                toastMessage.value = data.message || 'Gagal checkout';
                toastType.value = 'error';
            }
            return;
        }
        clearCart();
        playSFX('success');
        catatan.value = '';
        namaPemesan.value = '';
        tipePengiriman.value = 'antar';
        estimasiAmbil.value = null;
        window.location.href = `/kantin/self-order/invoice/${data.pesanan_id}`;
    } catch {
        toastMessage.value = 'Terjadi kesalahan, coba lagi';
        toastType.value = 'error';
    } finally {
        isCheckingOut.value = false;
    }
};

const handleKantinPickerSelect = (id) => {
    sessionStorage.setItem(KANTIN_PICKED_KEY, '1');
    showKantinPicker.value = false;
    if (id) {
        clearCart();
        router.get('/kantin/self-order', buildCleanParams({ work_unit: id, page: 1 }), { preserveState: false, preserveScroll: false });
    }
};

const filterByWorkUnit = (id) => {
    router.get('/kantin/self-order', buildCleanParams({ work_unit: id || '', page: 1 }), { preserveState: true, preserveScroll: false });
};

const handleToggleShowHabis = (val) => {
    router.get('/kantin/self-order', buildCleanParams({ show_habis: val ? 1 : 0, page: 1 }), { preserveState: true, preserveScroll: true });
};

// Drag scroll for kantin picker
let isDragging = false, dragStartX = 0, dragScrollLeft = 0, dragMoved = false;
const startDrag = (e) => { if (!kantinScroller.value) return; isDragging = true; dragMoved = false; dragStartX = e.pageX - kantinScroller.value.offsetLeft; dragScrollLeft = kantinScroller.value.scrollLeft; };
const onDrag = (e) => { if (!isDragging || !kantinScroller.value) return; e.preventDefault(); const walk = e.pageX - kantinScroller.value.offsetLeft - dragStartX; if (Math.abs(walk) > 5) dragMoved = true; kantinScroller.value.scrollLeft = dragScrollLeft - walk; };
const endDrag = () => { isDragging = false; };
const handleKantinClick = (e, id) => { if (dragMoved) { dragMoved = false; return; } filterByWorkUnit(id); };

const scrollFeatured = (dir) => {
    if (!carouselContainer.value) return;
    const c = carouselContainer.value;
    c.scrollLeft += dir === 'left' ? -(c.children[0]?.offsetWidth + 16) : (c.children[0]?.offsetWidth + 16);
};
const scrollToCard = (index) => {
    carouselContainer.value?.children[index]?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
};

const queryString = computed(() => {
    const p = new URLSearchParams();
    const clean = buildCleanParams();
    Object.keys(clean).forEach(k => { if (clean[k]) p.append(k, clean[k]); });
    return p.toString();
});

const addMejaToUrl = (url) => {
    if (!url || !props.lockedMeja?.qr_token) return url;
    const separator = url.includes('?') ? '&' : '?';
    return `${url}${separator}meja=${props.lockedMeja.qr_token}`;
};

const paginationPages = computed(() => {
    if (!props.menus) return [];
    const current = props.menus.current_page || 1, last = props.menus.last_page || 1, pages = [];
    if (last <= 7) { for (let i = 1; i <= last; i++) pages.push(i); }
    else if (current <= 3) { for (let i = 1; i <= 5; i++) pages.push(i); pages.push('...'); pages.push(last); }
    else if (current >= last - 2) { pages.push(1); pages.push('...'); for (let i = last - 4; i <= last; i++) pages.push(i); }
    else { pages.push(1); pages.push('...'); for (let i = current - 1; i <= current + 1; i++) pages.push(i); pages.push('...'); pages.push(last); }
    return pages;
});
</script>

<style scoped>
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
