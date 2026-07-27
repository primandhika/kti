<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipUsage extends Model
{
    protected $table = 'arsip_usage';

    protected $fillable = [
        'arsip_id',
        'usable_type',
        'usable_id',
        'field_name',
    ];

    /**
     * Relationship: Arsip
     */
    public function arsip()
    {
        return $this->belongsTo(Arsip::class);
    }

    /**
     * Relationship: Usable (polymorphic)
     */
    public function usable()
    {
        return $this->morphTo();
    }
}
