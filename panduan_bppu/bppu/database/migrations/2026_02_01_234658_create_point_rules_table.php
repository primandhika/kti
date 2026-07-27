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
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama rule (misal: "Transaksi Kecil")
            $table->decimal('min_amount', 15, 2); // Minimum transaksi
            $table->decimal('max_amount', 15, 2)->nullable(); // Maximum transaksi (null = unlimited)
            $table->integer('points'); // Poin yang didapat
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // Urutan rule
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};
