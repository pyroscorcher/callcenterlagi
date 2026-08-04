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
            
            $table->foreignId('balai_id')->constrained('balais')->cascadeOnDelete();
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            $table->foreignId('kabupaten_kota_id')->nullable()->constrained('kabupaten_kotas')->cascadeOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->cascadeOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->cascadeOnDelete();
            
            $table->timestamps();

            // Prevent duplicate exact assignments across all levels
            $table->unique(
                ['balai_id', 'provinsi_id', 'kabupaten_kota_id', 'kecamatan_id', 'kelurahan_id'], 
                'wilayah_balai_unique_assignment'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_balai');
    }
};