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
        Schema::table('penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('transaksi_kas_id')->nullable()->after('recorded_at');
            $table->foreign('transaksi_kas_id')->references('id')->on('transaksi_kas')->onDelete('set null');
            $table->index('transaksi_kas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['transaksi_kas_id']);
            $table->dropIndex(['transaksi_kas_id']);
            $table->dropColumn('transaksi_kas_id');
        });
    }
};
