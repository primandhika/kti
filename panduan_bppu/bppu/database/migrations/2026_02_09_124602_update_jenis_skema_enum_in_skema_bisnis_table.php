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
        DB::statement("ALTER TABLE skema_bisnis MODIFY COLUMN jenis_skema ENUM('konsinyasi', 'bagi_hasil', 'dropshipper', 'flat_per_item', 'flat_per_item_conditional', 'flat_per_transaksi', 'persentase_keuntungan') DEFAULT 'konsinyasi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE skema_bisnis MODIFY COLUMN jenis_skema ENUM('konsinyasi', 'bagi_hasil', 'dropshipper', 'flat_per_item', 'flat_per_transaksi', 'persentase_keuntungan') DEFAULT 'konsinyasi'");
    }
};
