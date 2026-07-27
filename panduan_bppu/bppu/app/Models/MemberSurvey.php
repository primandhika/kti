<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSurvey extends Model
{
    protected $fillable = [
        'user_id',
        'rating_pelayanan',
        'rating_kualitas',
        'rating_variasi',
        'rating_harga',
        'rating_suasana',
        'menu_favorit',
        'menu_rekomendasi',
        'kritik_saran',
    ];

    protected $casts = [
        'rating_pelayanan' => 'integer',
        'rating_kualitas' => 'integer',
        'rating_variasi' => 'integer',
        'rating_harga' => 'integer',
        'rating_suasana' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
