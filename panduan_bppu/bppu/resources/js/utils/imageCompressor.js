/**
 * Client-side image compression utility
 * Compress images before upload (like WhatsApp)
 */

/**
 * Compress image file to target size
 * @param {File} file - Image file to compress
 * @param {Object} options - Compression options
 * @returns {Promise<File>} Compressed image file
 */
export async function compressImage(file, options = {}) {
  const {
    maxWidth = 800,
    maxHeight = 800,
    quality = 0.85,
    targetSizeKB = 100,
    type = 'image/jpeg'
  } = options

  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.onload = (e) => {
      const img = new Image()

      img.onload = () => {
        // Calculate new dimensions - ONLY resize if larger
        let width = img.width
        let height = img.height
        let needsResize = false

        if (width > maxWidth || height > maxHeight) {
          needsResize = true
          const aspectRatio = width / height

          if (width > height) {
            width = maxWidth
            height = width / aspectRatio
          } else {
            height = maxHeight
            width = height * aspectRatio
          }
        }

        // Create canvas
        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height

        const ctx = canvas.getContext('2d')

        // Enable image smoothing for better quality
        ctx.imageSmoothingEnabled = true
        ctx.imageSmoothingQuality = 'high'

        // Draw image
        ctx.drawImage(img, 0, 0, width, height)

        // Try to compress to target size
        let currentQuality = quality
        let attempts = 0
        const maxAttempts = 8

        const tryCompress = () => {
          canvas.toBlob(
            (blob) => {
              if (!blob) {
                reject(new Error('Failed to compress image'))
                return
              }

              const sizeKB = blob.size / 1024

              // If size is acceptable or we've tried enough times
              if (sizeKB <= targetSizeKB || attempts >= maxAttempts || currentQuality <= 0.2) {
                // Convert blob to File
                const compressedFile = new File([blob], file.name, {
                  type: type,
                  lastModified: Date.now()
                })

                console.log('Image compressed:', {
                  originalSize: `${(file.size / 1024).toFixed(2)} KB`,
                  compressedSize: `${(blob.size / 1024).toFixed(2)} KB`,
                  reduction: `${(((file.size - blob.size) / file.size) * 100).toFixed(2)}%`,
                  quality: currentQuality,
                  dimensions: `${width}x${height}`
                })

                resolve(compressedFile)
                return
              }

              // Try again with lower quality
              attempts++
              currentQuality -= 0.1
              if (currentQuality < 0.2) currentQuality = 0.2

              tryCompress()
            },
            type,
            currentQuality
          )
        }

        tryCompress()
      }

      img.onerror = () => {
        reject(new Error('Failed to load image'))
      }

      img.src = e.target.result
    }

    reader.onerror = () => {
      reject(new Error('Failed to read file'))
    }

    reader.readAsDataURL(file)
  })
}

/**
 * Check if file is an image
 * @param {File} file
 * @returns {boolean}
 */
export function isImage(file) {
  return file && file.type.startsWith('image/')
}

/**
 * Format file size to human readable
 * @param {number} bytes
 * @returns {string}
 */
export function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'

  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i]
}

/**
 * Compress image and return base64 string
 * @param {File} file - Image file to compress
 * @param {Object} options - Compression options
 * @returns {Promise<string>} Base64 string with data:image prefix
 */
export async function compressImageToBase64(file, options = {}) {
  const {
    maxWidth = 800,
    maxHeight = 800,
    quality = 0.85,
    type = 'image/jpeg'
  } = options

  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.onload = (e) => {
      const img = new Image()

      img.onload = () => {
        let width = img.width
        let height = img.height

        if (width > maxWidth || height > maxHeight) {
          const aspectRatio = width / height

          if (width > height) {
            width = maxWidth
            height = width / aspectRatio
          } else {
            height = maxHeight
            width = height * aspectRatio
          }
        }

        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height

        const ctx = canvas.getContext('2d')
        ctx.imageSmoothingEnabled = true
        ctx.imageSmoothingQuality = 'high'
        ctx.drawImage(img, 0, 0, width, height)

        const base64 = canvas.toDataURL(type, quality)

        console.log('Image compressed to base64:', {
          originalSize: `${(file.size / 1024).toFixed(2)} KB`,
          compressedSize: `${((base64.length * 3) / 4 / 1024).toFixed(2)} KB`,
          quality: quality,
          dimensions: `${width}x${height}`
        })

        resolve(base64)
      }

      img.onerror = () => {
        reject(new Error('Failed to load image'))
      }

      img.src = e.target.result
    }

    reader.onerror = () => {
      reject(new Error('Failed to read file'))
    }

    reader.readAsDataURL(file)
  })
}
