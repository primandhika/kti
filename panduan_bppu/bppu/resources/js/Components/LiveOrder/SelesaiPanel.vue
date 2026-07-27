<template>
    <section class="col col-right">
        <div class="col-head">
            <div class="head-left">
                <span class="col-title">SELESAI</span>
                <span class="col-count">{{ list.length }} hari ini</span>
            </div>
            <button class="toggle-btn" @click="$emit('toggle')" :title="visible ? 'Sembunyikan' : 'Tampilkan'">
                <svg class="toggle-icon" :class="{ 'rotated': !visible }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>

        <template v-if="visible">
            <div class="row-legend row-legend-done">
                <span class="leg-no">No.</span>
                <span class="leg-time">Waktu</span>
            </div>

            <div v-if="!list.length" class="empty">
                Belum ada pesanan selesai
            </div>

            <div class="done-list-wrap">
                <TransitionGroup name="row" tag="div" class="row-list">
                    <div
                        v-for="p in list"
                        :key="p.id"
                        class="row row-done"
                    >
                        <span class="row-no-done">{{ p.nomor_antrian }}</span>
                        <span class="row-time">{{ formatWaktu(p.waktu) }}</span>
                    </div>
                </TransitionGroup>
            </div>
        </template>

        <div v-else class="collapsed-hint">
            Tap untuk tampilkan {{ list.length }} pesanan selesai
        </div>
    </section>
</template>

<script setup>
defineProps({
    list:    { type: Array,   default: () => [] },
    visible: { type: Boolean, default: true },
})

defineEmits(['toggle'])

function formatWaktu(raw) {
    const parts = raw.split(':')
    return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : raw
}
</script>

<style scoped>
.col {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

.col-right { background: #0f0a00; }

.col-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 2rem;
    background: #2d1e00;
    border-bottom: 1px solid #3d2800;
    flex-shrink: 0;
}

.head-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.col-title {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    color: #ccb27f;
    text-transform: uppercase;
}

.col-count {
    font-size: 0.9rem;
    color: #4c3300;
}

.toggle-btn {
    background: #2d1e00;
    border: 1px solid #3d2800;
    border-radius: 6px;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
    flex-shrink: 0;
}

.toggle-btn:hover {
    background: #3d2800;
    border-color: #4c3300;
}

.toggle-icon {
    width: 1rem;
    height: 1rem;
    color: #b7934c;
    transition: transform 0.25s ease;
}

.toggle-icon.rotated {
    transform: rotate(180deg);
}

.row-legend {
    display: grid;
    gap: 0.75rem;
    padding: 0.5rem 2rem;
    border-bottom: 1px solid #2d1e00;
    flex-shrink: 0;
}

.row-legend-done {
    grid-template-columns: 1fr 6rem;
}

.row-legend span {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #4c3300;
    text-transform: uppercase;
}

.done-list-wrap {
    flex: 1;
    overflow: hidden;
    min-height: 0;
}

.row-list {
    height: 100%;
    overflow-y: auto;
    scrollbar-width: none;
}

.row-list::-webkit-scrollbar { display: none; }

.row-done {
    display: grid;
    grid-template-columns: 1fr 6rem;
    gap: 0.75rem;
    align-items: center;
    padding: 0.9rem 1.75rem;
    border-bottom: 1px solid #1e1400;
    min-height: 5rem;
}

.row-no-done {
    font-size: 2.4rem;
    font-weight: 900;
    color: #4c3300;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

.row-time {
    font-size: 1.1rem;
    color: #7a5100;
    font-variant-numeric: tabular-nums;
    text-align: right;
}

.empty {
    padding: 3rem 2rem;
    font-size: 1rem;
    color: #3d2800;
    text-align: center;
}

.collapsed-hint {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    color: #3d2800;
    padding: 2rem;
    text-align: center;
}

.row-enter-active,
.row-leave-active { transition: all 0.35s ease; }
.row-enter-from   { opacity: 0; transform: translateX(8px); }
.row-leave-to     { opacity: 0; transform: translateX(-8px); }

/* 1920px+ */
@media (min-width: 1920px) {
    .row-done { min-height: 6.5rem; grid-template-columns: 1fr 8rem; padding: 1.1rem 2rem; }
    .row-no-done { font-size: 2.8rem; }
    .row-time    { font-size: 1.35rem; }
    .col-title   { font-size: 1.3rem; }
    .col-count   { font-size: 1.1rem; }
    .row-legend-done { grid-template-columns: 1fr 8rem; }
    .row-legend span { font-size: 1.1rem; }
    .toggle-btn  { width: 2.4rem; height: 2.4rem; }
    .toggle-icon { width: 1.2rem; height: 1.2rem; }
}

/* Mobile landscape */
@media (max-height: 520px) and (orientation: landscape) {
    .col-head   { padding: 0.4rem 1rem; }
    .col-title  { font-size: 0.75rem; letter-spacing: 0.1em; }
    .col-count  { font-size: 0.7rem; }
    .row-done   { padding: 0.4rem 1rem; min-height: 3.5rem; }
    .row-no-done { font-size: 1.6rem; }
    .row-time   { font-size: 0.8rem; }
    .toggle-btn { width: 1.6rem; height: 1.6rem; }
    .toggle-icon { width: 0.8rem; height: 0.8rem; }
    .row-legend { padding: 0.3rem 1rem; }
    .row-legend span { font-size: 0.65rem; }
}
</style>
