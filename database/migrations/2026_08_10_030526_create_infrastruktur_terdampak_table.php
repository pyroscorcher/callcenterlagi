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
        Schema::create('infrastruktur_terdampak', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('unor');
            $table->string('kategori');
            $table->string('nama');
            $table->string('satuan');
            $table->integer('jumlah');
            $table->string('detail')->nullable();
            $table->string('dokumentasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastruktur_terdampak');
    }
};
