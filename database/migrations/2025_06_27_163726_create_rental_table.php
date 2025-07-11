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
        Schema::create('rental', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewa_id')->constrained('penyewa');
            $table->foreignId('kendaraan_id')->constrained('kendaraan');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->unsignedBigInteger('biaya_dibayar')->nullable();
            $table->unsignedBigInteger('total_biaya')->nullable();
            $table->enum('status_rental', ['BERJALAN', 'SELESAI'])->default('BERJALAN');
            $table->enum('status_bayar', ['LUNAS', 'PENDING'])->default('PENDING');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental');
    }
};
