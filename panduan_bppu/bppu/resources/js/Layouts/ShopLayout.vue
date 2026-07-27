<template>
  <PoSLayout
    :page-title="pageTitle"
    title="BPPU Shop"
    pesanan-url="/pengelola/pesanan-toko"
    api-url="/api/belanja/pesanan-toko"
    notif-title="Pesanan Belanja Masuk!"
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
