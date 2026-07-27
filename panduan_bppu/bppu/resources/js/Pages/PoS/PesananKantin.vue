<template>
    <CanteenLayout page-title="Pesanan Kantin">
        <PesananSelfOrderPage
            :pesanan="pesanan"
            :stats="stats"
            page-title="Pesanan Self-Order"
            api-url="/api/self-order/pesanan-kantin"
            update-status-url="/api/self-order/update-status/{id}"
            edit-items-url="/api/self-order/edit-items/{id}"
            katalog-url="/api/self-order/katalog-kantin"
            audio-notif-text="Pesanan baru masuk!"
            audio-position-class="fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 animate-pulse"
            notif-title="Pesanan Kantin Baru"
        />
    </CanteenLayout>
</template>

<script setup>
import CanteenLayout from '@/Layouts/CanteenLayout.vue'
import PesananSelfOrderPage from '@/Components/PesananKantin/PesananSelfOrderPage.vue'

defineProps({
    pesanan: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
})
</script>
