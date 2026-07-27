<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('reaction_type'); // like, informatif, inspiratif, bagikan
            $table->string('session_id', 64)->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'reaction_type', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
