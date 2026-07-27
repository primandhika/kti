<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    protected $fillable = [
        'barang_id',
        'work_unit_id',
        'user_id',
        'opname_date',
        'stock_awal',
        'stock_fisik',
        'selisih',
        'harga_pokok',
        'selisih_rupiah',
        'keterangan',
        'expired_date',
        'status',
    ];

    protected $casts = [
        'opname_date' => 'datetime',
        'expired_date' => 'date',
        'stock_awal' => 'integer',
        'stock_fisik' => 'integer',
        'selisih' => 'integer',
        'harga_pokok' => 'decimal:2',
        'selisih_rupiah' => 'decimal:2',
    ];

    /**
     * Get the barang that owns the stock opname
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Get the work unit that owns the stock opname
     */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * Get the user who created the stock opname
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriod($query, $period, $startDate = null, $endDate = null)
    {
        if ($period === 'custom' && $startDate && $endDate) {
            return $query->whereBetween('opname_date', [$startDate, $endDate]);
        }

        $now = now();

        switch ($period) {
            case 'today':
                return $query->whereDate('opname_date', $now->toDateString());
            case 'yesterday':
                return $query->whereDate('opname_date', $now->subDay()->toDateString());
            case 'this_week':
                return $query->whereBetween('opname_date', [
                    $now->startOfWeek(),
                    $now->endOfWeek()
                ]);
            case 'last_week':
                return $query->whereBetween('opname_date', [
                    $now->subWeek()->startOfWeek(),
                    $now->subWeek()->endOfWeek()
                ]);
            case 'this_month':
                return $query->whereYear('opname_date', $now->year)
                             ->whereMonth('opname_date', $now->month);
            case 'last_month':
                return $query->whereYear('opname_date', $now->subMonth()->year)
                             ->whereMonth('opname_date', $now->subMonth()->month);
            case 'this_year':
                return $query->whereYear('opname_date', $now->year);
            case 'last_year':
                return $query->whereYear('opname_date', $now->subYear()->year);
            default:
                return $query;
        }
    }

    /**
     * Scope for approved opnames only
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending opnames only
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
