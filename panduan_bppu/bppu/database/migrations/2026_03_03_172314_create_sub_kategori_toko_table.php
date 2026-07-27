<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_kategori_toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Populate with existing sub_kategori from barangs marked for shop
        $subKategoris = DB::table('barangs')
            ->select('sub_kategori')
            ->where('show_in_shop', true)
            ->whereNotNull('sub_kategori')
            ->distinct()
            ->orderBy('sub_kategori')
            ->pluck('sub_kategori');

        foreach ($subKategoris as $index => $nama) {
            DB::table('sub_kategori_toko')->insert([
                'nama' => $nama,
                'urutan' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kategori_toko');
    }
};
