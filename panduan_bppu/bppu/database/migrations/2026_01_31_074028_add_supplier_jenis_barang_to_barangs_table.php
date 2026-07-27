<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('supplier')->nullable()->after('deskripsi');
            $table->enum('jenis_barang', [
                'jual_langsung',
                'konsinyasi',
                'preorder',
                'bundling',
                'langganan',
                'dropship_internal'
            ])->default('jual_langsung')->after('supplier');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['supplier', 'jenis_barang']);
        });
    }
};
