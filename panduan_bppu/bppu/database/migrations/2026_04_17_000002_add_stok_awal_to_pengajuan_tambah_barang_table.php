<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_tambah_barang', function (Blueprint $table) {
            $table->unsignedInteger('stok_awal')->default(0)->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_tambah_barang', function (Blueprint $table) {
            $table->dropColumn('stok_awal');
        });
    }
};
