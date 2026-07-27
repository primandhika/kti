<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            ['key' => 'kantin_minimal_order', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'belanja_minimal_order', 'value' => '20000', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'kantin_biaya_layanan_aktif', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'kantin_biaya_layanan', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'belanja_biaya_layanan_aktif', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'belanja_biaya_layanan', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'kantin_minimal_order',
            'belanja_minimal_order',
            'kantin_biaya_layanan_aktif',
            'kantin_biaya_layanan',
            'belanja_biaya_layanan_aktif',
            'belanja_biaya_layanan',
        ])->delete();
    }
};
