<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangVarianKomponen extends Model
{
    protected $fillable = [
        'varian_id',
        'barang_komponen_id',
        'qty',
        'satuan',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    public function varian()
    {
        return $this->belongsTo(BarangVarian::class, 'varian_id');
    }

    public function barangKomponen()
    {
        return $this->belongsTo(Barang::class, 'barang_komponen_id');
    }
}
