<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_self_order', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian', 10); // contoh: 001
            $table->date('tanggal_antrian');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pemesan')->nullable();
            $table->json('items'); // snapshot item yang dipesan
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('catatan')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'siap', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->nullOnDelete();
            $table->timestamps();

            // Unique per nomor antrian per hari
            $table->unique(['nomor_antrian', 'tanggal_antrian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_self_order');
    }
};
