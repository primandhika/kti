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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique()->comment('Nomor unik transaksi penjualan');
            $table->foreignId('work_unit_id')->constrained('work_units')->comment('Unit kerja penjualan');
            $table->foreignId('user_id')->constrained('users')->comment('User yang menginput (kasir)');
            $table->dateTime('tanggal_transaksi')->comment('Waktu transaksi');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Total sebelum diskon');
            $table->decimal('diskon', 15, 2)->default(0)->comment('Total diskon');
            $table->decimal('total', 15, 2)->default(0)->comment('Total setelah diskon');
            $table->decimal('bayar', 15, 2)->default(0)->comment('Jumlah uang dibayar');
            $table->decimal('kembalian', 15, 2)->default(0)->comment('Uang kembalian');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'debit', 'kredit'])->default('tunai');
            $table->string('nama_pelanggan')->nullable()->comment('Nama pembeli (opsional)');
            $table->text('catatan')->nullable()->comment('Catatan transaksi');
            $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');
            $table->timestamps();

            // Indexes
            $table->index('nomor_transaksi');
            $table->index('work_unit_id');
            $table->index('user_id');
            $table->index('tanggal_transaksi');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
