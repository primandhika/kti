<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Penjualan extends Model
{
    protected $fillable = [
        'nomor_transaksi',
        'work_unit_id',
        'user_id',
        'buyer_id',
        'tanggal_transaksi',
        'subtotal',
        'biaya_layanan',
        'diskon',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'nama_pelanggan',
        'catatan',
        'foto_bukti',
        'status',
        'is_entry',
        'is_verified',
        'is_approved',
        'is_recorded',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'recorded_by',
        'recorded_at',
        'transaksi_kas_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'subtotal' => 'decimal:2',
        'biaya_layanan' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'is_entry' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_recorded' => 'boolean',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'recorded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($penjualan) {
            if (empty($penjualan->nomor_transaksi)) {
                $penjualan->nomor_transaksi = self::generateNomorTransaksi();
            }
            if (empty($penjualan->tanggal_transaksi)) {
                $penjualan->tanggal_transaksi = now();
            }
        });
    }

    public static function generateNomorTransaksi()
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        do {
            $nomorTransaksi = $prefix . $date . $random;
        } while (self::where('nomor_transaksi', $nomorTransaksi)->exists());

        return $nomorTransaksi;
    }

    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items()
    {
        return $this->hasMany(PenjualanItem::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function transaksiKas()
    {
        return $this->belongsTo(\App\Models\TransaksiKas::class, 'transaksi_kas_id');
    }

    public function voucher()
    {
        return $this->hasOne(\App\Models\Voucher::class);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_transaksi', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_transaksi', now()->month)
                     ->whereYear('tanggal_transaksi', now()->year);
    }

    public function scopeNeedVerification($query)
    {
        return $query->where('is_entry', true)
                     ->where('is_verified', false)
                     ->where('status', 'selesai');
    }

    public function scopeNeedApproval($query)
    {
        return $query->where('is_verified', true)
                     ->where('is_approved', false)
                     ->where('status', 'selesai');
    }

    public function scopeNeedRecording($query)
    {
        return $query->where('is_approved', true)
                     ->where('is_recorded', false)
                     ->where('status', 'selesai');
    }

    public function getVerificationStatusAttribute()
    {
        if ($this->status === 'dibatalkan') {
            return 'cancelled';
        }
        if ($this->is_recorded) {
            return 'recorded';
        }
        if ($this->is_approved) {
            return 'approved';
        }
        if ($this->is_verified) {
            return 'verified';
        }
        if ($this->is_entry) {
            return 'entry';
        }
        return 'unknown';
    }
}
