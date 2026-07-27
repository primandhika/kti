import { ref } from 'vue'

export function useSound() {
  const playSFX = (type) => {
    try {
      let audioPath = ''
      if (type === 'add' || type === 'increment' || type === 'decrement') {
        audioPath = '/sfx/tambah_kurangi_item.mp3'
      } else if (type === 'remove') {
        audioPath = '/sfx/buang_item.mp3'
      } else if (type === 'clear') {
        audioPath = '/sfx/clear_items.mp3'
      } else if (type === 'success') {
        audioPath = '/sfx/berhasil_transaksi.mp3'
      }

      if (audioPath) {
        const audio = new Audio(audioPath)
        audio.volume = 0.5
        audio.play().catch(() => {})
      }
    } catch (err) {}
  }

  return {
    playSFX
  }
}
