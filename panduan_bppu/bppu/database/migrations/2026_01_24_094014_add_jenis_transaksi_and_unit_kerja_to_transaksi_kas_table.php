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
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->string('jenis_transaksi')->nullable()->after('kategori')->comment('Jenis transaksi: Tunai, Transfer, QRIS, dll');
            $table->foreignId('unit_kerja_id')->nullable()->after('jenis_transaksi')->constrained('work_units')->onDelete('set null')->comment('Unit kerja terkait transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn(['jenis_transaksi', 'unit_kerja_id']);
        });
    }
};
