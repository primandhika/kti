<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Potongan extends Model
{
    use SoftDeletes;

    protected $table = 'potongan';

    protected $fillable = [
        'kode_potongan',
        'nama',
        'deskripsi',
        'tipe',
        'nilai',
        'minimum_transaksi',
        'maksimum_potongan',
        'berlaku_mulai',
        'berlaku_sampai',
        'kuota_voucher',
        'is_active',
        'created_by',
        'barang_ids',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'minimum_transaksi' => 'decimal:2',
        'maksimum_potongan' => 'decimal:2',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'is_active' => 'boolean',
        'barang_ids' => 'array',
    ];

    protected function berlakuMulai(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $val ? \Carbon\Carbon::parse($val)->toDateString() : null,
        );
    }

    protected function berlakuSampai(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $val ? \Carbon\Carbon::parse($val)->toDateString() : null,
        );
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getJumlahDraftAttribute(): int
    {
        return $this->vouchers()->where('status', 'draft')->count();
    }

    public function getJumlahPrintedAttribute(): int
    {
        return $this->vouchers()->where('status', 'printed')->count();
    }

    public function getJumlahUsedAttribute(): int
    {
        return $this->vouchers()->where('status', 'used')->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return $this->kuota_voucher - $this->vouchers()->count();
    }
}
