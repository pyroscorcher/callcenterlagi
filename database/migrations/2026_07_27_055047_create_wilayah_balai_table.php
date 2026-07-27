<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_balai', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel balais
            $table->foreignId('balai_id')->constrained('balais')->cascadeOnDelete();
            
            // Relasi ke tabel provinsis
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balai_provinsi');
    }
};