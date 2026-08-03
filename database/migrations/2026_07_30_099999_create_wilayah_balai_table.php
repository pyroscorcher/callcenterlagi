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
            
            // Core Relationships
            $table->foreignId('balai_id')->constrained('balais')->cascadeOnDelete();
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            $table->foreignId('kabupaten_kota_id')->constrained('kabupaten_kotas')->cascadeOnDelete();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->cascadeOnDelete();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnDelete();
            
            $table->timestamps();

            // Ensure uniqueness across the entire hierarchy per Balai
            $table->unique(
                ['balai_id', 'kelurahan_id'], 
                'balai_kelurahan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_balai');
    }
};