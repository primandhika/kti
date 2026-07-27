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
        Schema::table('point_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('min_amount')->default(0)->change();
            $table->unsignedBigInteger('max_amount')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('point_rules', function (Blueprint $table) {
            $table->decimal('min_amount', 15, 2)->default(0)->change();
            $table->decimal('max_amount', 15, 2)->nullable()->change();
        });
    }
};
