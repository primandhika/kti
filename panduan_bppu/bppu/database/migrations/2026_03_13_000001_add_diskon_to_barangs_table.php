<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->enum('diskon_tipe', ['persen', 'nominal', 'harga_menjadi'])->nullable()->after('harga_konsinyasi');
            $table->decimal('diskon_nilai', 12, 2)->nullable()->after('diskon_tipe');
            $table->date('diskon_mulai')->nullable()->after('diskon_nilai');
            $table->date('diskon_selesai')->nullable()->after('diskon_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['diskon_tipe', 'diskon_nilai', 'diskon_mulai', 'diskon_selesai']);
        });
    }
};
