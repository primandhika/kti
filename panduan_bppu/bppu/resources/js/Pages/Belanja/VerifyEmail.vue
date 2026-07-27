<template>
    <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Loading State -->
                <div v-if="loading" class="p-12 text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-[#996600] mx-auto"></div>
                    <p class="mt-4 text-gray-600">Memverifikasi email Anda...</p>
                </div>

                <!-- Success State -->
                <div v-else-if="verified" class="p-8 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-3">Email Berhasil Diverifikasi!</h1>
                    <p class="text-gray-600 mb-6">
                        Selamat! Akun Anda sudah aktif dan siap digunakan.
                    </p>
                    <a
                        :href="route('self-order.index')"
                        class="inline-block bg-[#996600] hover:bg-[#7a5100] text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                    >
                        Login Sekarang
                    </a>
                </div>

                <!-- Error State -->
                <div v-else class="p-8 text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-3">Verifikasi Gagal</h1>
                    <p class="text-gray-600 mb-6">
                        {{ errorMessage }}
                    </p>
                    <div class="space-y-3">
                        <a
                            :href="route('self-order.index')"
                            class="block bg-[#996600] hover:bg-[#7a5100] text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                        >
                            Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Butuh bantuan? Hubungi admin kantin BPPU</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
});

const toast = useToast();
const loading = ref(true);
const verified = ref(false);
const errorMessage = ref('Token verifikasi tidak valid atau sudah kadaluarsa.');

onMounted(async () => {
    if (!props.token) {
        loading.value = false;
        errorMessage.value = 'Token verifikasi tidak ditemukan.';
        return;
    }

    try {
        const response = await fetch(route('member.verify-email'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                token: props.token,
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            verified.value = true;
            toast.success(data.message);
        } else {
            verified.value = false;
            errorMessage.value = data.message || 'Token verifikasi tidak valid atau sudah kadaluarsa.';
            toast.error(errorMessage.value);
        }
    } catch (error) {
        verified.value = false;
        errorMessage.value = 'Terjadi kesalahan saat verifikasi email. Silakan coba lagi.';
        toast.error(errorMessage.value);
    } finally {
        loading.value = false;
    }
});
</script>
