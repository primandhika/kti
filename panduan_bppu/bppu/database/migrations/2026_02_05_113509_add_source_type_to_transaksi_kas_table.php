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
            $table->enum('source_type', ['manual', 'kas-lain', 'unit-kerja'])
                  ->default('manual')
                  ->after('buku_kas_id')
                  ->comment('Tipe sumber transaksi: manual, dari kas lain, atau dari penghasilan unit kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
