<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->enum('metode_pencairan', ['tunai', 'transfer_bank'])->nullable()->after('dana_cair');
            $table->string('detail_transfer', 255)->nullable()->after('metode_pencairan')
                ->comment('Nama bank, no rekening, dll');
        });
    }

    public function down(): void
    {
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->dropColumn(['metode_pencairan', 'detail_transfer']);
        });
    }
};
