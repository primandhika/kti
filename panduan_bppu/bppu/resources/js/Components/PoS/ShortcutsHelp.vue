<template>
  <!-- Floating Help Button - Desktop Only -->
  <div class="hidden md:block fixed bottom-24 left-4 z-30">
    <button
      @click="showModal = !showModal"
      class="group relative bg-gradient-to-br from-[#996600] to-[#7a5100] text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
      title="Pintasan Tombol (tekan ? untuk toggle)"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
      </svg>

      <!-- Pulse animation -->
      <span class="absolute top-0 right-0 flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
      </span>
    </button>
  </div>

  <!-- Modal -->
  <Transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="showModal"
      @click="showModal = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    >
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="showModal"
          @click.stop
          class="bg-white rounded-xl shadow-2xl max-w-xl w-full max-h-[70vh] overflow-hidden"
        >
          <!-- Header -->
          <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] p-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
              </svg>
              <h3 class="text-base font-bold text-white">Pintasan Tombol</h3>
            </div>
            <button
              @click="showModal = false"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="p-4 overflow-y-auto max-h-[calc(70vh-100px)]">
            <!-- Shortcuts by Category -->
            <div v-for="category in categories" :key="category" class="mb-4 last:mb-0">
              <h4 class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                <span class="w-1 h-3 bg-[#996600] rounded-full"></span>
                {{ category }}
              </h4>
              <div class="space-y-1.5">
                <div
                  v-for="shortcut in getShortcutsByCategory(category)"
                  :key="shortcut.key"
                  class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors"
                >
                  <span class="text-xs text-gray-700">{{ shortcut.description }}</span>
                  <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-white border border-gray-300 rounded shadow-sm min-w-[50px] text-center">
                    {{ shortcut.key }}
                  </kbd>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between gap-2">
            <p class="text-xs text-gray-500">Tekan <kbd class="px-1.5 py-0.5 text-xs bg-white border border-gray-300 rounded">?</kbd> untuk toggle</p>
            <button
              @click="showModal = false"
              class="px-4 py-1.5 bg-[#996600] text-white text-xs font-medium rounded hover:bg-[#7a5100] transition-colors"
            >
              OK
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  shortcuts: {
    type: Array,
    required: true
  }
})

const showModal = ref(false)

// Group by category
const categories = computed(() => {
  return [...new Set(props.shortcuts.map(s => s.category))]
})

const getShortcutsByCategory = (category) => {
  return props.shortcuts.filter(s => s.category === category)
}

// Toggle with ? key
const handleKeyPress = (event) => {
  if (event.key === '?' && !event.ctrlKey && !event.altKey) {
    // Don't toggle if typing in input
    const isTyping = document.activeElement.tagName === 'INPUT' ||
                     document.activeElement.tagName === 'TEXTAREA'
    if (!isTyping) {
      event.preventDefault()
      showModal.value = !showModal.value
    }
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeyPress)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyPress)
})
</script>
