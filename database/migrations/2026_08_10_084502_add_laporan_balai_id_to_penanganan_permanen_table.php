<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penanganan_permanen', function (Blueprint $table) {
            $table->foreignId('laporan_balai_id')
                ->after('id')
                ->constrained('laporan_balai')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penanganan_permanen', function (Blueprint $table) {
            $table->dropForeign(['laporan_balai_id']);
            $table->dropColumn('laporan_balai_id');
        });
    }
};