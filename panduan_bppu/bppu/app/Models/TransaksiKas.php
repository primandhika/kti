<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $fillable = [
        'buku_kas_id',
        'source_type',
        'transaction_id',
        'tanggal',
        'kategori',
        'jenis_transaksi',
        'unit_kerja_id',
        'deskripsi',
        'pemasukan',
        'pengeluaran',
        'bukti_transaksi',
        'bukti_transaksi_type',
        'bukti_transaksi_link',
        'bukti_aktivitas',
        'bukti_aktivitas_type',
        'bukti_aktivitas_link',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pemasukan' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate transaction_id after creation (to avoid race condition)
        static::created(function ($transaksi) {
            if (empty($transaksi->transaction_id)) {
                // Use the actual ID that was just assigned
                $prefix = $transaksi->pemasukan > 0 ? 'PM' : 'PK';
                $transaksi->transaction_id = $prefix . str_pad($transaksi->id, 9, '0', STR_PAD_LEFT);

                // Save without triggering events again
                $transaksi->saveQuietly();
            }
        });
    }

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(WorkUnit::class, 'unit_kerja_id');
    }
}
