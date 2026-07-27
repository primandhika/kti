<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->string('metode_bayar', 20)->nullable()->after('bukti_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->dropColumn('metode_bayar');
        });
    }
};
