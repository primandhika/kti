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
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->string('slug')->unique(); // bronze, silver, gold, platinum
            $table->text('description')->nullable();
            $table->integer('min_points')->default(0); // Minimum poin untuk mencapai tier ini
            $table->decimal('discount_percentage', 5, 2)->default(0); // Persentase diskon (0-100)
            $table->decimal('points_multiplier', 3, 2)->default(1.00); // Pengali poin (1.00 = normal, 1.50 = 1.5x)
            $table->integer('order')->default(0); // Urutan tier (untuk sorting)
            $table->string('color')->nullable(); // Warna badge/card
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
