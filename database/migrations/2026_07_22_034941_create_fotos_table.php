<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            // Foreign key yang merujuk ke tabel laporan_masyarakats
            $table->foreignId('laporan_masyarakat_id')->constrained('laporan_masyarakats')->onDelete('cascade');
            $table->string('file_path'); // Menyimpan path/URL gambar
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fotos');
    }
};