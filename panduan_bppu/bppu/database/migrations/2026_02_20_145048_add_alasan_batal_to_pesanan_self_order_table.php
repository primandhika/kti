<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->string('kode_alasan_batal')->nullable()->after('status');
            $table->string('alasan_batal')->nullable()->after('kode_alasan_batal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->dropColumn(['kode_alasan_batal', 'alasan_batal']);
        });
    }
};
