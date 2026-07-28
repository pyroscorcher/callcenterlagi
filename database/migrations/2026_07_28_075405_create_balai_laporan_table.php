<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balai_laporan', function (Blueprint $table) {
            $table->id();
            
            // Link to the laporan_masyarakats table
            $table->foreignId('laporan_id')->constrained('laporan_masyarakats')->onDelete('cascade');
            
            // Link to the balais table
            $table->foreignId('balai_id')->constrained('balais')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balai_laporan');
    }
};