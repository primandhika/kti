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
        Schema::table('barangs', function (Blueprint $table) {
            // Drop the old unique constraint on kode_barang
            $table->dropUnique('barangs_kode_barang_unique');
            
            // Add composite unique constraint on work_unit_id + kode_barang
            // This allows same barcode to exist in different work units
            $table->unique(['work_unit_id', 'kode_barang'], 'barangs_work_unit_kode_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('barangs_work_unit_kode_unique');
            
            // Restore the old unique constraint on kode_barang
            $table->unique('kode_barang', 'barangs_kode_barang_unique');
        });
    }
};
