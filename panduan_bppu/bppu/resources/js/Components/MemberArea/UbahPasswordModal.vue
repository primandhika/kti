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
                        <h3 class="text-sm font-bold text-gray-900">Ubah Password</h3>
                        <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="handleSubmit" class="p-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                            <input
                                v-model="form.current_password"
                                type="password"
                                autocomplete="current-password"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="errors.current_password ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="errors.current_password" class="text-xs text-red-500 mt-1">{{ errors.current_password[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Password Baru</label>
                            <input
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
                                :class="errors.password ? 'border-red-400' : 'border-gray-200'"
                            />
                            <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input
                                v-model="form.password_confirmation"
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
                                :disabled="saving"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-[#996600] hover:bg-[#7a5100] rounded-lg transition-colors disabled:opacity-50"
                            >
                                {{ saving ? 'Menyimpan...' : 'Simpan' }}
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
    show: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'saved']);

const form = ref({ current_password: '', password: '', password_confirmation: '' });
const errors = ref({});
const saving = ref(false);

watch(() => props.show, (v) => {
    if (v) {
        form.value = { current_password: '', password: '', password_confirmation: '' };
        errors.value = {};
    }
});

const handleSubmit = async () => {
    saving.value = true;
    errors.value = {};
    try {
        const res = await fetch('/member/password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(form.value),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            if (data.errors) errors.value = data.errors;
            return;
        }
        emit('saved');
    } catch {
        errors.value = { current_password: ['Terjadi kesalahan, coba lagi.'] };
    } finally {
        saving.value = false;
    }
};
</script>
