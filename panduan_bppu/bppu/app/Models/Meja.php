<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Meja extends Model
{
    protected $table = 'mejas';

    protected $fillable = [
        'lokasi_meja_id',
        'kode_meja',
        'nama',
        'nomor',
        'is_active',
        'display_order',
        'qr_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $meja) {
            if (empty($meja->qr_token)) {
                do {
                    $token = Str::random(16);
                } while (self::where('qr_token', $token)->exists());

                $meja->qr_token = $token;
            }
        });
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiMeja::class, 'lokasi_meja_id');
    }

    public function lokasiMeja()
    {
        return $this->belongsTo(LokasiMeja::class, 'lokasi_meja_id');
    }

    public function getSelfOrderUrlAttribute(): string
    {
        return url('/kantin/self-order?meja=' . $this->qr_token);
    }
}
