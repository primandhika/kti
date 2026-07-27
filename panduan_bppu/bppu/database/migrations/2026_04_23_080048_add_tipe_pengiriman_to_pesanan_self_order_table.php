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
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->enum('tipe_pengiriman', ['antar', 'pickup'])->default('antar')->after('catatan');
            $table->string('estimasi_ambil', 100)->nullable()->after('tipe_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->dropColumn(['tipe_pengiriman', 'estimasi_ambil']);
        });
    }
};
