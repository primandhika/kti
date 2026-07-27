<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Meja;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mejas', function (Blueprint $table) {
            $table->string('qr_token', 32)->nullable()->unique()->after('is_active');
        });

        // Generate token untuk meja yang sudah ada
        Meja::whereNull('qr_token')->each(function ($meja) {
            $meja->update(['qr_token' => Str::random(16)]);
        });
    }

    public function down(): void
    {
        Schema::table('mejas', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
