<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header/Navbar -->
        <Navbar />

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 pt-24 md:pt-28 pb-16">
            <div class="container mx-auto px-4">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Pesan Menu Kantin BPPU</h1>
                    <p class="text-xl opacity-90">Pilih menu favorit Anda dan tambahkan ke keranjang</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12">
            <!-- Category Tabs -->
            <div class="mb-8 overflow-x-auto">
                <div class="flex space-x-4 min-w-max pb-4">
                    <button
                        @click="selectedCategory = null"
                        :class="[
                            'px-6 py-3 rounded-lg font-semibold transition-all duration-200',
                            selectedCategory === null
                                ? 'bg-primary-600 text-white shadow-lg scale-105'
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        Semua Menu
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :class="[
                            'px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center space-x-2',
                            selectedCategory === category.id
                                ? 'bg-primary-600 text-white shadow-lg scale-105'
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <component :is="getIconComponent(category.icon)" class="w-6 h-6" />
                        <span>{{ category.name }}</span>
                    </button>
                </div>
            </div>

            <!-- Menu Items by Category -->
            <div class="space-y-12">
                <template v-if="selectedCategory === null">
                    <!-- Show all categories -->
                    <div v-for="category in filteredCategories" :key="category.id" class="category-section">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                                <component :is="getIconComponent(category.icon)" class="w-8 h-8 text-primary-600" />
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">{{ category.name }}</h2>
                                <p class="text-gray-600">{{ category.description }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                            <div
                                v-for="menu in category.active_menus"
                                :key="menu.id"
                                class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300"
                            >
                                <div class="relative h-48 overflow-hidden">
                                    <img
                                        :src="menu.image"
                                        :alt="menu.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-if="menu.badges && menu.badges.length > 0" class="absolute top-2 left-2 flex flex-wrap gap-2">
                                        <span
                                            v-for="(badge, index) in menu.badges"
                                            :key="index"
                                            :class="`px-3 py-1 rounded-full text-xs font-semibold bg-${badge.color}-500 text-white`"
                                        >
                                            {{ badge.text }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-3 md:p-5">
                                    <h3 class="text-base md:text-xl font-bold text-gray-800 mb-1 md:mb-2 line-clamp-1">{{ menu.name }}</h3>
                                    <p class="text-gray-600 text-xs md:text-sm mb-3 md:mb-4 line-clamp-2">{{ menu.description }}</p>

                                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
                                        <div class="text-lg md:text-2xl font-bold text-primary-600">
                                            Rp {{ formatPrice(menu.price) }}
                                        </div>
                                        <!-- Tombol Pesan (ketika quantity = 0) -->
                                        <button
                                            v-if="!menuQuantities[menu.id]"
                                            @click="addToCart(menu)"
                                            class="w-full md:w-auto bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] active:scale-95 text-white px-3 md:px-6 py-2.5 rounded-xl font-bold transition-all duration-200 flex items-center justify-center space-x-2 text-sm md:text-base shadow-lg hover:shadow-xl"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                            </svg>
                                            <span>Pesan</span>
                                        </button>
                                        <!-- Counter (ketika quantity > 0) -->
                                        <div
                                            v-else
                                            class="w-full md:w-auto flex items-center justify-center bg-white border-2 border-[#996600] rounded-xl overflow-hidden shadow-md"
                                        >
                                            <button
                                                @click="decrementQuantity(menu)"
                                                class="px-3 md:px-4 py-2 bg-[#FFF8E8] hover:bg-[#FFE8B8] active:bg-[#FFD898] text-[#996600] font-bold transition-all duration-150 flex items-center justify-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div class="px-4 md:px-6 py-2 bg-white text-[#996600] font-bold text-base md:text-lg min-w-[3rem] text-center">
                                                {{ menuQuantities[menu.id] }}
                                            </div>
                                            <button
                                                @click="incrementQuantity(menu)"
                                                class="px-3 md:px-4 py-2 bg-[#996600] hover:bg-[#CC8800] active:bg-[#AA7700] text-white font-bold transition-all duration-150 flex items-center justify-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <!-- Show selected category only -->
                    <div v-for="category in filteredCategories" :key="category.id" class="category-section">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                                <component :is="getIconComponent(category.icon)" class="w-8 h-8 text-primary-600" />
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">{{ category.name }}</h2>
                                <p class="text-gray-600">{{ category.description }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                            <div
                                v-for="menu in category.active_menus"
                                :key="menu.id"
                                class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300"
                            >
                                <div class="relative h-48 overflow-hidden">
                                    <img
                                        :src="menu.image"
                                        :alt="menu.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-if="menu.badges && menu.badges.length > 0" class="absolute top-2 left-2 flex flex-wrap gap-2">
                                        <span
                                            v-for="(badge, index) in menu.badges"
                                            :key="index"
                                            :class="`px-3 py-1 rounded-full text-xs font-semibold bg-${badge.color}-500 text-white`"
                                        >
                                            {{ badge.text }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-3 md:p-5">
                                    <h3 class="text-base md:text-xl font-bold text-gray-800 mb-1 md:mb-2 line-clamp-1">{{ menu.name }}</h3>
                                    <p class="text-gray-600 text-xs md:text-sm mb-3 md:mb-4 line-clamp-2">{{ menu.description }}</p>

                                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
                                        <div class="text-lg md:text-2xl font-bold text-primary-600">
                                            Rp {{ formatPrice(menu.price) }}
                                        </div>
                                        <!-- Tombol Pesan (ketika quantity = 0) -->
                                        <button
                                            v-if="!menuQuantities[menu.id]"
                                            @click="addToCart(menu)"
                                            class="w-full md:w-auto bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] active:scale-95 text-white px-3 md:px-6 py-2.5 rounded-xl font-bold transition-all duration-200 flex items-center justify-center space-x-2 text-sm md:text-base shadow-lg hover:shadow-xl"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                            </svg>
                                            <span>Pesan</span>
                                        </button>
                                        <!-- Counter (ketika quantity > 0) -->
                                        <div
                                            v-else
                                            class="w-full md:w-auto flex items-center justify-center bg-white border-2 border-[#996600] rounded-xl overflow-hidden shadow-md"
                                        >
                                            <button
                                                @click="decrementQuantity(menu)"
                                                class="px-3 md:px-4 py-2 bg-[#FFF8E8] hover:bg-[#FFE8B8] active:bg-[#FFD898] text-[#996600] font-bold transition-all duration-150 flex items-center justify-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div class="px-4 md:px-6 py-2 bg-white text-[#996600] font-bold text-base md:text-lg min-w-[3rem] text-center">
                                                {{ menuQuantities[menu.id] }}
                                            </div>
                                            <button
                                                @click="incrementQuantity(menu)"
                                                class="px-3 md:px-4 py-2 bg-[#996600] hover:bg-[#CC8800] active:bg-[#AA7700] text-white font-bold transition-all duration-150 flex items-center justify-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <Footer />

        <!-- Cart Sidebar -->
        <CartSidebar
            :isOpen="isCartOpen"
            @close="isCartOpen = false"
            @cartUpdated="handleCartUpdated"
        />

        <!-- Floating Cart Button -->
        <button
            @click="isCartOpen = true"
            class="fixed bottom-8 right-8 bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white p-4 rounded-full shadow-2xl transition-all duration-200 hover:scale-110 z-40"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="cartItemCount > 0" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                {{ cartItemCount }}
            </span>
        </button>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import CartSidebar from '@/Components/CartSidebar.vue';
import {
    RectangleStackIcon,
    ShoppingBagIcon,
    GiftIcon,
    FireIcon,
    BeakerIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
});

const iconMap = {
    'square-3-stack-3d': RectangleStackIcon,
    'shopping-bag': ShoppingBagIcon,
    'cake': GiftIcon,
    'fire': FireIcon,
    'beaker': BeakerIcon,
};

const getIconComponent = (iconName) => {
    return iconMap[iconName] || Square3Stack3dIcon;
};

const toast = useToast();
const selectedCategory = ref(null);
const isCartOpen = ref(false);
const cartItemCount = ref(0);
const menuQuantities = ref({});
const cartItems = ref({});

const filteredCategories = computed(() => {
    if (selectedCategory.value === null) {
        return props.categories;
    }
    return props.categories.filter(cat => cat.id === selectedCategory.value);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

// Load cart saat mount
const loadCart = async () => {
    try {
        const response = await fetch('/api/cart');
        const data = await response.json();

        if (data.cart && data.cart.items) {
            data.cart.items.forEach(item => {
                menuQuantities.value[item.canteen_menu_id] = item.quantity;
                cartItems.value[item.canteen_menu_id] = item.id;
            });
            cartItemCount.value = data.total_items;
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
};

onMounted(() => {
    loadCart();
});

const addToCart = async (menu) => {
    // Optimistic update - update UI immediately
    menuQuantities.value[menu.id] = 1;
    toast.success(`${menu.name} ditambahkan ke keranjang`);

    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                canteen_menu_id: menu.id,
                quantity: 1,
            }),
        });

        const data = await response.json();

        if (data.success) {
            cartItemCount.value = data.total_items;
            // Update cart item ID for future operations
            if (data.cart && data.cart.items) {
                const item = data.cart.items.find(i => i.canteen_menu_id === menu.id);
                if (item) {
                    cartItems.value[menu.id] = item.id;
                }
            }
        } else {
            // Rollback if failed
            delete menuQuantities.value[menu.id];
            toast.error('Gagal menambahkan item ke keranjang');
        }
    } catch (error) {
        // Rollback if failed
        delete menuQuantities.value[menu.id];
        toast.error('Gagal menambahkan item ke keranjang');
        console.error('Error adding to cart:', error);
    }
};

const incrementQuantity = async (menu) => {
    const currentQuantity = menuQuantities.value[menu.id] || 0;
    const newQuantity = currentQuantity + 1;

    // Optimistic update - update UI immediately
    menuQuantities.value[menu.id] = newQuantity;
    const quantityText = newQuantity > 1 ? `(${newQuantity}) ` : '';
    toast.success(`${quantityText}${menu.name} ditambahkan ke keranjang`);

    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                canteen_menu_id: menu.id,
                quantity: 1,
            }),
        });

        const data = await response.json();

        if (data.success) {
            cartItemCount.value = data.total_items;
        } else {
            // Rollback if failed
            menuQuantities.value[menu.id] = currentQuantity;
            toast.error('Gagal menambahkan item');
        }
    } catch (error) {
        // Rollback if failed
        menuQuantities.value[menu.id] = currentQuantity;
        toast.error('Gagal menambahkan item ke keranjang');
        console.error('Error incrementing quantity:', error);
    }
};

