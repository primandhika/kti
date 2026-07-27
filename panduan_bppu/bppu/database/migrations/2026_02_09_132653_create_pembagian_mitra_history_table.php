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
        Schema::create('pembagian_mitra_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('work_unit_id')->nullable()->constrained('work_units')->onDelete('set null');

            $table->date('periode_mulai');
            $table->date('periode_selesai');

            $table->string('jenis_skema', 50);
            $table->text('skema_config')->nullable()->comment('JSON snapshot skema bisnis');

            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->integer('total_qty')->default(0);
            $table->integer('total_items')->default(0);
            $table->decimal('bagian_vendor', 15, 2)->default(0);
            $table->decimal('bagian_badan_usaha', 15, 2)->default(0);

            $table->json('detail_items')->nullable()->comment('Detail per item untuk audit');

            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['supplier_id', 'periode_mulai'], 'idx_supplier_periode');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembagian_mitra_history');
    }
};
