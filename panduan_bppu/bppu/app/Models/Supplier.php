<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'user_id',
        'kode_supplier',
        'logo',
        'tipe_mitra',
        'nama',
        'perusahaan',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'kontak_person',
        'telepon_kontak',
        'catatan',
        'penempatan_work_unit_id',
        'biaya_kontribusi',
        'is_active',
    ];

    // Enum values for tipe_mitra
    const TIPE_SUPPLIER = 'supplier';
    const TIPE_TENANT = 'tenant';
    const TIPE_VENDOR = 'vendor';
    const TIPE_DISTRIBUTOR = 'distributor';
    const TIPE_PRODUSEN = 'produsen';
    const TIPE_RESELLER = 'reseller';
    const TIPE_KONSINYASI = 'konsinyasi';
    const TIPE_LAINNYA = 'lainnya';

    public static function getTipeMitraOptions()
    {
        return [
            self::TIPE_SUPPLIER => 'Supplier',
            self::TIPE_TENANT => 'Tenant',
            self::TIPE_VENDOR => 'Vendor',
            self::TIPE_DISTRIBUTOR => 'Distributor',
            self::TIPE_PRODUSEN => 'Produsen',
            self::TIPE_RESELLER => 'Reseller',
            self::TIPE_KONSINYASI => 'Konsinyasi',
            self::TIPE_LAINNYA => 'Lainnya',
        ];
    }

    protected $casts = [
        'is_active' => 'boolean',
        'biaya_kontribusi' => 'decimal:2',
    ];

    public function penempatanWorkUnit()
    {
        return $this->belongsTo(\App\Models\WorkUnit::class, 'penempatan_work_unit_id');
    }

    /**
     * Relationship with User (account)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Barang (items supplied)
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }

    /**
     * Relationship with SkemaBisnis
     */
    public function skemaBisnis()
    {
        return $this->hasMany(SkemaBisnis::class, 'supplier_id');
    }

    /**
     * Get active skema bisnis
     */
    public function skemaBisnisAktif()
    {
        return $this->hasOne(SkemaBisnis::class, 'supplier_id')
            ->where('is_active', true)
            ->latest();
    }
}
