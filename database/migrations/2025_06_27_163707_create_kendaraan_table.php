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
            $table->string('nopol')->unique();
            $table->enum('jenis', ['MOBIL', 'MOTOR']);
            $table->string('model');
            $table->year('tahun');
            $table->unsignedBigInteger('kilometer')->nullable();
            $table->string('bensin')->nullable();
            $table->enum('status', ['TERSEDIA', 'DISEWA', 'PERBAIKAN'])->default('TERSEDIA');
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
