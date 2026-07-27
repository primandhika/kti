<template>
    <div class="row" :class="`row-${pesanan.status}`">
        <span class="row-no">{{ pesanan.nomor_antrian }}</span>
        <div class="row-name-wrap">
            <span class="row-name">{{ pesanan.nama_pemesan }}</span>
            <span v-if="pesanan.tipe_pengiriman === 'pickup'" class="tipe-badge tipe-pickup">
                <svg class="tipe-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Pickup
            </span>
            <span v-else class="tipe-badge tipe-antar">
                <svg class="tipe-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Antar
            </span>
        </div>
        <div class="row-meja-wrap">
            <span class="row-meja">{{ pesanan.nama_meja || '—' }}</span>
            <span v-if="pesanan.tipe_pengiriman === 'pickup' && pesanan.estimasi_ambil" class="estimasi-text">
                <svg class="tipe-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ pesanan.estimasi_ambil }}
            </span>
        </div>
        <span class="row-item">{{ pesanan.items_count }} item</span>
        <span class="row-time">{{ formatWaktu(pesanan.waktu) }}</span>
        <span class="row-badge" :class="`badge-${pesanan.status}`">
            <span v-if="pesanan.status !== 'siap'" class="badge-dot" />
            {{ pesanan.label_status }}
        </span>
    </div>
</template>

<script setup>
defineProps({
    pesanan: { type: Object, required: true },
})

function formatWaktu(raw) {
    const parts = raw.split(':')
    return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : raw
}
</script>

<style scoped>
.row {
    display: grid;
    grid-template-columns: 6rem 1fr 1fr 5.5rem 6rem 9rem;
    gap: 0.75rem;
    align-items: center;
    padding: 0.9rem 2rem;
    border-bottom: 1px solid #2d1e00;
    transition: background 0.3s;
    min-height: 6.5rem;
}

.row-menunggu { background: transparent; }
.row-diproses { background: rgba(59,130,246,0.06); }
.row-siap     { background: rgba(34,197,94,0.07); }

.row-no {
    font-size: 2.8rem;
    font-weight: 900;
    color: #ccb27f;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

.row-name-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    min-width: 0;
}

.row-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: #f4efe5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}

.row-meja-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    min-width: 0;
}

.row-meja {
    font-size: 1.1rem;
    color: #b7934c;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tipe-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    width: fit-content;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.tipe-pickup {
    background: rgba(153, 102, 0, 0.2);
    color: #ccb27f;
    border: 1px solid rgba(153, 102, 0, 0.4);
}

.tipe-antar {
    background: rgba(59, 130, 246, 0.12);
    color: #93c5fd;
    border: 1px solid rgba(59, 130, 246, 0.25);
}

.tipe-icon {
    width: 0.75rem;
    height: 0.75rem;
    flex-shrink: 0;
}

.estimasi-text {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #7a5100;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.row-item {
    font-size: 1.1rem;
    color: #7a5100;
    font-variant-numeric: tabular-nums;
}

.row-time {
    font-size: 1.1rem;
    color: #7a5100;
    font-variant-numeric: tabular-nums;
    text-align: right;
}

.row-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 700;
    white-space: nowrap;
}

.badge-dot {
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 50%;
    background: currentColor;
    animation: blink 1.4s infinite;
}

.badge-menunggu { background: rgba(234,179,8,0.15); color: #fbbf24; }
.badge-diproses  { background: rgba(59,130,246,0.15); color: #60a5fa; }
.badge-siap      { background: rgba(34,197,94,0.2);  color: #4ade80; }

@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.25; }
}

/* 1920px+ */
@media (min-width: 1920px) {
    .row { padding: 1.1rem 2.5rem; min-height: 8rem; grid-template-columns: 8rem 1fr 1fr 7rem 8rem 11rem; gap: 1rem; }
    .row-no    { font-size: 3.8rem; }
    .row-name  { font-size: 1.8rem; }
    .row-meja  { font-size: 1.4rem; }
    .row-item  { font-size: 1.4rem; }
    .row-time  { font-size: 1.4rem; }
    .row-badge { font-size: 1.2rem; padding: 0.5rem 1.1rem; }
    .tipe-badge { font-size: 0.85rem; padding: 0.2rem 0.65rem; }
    .estimasi-text { font-size: 0.9rem; }
    .tipe-icon { width: 0.9rem; height: 0.9rem; }
}

/* Mobile landscape */
@media (max-height: 520px) and (orientation: landscape) {
    .row { padding: 0.5rem 1rem; min-height: 4.5rem; gap: 0.5rem;
           grid-template-columns: 4.5rem 1fr 1fr 4rem 5rem 7rem; }
    .row-no    { font-size: 2rem; }
    .row-name  { font-size: 1rem; }
    .row-meja  { font-size: 0.85rem; }
    .row-item  { font-size: 0.85rem; }
    .row-time  { font-size: 0.85rem; }
    .row-badge { font-size: 0.75rem; padding: 0.25rem 0.6rem; }
    .tipe-badge { font-size: 0.6rem; padding: 0.1rem 0.35rem; }
    .estimasi-text { font-size: 0.65rem; }
}
</style>
