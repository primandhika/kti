<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'work_unit_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'kategori_id',
        'sub_kategori',
        'satuan',
        'stok',
        'harga_beli',
        'harga_jual',
        'harga_grosir',
        'harga_konsinyasi',
        'diskon_tipe',
        'diskon_nilai',
        'diskon_mulai',
        'diskon_selesai',
        'minimum_stok',
        'deskripsi',
        'supplier',
        'supplier_id',
        'jenis_barang',
        'tanggal_kadaluarsa',
        'is_menu_item',
        'is_featured',
        'show_in_shop',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'stok' => 'integer',
        'minimum_stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'harga_grosir' => 'decimal:2',
        'harga_konsinyasi' => 'decimal:2',
        'diskon_nilai' => 'decimal:2',
        'diskon_mulai' => 'date',
        'diskon_selesai' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'is_menu_item' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_shop' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    // Alias untuk backward compatibility
    public function toko()
    {
        return $this->workUnit();
    }

    /**
     * Get kategori barang
     */
    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    /**
     * Get menu display for this barang
     */
    public function menuDisplay()
    {
        return $this->hasOne(MenuDisplay::class);
    }

    /**
     * Get stock opname records for this barang
     */
    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }

    /**
     * Get the latest approved stock opname
     */
    public function latestStockOpname()
    {
        return $this->hasOne(StockOpname::class)
            ->where('status', 'approved')
            ->latest('opname_date');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stok', '<=', 'minimum_stok');
    }

    public function isLowStock()
    {
        return $this->stok <= $this->minimum_stok;
    }

    /**
     * Many-to-many relationship with work units (via pivot table)
     */
    public function workUnits()
    {
        return $this->belongsToMany(WorkUnit::class, 'barang_work_unit')
            ->withPivot('stok', 'minimum_stok', 'harga_jual_override', 'harga_grosir_override', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get penjualan items for this barang
     */
    public function penjualanItems()
    {
        return $this->hasMany(PenjualanItem::class);
    }

    /**
     * Get varian-varian barang
     */
    public function varians()
    {
        return $this->hasMany(BarangVarian::class);
    }

    /**
     * Get active varians only
     */
    public function activeVarians()
    {
        return $this->hasMany(BarangVarian::class)->where('is_active', true)->orderBy('display_order')->orderBy('nama_varian');
    }

    /**
     * Get the user who created this barang
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the supplier for this barang
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pengajuanBarang()
    {
        return $this->hasMany(\App\Models\PengajuanBarang::class, 'barang_id');
    }

    /**
     * Accessor: hpp adalah alias untuk harga_beli
     */
    public function getHppAttribute()
    {
        return $this->harga_beli;
    }

    /**
     * Cek apakah diskon sedang aktif hari ini
     */
    public function isDiskonAktif(): bool
    {
        if (!$this->diskon_tipe || !$this->diskon_nilai) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->diskon_mulai && $today < $this->diskon_mulai->toDateString()) {
            return false;
        }

        if ($this->diskon_selesai && $today > $this->diskon_selesai->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Hitung potongan nominal dari diskon aktif
     * Mengembalikan 0 jika tidak ada diskon aktif
     */
    public function getNominalDiskonAttribute(): float
    {
        if (!$this->isDiskonAktif()) {
            return 0;
        }

        $harga = (float) $this->harga_jual;
        $nilai = (float) $this->diskon_nilai;

        return match ($this->diskon_tipe) {
            'persen'        => round($harga * $nilai / 100),
            'nominal'       => min($nilai, $harga),
            'harga_menjadi' => max(0, $harga - $nilai),
            default         => 0,
        };
    }
}
