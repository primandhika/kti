<template>
    <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-6 py-6 text-white">
                    <div class="flex items-center gap-4">
                        <img src="/logo-white.png" alt="Logo IKIP Siliwangi" class="h-16 w-auto flex-shrink-0" />
                        <div class="flex-1">
                            <h1 class="text-2xl sm:text-3xl font-bold">
                                Daftar Member Kantin/Toko IKIP Siliwangi
                            </h1>
                            <p class="mt-1 text-amber-100">
                                Dapatkan keuntungan menarik!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="px-6 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center">
                            <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-semibold', currentStep === 1 ? 'bg-[#996600] text-white' : 'bg-green-500 text-white']">
                                <span v-if="currentStep > 1">✓</span>
                                <span v-else>1</span>
                            </div>
                            <span class="ml-3 text-sm font-medium" :class="currentStep === 1 ? 'text-[#996600]' : 'text-gray-500'">Data Diri</span>
                        </div>
                        <div class="w-24 h-1 mx-4" :class="currentStep === 2 ? 'bg-[#996600]' : 'bg-gray-300'"></div>
                        <div class="flex items-center">
                            <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-semibold', currentStep === 2 ? 'bg-[#996600] text-white' : 'bg-gray-300 text-gray-600']">
                                2
                            </div>
                            <span class="ml-3 text-sm font-medium" :class="currentStep === 2 ? 'text-[#996600]' : 'text-gray-500'">Survey</span>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="px-6 py-8">
                    <Transition name="slide-fade" mode="out-in">
                        <RegistrationFormStep
                            v-if="currentStep === 1"
                            key="step-1"
                            :form="registrationForm"
                            :errors="errors"
                            :processing="processing"
                            @submit="submitRegistration"
                            @openTerms="showTermsModal = true"
                        />

                        <SurveyFormStep
                            v-else-if="currentStep === 2"
                            key="step-2"
                            :form="surveyForm"
                            :errors="errors"
                            :processing="processing"
                            @submit="submitSurvey"
                            @back="currentStep = 1"
                        />
                    </Transition>
                </div>
            </div>

            <!-- Back to Login Link -->
            <div class="text-center mt-6">
                <a :href="route('self-order.index')" class="text-[#996600] hover:text-[#7a5100] font-medium">
                    ← Kembali ke Halaman Self Order
                </a>
            </div>
        </div>

        <!-- Terms & Conditions Modal -->
        <TermsConditionsModal
            :show="showTermsModal"
            :membershipTiers="membershipTiers"
            @close="showTermsModal = false"
        />

        <!-- Success Modal -->
        <RegistrationSuccessModal
            :show="showSuccessModal"
            :email="registrationForm.email"
            @close="showSuccessModal = false"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import RegistrationFormStep from '@/Components/SelfOrder/RegistrationFormStep.vue';
import SurveyFormStep from '@/Components/SelfOrder/SurveyFormStep.vue';
import TermsConditionsModal from '@/Components/SelfOrder/TermsConditionsModal.vue';
import RegistrationSuccessModal from '@/Components/SelfOrder/RegistrationSuccessModal.vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    membershipTiers: {
        type: Array,
        default: () => [],
    },
});

const toast = useToast();

const currentStep = ref(1);
const processing = ref(false);
const errors = ref({});
const userId = ref(null);
const showTermsModal = ref(false);
const showSuccessModal = ref(false);

const registrationForm = ref({
    nim: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const surveyForm = ref({
    rating_pelayanan: 0,
    rating_kualitas: 0,
    rating_variasi: 0,
    rating_harga: 0,
    rating_suasana: 0,
    menu_favorit: '',
    menu_rekomendasi: '',
    kritik_saran: '',
});

const submitRegistration = async () => {
    processing.value = true;
    errors.value = {};

    try {
        console.log('Submitting registration:', registrationForm.value);

        const response = await fetch(route('member.register'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(registrationForm.value),
        });

        console.log('Response status:', response.status);

        const data = await response.json();
        console.log('Response data:', data);

        if (response.ok && data.success) {
            userId.value = data.user_id;
            toast.success(data.message);
            currentStep.value = 2;
        } else {
            if (data.errors) {
                errors.value = data.errors;
                const firstError = Object.values(data.errors)[0];
                toast.error(firstError[0] || 'Validasi gagal');
            } else {
                toast.error(data.message || 'Terjadi kesalahan saat registrasi');
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        toast.error('Terjadi kesalahan. Silakan coba lagi. Error: ' + error.message);
    } finally {
        processing.value = false;
    }
};

const submitSurvey = async () => {
    processing.value = true;
    errors.value = {};

    try {
        console.log('Submitting survey:', surveyForm.value, 'User ID:', userId.value);

        const response = await fetch(route('member.survey'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                ...surveyForm.value,
                user_id: userId.value,
            }),
        });

        console.log('Survey response status:', response.status);

        const data = await response.json();
        console.log('Survey response data:', data);

        if (response.ok && data.success) {
            toast.success(data.message);
            showSuccessModal.value = true;
        } else {
            if (data.errors) {
                errors.value = data.errors;
                const firstError = Object.values(data.errors)[0];
                toast.error(firstError[0] || 'Validasi gagal');
            } else {
                toast.error(data.message || 'Terjadi kesalahan saat menyimpan survey');
            }
        }
    } catch (error) {
        console.error('Survey error:', error);
        toast.error('Terjadi kesalahan. Silakan coba lagi. Error: ' + error.message);
    } finally {
        processing.value = false;
    }
};
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.4s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
    transform: translateX(30px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(-30px);
    opacity: 0;
}
</style>
