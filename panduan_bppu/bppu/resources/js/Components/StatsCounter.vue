<template>
    <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8">
        <div
            v-for="(stat, index) in stats"
            :key="index"
            class="text-center px-4 py-2"
            data-aos="fade-up"
            :data-aos-delay="index * 100"
        >
            <div class="drop-shadow-lg">
                <div class="text-2xl md:text-3xl font-bold text-[#6b4700] dark:text-[#ccb27f] mb-1">
                    <span v-if="stat.prefix">{{ stat.prefix }}</span>
                    <span ref="counters" :data-target="stat.value">0</span>
                    <span v-if="stat.suffix">{{ stat.suffix }}</span>
                </div>
                <div class="text-xs md:text-sm text-gray-800 dark:text-[#d6c199] font-semibold">{{ stat.label }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    unitKerja: {
        type: Number,
        default: 0
    },
    yearsOperating: {
        type: Number,
        default: 0
    },
    memberCount: {
        type: Number,
        default: 0
    }
});

const stats = computed(() => [
    { value: props.unitKerja, suffix: '', label: 'Unit Usaha' },
    { value: props.yearsOperating, suffix: '+', label: 'Tahun Beroperasi' },
    { value: props.memberCount, suffix: '+', label: 'Member Terdaftar' },
    { label: 'Akreditasi Kampus', prefix: '', value: 'Unggul', suffix: '' }
]);

const counters = ref([]);

const animateCounter = (element, target) => {
    if (isNaN(target)) {
        element.textContent = target;
        return;
    }

    const duration = 2000;
    const steps = 60;
    const increment = target / steps;
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, duration / steps);
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target.dataset.target;
                animateCounter(entry.target, isNaN(target) ? target : parseInt(target));
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.value.forEach(counter => {
        if (counter) observer.observe(counter);
    });
});
</script>
