<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BukuKas extends Model
{
    protected $table = 'buku_kas';

    protected $fillable = [
        'nama',
        'keterangan',
        'user_id',
        'jenis_buku_kas_id',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Global scope to filter out soft deleted records
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_deleted', 0);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisBukuKas()
    {
        return $this->belongsTo(JenisBukuKas::class, 'jenis_buku_kas_id');
    }

    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class);
    }

    public function getTotalPemasukanAttribute()
    {
        return $this->transaksiKas()->sum('pemasukan');
    }

    public function getTotalPengeluaranAttribute()
    {
        return $this->transaksiKas()->sum('pengeluaran');
    }

    public function getSaldoAttribute()
    {
        return $this->total_pemasukan - $this->total_pengeluaran;
    }

    /**
     * Soft delete the record by setting is_deleted to 1
     */
    public function softDelete()
    {
        $this->is_deleted = 1;
        return $this->save();
    }

    /**
     * Restore soft deleted record
     */
    public function restore()
    {
        $this->is_deleted = 0;
        return $this->save();
    }
}
