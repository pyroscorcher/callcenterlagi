<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kewenangan_infrastruktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_balai_id')->constrained('laporan_balai')->cascadeOnDelete();
            
            $table->string('tipe'); // <--- YOU ARE MISSING THIS LINE

            // Balai fields
            $table->unsignedBigInteger('balai_id')->nullable();
            $table->string('unor')->nullable();
            $table->string('kepala')->nullable();
            $table->string('kontak')->nullable();
            
            // Delegasi fields
            $table->string('das')->nullable();
            $table->string('pch')->nullable();
            $table->string('ruas_jalan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('telepon')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kewenangan_infrastruktur');
    }
};