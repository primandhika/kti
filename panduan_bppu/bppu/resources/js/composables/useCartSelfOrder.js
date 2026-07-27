import { ref, computed } from 'vue';

const stores = {};

function getStore(key) {
    if (!stores[key]) {
        stores[key] = {
            cartItems: ref([]),
            isCartOpen: ref(false),
        };
    }
    return stores[key];
}

export function useCartSelfOrder(storageKey = 'bppu-self-order-cart') {
    const { cartItems, isCartOpen } = getStore(storageKey);

    const addToCart = (menu, quantity = 1, varian = null) => {
        const existingItemIndex = cartItems.value.findIndex(item => {
            if (varian && item.varian) {
                return item.id === menu.id && item.varian.id === varian.id;
            }
            return item.id === menu.id && !item.varian;
        });

        if (existingItemIndex !== -1) {
            cartItems.value[existingItemIndex].quantity += quantity;
        } else {
            const hargaAsli = varian ? varian.harga_jual : menu.price;
            const diskonNominal = (!varian && menu.diskon_aktif) ? (menu.nominal_diskon || 0) : 0;
            const price = Math.max(0, hargaAsli - diskonNominal);

            cartItems.value.push({
                id: menu.id,
                name: menu.name,
                price: price,
                original_price: hargaAsli,
                diskon_nominal: diskonNominal,
                quantity: quantity,
                image: menu.image,
                varian: varian ? {
                    id: varian.id,
                    nama_varian: varian.nama_varian,
                } : null,
                work_unit_name: menu.work_unit?.name ?? menu.work_unit_name ?? null,
                maxStock: menu.stok,
            });
        }

        saveCartToStorage();
    };

    const removeFromCart = (index) => {
        cartItems.value.splice(index, 1);
        saveCartToStorage();
    };

    const updateQuantity = (index, quantity) => {
        if (quantity <= 0) {
            removeFromCart(index);
            return;
        }

        const item = cartItems.value[index];
        if (item.maxStock && quantity > item.maxStock) {
            quantity = item.maxStock;
        }

        cartItems.value[index].quantity = quantity;
        saveCartToStorage();
    };

    const clearCart = () => {
        cartItems.value = [];
        saveCartToStorage();
    };

    const saveCartToStorage = () => {
        localStorage.setItem(storageKey, JSON.stringify(cartItems.value));
    };

    const loadCartFromStorage = () => {
        const stored = localStorage.getItem(storageKey);
        if (stored) {
            try {
                cartItems.value = JSON.parse(stored);
            } catch (e) {
                cartItems.value = [];
            }
        }
    };

    const toggleCart = () => {
        isCartOpen.value = !isCartOpen.value;
    };

    const openCart = () => {
        isCartOpen.value = true;
    };

    const closeCart = () => {
        isCartOpen.value = false;
    };

    const totalItems = computed(() => {
        return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const totalPrice = computed(() => {
        return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    const isEmpty = computed(() => {
        return cartItems.value.length === 0;
    });

    if (cartItems.value.length === 0) {
        loadCartFromStorage();
    }

    return {
        cartItems,
        isCartOpen,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        toggleCart,
        openCart,
        closeCart,
        totalItems,
        totalPrice,
        isEmpty,
    };
}
