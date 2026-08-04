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
        Schema::create('balais', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Added unique() since this is a login credential
            $table->string('password');
            $table->string('nama_balai');
            
            // Make these nullable so placeholders can be created without throwing errors
            $table->string('unker')->nullable();
            $table->string('unor')->nullable(); 
            $table->string('provinsi')->nullable();
            $table->string('pulau')->nullable();
            $table->string('kepala')->nullable();
            $table->string('kontak')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balais');
    }
};