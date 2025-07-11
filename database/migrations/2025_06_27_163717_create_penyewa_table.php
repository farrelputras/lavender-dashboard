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
        Schema::create('penyewa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat');
            $table->string('no_telp');
            $table->string('asal')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('jaminan1', ['KTP', 'KTM', 'SIM', 'LAINNYA']);
            $table->enum('jaminan2', ['KTP', 'KTM', 'SIM', 'LAINNYA'])->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewa');
    }
};
