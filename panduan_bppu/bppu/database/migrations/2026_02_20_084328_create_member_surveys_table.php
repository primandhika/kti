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
        Schema::create('member_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Likert scale questions (1-5)
            $table->tinyInteger('rating_pelayanan')->comment('Bagaimana pelayanan kantin BPPU saat ini');
            $table->tinyInteger('rating_kualitas')->comment('Bagaimana kualitas menu yang tersedia saat ini');
            $table->tinyInteger('rating_variasi')->comment('Bagaimana variasi menu yang tersedia saat ini');
            $table->tinyInteger('rating_harga')->comment('Bagaimana kesesuaian harga dengan kualitas menu');
            $table->tinyInteger('rating_suasana')->comment('Bagaimana suasana kantin saat ini');

            // Essay questions
            $table->text('menu_favorit')->comment('Menu favorit Anda di Kantin IKIP Siliwangi saat ini');
            $table->text('menu_rekomendasi')->comment('Menu apa yang kamu rekomendasikan untuk ada di Kantin IKIP Siliwangi');
            $table->text('kritik_saran')->comment('Kritik dan saran untuk kantin');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_surveys');
    }
};
