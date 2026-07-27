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
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->string('transaction_id', 50)->nullable()->unique()->after('id');
            $table->enum('bukti_transaksi_type', ['upload', 'link'])->default('upload')->after('bukti_transaksi');
            $table->text('bukti_transaksi_link')->nullable()->after('bukti_transaksi_type');
            $table->enum('bukti_aktivitas_type', ['upload', 'link'])->default('upload')->after('bukti_aktivitas');
            $table->text('bukti_aktivitas_link')->nullable()->after('bukti_aktivitas_type');
        });

        // Generate transaction_id for existing records
        DB::statement("UPDATE transaksi_kas SET transaction_id = CONCAT('TRX', LPAD(id, 9, '0')) WHERE transaction_id IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'bukti_transaksi_type', 'bukti_transaksi_link', 'bukti_aktivitas_type', 'bukti_aktivitas_link']);
        });
    }
};
