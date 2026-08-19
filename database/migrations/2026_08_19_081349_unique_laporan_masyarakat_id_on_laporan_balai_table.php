<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_balai', function (Blueprint $table) {
            // One shared LaporanBalai per LaporanMasyarakat, regardless of
            // which Balai created it -- all assigned Balai jointly edit it.
            $table->unique('laporan_masyarakat_id');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_balai', function (Blueprint $table) {
            $table->dropUnique(['laporan_masyarakat_id']);
        });
    }
};