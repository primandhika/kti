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
        Schema::create('barang_work_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('work_unit_id')->constrained('work_units')->onDelete('cascade');

            // Stock specific to this work unit
            $table->integer('stok')->default(0);
            $table->integer('minimum_stok')->default(5);

            // Optional: specific pricing per work unit (override master pricing)
            $table->decimal('harga_jual_override', 12, 2)->nullable();
            $table->decimal('harga_grosir_override', 12, 2)->nullable();

            $table->boolean('is_active')->default(true); // Barang aktif di unit ini?

            $table->timestamps();

            // Composite unique: 1 barang only once per work unit
            $table->unique(['barang_id', 'work_unit_id']);

            // Indexes
            $table->index('barang_id');
            $table->index('work_unit_id');
            $table->index('is_active');
        });

        // Migrate existing data from barangs table
        // Copy work_unit_id and stok to pivot table (only for valid work units)
        DB::statement("
            INSERT INTO barang_work_unit (barang_id, work_unit_id, stok, minimum_stok, is_active, created_at, updated_at)
            SELECT b.id, b.work_unit_id, b.stok, b.minimum_stok, 1, b.created_at, b.updated_at
            FROM barangs b
            INNER JOIN work_units wu ON b.work_unit_id = wu.id
            WHERE b.work_unit_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_work_unit');
    }
};
