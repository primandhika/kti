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
        Schema::table('pengajuan_pencairan', function (Blueprint $table) {
            $table->decimal('jumlah_diajukan', 15, 2)->default(0)->after('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pencairan', function (Blueprint $table) {
            $table->dropColumn('jumlah_diajukan');
        });
    }
};
