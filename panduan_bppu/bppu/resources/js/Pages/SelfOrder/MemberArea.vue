<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Toast :message="toastMessage" :type="toastType" @close="toastMessage = ''" />

        <!-- Header -->
        <header class="bg-white border-b-4 border-[#996600] shadow-md">
            <div class="h-1 bg-gradient-to-r from-[#996600] via-[#c1a366] to-[#996600]"></div>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-3">
                <Link href="/kantin/self-order" class="p-1.5 hover:bg-[#f4efe5] rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <img src="/logo-BPPU-flat.png" alt="Logo BPPU" class="w-9 h-9 object-contain" />
                <div>
                    <h1 class="text-sm font-bold text-gray-900 leading-tight">Area Member</h1>
                    <p class="text-xs text-[#996600] font-medium">BPPU IKIP Siliwangi</p>
                </div>
            </div>
        </header>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 space-y-5">

            <!-- Profil Card -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-[#996600] text-white flex items-center justify-center font-bold text-xl flex-shrink-0">
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900 truncate">{{ localUser.name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ localUser.email }}</p>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <span class="text-xs text-[#996600] font-semibold bg-[#f4efe5] px-2 py-0.5 rounded-full">
                                {{ localUser.member_code }}
                            </span>
                            <span v-if="localUser.member_since" class="text-xs text-gray-400">
                                Sejak {{ localUser.member_since }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-sm font-bold text-gray-900">{{ localUser.total_points }}</span>
                        <span class="text-sm text-gray-500">poin tersedia</span>
                    </div>
                    <button
                        @click="openEditProfil"
                        class="text-xs font-semibold text-[#996600] hover:text-[#7a5100] transition-colors"
                    >
                        Edit Profil
                    </button>
                </div>
            </div>

            <!-- Setting Default Meja -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-900">Meja Default</h2>
                </div>
                <p class="text-xs text-gray-500 mb-4">
                    Meja ini akan otomatis dipilih saat kamu membuka halaman self-order.
                </p>

                <div v-if="currentDefaultMeja" class="flex items-center justify-between p-3 bg-[#f4efe5] border border-[#ccb27f] rounded-lg mb-3">
                    <div>
                        <p class="text-sm font-semibold text-[#996600]">{{ currentDefaultMeja.nama || currentDefaultMeja.kode_meja }}</p>
                        <p v-if="currentDefaultMeja.lokasi" class="text-xs text-gray-500">{{ currentDefaultMeja.lokasi }}</p>
                        <p v-if="currentDefaultMeja.nomor" class="text-xs text-gray-400">No. {{ currentDefaultMeja.nomor }}</p>
                    </div>
                    <button
                        @click="handleClearDefault"
                        :disabled="savingMeja"
                        class="text-xs text-red-500 hover:text-red-700 font-semibold transition-colors disabled:opacity-50"
                    >
                        Hapus
                    </button>
                </div>

                <div v-else class="p-3 bg-gray-50 rounded-lg mb-3 text-sm text-gray-400 text-center">
                    Belum ada meja default
                </div>

                <button
                    @click="showMejaPicker = true"
                    class="w-full py-2.5 text-sm font-semibold border-2 border-[#996600] text-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors"
                >
                    {{ currentDefaultMeja ? 'Ganti Meja Default' : 'Pilih Meja Default' }}
                </button>
            </div>

            <!-- Akun & Keamanan -->
            <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-100">
                <div class="px-5 py-3">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Akun & Keamanan</p>
                </div>
                <button
                    @click="openEditProfil('password')"
                    class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#f4efe5] transition-colors w-full text-left rounded-b-xl"
                >
                    <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span class="text-sm text-gray-700">Ubah Password</span>
                    <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Link ke Self Order -->
            <div class="bg-white rounded-xl shadow-sm">
                <Link
                    href="/kantin/self-order"
                    class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#f4efe5] transition-colors rounded-xl"
                >
                    <svg class="w-5 h-5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm text-gray-700">Pesan Makanan</span>
                    <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>

        </div>

        <!-- Meja Picker Modal -->
        <MejaPickerModal
            :show="showMejaPicker"
            :lokasi-mejas="lokasiMejas"
            :current-meja="currentDefaultMeja"
            @close="showMejaPicker = false"
            @select="handleSelectDefault"
        />

        <!-- Edit Profil / Password Modal -->
        <EditProfilModal
            :show="showProfilModal"
            :user="localUser"
            :init-tab="profilInitTab"
            @close="showProfilModal = false"
            @saved="handleProfilSaved"
            @password-saved="handlePasswordSaved"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MejaPickerModal from '@/Components/SelfOrder/MejaPickerModal.vue';
import Toast from '@/Components/SelfOrder/Toast.vue';
import EditProfilModal from '@/Components/MemberArea/EditProfilModal.vue';

const props = defineProps({
    user: { type: Object, required: true },
    defaultMeja: { type: Object, default: null },
    lokasiMejas: { type: Array, default: () => [] },
});

const localUser = ref({ ...props.user });
const currentDefaultMeja = ref(props.defaultMeja);
const showMejaPicker = ref(false);
const showProfilModal = ref(false);
const profilInitTab = ref('profil');
const savingMeja = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const userInitials = computed(() => {
    if (!localUser.value?.name) return '?';
    const names = localUser.value.name.split(' ');
    return names.length >= 2
        ? (names[0][0] + names[1][0]).toUpperCase()
        : localUser.value.name.substring(0, 2).toUpperCase();
});

const openEditProfil = (tab = 'profil') => {
    profilInitTab.value = tab;
    showProfilModal.value = true;
};

const handleProfilSaved = (updatedUser) => {
    localUser.value = { ...localUser.value, ...updatedUser };
    showProfilModal.value = false;
    toastMessage.value = 'Profil berhasil diperbarui.';
    toastType.value = 'success';
};

const handlePasswordSaved = () => {
    showProfilModal.value = false;
    toastMessage.value = 'Password berhasil diperbarui.';
    toastType.value = 'success';
};

const saveDefaultMeja = async (mejaId) => {
    savingMeja.value = true;
    try {
        const res = await fetch('/member/default-meja', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ meja_id: mejaId }),
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Gagal menyimpan');
        toastMessage.value = data.message;
        toastType.value = 'success';
    } catch (e) {
        toastMessage.value = e.message || 'Terjadi kesalahan';
        toastType.value = 'error';
    } finally {
        savingMeja.value = false;
    }
};

const handleSelectDefault = (meja) => {
    currentDefaultMeja.value = meja;
    showMejaPicker.value = false;
    saveDefaultMeja(meja.id);
};

const handleClearDefault = () => {
    currentDefaultMeja.value = null;
    saveDefaultMeja(null);
};
</script>
