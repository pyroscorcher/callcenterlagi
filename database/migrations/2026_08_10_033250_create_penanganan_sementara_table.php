<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penanganan_sementara', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('tanggal'); // Changed to date() for better practice
            $table->string('kewenangan');
            
            // Added nullable() before constrained()
            $table->foreignId('balai_id')->nullable()->constrained('balais')->nullOnDelete(); 
            
            $table->string('keterangan');
            $table->integer('jumlah_personil');
        });

        Schema::create('penanganan_sementara_foto', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('penanganan_sementara_id')->constrained('penanganan_sementara')->cascadeOnDelete();
            $table->string('foto');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('keterangan')->nullable();
        });

        Schema::create('alat_dan_bahan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('penanganan_sementara_id')->constrained('penanganan_sementara')->cascadeOnDelete();
            $table->string('kategori');
            $table->string('kelas');
            $table->string('model');
            $table->integer('jumlah');
        });
    }

    public function down(): void
    {
        // Drop child tables first
        Schema::dropIfExists('alat_dan_bahan');
        Schema::dropIfExists('penanganan_sementara_foto');
        Schema::dropIfExists('penanganan_sementara');
    }
};