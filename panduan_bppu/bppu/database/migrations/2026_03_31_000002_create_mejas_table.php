<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_meja_id')->constrained('lokasi_mejas')->cascadeOnDelete();
            $table->string('kode_meja', 20);
            $table->string('nama', 100)->nullable();
            $table->string('nomor', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['lokasi_meja_id', 'kode_meja']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
