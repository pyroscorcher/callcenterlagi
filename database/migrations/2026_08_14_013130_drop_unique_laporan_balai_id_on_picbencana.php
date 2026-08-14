<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('picbencana', function (Blueprint $table) {
            $table->dropUnique(['laporan_balai_id']);
        });
    }

    public function down(): void
    {
        Schema::table('picbencana', function (Blueprint $table) {
            $table->unique('laporan_balai_id');
        });
    }
};