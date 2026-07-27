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
        Schema::table('penjualans', function (Blueprint $table) {
            // Status verifikasi transaksi (entry > verified > approved > recorded)
            $table->boolean('is_entry')->default(true)->after('status')->comment('Transaksi sudah di-entry');
            $table->boolean('is_verified')->default(false)->after('is_entry')->comment('Transaksi sudah diverifikasi officer');
            $table->boolean('is_approved')->default(false)->after('is_verified')->comment('Transaksi sudah diapprove sysadmin');
            $table->boolean('is_recorded')->default(false)->after('is_approved')->comment('Transaksi sudah masuk buku kas');

            // Siapa yang melakukan verifikasi
            $table->foreignId('verified_by')->nullable()->after('is_recorded')->constrained('users')->comment('User yang verifikasi');
            $table->timestamp('verified_at')->nullable()->after('verified_by')->comment('Waktu verifikasi');

            $table->foreignId('approved_by')->nullable()->after('verified_at')->constrained('users')->comment('User yang approve');
            $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('Waktu approve');

            $table->foreignId('recorded_by')->nullable()->after('approved_at')->constrained('users')->comment('User yang rekam ke buku kas');
            $table->timestamp('recorded_at')->nullable()->after('recorded_by')->comment('Waktu rekam ke buku kas');

            // Index untuk query performance
            $table->index('is_verified');
            $table->index('is_approved');
            $table->index('is_recorded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['recorded_by']);

            $table->dropIndex(['is_verified']);
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['is_recorded']);

            $table->dropColumn([
                'is_entry',
                'is_verified',
                'is_approved',
                'is_recorded',
                'verified_by',
                'verified_at',
                'approved_by',
                'approved_at',
                'recorded_by',
                'recorded_at',
            ]);
        });
    }
};
