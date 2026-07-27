<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Meja;

class PesananSelfOrder extends Model
{
    protected $table = 'pesanan_self_order';

    protected $fillable = [
        'nomor_antrian',
        'tanggal_antrian',
        'buyer_id',
        'meja_id',
        'nama_meja',
        'nama_pemesan',
        'items',
        'subtotal',
        'biaya_layanan',
        'total',
        'catatan',
        'tipe_pengiriman',
        'estimasi_ambil',
        'bukti_bayar',
        'metode_bayar',
        'status',
        'kode_alasan_batal',
        'alasan_batal',
        'penjualan_id',
    ];

    protected $casts = [
        'items' => 'array',
        'tanggal_antrian' => 'date',
        'subtotal' => 'decimal:2',
        'biaya_layanan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public static function generateNomorAntrian(): string
    {
        $today = today()->toDateString();

        $last = self::where('tanggal_antrian', $today)
            ->lockForUpdate()
            ->orderBy('nomor_antrian', 'desc')
            ->first();

        if (!$last) {
            return '001';
        }

        $next = (int) $last->nomor_antrian + 1;

        if ($next > 999) {
            $next = 999; // batas maksimal
        }

        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function workUnitIds(): array
    {
        $ids = [];
        foreach ($this->items ?? [] as $item) {
            $barang = Barang::find($item['id']);
            if ($barang && $barang->work_unit_id && !in_array($barang->work_unit_id, $ids)) {
                $ids[] = $barang->work_unit_id;
            }
        }
        return $ids;
    }

    public function getLabelStatusAttribute(): string
    {
        return match($this->status) {
            'menunggu'   => 'Menunggu',
            'diproses'   => 'Sedang Diproses',
            'siap'       => 'Siap Diambil',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default      => 'Tidak Diketahui',
        };
    }
}
