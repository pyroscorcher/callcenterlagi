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
        Schema::create('penanganan_permanen', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('tanggal');
            $table->string('kewenangan');
            $table->foreignId('balai_id')->constrained('balais')->nullOnDelete();
            $table->string('keterangan');
        });

        Schema::create('penanganan_permanen_foto', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('penanganan_permanen_id')->constrained('penanganan_permanen')->cascadeOnDelete();
            $table->string('foto');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penanganan_permanen');
    }
};
