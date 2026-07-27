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
        Schema::create('user_work_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('work_unit_id')->constrained('work_units')->onDelete('cascade');
            $table->timestamps();

            // Composite unique: 1 user can be assigned to 1 work unit only once
            $table->unique(['user_id', 'work_unit_id']);

            // Indexes
            $table->index('user_id');
            $table->index('work_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_work_unit');
    }
};
