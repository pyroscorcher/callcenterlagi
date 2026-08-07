<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('balais', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'password', 'remember_token']);
        });
    }
    
    public function down(): void {
        Schema::table('balais', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
        });
    }
};
