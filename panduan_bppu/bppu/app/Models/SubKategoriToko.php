<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategoriToko extends Model
{
    protected $table = 'sub_kategori_toko';

    protected $fillable = [
        'kategori_barang_id',
        'nama',
        'urutan',
    ];

    /**
     * Get kategori barang relationship
     */
    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    /**
     * Get count of shop items using this sub kategori
     */
    public function getJumlahBarangAttribute()
    {
        return Barang::where('show_in_shop', true)
            ->where('sub_kategori', $this->nama)
            ->count();
    }
}
