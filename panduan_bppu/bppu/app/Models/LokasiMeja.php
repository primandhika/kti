<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiMeja extends Model
{
    protected $table = 'lokasi_mejas';

    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
        'is_active',
        'is_public',
        'display_order',
        'default_work_unit_id',
        'latitude',
        'longitude',
        'radius_meter',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'display_order' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meter' => 'integer',
    ];

    public function mejas()
    {
        return $this->hasMany(Meja::class, 'lokasi_meja_id');
    }

    public function defaultWorkUnit()
    {
        return $this->belongsTo(\App\Models\WorkUnit::class, 'default_work_unit_id');
    }
}
