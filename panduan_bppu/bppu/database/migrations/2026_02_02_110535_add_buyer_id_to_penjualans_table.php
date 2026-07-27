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
            // Add buyer_id after user_id
            // user_id = kasir/operator yang input
            // buyer_id = buyer member (nullable untuk transaksi manual/guest)
            $table->foreignId('buyer_id')->nullable()->after('user_id')->constrained('users')->onDelete('cascade');
            $table->index('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropIndex(['buyer_id']);
            $table->dropColumn('buyer_id');
        });
    }
};
