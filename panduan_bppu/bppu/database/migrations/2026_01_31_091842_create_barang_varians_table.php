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
        Schema::create('barang_varians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('nama_varian'); // e.g., "Paket Nasi Ayam Goreng Komplit"
            $table->text('deskripsi')->nullable(); // e.g., "Nasi + Ayam Goreng + Lalapan + Sambal"
            $table->decimal('harga_jual', 12, 2); // Harga khusus untuk varian ini
            $table->decimal('harga_grosir', 12, 2)->nullable(); // Harga grosir untuk varian
            $table->boolean('is_active')->default(true); // Varian aktif atau tidak
            $table->integer('display_order')->default(0); // Urutan tampilan
            $table->timestamps();

            $table->index(['barang_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_varians');
    }
};
