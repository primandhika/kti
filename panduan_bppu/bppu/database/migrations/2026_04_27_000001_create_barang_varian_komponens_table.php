<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_varian_komponens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('varian_id')->constrained('barang_varians')->onDelete('cascade');
            $table->foreignId('barang_komponen_id')->constrained('barangs')->onDelete('cascade');
            $table->decimal('qty', 10, 3)->default(1);
            $table->string('satuan', 50)->nullable();
            $table->timestamps();

            $table->index(['varian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_varian_komponens');
    }
};
