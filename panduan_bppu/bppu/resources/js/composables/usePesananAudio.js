import { ref, watch, onUnmounted } from 'vue'

export function usePesananAudio(audioPath = '/sfx/pesanan_diterima.mp3') {
    const audio = ref(null)
    const isPlaying = ref(false)
    const hasPendingOrders = ref(false)

    // Initialize audio
    if (typeof window !== 'undefined') {
        audio.value = new Audio(audioPath)
        audio.value.loop = true
        audio.value.volume = 0.7
    }

    function play() {
        if (audio.value && !isPlaying.value) {
            audio.value.play()
                .then(() => {
                    isPlaying.value = true
                })
                .catch(err => {
                    console.warn('Audio play error:', err)
                })
        }
    }

    function stop() {
        if (audio.value && isPlaying.value) {
            audio.value.pause()
            audio.value.currentTime = 0
            isPlaying.value = false
        }
    }

    // Watch for pending orders changes
    watch(hasPendingOrders, (newVal) => {
        if (newVal) {
            play()
        } else {
            stop()
        }
    })

    onUnmounted(() => {
        stop()
        if (audio.value) {
            audio.value = null
        }
    })

    return {
        isPlaying,
        hasPendingOrders,
        play,
        stop
    }
}
