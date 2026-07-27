<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kabupaten_kotas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 13)->unique(); // e.g. "11.01"
            $table->string('nama', 100);
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kabupaten_kotas');
    }
};