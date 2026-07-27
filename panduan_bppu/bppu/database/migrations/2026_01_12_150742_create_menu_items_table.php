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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // Label yang ditampilkan di navbar
            $table->string('url')->nullable(); // URL tujuan (bisa null jika hanya parent)
            $table->string('type')->default('internal'); // internal, external, page
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade'); // Parent menu untuk nested
            $table->integer('order')->default(0); // Urutan tampilan
            $table->boolean('is_active')->default(true); // Aktif/tidak
            $table->boolean('open_new_tab')->default(false); // Buka di tab baru
            $table->string('icon')->nullable(); // Icon (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
