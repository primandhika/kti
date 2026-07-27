<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuDisplay extends Model
{
    protected $fillable = [
        'barang_id',
        'gambar',
        'deskripsi_display',
        'is_available',
        'display_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the barang (menu item) for this display
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
