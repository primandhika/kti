<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategoriMenu extends Model
{
    protected $table = 'sub_kategori_menu';

    protected $fillable = [
        'nama',
        'urutan',
    ];

    /**
     * Get count of menu items using this sub kategori
     */
    public function getJumlahMenuAttribute()
    {
        return Barang::where('is_menu_item', true)
            ->where('sub_kategori', $this->nama)
            ->count();
    }
}
