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
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->enum('status_pencairan', ['belum_dicairkan', 'mengajukan', 'dicairkan'])
                ->default('belum_dicairkan')
                ->after('catatan');
        });

        // Migrate data lama: dana_cair=true → dicairkan
        \DB::statement("UPDATE pembagian_mitra_history SET status_pencairan = IF(dana_cair = 1, 'dicairkan', 'belum_dicairkan')");
    }

    public function down(): void
    {
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->dropColumn('status_pencairan');
        });
    }
};
