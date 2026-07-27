<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPencairan extends Model
{
    protected $table = 'pengajuan_pencairan';

    protected $fillable = [
        'supplier_id',
        'jumlah_diajukan',
        'tanggal_dari',
        'tanggal_sampai',
        'metode',
        'detail_rekening',
        'catatan',
        'status',
        'catatan_pengelola',
        'diproses_oleh',
        'diproses_at',
    ];

    protected $casts = [
        'tanggal_dari'   => 'date',
        'tanggal_sampai' => 'date',
        'diproses_at'    => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
