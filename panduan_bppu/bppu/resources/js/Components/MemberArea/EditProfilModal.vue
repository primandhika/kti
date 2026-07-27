<template>
    <teleport to="body">
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center">
                <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />

                <div class="relative bg-white w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900">Pengaturan Akun</h3>
                        <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-gray-100">
                        <button
                            @click="activeTab = 'profil'"
                            class="flex-1 py-2.5 text-xs font-semibold transition-colors"
                            :class="activeTab === 'profil'
                                ? 'text-[#996600] border-b-2 border-[#996600]'
                                : 'text-gray-400 hover:text-gray-600'"
                        >
                            Profil
                        </button>
                        <button
                            @click="activeTab = 'password'"
                            class="flex-1 py-2.5 text-xs font-semibold transition-colors"
                            :class="activeTab === 'password'
                                ? 'text-[#996600] border-b-2 border-[#996600]'
                                : 'text-gray-400 hover:text-gray-600'"
                        >
                            Password
                        </button>
                    </div>

                    <!-- Tab: Profil -->
                    <form v-if="activeTab === 'profil'" @submit.prevent="handleSubmitProfil" class="p-4 space-y-3 overflow-y-auto max-h-[65vh]">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                            <input
                                v-model="profilForm.name"
                                type="text"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="profilErrors.name ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="profilErrors.name" class="text-xs text-red-500 mt-1">{{ profilErrors.name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                            <input
                                v-model="profilForm.email"
                                type="email"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="profilErrors.email ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="profilErrors.email" class="text-xs text-red-500 mt-1">{{ profilErrors.email[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <input
                                v-model="profilForm.phone"
                                type="tel"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                            />
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button
                                type="button"
                                @click="$emit('close')"
                                class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="savingProfil"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors disabled:opacity-50"
                            >
                                {{ savingProfil ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>

                    <!-- Tab: Password -->
                    <form v-if="activeTab === 'password'" @submit.prevent="handleSubmitPassword" class="p-4 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                autocomplete="current-password"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="passwordErrors.current_password ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="passwordErrors.current_password" class="text-xs text-red-500 mt-1">{{ passwordErrors.current_password[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Password Baru</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                autocomplete="new-password"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="passwordErrors.password ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="passwordErrors.password" class="text-xs text-red-500 mt-1">{{ passwordErrors.password[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                            />
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button
                                type="button"
                                @click="$emit('close')"
                                class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="savingPassword"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors disabled:opacity-50"
                            >
                                {{ savingPassword ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show:      { type: Boolean, default: false },
    user:      { type: Object, required: true },
    initTab:   { type: String, default: 'profil' },
});

const emit = defineEmits(['close', 'saved', 'password-saved']);

const activeTab     = ref('profil');
const profilForm    = ref({ name: '', email: '', phone: '' });
const profilErrors  = ref({});
const savingProfil  = ref(false);

const passwordForm   = ref({ current_password: '', password: '', password_confirmation: '' });
const passwordErrors = ref({});
const savingPassword = ref(false);

watch(() => props.show, (v) => {
    if (v) {
        activeTab.value = props.initTab || 'profil';
        profilForm.value = {
            name:  props.user.name  || '',
            email: props.user.email || '',
            phone: props.user.phone || '',
        };
        profilErrors.value  = {};
        passwordForm.value  = { current_password: '', password: '', password_confirmation: '' };
        passwordErrors.value = {};
    }
});

const handleSubmitProfil = async () => {
    savingProfil.value = true;
    profilErrors.value = {};
    try {
        const res = await fetch('/member/profil', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(profilForm.value),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            if (data.errors) profilErrors.value = data.errors;
            return;
        }
        emit('saved', data.user);
    } catch {
        profilErrors.value = { name: ['Terjadi kesalahan, coba lagi.'] };
    } finally {
        savingProfil.value = false;
    }
};

const handleSubmitPassword = async () => {
    savingPassword.value  = true;
    passwordErrors.value  = {};
    try {
        const res = await fetch('/member/password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(passwordForm.value),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            if (data.errors) passwordErrors.value = data.errors;
            return;
        }
        emit('password-saved');
    } catch {
        passwordErrors.value = { current_password: ['Terjadi kesalahan, coba lagi.'] };
    } finally {
        savingPassword.value = false;
    }
};
</script>
