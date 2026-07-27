<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->foreignId('meja_id')->nullable()->constrained('mejas')->nullOnDelete()->after('buyer_id');
            $table->string('nama_meja', 100)->nullable()->after('meja_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('default_meja_id')->nullable()->constrained('mejas')->nullOnDelete()->after('member_since');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_self_order', function (Blueprint $table) {
            $table->dropForeign(['meja_id']);
            $table->dropColumn(['meja_id', 'nama_meja']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_meja_id']);
            $table->dropColumn('default_meja_id');
        });
    }
};
