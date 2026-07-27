<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTransaksi extends Model
{
    protected $table = 'kategori_transaksi';

    protected $fillable = [
        'nama',
        'jenis',
        'tipe',
        'kode_akun',
        'kode',
        'deskripsi',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected $appends = ['tipe', 'kode'];

    /**
     * Accessor for 'tipe' - alias for 'jenis'
     */
    public function getTipeAttribute()
    {
        return $this->attributes['jenis'] ?? $this->attributes['tipe'] ?? null;
    }

    /**
     * Mutator for 'tipe' - set 'jenis'
     */
    public function setTipeAttribute($value)
    {
        $this->attributes['jenis'] = $value;
    }

    /**
     * Accessor for 'kode' - alias for 'kode_akun'
     */
    public function getKodeAttribute()
    {
        return $this->attributes['kode_akun'] ?? $this->attributes['kode'] ?? null;
    }

    /**
     * Mutator for 'kode' - set 'kode_akun'
     */
    public function setKodeAttribute($value)
    {
        $this->attributes['kode_akun'] = $value;
    }

    /**
     * Scope untuk kategori yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk sorting berdasarkan urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('nama', 'asc');
    }

    /**
     * Scope untuk filter berdasarkan jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where(function($q) use ($jenis) {
            $q->where('jenis', $jenis)
              ->orWhere('jenis', 'both');
        });
    }
}
