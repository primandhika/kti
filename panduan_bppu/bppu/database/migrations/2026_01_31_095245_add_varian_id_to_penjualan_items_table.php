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
        Schema::table('penjualan_items', function (Blueprint $table) {
            $table->foreignId('varian_id')->nullable()->after('barang_id')->constrained('barang_varians')->onDelete('set null');
            $table->index('varian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_items', function (Blueprint $table) {
            $table->dropForeign(['varian_id']);
            $table->dropIndex(['varian_id']);
            $table->dropColumn('varian_id');
        });
    }
};
