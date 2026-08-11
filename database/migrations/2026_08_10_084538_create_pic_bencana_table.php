<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picbencana', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('laporan_balai_id')
                ->unique() // enforces the one-to-one with laporan_balai
                ->constrained('laporan_balai')
                ->cascadeOnDelete();

            // Registered-user PIC — nama_pic/kontak/balai_id are snapshots
            // taken at assignment time, same pattern as
            // KewenanganInfrastruktur's Type 1.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pic')->nullable();
            $table->string('kontak')->nullable();
            $table->foreignId('balai_id')->nullable()->constrained('balais')->nullOnDelete();

            // Escape hatch when the PIC isn't a registered user at all
            $table->string('pic_lainnya')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picbencana');
    }
};