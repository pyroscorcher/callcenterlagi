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
        Schema::create('laporan_masyarakats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('pelapor', 255);
            $table->string('telepon', 255);
            $table->string('jenis_bencana', 255);
            $table->string('nama_bencana', 255);
            $table->string('dampak_bencana', 255);
            $table->string('waktu_kejadian', 255);
            $table->string('wilayah_waktu', 255);
            $table->string('lokasi', 255);
            $table->string('deskripsi', 255);
            $table->string('infrastruktur_terdampak', 255);
            $table->string('status', 255);
            $table->string('detail_status', 255)->nullable();
            $table->string('kebutuhan_mendesak', 255)->nullable();
            $table->string('foto', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_masyarakats');
    }
};
