<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'deskripsi',
        'status',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
