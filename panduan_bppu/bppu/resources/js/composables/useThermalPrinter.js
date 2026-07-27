/**
 * Thermal Printer Composable
 * Supports 58mm thermal printers (Bluetooth/USB)
 * Optimized for mobile printing via Web Bluetooth API
 */

export function useThermalPrinter() {
  const PAPER_WIDTH = 32 // Characters per line for 58mm paper

  /**
   * Format text untuk thermal printer
   */
  const formatLine = (left, right = '', width = PAPER_WIDTH) => {
    const leftStr = String(left)
    const rightStr = String(right)
    const spaces = width - leftStr.length - rightStr.length
    return leftStr + ' '.repeat(Math.max(0, spaces)) + rightStr
  }

  const centerText = (text, width = PAPER_WIDTH) => {
    const textStr = String(text)
    const spaces = Math.floor((width - textStr.length) / 2)
    return ' '.repeat(Math.max(0, spaces)) + textStr
  }

  const separator = (char = '-', width = PAPER_WIDTH) => {
    return char.repeat(width)
  }

  /**
   * Format currency
   */
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID').format(value)
  }

  /**
   * Format tanggal Indonesia
   */
  const formatDate = (date) => {
    const d = new Date(date)
    const options = {
      year: 'numeric',
      month: 'short',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta'
    }
    return d.toLocaleDateString('id-ID', options)
  }

  /**
   * Generate receipt content untuk 58mm thermal printer
   * Returns array of line objects with formatting info
   */
  const generateReceiptContent = (transaction) => {
    let content = []

    // Header - BOLD (no logo)
    content.push({ text: centerText('BPPU'), bold: true })
    content.push({ text: centerText('IKIP SILIWANGI'), bold: true })

    // Work unit name (dynamic)
    const workUnitName = transaction.work_unit_name || transaction.workUnit?.nama || 'Kantin'
    content.push({ text: centerText(workUnitName) })
    content.push({ text: centerText('Jl. Terusan Jendral Sudirman') })
    content.push({ text: centerText('Cimahi, Jawa Barat') })
    content.push({ text: separator('=') })

    // Transaction Info
    content.push({ text: `No: ${transaction.nomor_transaksi || '-'}` })
    content.push({ text: `Tgl: ${formatDate(transaction.tanggal || new Date())}` })
    content.push({ text: separator('-') })

    // Items - 2 LINE FORMAT (compact but complete)
    const items = transaction.items || []
    items.forEach(item => {
      const namaBarang = item.nama || item.nama_barang || 'Item'
      const qty = item.qty || 0
      const subtotal = item.subtotal || (qty * (item.harga_satuan || item.harga || 0))

      // Line 1: Full item name (no truncate)
      content.push({ text: namaBarang })

      // Line 2: Qty and price (indented)
      const qtyStr = `${qty}x`
      const priceStr = `Rp ${formatCurrency(subtotal)}`
      content.push({ text: formatLine(`  ${qtyStr}`, priceStr) })
    })

    content.push({ text: '' })
    content.push({ text: separator('-') })

    // Summary
    const subtotal = transaction.subtotal || transaction.total || 0
    content.push({ text: formatLine('Subtotal:', `Rp ${formatCurrency(subtotal)}`) })

    if (transaction.diskon && transaction.diskon > 0) {
      content.push({ text: formatLine('Diskon:', `Rp ${formatCurrency(transaction.diskon)}`) })
    }

    if (transaction.potongan_poin && transaction.potongan_poin > 0) {
      content.push({ text: formatLine('Potongan Poin:', `Rp ${formatCurrency(transaction.potongan_poin)}`) })
    }

    content.push({ text: separator('=') })
    content.push({ text: formatLine('TOTAL:', `Rp ${formatCurrency(transaction.total)}`) })
    content.push({ text: separator('=') })

    // Payment info
    if (transaction.metode_pembayaran) {
      const metode = transaction.metode_pembayaran.toUpperCase()
      content.push({ text: `Pembayaran: ${metode}` })
    }

    if (transaction.bayar && transaction.bayar > 0) {
      content.push({ text: formatLine('Bayar:', `Rp ${formatCurrency(transaction.bayar)}`) })
    }

    if (transaction.kembalian && transaction.kembalian > 0) {
      content.push({ text: formatLine('Kembalian:', `Rp ${formatCurrency(transaction.kembalian)}`) })
    }

    // Poin earned
    if (transaction.poin_didapat && transaction.poin_didapat > 0) {
      content.push({ text: '' })
      content.push({ text: centerText('* POIN REWARD *') })
      content.push({ text: centerText(`+${transaction.poin_didapat} poin`) })
    }

    content.push({ text: '' })
    content.push({ text: separator('-') })
    content.push({ text: centerText('Terima Kasih') })
    content.push({ text: separator('-') })

    // Member info at the bottom
    // Support 2 formats:
    // 1. From PoS transaction: member_name, member_id
    // 2. From database relation: buyer.name, buyer.member_code
    const memberName = transaction.member_name || transaction.buyer?.name
    const memberId = transaction.member_id || transaction.buyer?.member_code

    if (memberName) {
      content.push({ text: centerText(memberName) })
      content.push({ text: centerText(`Member ID: ${memberId || '-'}`) })
    }

    content.push({ text: '' })
    content.push({ text: '' })
    content.push({ text: '' })

    return content
  }

  /**
   * Print via Web Bluetooth API (for mobile Bluetooth printers)
   */
  const printViaBluetooth = async (contentArray) => {
    if (!navigator.bluetooth) {
      throw new Error('Web Bluetooth API tidak didukung di browser ini.')
    }

    try {
      // Request Bluetooth device - try multiple service UUIDs
      const device = await navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: [
          '000018f0-0000-1000-8000-00805f9b34fb', // Common thermal printer
          'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // BLE printer service
          '49535343-fe7d-4ae5-8fa9-9fafd205e455', // Some ESC/POS printers
        ]
      })

      const server = await device.gatt.connect()

      // Try to find write characteristic
      let characteristic = null
      const services = await server.getPrimaryServices()

      for (const service of services) {
        try {
          const characteristics = await service.getCharacteristics()
          for (const char of characteristics) {
            if (char.properties.write || char.properties.writeWithoutResponse) {
              characteristic = char
              break
            }
          }
          if (characteristic) break
        } catch (e) {
          continue
        }
      }

      if (!characteristic) {
        throw new Error('Tidak dapat menemukan karakteristik write pada printer')
      }

      // Convert content to bytes with ESC/POS commands
      const encoder = new TextEncoder()

      // ESC/POS initialization
      const ESC = '\x1B'
      const GS = '\x1D'

      // Commands
      const INIT = ESC + '@' // Initialize printer
      const ALIGN_CENTER = ESC + 'a' + '1'
      const ALIGN_LEFT = ESC + 'a' + '0'
      const BOLD_ON = ESC + 'E' + '1'
      const BOLD_OFF = ESC + 'E' + '0'
      const FONT_SMALL = ESC + '!' + '\x01' // Small font (condensed)
      const FONT_NORMAL = ESC + '!' + '\x00' // Normal font
      const CUT_PAPER = GS + 'V' + '\x00' // Full cut
      const FEED_LINES = ESC + 'd' + '\x03' // Feed 3 lines

      let formattedContent = ''

      // Process each line with formatting
      contentArray.forEach((line) => {
        const lineObj = typeof line === 'string' ? { text: line } : line
        const text = lineObj.text || ''

        // Apply formatting
        let lineFormat = ''

        // Bold formatting
        if (lineObj.bold) {
          lineFormat += BOLD_ON
        }

        // Small font formatting
        if (lineObj.small) {
          lineFormat += FONT_SMALL
        }

        // Detect separator alignment
        if (text.includes('===')) {
          lineFormat += ALIGN_CENTER
        } else if (text.includes('---')) {
          lineFormat += ALIGN_LEFT
        } else if (text.trim() && !lineObj.bold && !lineObj.small) {
          // Regular text - left aligned
          lineFormat += ALIGN_LEFT
        } else if (lineObj.bold || lineObj.small) {
          // Formatted text - centered for header
          if (lineObj.bold && (text.includes('BPPU') || text.includes('IKIP'))) {
            lineFormat += ALIGN_CENTER
          } else {
            lineFormat += ALIGN_LEFT
          }
        } else {
          lineFormat += ALIGN_LEFT
        }

        // Add text
        formattedContent += lineFormat + text + '\n'

        // Reset formatting after each line
        if (lineObj.bold) {
          formattedContent = formattedContent.trimEnd() + BOLD_OFF + '\n'
        }
        if (lineObj.small) {
          formattedContent = formattedContent.trimEnd() + FONT_NORMAL + '\n'
        }
      })

      // Build command sequence
      let commandSequence = INIT + formattedContent + FEED_LINES + CUT_PAPER

      const data = encoder.encode(commandSequence)

      // Send to printer in chunks (max 20 bytes per chunk for compatibility)
      const chunkSize = 20
      for (let i = 0; i < data.length; i += chunkSize) {
        const chunk = data.slice(i, Math.min(i + chunkSize, data.length))
        await characteristic.writeValue(chunk)
        // Small delay between chunks
        await new Promise(resolve => setTimeout(resolve, 50))
      }

      // Disconnect after short delay
      setTimeout(() => {
        device.gatt.disconnect()
      }, 1000)

      return true
    } catch (error) {
      console.error('Bluetooth print error:', error)
      throw error
    }
  }

  /**
   * Print via standard browser print (fallback)
   */
  const printViaWindow = (contentArray) => {
    const printWindow = window.open('', '_blank', 'width=302,height=600')

    // Convert array to HTML with formatting
    let htmlContent = ''
    contentArray.forEach((line) => {
      const lineObj = typeof line === 'string' ? { text: line } : line
      const text = lineObj.text || ''

      if (lineObj.bold) {
        htmlContent += `<strong>${text}</strong>\n`
      } else if (lineObj.small) {
        htmlContent += `<small>${text}</small>\n`
      } else {
        htmlContent += `${text}\n`
      }
    })

    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>Struk BPPU</title>
        <style>
          @page {
            size: 58mm auto;
            margin: 0;
          }
          body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 5mm;
            width: 58mm;
            background: white;
          }
          pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
          }
          strong {
            font-weight: bold;
          }
          small {
            font-size: 9px;
          }
        </style>
      </head>
      <body>
        <pre>${htmlContent}</pre>
      </body>
      </html>
    `)

    printWindow.document.close()

    // Auto print after load
    printWindow.onload = () => {
      printWindow.focus()
      printWindow.print()
      setTimeout(() => printWindow.close(), 1000)
    }
  }

  /**
   * Generate item summary content untuk 58mm thermal printer (COMPACT)
   */
  const generateItemSummaryContent = ({ dateRange, items, total, totalVerified, totalTransaksi }) => {
    let content = []

    // Header - Compact
    content.push({ text: centerText('BPPU IKIP SILIWANGI'), bold: true })
    content.push({ text: separator('=') })
    content.push({ text: centerText('RINGKASAN ITEM') })
    content.push({ text: centerText(dateRange) })
    content.push({ text: separator('=') })

    // Sort items alphabetically by nama_barang
    const sortedItems = [...items].sort((a, b) => {
      const nameA = (a.nama_barang || 'Item').toLowerCase()
      const nameB = (b.nama_barang || 'Item').toLowerCase()
      return nameA.localeCompare(nameB)
    })

    // Items - COMPACT FORMAT (1-2 lines per item)
    sortedItems.forEach((item, index) => {
      const namaBarang = item.nama_barang || 'Item'
      const qty = item.total_qty || 0
      const harga = item.total_harga || 0
      const mitra = item.supplier_name || '-'

      // Line 1: No. Name
      content.push({ text: `${index + 1}. ${namaBarang}` })

      // Line 2: Qty x Price | Mitra (compact in 1 line)
      const qtyPrice = `${qty}x Rp${formatCurrency(harga)}`
      const mitraStr = mitra.length > 12 ? mitra.substring(0, 10) + '..' : mitra
      content.push({ text: formatLine(`  ${qtyPrice}`, `M:${mitraStr}`) })
    })

    content.push({ text: separator('-') })
    content.push({ text: formatLine('TOTAL:', `${items.length} item`) })
    content.push({ text: separator('=') })
    content.push({ text: formatLine('TOTAL:', `Rp ${formatCurrency(total)}`) })

    // Add verified info if available
    if (totalTransaksi && totalTransaksi > 0) {
      content.push({ text: formatLine('Verified:', `${totalVerified || 0}/${totalTransaksi}`) })
    }

    content.push({ text: separator('=') })
    content.push({ text: '' })

    return content
  }

  /**
   * Main print function
   */
  const printReceipt = async (transaction) => {
    // Check if this is item summary print
    if (transaction.isItemSummary) {
      const content = generateItemSummaryContent(transaction)

      // Try Bluetooth first (for mobile), fallback to window.print
      if (navigator.bluetooth && /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        try {
          await printViaBluetooth(content)
        } catch (error) {
          console.warn('Bluetooth print failed, falling back to window.print:', error)
          printViaWindow(content)
        }
      } else {
        // Desktop or no Bluetooth support
        printViaWindow(content)
      }
    } else {
      // Regular transaction receipt
      const content = generateReceiptContent(transaction)

      // Try Bluetooth first (for mobile), fallback to window.print
      if (navigator.bluetooth && /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        try {
          await printViaBluetooth(content)
        } catch (error) {
          console.warn('Bluetooth print failed, falling back to window.print:', error)
          printViaWindow(content)
        }
      } else {
        // Desktop or no Bluetooth support
        printViaWindow(content)
      }
    }
  }

  return {
    printReceipt,
    generateReceiptContent,
    generateItemSummaryContent,
    formatCurrency,
    formatDate
  }
}
