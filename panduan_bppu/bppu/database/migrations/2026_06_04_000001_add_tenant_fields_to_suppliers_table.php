<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('penempatan_work_unit_id')->nullable()->after('catatan');
            $table->decimal('biaya_kontribusi', 15, 2)->nullable()->after('penempatan_work_unit_id');

            $table->foreign('penempatan_work_unit_id')->references('id')->on('work_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['penempatan_work_unit_id']);
            $table->dropColumn(['penempatan_work_unit_id', 'biaya_kontribusi']);
        });
    }
};
