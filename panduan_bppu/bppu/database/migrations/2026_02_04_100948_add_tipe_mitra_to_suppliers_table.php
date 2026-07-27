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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('tipe_mitra', [
                'supplier',
                'tenant',
                'vendor',
                'distributor',
                'produsen',
                'reseller',
                'konsinyasi',
                'lainnya'
            ])->default('supplier')->after('kode_supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('tipe_mitra');
        });
    }
};
