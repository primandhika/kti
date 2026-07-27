<template>
    <Transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl" @click.stop>
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-4">
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            Pendaftaran Berhasil!
                        </h3>

                        <div class="mt-4 mb-6">
                            <p class="text-gray-600 mb-4">
                                Selamat! Akun Anda telah berhasil didaftarkan.
                            </p>

                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-amber-800 mb-2">
                                    <strong>Langkah selanjutnya:</strong>
                                </p>
                                <p class="text-sm text-amber-700">
                                    Silakan cek email Anda untuk verifikasi akun. Link verifikasi telah dikirim ke <strong>{{ email }}</strong>
                                </p>
                            </div>

                            <p class="text-xs text-gray-500">
                                Setelah verifikasi, Anda dapat login dan mulai berbelanja di Kantin/Toko IKIP Siliwangi.
                            </p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button
                                @click="goToSelfOrder"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 bg-[#996600] text-base font-medium text-white hover:bg-[#7a5100] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#996600]"
                            >
                                Kembali ke Self Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    email: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};

const goToSelfOrder = () => {
    window.location.href = '/kantin/self-order';
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
