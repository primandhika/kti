<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaBisnis extends Model
{
    protected $table = 'skema_bisnis';

    protected $fillable = [
        'supplier_id',
        'jenis_skema',
        'persentase_vendor',
        'persentase_badan_usaha',
        'nominal_flat',
        'persentase_dari_keuntungan',
        'minimum_harga_item',
        'is_active',
        'catatan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'persentase_vendor' => 'decimal:2',
        'persentase_badan_usaha' => 'decimal:2',
        'nominal_flat' => 'decimal:2',
        'persentase_dari_keuntungan' => 'decimal:2',
        'minimum_harga_item' => 'decimal:2',
    ];

    const JENIS_KONSINYASI = 'konsinyasi';
    const JENIS_BAGI_HASIL = 'bagi_hasil';
    const JENIS_DROPSHIPPER = 'dropshipper';
    const JENIS_SUPPLIER = 'supplier';

    public static function getJenisSkemaOptions()
    {
        return [
            self::JENIS_KONSINYASI => 'Konsinyasi',
            self::JENIS_BAGI_HASIL => 'Bagi Hasil',
            self::JENIS_DROPSHIPPER => 'Dropshipper',
            self::JENIS_SUPPLIER => 'Supplier',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function hitungPembagian($totalPenjualan, $totalKeuntungan = null, $qtyItem = 1, $hargaPerItem = null, $hargaKonsinyasi = null)
    {
        if (!$this->is_active) {
            return [
                'bagian_vendor' => 0,
                'bagian_badan_usaha' => 0,
            ];
        }

        switch ($this->jenis_skema) {
            case self::JENIS_KONSINYASI:
                // Cek apakah ada conditional flat (untuk item dengan harga > minimum)
                if ($this->nominal_flat !== null && $this->minimum_harga_item !== null && $hargaPerItem !== null) {
                    // Konsinyasi Bersyarat: cek harga per item
                    if ($hargaPerItem > $this->minimum_harga_item) {
                        // Item mahal: badan usaha dapat flat fee
                        $bagianBadanUsaha = $this->nominal_flat * $qtyItem;
                        $bagianVendor = $totalPenjualan - $bagianBadanUsaha;
                    } else {
                        // Item murah: konsinyasi normal
                        if ($hargaKonsinyasi !== null && $hargaKonsinyasi > 0) {
                            $bagianBadanUsaha = $hargaKonsinyasi * $qtyItem;
                            $bagianVendor = $totalPenjualan - $bagianBadanUsaha;
                        } else {
                            $bagianVendor = $totalPenjualan;
                            $bagianBadanUsaha = 0;
                        }
                    }
                } else {
                    // Konsinyasi normal
                    if ($hargaKonsinyasi !== null && $hargaKonsinyasi > 0) {
                        $bagianBadanUsaha = $hargaKonsinyasi * $qtyItem;
                        $bagianVendor = $totalPenjualan - $bagianBadanUsaha;
                    } else {
                        $bagianVendor = $totalPenjualan;
                        $bagianBadanUsaha = 0;
                    }
                }
                break;

            case self::JENIS_BAGI_HASIL:
                $bagianVendor = $totalPenjualan * ($this->persentase_vendor / 100);
                $bagianBadanUsaha = $totalPenjualan * ($this->persentase_badan_usaha / 100);
                break;

            case self::JENIS_DROPSHIPPER:
                $bagianVendor = $totalPenjualan;
                $bagianBadanUsaha = 0;
                break;

            case self::JENIS_SUPPLIER:
            default:
                $bagianVendor = 0;
                $bagianBadanUsaha = $totalPenjualan;
                break;
        }

        return [
            'bagian_vendor' => round($bagianVendor, 2),
            'bagian_badan_usaha' => round($bagianBadanUsaha, 2),
        ];
    }
}
