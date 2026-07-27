<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'kode_voucher',
        'potongan_id',
        'status',
        'penjualan_id',
        'used_by',
        'printed_at',
        'used_at',
        'created_by',
        'assigned_to',
        'assigned_at',
        'transaksi_kas_id',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'used_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function potongan()
    {
        return $this->belongsTo(Potongan::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class, 'transaksi_kas_id');
    }
}
