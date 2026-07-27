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
        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_id', 6)->unique()->comment('6-digit alfanumerik ID');
            $table->string('name')->comment('Nama unit kerja');
            $table->string('type')->comment('Tipe unit: kantin, shop, dll');
            $table->text('description')->nullable()->comment('Deskripsi/profil unit');
            $table->string('location')->nullable()->comment('Lokasi fisik unit');
            $table->string('manager_name')->nullable()->comment('Nama pengelola');
            $table->string('contact_phone')->nullable()->comment('Nomor kontak');
            $table->string('contact_email')->nullable()->comment('Email kontak');
            $table->string('operating_hours')->nullable()->comment('Jam operasional');
            $table->string('logo')->nullable()->comment('Logo/gambar unit');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->integer('display_order')->default(0)->comment('Urutan tampilan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_units');
    }
};
