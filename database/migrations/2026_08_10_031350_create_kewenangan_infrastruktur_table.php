<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kewenangan_infrastruktur', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('infrastruktur_terdampak_id')
                ->constrained('infrastruktur_terdampak')
                ->cascadeOnDelete();

            $table->enum('tipe', ['balai', 'delegasi']);

            // Type 1: Balai
            // NOTE: unor/kepala/kontak are intentionally denormalized snapshots
            // of the balais table at the time of reporting (audit trail — if a
            // Balai's kepala changes later, this report still reflects who was
            // responsible when it was filed). `unor` is ALSO used live on the
            // frontend to filter the balai_id dropdown before submission.
            $table->foreignId('balai_id')->nullable()->constrained('balais')->nullOnDelete();
            $table->string('unor')->nullable();
            $table->string('kepala')->nullable();
            $table->string('kontak')->nullable();

            // Type 2: Delegasi (handled by another party, still reported by Balai)
            $table->string('das')->nullable();
            $table->string('pch')->nullable();
            $table->string('ruas_jalan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('telepon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kewenangan_infrastruktur');
    }
};