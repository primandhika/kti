<template>
    <Teleport to="body">
        <!-- Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                @click="$emit('close')"
                class="fixed inset-0 bg-black bg-opacity-50 z-50"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <Transition
            enter-active-class="transition-transform duration-300"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="isOpen"
                class="fixed right-0 top-0 h-full w-full md:w-96 bg-white shadow-2xl z-50 flex flex-col"
            >
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#996600] to-[#CC8800] text-white p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button
                            @click="$emit('close')"
                            class="p-2 hover:bg-black/10 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h2 class="text-lg font-bold">Keranjang Belanja</h2>
                    </div>
                    <button
                        @click="$emit('close')"
                        class="p-2 hover:bg-black/10 rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="loading" class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
                        <p class="text-gray-600 mt-4">Memuat keranjang...</p>
                    </div>

                    <div v-else-if="!cart || cart.items.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-gray-600 mt-4 text-lg font-semibold">Keranjang Anda Kosong</p>
                        <p class="text-gray-500 text-sm mt-2">Tambahkan menu favorit Anda ke keranjang</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                        >
                            <div class="flex gap-3">
                                <img
                                    :src="item.canteen_menu.image"
                                    :alt="item.canteen_menu.name"
                                    class="w-16 h-16 object-cover rounded-lg flex-shrink-0"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2">{{ item.canteen_menu.name }}</h3>
                                        <button
                                            @click="removeItem(item)"
                                            class="text-red-500 hover:text-red-700 flex-shrink-0"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <p class="text-sm text-[#996600] font-bold">
                                            Rp {{ formatPrice(item.price * item.quantity) }}
                                        </p>
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-2 py-1">
                                            <button
                                                @click="updateQuantity(item, item.quantity - 1)"
                                                :disabled="item.quantity <= 1"
                                                class="w-6 h-6 rounded bg-white hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center text-gray-600"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="font-bold text-sm w-6 text-center">{{ item.quantity }}</span>
                                            <button
                                                @click="updateQuantity(item, item.quantity + 1)"
                                                class="w-6 h-6 rounded bg-[#996600] hover:bg-[#CC8800] text-white flex items-center justify-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="cart && cart.items && cart.items.length > 0" class="border-t-2 border-gray-200 p-4 space-y-3 bg-gray-50">
                    <!-- Total -->
                    <div class="bg-white rounded-lg p-3 border-2 border-[#996600]">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 font-semibold">Total Pesanan:</span>
                            <span class="text-xl font-bold text-[#996600]">Rp {{ formatPrice(cartTotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-500">{{ cartTotalItems }} item</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <button
                            @click="checkout"
                            class="w-full bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white py-3 rounded-lg font-bold transition-all shadow-lg hover:shadow-xl"
                        >
                            Checkout
                        </button>
                        <button
                            @click="clearCart"
                            class="w-full bg-white border-2 border-gray-300 hover:bg-gray-100 text-gray-700 py-2.5 rounded-lg font-semibold transition-colors"
                        >
                            Kosongkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(['close', 'cartUpdated']);

const toast = useToast();
const cart = ref(null);
const loading = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

// Computed total yang akurat
const cartTotal = computed(() => {
    if (!cart.value || !cart.value.items || cart.value.items.length === 0) {
        return 0;
    }
    return cart.value.items.reduce((total, item) => {
        return total + (item.price * item.quantity);
    }, 0);
});

const cartTotalItems = computed(() => {
    if (!cart.value || !cart.value.items || cart.value.items.length === 0) {
        return 0;
    }
    return cart.value.items.reduce((total, item) => {
        return total + item.quantity;
    }, 0);
});

const fetchCart = async () => {
    loading.value = true;
    try {
        const response = await fetch('/api/cart');
        const data = await response.json();
        cart.value = data.cart;
        emit('cartUpdated', cartTotalItems.value);
    } catch (error) {
        console.error('Error fetching cart:', error);
        toast.error('Gagal memuat keranjang');
    } finally {
        loading.value = false;
    }
};

const updateQuantity = async (item, newQuantity) => {
    if (newQuantity < 1) return;

    try {
        const response = await fetch(`/api/cart/items/${item.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                quantity: newQuantity,
                notes: item.notes,
            }),
        });

        const data = await response.json();

        if (data.success) {
            cart.value = data.cart;
            emit('cartUpdated', cartTotalItems.value);
            toast.success('Kuantitas berhasil diupdate');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        toast.error('Gagal mengupdate kuantitas');
    }
};

const removeItem = async (item) => {
    try {
        const response = await fetch(`/api/cart/items/${item.id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            cart.value = data.cart;
            emit('cartUpdated', cartTotalItems.value);
            toast.success(data.message);
        }
    } catch (error) {
        console.error('Error removing item:', error);
        toast.error('Gagal menghapus item');
    }
};

const clearCart = async () => {
    if (!confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) return;

    try {
        const response = await fetch('/api/cart/clear', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            cart.value = data.cart;
            emit('cartUpdated', 0);
            toast.success(data.message);
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        toast.error('Gagal mengosongkan keranjang');
    }
};

const checkout = () => {
    toast.info('Fitur checkout akan segera hadir!');
    // TODO: Implement checkout functionality
};

watch(() => props.isOpen, (newValue) => {
    if (newValue) {
        fetchCart();
    }
});

onMounted(() => {
    if (props.isOpen) {
        fetchCart();
    }
});
</script>
