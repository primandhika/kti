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
        Schema::table('barang_varians', function (Blueprint $table) {
            $table->decimal('hpp_tambahan', 12, 2)->default(0)->after('harga_grosir')
                ->comment('Tambahan HPP untuk varian ini. HPP final = barang.hpp + hpp_tambahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_varians', function (Blueprint $table) {
            $table->dropColumn('hpp_tambahan');
        });
    }
};
