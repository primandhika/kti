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
        Schema::create('skema_bisnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');

            $table->enum('jenis_skema', [
                'konsinyasi',
                'bagi_hasil',
                'dropshipper',
                'flat_per_item',
                'flat_per_transaksi',
                'persentase_keuntungan'
            ])->default('konsinyasi');

            // Config untuk bagi hasil
            $table->decimal('persentase_vendor', 5, 2)->nullable()->comment('% untuk tenant/vendor');
            $table->decimal('persentase_badan_usaha', 5, 2)->nullable()->comment('% untuk badan usaha');

            // Config untuk flat/rupiah
            $table->decimal('nominal_flat', 12, 2)->nullable()->comment('Nominal flat per item/transaksi');

            // Config untuk persentase keuntungan
            $table->decimal('persentase_dari_keuntungan', 5, 2)->nullable()->comment('% dari keuntungan');

            // Status berlakukan
            $table->boolean('is_active')->default(true)->comment('Apakah skema berlakukan');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skema_bisnis');
    }
};
