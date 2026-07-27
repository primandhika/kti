<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanTambahBarang extends Model
{
    protected $table = 'pengajuan_tambah_barang';

    protected $fillable = [
        'work_unit_id',
        'diajukan_oleh',
        'diproses_oleh',
        'barang_id',
        'nama_barang',
        'kode_barang',
        'kategori',
        'satuan',
        'stok_awal',
        'harga_beli',
        'harga_jual',
        'keterangan',
        'status',
        'catatan_pengelola',
        'diproses_at',
    ];

    protected $casts = [
        'harga_beli'  => 'decimal:2',
        'harga_jual'  => 'decimal:2',
        'diproses_at' => 'datetime',
    ];

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

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
