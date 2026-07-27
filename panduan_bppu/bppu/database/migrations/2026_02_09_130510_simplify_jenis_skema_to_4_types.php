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
        // Migrate ALL data dari skema lama ke skema baru
        DB::statement("UPDATE skema_bisnis SET jenis_skema = 'bagi_hasil' WHERE jenis_skema IN ('flat_per_item_conditional', 'flat_per_item', 'flat_per_transaksi', 'persentase_keuntungan')");

        // Update enum to 4 types only
        DB::statement("ALTER TABLE skema_bisnis MODIFY COLUMN jenis_skema ENUM('konsinyasi', 'bagi_hasil', 'dropshipper', 'supplier') DEFAULT 'supplier'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback enum
        DB::statement("ALTER TABLE skema_bisnis MODIFY COLUMN jenis_skema ENUM('konsinyasi', 'bagi_hasil', 'dropshipper', 'flat_per_item', 'flat_per_item_conditional', 'flat_per_transaksi', 'persentase_keuntungan') DEFAULT 'konsinyasi'");
    }
};
