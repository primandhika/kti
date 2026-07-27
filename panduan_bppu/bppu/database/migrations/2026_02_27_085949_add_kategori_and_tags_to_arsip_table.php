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
        Schema::table('arsip', function (Blueprint $table) {
            $table->string('kategori_arsip')->nullable()->after('jenis');
            $table->json('tags')->nullable()->after('kategori_arsip');
            $table->text('deskripsi')->nullable()->after('tags');
            $table->string('folder')->nullable()->after('deskripsi');
            $table->string('original_dimensions')->nullable()->after('ukuran');
            $table->boolean('is_public')->default(false)->after('original_dimensions');

            $table->index('kategori_arsip');
            $table->index('folder');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropIndex(['kategori_arsip']);
            $table->dropIndex(['folder']);
            $table->dropIndex(['is_public']);

            $table->dropColumn([
                'kategori_arsip',
                'tags',
                'deskripsi',
                'folder',
                'original_dimensions',
                'is_public',
            ]);
        });
    }
};
