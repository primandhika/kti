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
        Schema::table('lokasi_mejas', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('is_public');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedSmallInteger('radius_meter')->nullable()->default(200)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('lokasi_mejas', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius_meter']);
        });
    }
};
