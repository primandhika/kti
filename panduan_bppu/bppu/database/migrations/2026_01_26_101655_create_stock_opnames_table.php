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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('work_unit_id')->constrained('work_units')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->dateTime('opname_date'); // Tanggal SO
            $table->integer('stock_awal')->default(0); // Stock Awal (dari sistem)
            $table->integer('stock_fisik')->default(0); // Penyesuaian (stock fisik hasil hitung)
            $table->integer('selisih')->default(0); // Selisih Quantity (stock_fisik - stock_awal)

            $table->decimal('harga_pokok', 12, 2)->default(0); // COGS / Harga Beli
            $table->decimal('selisih_rupiah', 12, 2)->default(0); // Selisih dalam Rupiah

            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->string('status')->default('pending'); // pending, approved, rejected

            $table->timestamps();

            // Indexes untuk query performance
            $table->index(['barang_id', 'opname_date']);
            $table->index(['work_unit_id', 'opname_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
