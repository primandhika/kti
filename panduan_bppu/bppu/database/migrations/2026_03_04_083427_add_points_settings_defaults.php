<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default points settings
        DB::table('settings')->insert([
            [
                'key' => 'minimal_poin_redeem',
                'value' => '5000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'kurs_poin_ke_rupiah',
                'value' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove points settings
        DB::table('settings')->whereIn('key', [
            'minimal_poin_redeem',
            'kurs_poin_ke_rupiah',
        ])->delete();
    }
};
