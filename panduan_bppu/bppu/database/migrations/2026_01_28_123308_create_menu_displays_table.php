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
        Schema::create('menu_displays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('gambar')->nullable(); // path to image
            $table->text('deskripsi_display')->nullable(); // marketing description
            $table->boolean('is_available')->default(true); // available today?
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('barang_id'); // one display per barang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_displays');
    }
};
