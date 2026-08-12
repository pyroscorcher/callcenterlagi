<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporan_balai_logs', function (Blueprint $table) {
            $table->id();
            // Sesuaikan 'laporan_masyarakats' dengan nama tabel laporan Anda
            $table->foreignId('laporan_id')->constrained('laporan_masyarakats')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Opsional: Siapa user yang klik
            
            $table->string('action'); // 'created' atau 'updated'
            $table->string('nama_balai');
            $table->string('kepala_balai')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_balai_logs');
    }
};
