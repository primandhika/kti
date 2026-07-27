<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanBarang extends Model
{
    protected $table = 'pengajuan_barang';

    protected $fillable = [
        'barang_id',
        'work_unit_id',
        'diajukan_oleh',
        'diproses_oleh',
        'jenis',
        'nilai_lama',
        'nilai_baru',
        'keterangan',
        'status',
        'catatan_pengelola',
        'diproses_at',
    ];

    protected $casts = [
        'diproses_at' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForBarang($query, $barangId)
    {
        return $query->where('barang_id', $barangId);
    }
}
