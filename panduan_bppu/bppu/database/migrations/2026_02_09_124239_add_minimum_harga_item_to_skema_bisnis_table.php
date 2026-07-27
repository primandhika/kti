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
        Schema::table('skema_bisnis', function (Blueprint $table) {
            $table->decimal('minimum_harga_item', 15, 2)->nullable()->after('nominal_flat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skema_bisnis', function (Blueprint $table) {
            $table->dropColumn('minimum_harga_item');
        });
    }
};
