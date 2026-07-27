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
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->boolean('dana_cair')->default(false)->after('catatan');
            $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->onDelete('set null')->after('dana_cair');
            $table->timestamp('dicairkan_at')->nullable()->after('dicairkan_oleh');
            $table->text('catatan_pencairan')->nullable()->after('dicairkan_at');
        });
    }

    public function down(): void
    {
        Schema::table('pembagian_mitra_history', function (Blueprint $table) {
            $table->dropForeign(['dicairkan_oleh']);
            $table->dropColumn(['dana_cair', 'dicairkan_oleh', 'dicairkan_at', 'catatan_pencairan']);
        });
    }
};
