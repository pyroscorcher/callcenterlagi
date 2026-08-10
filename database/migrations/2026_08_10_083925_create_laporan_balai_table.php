<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_balai', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('laporan_masyarakat_id')
                ->constrained('laporan_masyarakats')
                ->cascadeOnDelete();

            $table->foreignId('balai_id')
                ->constrained('balais')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Adjust the enum values to match your actual workflow states
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'selesai'])
                ->default('draft');

            $table->date('tanggal_respon')->nullable();
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_balai');
    }
};