<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
// drop_pics_table
    public function up(): void { Schema::dropIfExists('pics'); }
    public function down(): void {
        Schema::create('pics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balai_id')->constrained('balais')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kontak');
            $table->timestamps();
        });
    }
};
