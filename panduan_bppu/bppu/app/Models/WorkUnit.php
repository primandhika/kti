<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkUnit extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'type',
        'description',
        'location',
        'manager_name',
        'contact_phone',
        'contact_email',
        'operating_hours',
        'logo',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get barangs (items) for this work unit
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    /**
     * Scope for filtering by type (toko, kantin, or shop)
     */
    public function scopeTokoOrKantin($query)
    {
        return $query->whereRaw('LOWER(type) IN (?, ?, ?)', ['toko', 'kantin', 'shop']);
    }

    /**
     * Generate a unique 6-digit alphanumeric unit ID
     */
    public static function generateUnitId(): string
    {
        do {
            $unitId = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (self::where('unit_id', $unitId)->exists());

        return $unitId;
    }

    /**
     * Get users yang mengelola work unit ini
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_work_unit');
    }

    /**
     * Many-to-many relationship with barangs (via pivot table)
     */
    public function barangsWithPivot()
    {
        return $this->belongsToMany(Barang::class, 'barang_work_unit')
            ->withPivot('stok', 'minimum_stok', 'harga_jual_override', 'harga_grosir_override', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get penjualan for this work unit
     */
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}
