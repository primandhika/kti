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
        Schema::table('sub_kategori_toko', function (Blueprint $table) {
            $table->foreignId('kategori_barang_id')->nullable()->after('id')->constrained('kategori_barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_kategori_toko', function (Blueprint $table) {
            $table->dropForeign(['kategori_barang_id']);
            $table->dropColumn('kategori_barang_id');
        });
    }
};
