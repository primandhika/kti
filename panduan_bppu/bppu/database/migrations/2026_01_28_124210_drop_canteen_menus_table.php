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
        // Drop foreign key from cart_items first
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['canteen_menu_id']);
        });

        // Now drop the table
        Schema::dropIfExists('canteen_menus');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot rollback - would need to recreate table structure
    }
};
