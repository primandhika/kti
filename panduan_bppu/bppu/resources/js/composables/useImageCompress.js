/**
 * Composable untuk mengompresi gambar di browser sebelum upload
 * menggunakan Canvas API (tanpa dependency eksternal).
 */
export function useImageCompress() {
    /**
     * @param {File} file - File gambar original
     * @param {{ maxWidth?: number, maxHeight?: number, quality?: number, mimeType?: string }} opts
     * @returns {Promise<File>}
     */
    function compress(file, opts = {}) {
        const {
            maxWidth = 1200,
            maxHeight = 1200,
            quality = 0.75,
            mimeType = 'image/jpeg',
        } = opts

        return new Promise((resolve, reject) => {
            const reader = new FileReader()
            reader.onerror = reject
            reader.onload = (e) => {
                const img = new Image()
                img.onerror = reject
                img.onload = () => {
                    let { width, height } = img

                    // Hitung rasio resize
                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height)
                        width = Math.round(width * ratio)
                        height = Math.round(height * ratio)
                    }

                    const canvas = document.createElement('canvas')
                    canvas.width = width
                    canvas.height = height

                    const ctx = canvas.getContext('2d')
                    ctx.drawImage(img, 0, 0, width, height)

                    canvas.toBlob(
                        (blob) => {
                            if (!blob) {
                                // Fallback: kembalikan file asli jika gagal
                                resolve(file)
                                return
                            }
                            resolve(new File([blob], file.name, { type: blob.type, lastModified: Date.now() }))
                        },
                        mimeType,
                        quality,
                    )
                }
                img.src = e.target.result
            }
            reader.readAsDataURL(file)
        })
    }

    return { compress }
}
