<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_laporan_pimpinan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('laporan_balai_id')
                ->constrained('laporan_balai')
                ->cascadeOnDelete();

            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('deskripsi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_laporan_pimpinan');
    }
};