const decrementQuantity = async (menu) => {
    const currentQuantity = menuQuantities.value[menu.id] || 0;
    if (currentQuantity <= 0) return;

    const newQuantity = currentQuantity - 1;
    const cartItemId = cartItems.value[menu.id];

    if (!cartItemId) {
        toast.error('Item tidak ditemukan di keranjang');
        return;
    }

    // Optimistic update - update UI immediately
    if (newQuantity === 0) {
        delete menuQuantities.value[menu.id];
        toast.info(`${menu.name} dihapus dari keranjang`);
    } else {
        menuQuantities.value[menu.id] = newQuantity;
        const quantityText = newQuantity > 1 ? `(${newQuantity}) ` : '';
        toast.info(`${quantityText}${menu.name} di keranjang`);
    }

    try {
        let response;

        if (newQuantity === 0) {
            // Delete item completely
            response = await fetch(`/api/cart/items/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
        } else {
            // Update quantity
            response = await fetch(`/api/cart/items/${cartItemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    quantity: newQuantity,
                }),
            });
        }

        const data = await response.json();

        if (data.success) {
            cartItemCount.value = data.total_items;
            if (newQuantity === 0) {
                delete cartItems.value[menu.id];
            }
        } else {
            // Rollback if failed
            menuQuantities.value[menu.id] = currentQuantity;
            toast.error('Gagal mengupdate keranjang');
        }
    } catch (error) {
        // Rollback if failed
        menuQuantities.value[menu.id] = currentQuantity;
        toast.error('Gagal mengurangi item dari keranjang');
        console.error('Error decrementing quantity:', error);
    }
};

const handleCartUpdated = (totalItems) => {
    cartItemCount.value = totalItems;
    // Reload cart to sync quantities
    loadCart();
};
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
