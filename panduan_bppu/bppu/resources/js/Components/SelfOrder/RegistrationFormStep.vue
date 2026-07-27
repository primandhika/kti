<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <!-- NIM Field -->
        <div>
            <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">
                NIM <span class="text-red-500">*</span>
            </label>
            <input
                id="nim"
                v-model="form.nim"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                placeholder="Masukkan NIM Anda"
                required
                @input="validateNIM"
            />
            <p v-if="errors.nim" class="mt-1 text-sm text-red-600">{{ errors.nim[0] }}</p>
        </div>

        <!-- Nama Lengkap Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 mb-2">Sesuai yang terdaftar di Sikap/Siakad</p>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                placeholder="Masukkan nama lengkap Anda"
                required
                @input="validateName"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
            </label>
            <input
                id="email"
                v-model="form.email"
                type="email"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                placeholder="nama@example.com"
                required
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition pr-12"
                    placeholder="Minimal 8 karakter"
                    required
                />
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <span v-if="showPassword">🙈</span>
                    <span v-else>👁️</span>
                </button>
            </div>
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
        </div>

        <!-- Password Confirmation Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition pr-12"
                    placeholder="Ketik ulang password Anda"
                    required
                />
                <button
                    type="button"
                    @click="showPasswordConfirm = !showPasswordConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <span v-if="showPasswordConfirm">🙈</span>
                    <span v-else>👁️</span>
                </button>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex items-start">
                <input
                    id="terms"
                    v-model="form.terms"
                    type="checkbox"
                    class="mt-1 h-5 w-5 text-[#996600] focus:ring-[#996600] border-gray-300 rounded"
                    required
                />
                <label for="terms" class="ml-3 text-sm text-gray-700">
                    Saya menyetujui
                    <button
                        type="button"
                        @click="$emit('openTerms')"
                        class="text-[#996600] hover:text-[#7a5100] font-medium underline"
                    >
                        ketentuan dan kebijakan
                    </button>
                    yang berlaku di Kantin BPPU IKIP Siliwangi
                    <span class="text-red-500">*</span>
                </label>
            </div>
            <p v-if="errors.terms" class="mt-2 text-sm text-red-600">{{ errors.terms[0] }}</p>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button
                type="submit"
                :disabled="processing || !form.terms"
                class="w-full bg-[#996600] hover:bg-[#7a5100] text-white font-semibold py-3 px-6 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
            >
                <span v-if="processing">Memproses...</span>
                <span v-else>Lanjut ke Survey →</span>
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit', 'openTerms']);

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const validateNIM = (event) => {
    event.target.value = event.target.value.replace(/[^0-9]/g, '');
    props.form.nim = event.target.value;
};

const validateName = (event) => {
    event.target.value = event.target.value.replace(/[^a-zA-Z\s]/g, '');
    props.form.name = event.target.value;
};
</script>
