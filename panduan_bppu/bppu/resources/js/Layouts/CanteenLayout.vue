<template>
  <PoSLayout
    :page-title="pageTitle"
    title="BPPU PoS"
    pesanan-url="/pengelola/pesanan-kantin"
    api-url="/api/self-order/pesanan-kantin"
    notif-title="Pesanan Baru Masuk!"
  >
    <template #sub-navbar>
      <slot name="sub-navbar" />
    </template>
    <slot />
  </PoSLayout>
</template>

<script setup>
import PoSLayout from './PoSLayout.vue';

defineProps({
  pageTitle: {
    type: String,
    default: 'Point of Sale'
  }
});
</script>
