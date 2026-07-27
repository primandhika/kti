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
        Schema::create('penjualan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->string('nama_barang')->comment('Snapshot nama barang saat transaksi');
            $table->integer('qty')->comment('Jumlah barang');
            $table->string('satuan')->comment('Satuan barang');
            $table->decimal('harga_satuan', 12, 2)->comment('Harga per unit saat transaksi');
            $table->decimal('diskon_per_item', 12, 2)->default(0)->comment('Diskon per item');
            $table->decimal('subtotal', 15, 2)->comment('Subtotal (qty * harga - diskon)');
            $table->timestamps();

            // Indexes
            $table->index('penjualan_id');
            $table->index('barang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_items');
    }
};
