<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangVarian extends Model
{
    protected $fillable = [
        'barang_id',
        'nama_varian',
        'deskripsi',
        'harga_jual',
        'harga_grosir',
        'hpp_tambahan',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'harga_grosir' => 'decimal:2',
        'hpp_tambahan' => 'decimal:2',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = ['hpp_final', 'margin', 'margin_persen'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function komponens()
    {
        return $this->hasMany(BarangVarianKomponen::class, 'varian_id');
    }

    /**
     * Scope untuk hanya mendapatkan varian yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk ordering berdasarkan display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('nama_varian');
    }

    /**
     * Get HPP final (HPP base barang + HPP tambahan varian)
     */
    public function getHppFinalAttribute()
    {
        $hppBase = $this->barang->hpp ?? 0;
        return $hppBase + ($this->hpp_tambahan ?? 0);
    }

    /**
     * Get margin dalam rupiah (Harga Jual - HPP Final)
     */
    public function getMarginAttribute()
    {
        return $this->harga_jual - $this->hpp_final;
    }

    public function getMarginPersenAttribute()
    {
        if ($this->harga_jual == 0) {
            return 0;
        }
        return ($this->margin / $this->harga_jual) * 100;
    }

    /**
     * Total HPP dari semua komponen (qty * harga_beli masing-masing komponen)
     */
    public function getHppKomponenAttribute()
    {
        if (!$this->relationLoaded('komponens')) {
            return 0;
        }

        $komponens = $this->komponens;
        if (!($komponens instanceof \Illuminate\Support\Collection)) {
            return 0;
        }

        return $komponens->sum(function ($komponen) {
            $hargaBeli = $komponen->barangKomponen?->harga_beli ?? 0;
            return (float) $komponen->qty * (float) $hargaBeli;
        });
    }

    public function getHargaRekomendasiAttribute()
    {
        if (!$this->relationLoaded('komponens')) {
            return null;
        }

        $komponens = $this->komponens;
        if (!($komponens instanceof \Illuminate\Support\Collection) || $komponens->isEmpty()) {
            return null;
        }

        $hppTotal = $this->hpp_final + $this->hpp_komponen;
        return round($hppTotal * 1.3 / 100) * 100;
    }
}
