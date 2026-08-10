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
        Schema::create('kewenangan_infrastruktur', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('kewenangan');
            $table->string('unor')->nullable();
            $table->foreignId('balai_id')->nullable()->constrained('balais')->nullOnDelete();
            $table->string('das')->nullable();
            $table->string('pch')->nullable();
            $table->string('ruas_jalan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('telepon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kewenangan_infrastruktur');
    }
};
