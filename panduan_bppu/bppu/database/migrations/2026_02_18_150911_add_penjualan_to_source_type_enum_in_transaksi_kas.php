<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksi_kas MODIFY COLUMN source_type ENUM('manual','kas-lain','unit-kerja','penjualan') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transaksi_kas MODIFY COLUMN source_type ENUM('manual','kas-lain','unit-kerja') NULL");
    }
};
