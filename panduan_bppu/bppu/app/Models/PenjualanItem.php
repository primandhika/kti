<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanItem extends Model
{
    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'varian_id',
        'nama_barang',
        'qty',
        'satuan',
        'harga_satuan',
        'diskon_per_item',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'diskon_per_item' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected $appends = ['hpp_satuan', 'total_hpp', 'margin_satuan', 'total_margin'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function varian()
    {
        return $this->belongsTo(BarangVarian::class, 'varian_id');
    }

    /**
     * Get HPP satuan (jika ada varian, gunakan HPP varian, jika tidak gunakan HPP barang)
     */
    public function getHppSatuanAttribute()
    {
        if ($this->varian_id && $this->varian) {
            return $this->varian->hpp_final;
        }

        if ($this->barang) {
            return $this->barang->hpp ?? 0;
        }

        return 0;
    }

    /**
     * Get total HPP untuk item ini (HPP satuan * qty)
     */
    public function getTotalHppAttribute()
    {
        return $this->hpp_satuan * $this->qty;
    }

    /**
     * Get margin per satuan (harga_satuan - diskon_per_item - hpp_satuan)
     */
    public function getMarginSatuanAttribute()
    {
        return ($this->harga_satuan - $this->diskon_per_item) - $this->hpp_satuan;
    }

    /**
     * Get total margin untuk item ini
     */
    public function getTotalMarginAttribute()
    {
        return $this->margin_satuan * $this->qty;
    }
}
