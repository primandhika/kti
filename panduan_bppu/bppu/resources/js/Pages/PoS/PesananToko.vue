<template>
    <ShopLayout page-title="Pesanan Toko">
        <PesananSelfOrderPage
            :pesanan="pesanan"
            :stats="stats"
            page-title="Pesanan Belanja Self-Order"
            api-url="/api/belanja/pesanan-toko"
            update-status-url="/api/belanja/update-status/{id}"
            edit-items-url="/api/belanja/edit-items/{id}"
            katalog-url="/api/belanja/katalog-toko"
            audio-notif-text="Pesanan belanja baru masuk!"
            audio-position-class="fixed bottom-20 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 animate-pulse"
            notif-title="Pesanan Belanja Baru"
        />
    </ShopLayout>
</template>

<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue'
import PesananSelfOrderPage from '@/Components/PesananKantin/PesananSelfOrderPage.vue'

defineProps({
    pesanan: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
})
</script>
