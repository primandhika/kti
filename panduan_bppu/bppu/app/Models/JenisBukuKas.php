<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JenisBukuKas extends Model
{
    protected $table = 'jenis_buku_kas';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'warna',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function bukuKas()
    {
        return $this->hasMany(BukuKas::class, 'jenis_buku_kas_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('display_order')->orderBy('nama');
    }
}
