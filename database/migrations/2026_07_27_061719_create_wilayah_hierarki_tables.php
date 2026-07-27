<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan: Tabel provinsis sudah dibuat di langkah Skenario 2 sebelumnya

        Schema::create('kabupaten_kotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            $table->string('kode', 13)->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_kota_id')->constrained('kabupaten_kotas')->cascadeOnDelete();
            $table->string('kode', 13)->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kelurahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->cascadeOnDelete();
            $table->string('kode', 13)->unique();
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelurahans');
        Schema::dropIfExists('kecamatans');
        Schema::dropIfExists('kabupaten_kotas');
    }
};