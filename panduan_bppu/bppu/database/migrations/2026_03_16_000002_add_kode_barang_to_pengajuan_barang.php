<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum kolom jenis agar include kode_barang
        DB::statement("ALTER TABLE pengajuan_barang MODIFY COLUMN jenis ENUM('stok', 'nama_barang', 'kode_barang') NOT NULL DEFAULT 'stok'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengajuan_barang MODIFY COLUMN jenis ENUM('stok', 'nama_barang') NOT NULL DEFAULT 'stok'");
    }
};
