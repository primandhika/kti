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
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->foreignId('jenis_buku_kas_id')->nullable()->after('user_id')->constrained('jenis_buku_kas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropForeign(['jenis_buku_kas_id']);
            $table->dropColumn('jenis_buku_kas_id');
        });
    }
};
