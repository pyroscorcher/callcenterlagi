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
            $table->timestamps();
            $table->string('username');
            $table->string('password');
            $table->string('nama_balai');
            $table->string('unker');
            $table->string('unor');
            $table->string('provinsi');
            $table->string('pulau');
            $table->string('kepala');
            $table->string('kontak');
        });

        Schema::table('balais', function (Blueprint $table) {
            $table->rememberToken();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balais', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        Schema::dropIfExists('balais');
        
    }
};
