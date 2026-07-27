<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lokasi_mejas', function (Blueprint $table) {
            $table->foreignId('default_work_unit_id')
                ->nullable()
                ->after('display_order')
                ->constrained('work_units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lokasi_mejas', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\WorkUnit::class, 'default_work_unit_id');
            $table->dropColumn('default_work_unit_id');
        });
    }
};
