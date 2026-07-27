<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberPoint extends Model
{
    /**
     * Point transaction types:
     * - earn: Mendapat poin dari transaksi (setelah approve)
     * - redeem: Menukar poin untuk potongan
     * - refund: Pengembalian poin dari redeem saat transaksi dibatalkan
     * - cancel: Pembatalan poin earn saat transaksi dibatalkan
     */
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'transaction_amount',
        'penjualan_id',
        'transaksi_kas_id',
        'description',
    ];

    protected $casts = [
        'transaction_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class, 'transaksi_kas_id');
    }
}
