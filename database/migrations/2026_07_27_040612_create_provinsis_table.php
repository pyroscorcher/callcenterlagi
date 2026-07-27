<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 13)->unique()->nullable(); // Kode Kemendagri jika diperlukan
            $table->string('nama');
            
            // Relasi ke tabel balais. 
            // Nullable agar provinsi yang belum punya balai tidak error.
            $table->foreignId('balai_id')
                  ->nullable()
                  ->constrained('balais')
                  ->nullOnDelete(); 
                  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinsis');
    }
};