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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['MOBIL', 'MOTOR']);
            $table->string('nopol')->unique();
            $table->string('model');
            $table->year('tahun');
            $table->date('tgl_pajak')->nullable();
            $table->string('stnk_nama')->nullable();
            $table->string('no_gps')->nullable();
            $table->string('imei')->nullable();
            $table->unsignedBigInteger('kilometer')->default(0);
            $table->string('gambar')->nullable();
            $table->unsignedBigInteger('harga_6jam');
            $table->unsignedBigInteger('harga_12jam');
            $table->unsignedBigInteger('harga_24jam');
            $table->enum('status', ['TERSEDIA', 'DISEWA', 'PERBAIKAN'])->default('TERSEDIA');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
