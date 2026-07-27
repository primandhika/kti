<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('created_by');
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users');
        });

        Schema::table('potongan', function (Blueprint $table) {
            $table->json('barang_ids')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'assigned_at']);
        });

        Schema::table('potongan', function (Blueprint $table) {
            $table->dropColumn('barang_ids');
        });
    }
};
