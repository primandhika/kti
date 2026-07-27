<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potongan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_potongan', 20)->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['persen', 'nominal']); // persen = %, nominal = Rp
            $table->decimal('nilai', 15, 2);
            $table->decimal('minimum_transaksi', 15, 2)->default(0);
            $table->decimal('maksimum_potongan', 15, 2)->nullable(); // cap untuk tipe persen
            $table->date('berlaku_mulai')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->integer('kuota_voucher')->default(1); // berapa voucher yang bisa digenerate
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher', 20)->unique();
            $table->unsignedBigInteger('potongan_id');
            $table->enum('status', ['draft', 'printed', 'used'])->default('draft');
            $table->unsignedBigInteger('penjualan_id')->nullable(); // transaksi yg memakai
            $table->unsignedBigInteger('used_by')->nullable(); // user pembeli
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('potongan_id')->references('id')->on('potongan');
            $table->foreign('penjualan_id')->references('id')->on('penjualans');
            $table->foreign('used_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('potongan');
    }
};
