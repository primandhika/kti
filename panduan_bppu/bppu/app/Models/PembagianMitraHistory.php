<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembagianMitraHistory extends Model
{
    protected $table = 'pembagian_mitra_history';

    protected $fillable = [
        'supplier_id',
        'work_unit_id',
        'periode_mulai',
        'periode_selesai',
        'jenis_skema',
        'skema_config',
        'total_penjualan',
        'total_qty',
        'total_items',
        'bagian_vendor',
        'bagian_badan_usaha',
        'detail_items',
        'status',
        'finalized_by',
        'finalized_at',
        'catatan',
        'dana_cair',
        'metode_pencairan',
        'detail_transfer',
        'dicairkan_oleh',
        'dicairkan_at',
        'catatan_pencairan',
        'status_pencairan',
    ];

    protected $casts = [
        'periode_mulai'  => 'date',
        'periode_selesai' => 'date',
        'total_penjualan' => 'decimal:2',
        'bagian_vendor'   => 'decimal:2',
        'bagian_badan_usaha' => 'decimal:2',
        'skema_config'    => 'array',
        'detail_items'    => 'array',
        'finalized_at'    => 'datetime',
        'dana_cair'       => 'boolean',
        'dicairkan_at'    => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function dicairkanOleh()
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
